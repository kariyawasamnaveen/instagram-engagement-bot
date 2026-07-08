<?php
defined('BASEPATH') or exit('No direct script access allowed');

class add_funds_model extends MY_Model
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $module;
    public $module_icon;

    public function __construct()
    {
        parent::__construct();
        $this->tb_users            = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments         = PAYMENTS_METHOD;
        $this->tb_payments_bonuses = PAYMENTS_BONUSES;
    }

    // Add fund, bonus and send email
    public function add_funds_bonus_email($data_tnx, $payment_id = "")
    {
        if (!$data_tnx || !isset($data_tnx->transaction_id)) {
            return false;
        }

        // Start transaction
        $this->db->trans_start();

        try {
            // Update Balance  and total spent
            $user = $this->get_user_by_id($data_tnx->uid);
            if (!$user) {
                throw new Exception("User not found");
            }

            $new_funds = $data_tnx->amount - $data_tnx->txn_fee;
            $new_balance = $user->balance + $new_funds;
            $total_spent = $this->calculate_total_spent($data_tnx->uid, $user->spent, $data_tnx->amount);

            // Update Transaction for previous balance
            $user_update_data = [
                "balance" => $new_balance,
                "spent" => $total_spent,
            ];
            $this->db->update($this->tb_users, $user_update_data, ["id" => $data_tnx->uid]);
            $this->db->update($this->tb_transaction_logs, ["old_balance" => $user->balance], ["id" => $data_tnx->id]);
            //Add bonus
            if ($payment_id) {
                $data_pm_bonus = [
                    'payment_id'      => $payment_id,
                    'uid'             => $data_tnx->uid,
                    'amount'          => $new_funds,
                    'id'              => $data_tnx->id ?? "",
                    'current_balance' => $new_balance,
                ];
                $this->add_payment_bonuses((object) $data_pm_bonus);
            }

            // Process affiliates if exists
            $this->process_affiliates($user, $data_tnx->amount);

           
            // Send email notification
            if (get_option("is_payment_notice_email", '')) {
                $this->send_mail_payment_notification(['user' => $user]);
            }

            // Commit transaction
            $this->db->trans_complete();

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in add_funds_bonus_email: ' . $e->getMessage());
            
            return false;
        }
    }

    // Helper function to get user data
    private function get_user_by_id($uid)
    {
        $fields = is_table_exists(AFFILIATE) ? 'id, first_name, last_name, email, balance, timezone, spent, ref_uid' : 'id, first_name, last_name, email, balance, timezone, spent';
        return $this->model->get($fields, $this->tb_users, ["id" => $uid]);
    }

    // Helper function to calculate total spent
    private function calculate_total_spent($uid, $user_spent, $txn_amount)
    {
        if ($user_spent === "") {
            $total_spent_before = $this->model->sum_results('amount', $this->tb_transaction_logs, ['status' => 1, 'uid' => $uid]);
            return round($total_spent_before + $txn_amount, 4);
        }
        return round($user_spent + $txn_amount, 4);
    }

    // Helper function to process affiliates
    private function process_affiliates($user, $txn_amount)
    {
        if (is_table_exists(AFFILIATE) && $user->ref_uid > 0) {
            $this->load->model('affiliates/affiliates_model', 'affiliates_model');
            $this->affiliates_model->save_item(['id' => $user->ref_uid, 'amount' => $txn_amount], ['task' => 'referral']);
        }
    }

    /**
     * Adds payment bonuses to the user's balance and logs the transaction.
     *
     * @param object $data_pm The payment data containing information such as user ID, payment amount, and current balance.
     * @return bool Returns true if the bonus was added successfully, false otherwise.
     */
    private function add_payment_bonuses($data_pm = "")
    {
        // Check if the input data is valid
        if (empty($data_pm) || empty($data_pm->payment_id)) {
            return false;
        }

        try {
            // Retrieve the bonus information from the database based on payment_id and amount
            $payment_bonus = $this->model->get("id, bonus_from, percentage, status", $this->tb_payments_bonuses, [
                'payment_id' => $data_pm->payment_id, 
                'status' => 1,                        
                'bonus_from <=' => $data_pm->amount 
            ]);

            // If no bonus information is found, return false
            if (!$payment_bonus) {
                return false;
            }

            // Calculate the bonus based on the percentage
            $bonus = ($payment_bonus->percentage / 100) * $data_pm->amount;

            // Update the user's balance by adding the calculated bonus
            $new_balance = $data_pm->current_balance + $bonus;
            $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => $data_pm->uid]);

            // Prepare the transaction log data to record the bonus transaction
            $data_tnx_log = [
                "ids" => ids(), 
                "uid" => $data_pm->uid,
                "type" => 'Bonus', 
                "transaction_id" => isset($data_pm->id) ? "Transaction Bonus #" . $data_pm->id : "", 
                "old_balance" => $data_pm->current_balance, // Previous balance
                "amount" => $bonus,
                "status" => 1, 
                "created" => NOW, 
            ];

            // Insert the transaction log into the database
            $this->db->insert($this->tb_transaction_logs, $data_tnx_log);
            $transaction_log_id = $this->db->insert_id();
            return true;
        } catch (Exception $e) {
            // Log any errors that occur during the process
            log_message('error', 'Error in add_payment_bonuses: ' . $e->getMessage());
            // Return false if an error occurred
            return false;
        }
    }

    private function send_mail_payment_notification($data_pm_mail = "")
    {
        if ($data_pm_mail['user']) {

            $user = $data_pm_mail['user'];
            $subject = get_option('email_payment_notice_subject', '');
            $message = get_option('email_payment_notice_content', '');
            // get Merge Fields
            $merge_fields = [
                '{{user_firstname}}' => $user->first_name,
            ];
            $template = ['subject' => $subject, 'message' => $message, 'type' => 'default', 'merge_fields' => $merge_fields];
            $send_message = $this->model->send_mail_template($template, $user->id);
            return true;
        } else {
            return false;
        }
    }
}
