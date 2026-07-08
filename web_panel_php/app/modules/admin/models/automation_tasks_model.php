<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class automation_tasks_model extends MY_Model
{
    public $tb_main;
    public $tb_logs;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = 'automation_tasks';
        $this->tb_logs = 'automation_logs';
    }

    public function create_tasks($order_id, $service_id, $action, $target, $quantity, $data = null, $schedule = [])
    {
        $tasks = [];
        $now = date('Y-m-d H:i:s');
        $quantity = (int) $quantity;
        $schedule_profile = $this->get_schedule_profile($quantity, $schedule);

        for ($i = 0; $i < $quantity; $i++) {
            $run_after = $this->get_task_run_after($now, $i, $schedule_profile);
            $task_data = $this->build_task_data($data, $schedule_profile['delivery_speed'], $i, $run_after, $schedule_profile);
            $tasks[] = [
                'order_id' => $order_id,
                'service_id' => $service_id,
                'action' => $action,
                'target' => $target,
                'data' => is_array($task_data) ? json_encode($task_data) : $task_data,
                'status' => 0,
                'run_after' => $run_after,
                'created' => $now,
                'changed' => $now
            ];
        }
        if (!empty($tasks)) {
            return $this->db->insert_batch($this->tb_main, $tasks);
        }
        return false;
    }

    private function get_schedule_profile($quantity, $schedule = [])
    {
        $delivery_speed = strtolower((string) ($schedule['delivery_speed'] ?? 'instant'));
        $interval_minutes = 0;
        $batch_size = max(1, (int) $quantity);

        if (!empty($schedule['runs']) && !empty($schedule['interval']) && !empty($schedule['dripfeed_quantity'])) {
            $interval_minutes = max(0, (int) $schedule['interval']);
            $batch_size = max(1, (int) $schedule['dripfeed_quantity']);
        } else {
            switch ($delivery_speed) {
                case 'organic':
                    $interval_minutes = 60;
                    $batch_size = max(1, (int) ceil($quantity / 10));
                    break;

                case 'slow':
                    $interval_minutes = 120;
                    $batch_size = max(1, (int) ceil($quantity / 24));
                    break;

                case 'elite':
                    $interval_minutes = 8;
                    $batch_size = 1;
                    break;

                default:
                    $delivery_speed = 'instant';
                    break;
            }
        }

        return [
            'delivery_speed' => $delivery_speed,
            'interval_minutes' => $interval_minutes,
            'batch_size' => $batch_size,
        ];
    }

    private function get_task_run_after($created_at, $task_index, $schedule_profile)
    {
        if (empty($schedule_profile['interval_minutes'])) {
            return null;
        }

        $batch_index = (int) floor($task_index / max(1, (int) $schedule_profile['batch_size']));
        if ($batch_index <= 0) {
            return null;
        }

        return date('Y-m-d H:i:s', strtotime('+' . ($batch_index * (int) $schedule_profile['interval_minutes']) . ' minutes', strtotime($created_at)));
    }

    private function build_task_data($data, $delivery_speed, $task_index, $run_after, $schedule_profile)
    {
        if (!is_array($data) && $data !== null) {
            return $data;
        }

        $payload = is_array($data) ? $data : [];
        $payload['delivery_speed'] = $delivery_speed;
        $payload['sequence'] = $task_index + 1;
        $payload['batch_size'] = (int) $schedule_profile['batch_size'];
        if ($run_after !== null) {
            $payload['run_after'] = $run_after;
        }

        return $payload;
    }

    public function get_pending_tasks($limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->tb_main);
        $this->db->where('status', 0);
        $this->db->where('(run_after IS NULL OR run_after <= NOW())');
        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    public function update_task($id, $data)
    {
        $data['changed'] = date('Y-m-d H:i:s');
        return $this->db->update($this->tb_main, $data, ['id' => $id]);
    }

    public function update_order_status($order_id, $status)
    {
        return $this->db->update(ORDER, ['status' => $status, 'changed' => date('Y-m-d H:i:s')], ['id' => $order_id]);
    }

    public function update_order_remains($order_id, $remains)
    {
        return $this->db->update(ORDER, ['remains' => $remains, 'changed' => date('Y-m-d H:i:s')], ['id' => $order_id]);
    }

    public function get_order_remains($order_id)
    {
        $this->db->select('remains');
        $this->db->from(ORDER);
        $this->db->where('id', $order_id);
        $query = $this->db->get();
        $result = $query->row();
        return ($result) ? (int)$result->remains : 0;
    }

    public function check_order_completion($order_id)
    {
        $this->db->select('count(id) as pending');
        $this->db->from($this->tb_main);
        $this->db->where('order_id', $order_id);
        $this->db->where_in('status', [0, 1]); // Pending or In-progress
        $query = $this->db->get();
        $result = $query->row();
        return ($result && $result->pending == 0);
    }

    public function refresh_order_status($order_id)
    {
        $this->db->select('status, count(id) as total');
        $this->db->from($this->tb_main);
        $this->db->where('order_id', $order_id);
        $this->db->group_by('status');
        $query = $this->db->get();

        $counts = [
            0 => 0, // pending
            1 => 0, // processing
            2 => 0, // success
            3 => 0, // failed
        ];

        foreach ($query->result() as $row) {
            $counts[(int) $row->status] = (int) $row->total;
        }

        if (($counts[0] + $counts[1]) > 0) {
            return $this->update_order_status($order_id, ORDER_STATUS_PROCESSING);
        }

        if ($counts[2] > 0 && $counts[3] > 0) {
            return $this->update_order_status($order_id, ORDER_STATUS_PARTIAL);
        }

        if ($counts[2] > 0) {
            return $this->update_order_status($order_id, ORDER_STATUS_COMPLETED);
        }

        if ($counts[3] > 0) {
            return $this->update_order_status($order_id, ORDER_STATUS_FAIL);
        }

        return $this->update_order_status($order_id, ORDER_STATUS_AWAITING);
    }

    public function log_result($task_id, $account_id, $proxy_id, $action, $result, $response)
    {
        $data = [
            'task_id' => $task_id,
            'account_id' => $account_id,
            'proxy_id' => $proxy_id,
            'action' => $action,
            'result' => $result,
            'response' => is_array($response) ? json_encode($response) : $response,
            'created' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert($this->tb_logs, $data);
    }
}
