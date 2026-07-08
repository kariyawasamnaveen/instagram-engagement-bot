<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ig_accounts_model extends MY_Model
{
    protected $tb_main = 'ig_accounts';

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = 'ig_accounts';
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $this->db->select('*');
            $this->db->from($this->tb_main);
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->db->get_where($this->tb_main, ['id' => $params['id']])->row_array();
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        $data = array(
            "username" => post('username'),
            "password" => post('password'),
            "proxy" => post('proxy'),
            "gender" => post('gender'),
            "interest_tags" => post('interest_tags'),
            "status"        => post('status'),
            "notes"         => post('notes'),
            "updated"       => date('Y-m-d H:i:s'),
        );

        if ($option['task'] == 'add-item') {
            $data['created'] = date('Y-m-d H:i:s');
            $this->db->insert($this->tb_main, $data);
            return array("status" => "success", "message" => "Added successfully");
        }

        if ($option['task'] == 'edit-item') {
            $this->db->update($this->tb_main, $data, array('id' => post('id')));
            return array("status" => "success", "message" => "Updated successfully");
        }

        if ($option['task'] == 'bulk-append') {
            $items = post('bulk_items');
            $lines = explode("\n", $items);
            $count = 0;
            foreach ($lines as $line) {
                $parts = explode("|", trim($line));
                if (count($parts) >= 2) {
                    $insert_data = [
                        'username' => $parts[0],
                        'password' => $parts[1],
                        'proxy' => isset($parts[2]) ? $parts[2] : '',
                        'gender' => isset($parts[3]) ? strtolower($parts[3]) : 'neutral',
                        'interest_tags' => isset($parts[4]) ? $parts[4] : '',
                        'status' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert($this->tb_main, $insert_data);
                    $count++;
                }
            }
            return array("status" => "success", "message" => "Imported $count accounts successfully");
        }

        if ($option['task'] == 'bulk-action') {
            if (empty($params['ids'])) {
                return ["status"  => "error", "message" => 'Please choose at least one item'];
            }
            $arr_ids = convert_str_number_list_to_array($params['ids']);
            switch ($params['type']) {
                case 'delete':
                    $this->db->where_in('id', $arr_ids);
                    $this->db->delete($this->tb_main);
                    return ["status"  => "success", "message" => 'Delete successfully'];
                    break;
                case 'deactive':
                    $this->db->where_in('id', $arr_ids);
                    $this->db->update($this->tb_main, ['status' => 0]);
                    return ["status"  => "success", "message" => 'Update successfully'];
                    break;
                case 'active':
                    $this->db->where_in('id', $arr_ids);
                    $this->db->update($this->tb_main, ['status' => 1]);
                    return ["status"  => "success", "message" => 'Update successfully'];
                    break;
            }
        }
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        if($option['task'] == 'delete-item'){
            $item = $this->get("id", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted successfully',
                    "ids"     => $params['id'],
                ];
            }else{
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }
        return $result;
    }

    public function get_available_accounts($limit = 5, $cooldown_minutes = 30)
    {
        $this->db->select('*');
        $this->db->from($this->tb_main);
        $this->db->where('status', 1);
        $this->db->group_start();
        $this->db->where('last_action_at IS NULL');
        $this->db->or_where('last_action_at <=', date('Y-m-d H:i:s', strtotime("-$cooldown_minutes minutes")));
        $this->db->group_end();
        $this->db->order_by('last_action_at', 'ASC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    public function update_action_time($id, $status_message = null)
    {
        // Anti-ban Cooldown Engine
        $account = $this->db->get_where($this->tb_main, ['id' => $id])->row();
        if ($account) {
            $has_actions_done = $this->db->field_exists('actions_done', $this->tb_main);
            $actions_done = $has_actions_done && isset($account->actions_done) ? ((int)$account->actions_done + 1) : null;

            $data = [
                'updated' => date('Y-m-d H:i:s')
            ];

            if ($status_message === 'BANNED/CHECKPOINT') {
                $data['status_message'] = $status_message;
                $data['status'] = 3;
            } else {
                if ($has_actions_done && $actions_done >= 15) {
                    // Cooldown for 4 hours
                    $data['last_action_at'] = date('Y-m-d H:i:s', strtotime("+4 hours"));
                    $data['actions_done'] = 0;
                    $data['status_message'] = "Cooldown mode (15 actions reached)";
                } else {
                    $data['last_action_at'] = date('Y-m-d H:i:s');
                    if ($has_actions_done) {
                        $data['actions_done'] = $actions_done;
                    }
                    if ($status_message !== null) {
                        $data['status_message'] = $status_message;
                    }
                }
            }

            return $this->db->update($this->tb_main, $data, ['id' => $id]);
        }
        return false;
    }
}
