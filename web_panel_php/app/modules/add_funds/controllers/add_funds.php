<?php
defined('BASEPATH') or exit('No direct script access allowed');

class add_funds extends My_UserController
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
        $this->load->model(get_class($this) . '_model', 'model');
        $this->module                       = get_class($this);
        $this->tb_users                     = USERS;
        $this->tb_transaction_logs          = TRANSACTION_LOGS;
        $this->tb_payments                  = PAYMENTS_METHOD;
        $this->tb_payments_bonuses          = PAYMENTS_BONUSES;
    }

    public function index()
    {
        /*----------  Get Payment Gate Way for user  ----------*/
        $items_payments = $this->model->fetch('type, name, id, params', $this->tb_payments, ['status' => 1], 'sort', 'ASC', '', '', true);
        $item_users = $this->model->get('settings', $this->tb_users, ['id' => session('uid')], '', '', true);
        $limit_payments = get_value($item_users['settings'], 'limit_payments');
        
        if (!empty($items_payments) && is_array($items_payments)) {
            if (empty($limit_payments)) {
                $active_payments = $items_payments;
            } else {
                $active_payments = array_filter($items_payments, function($item) use ($limit_payments) {
                    $type = $item['type'] ?? '';
                    return !isset($limit_payments[$type]) || $limit_payments[$type] == 1;
                });
            }
        }
      
        $data = array(
            "module"          => get_class($this),
            "active_payments" => $active_payments,
            "currency_code"   => get_option("currency_code", 'USD'),
            "currency_symbol" => get_option("currency_symbol", '$'),
        );
        $this->template->set_layout('user');
        $this->template->build('index', $data);
    }

    public function process()
    {
        _is_ajax($this->module);
        $payment_id     = (int)post("payment_id");
        
        $amount         = (double)post("amount");
        $agree = post("agree");
        if ($amount == "") {
            ms(array(
                "status" => "error",
                "message" => lang("amount_is_required"),
            ));
        }

        if ($amount < 0) {
            ms(array(
                "status" => "error",
                "message" => lang("amount_must_be_greater_than_zero"),
            ));
        }

        /*----------  Check payment method  ----------*/
        $payment = $this->model->get('id, type, name, params', $this->tb_payments, ['id' => $payment_id]);
        if (!$payment) {
            _validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
        }

        $min_payment = get_value($payment->params, 'min');
        $max_payment = get_value($payment->params, 'max');

        if ($amount < $min_payment) {
            _validation('error', lang("minimum_amount_is") . " " . $min_payment);
        }

        if ($max_payment > 0 && $amount > $max_payment) {
            _validation('error', 'Maximal amount is' . " " . $max_payment);
        }

        if (!$agree) {
            _validation('error', lang("you_must_confirm_to_the_conditions_before_paying"));
        }

        $data_payment = array(
            "module" => get_class($this),
            "amount" => $amount,
        );
        $payment_method = $payment->type;
        require_once $payment_method . '.php';
        $payment_module = new $payment_method($payment);
        $payment_module->create_payment($data_payment);

    }

    public function success()
    {
        $id = session("transaction_id");
        $transaction = $this->model->get("*", $this->tb_transaction_logs, ['id' => $id, 'uid' => session('uid')], '','', true);
        if (!empty($transaction)) {
            $data = array(
                "module" => get_class($this),
                "transaction" => $transaction,
            );
            unset_session("transaction_id");
            $this->template->set_layout('user');
            $this->template->build('payment_successfully', $data);
        } else {
            redirect(cn("add_funds"));
        }
    }

    public function unsuccess()
    {
        $data = array(
            "module" => get_class($this),
        );
        $this->template->set_layout('user');
        $this->template->build('payment_unsuccessfully', $data);
    }
}
