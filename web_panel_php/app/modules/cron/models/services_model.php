<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class services_model extends MY_Model {
    protected $tb_main;
    protected $orders_for_avg_time;
    protected $max_service_requests;

    public function __construct(){
        parent::__construct();
        $this->tb_main     = SERVICES;
        $this->orders_for_avg_time      = get_option("default_orders_for_avg_time", 10);
        $this->max_service_requests     = app_config('config')['service_avg_time']['max_service_requests'];
    }

    public function update_avg_time() {
        // Start the transaction to ensure atomicity of the updates
        $this->db->trans_start();
        $this->db->select('s.id as service_id');
        $this->db->from($this->tb_main . ' s');
        $this->db->where('s.last_check_avg_time <', NOW);
        $this->db->where('s.status', 1);
        $this->db->order_by('s.last_check_avg_time', 'ASC');
        $this->db->limit($this->max_service_requests); 
        $service_query = $this->db->get();
        if ($service_query->num_rows() > 0) {
            $services = $service_query->result_array();

            $update_data = [];

            foreach ($services as $service) {
                $service_id = $service['service_id'];

                $this->db->select('created, finished');
                $this->db->from($this->tb_order);
                $this->db->where('status', 'completed');
                $this->db->where('service_id', $service_id);
                $this->db->where('finished IS NOT NULL');
                $this->db->where('finished >=', 'CURDATE() - INTERVAL 2 MONTH');
                $this->db->order_by('finished', 'DESC');
                $this->db->limit($this->orders_for_avg_time);  

                $order_query = $this->db->get();

                $item_data = [
                    'id' => $service_id, 
                    'last_check_avg_time' => NOW 
                ];

                if ($order_query->num_rows() > 0) {
                    $time_diffs = $order_query->result_array();
                    $total = 0;  
                    $count = 0;  
                
                    foreach ($time_diffs as $item) {
                        $created = strtotime($item['created']);
                        $finished = strtotime($item['finished']);
                        
                        $time_diff_seconds = $finished - $created;
                        
                        $total += $time_diff_seconds;
                        $count++;
                    }
                
                    $avg_time = ($count > 0) ? $total / $count : 0;
                    $item_data['avg_time'] = (int) round($avg_time);  
                } else {
                    $item_data['avg_time'] = '';  
                }

                echo $item_data['id'] . ' - '. $item_data['avg_time'] . '<br>';
                $update_data[] = $item_data;
            }

            if (!empty($update_data)) {
                $this->db->update_batch($this->tb_main, $update_data, 'id');
            }
        }
    
        // Complete the transaction
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    
}
