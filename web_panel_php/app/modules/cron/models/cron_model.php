<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class cron_model extends MY_Model {
    protected $tb_main;

    public function __construct(){
        parent::__construct();
        $this->get_class();
        $this->tb_main    = ORDER;
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        
        if ($option['task'] == 'list-items-new-order') {
            $this->db->select("o.*");
            $this->db->select("s.overflow as service_overflow");
            $this->db->from($this->tb_main . ' o');
            $this->db->join($this->tb_services . ' s', 'o.service_id = s.id', 'left');
            $this->db->where('o.status', ORDER_STATUS_AWAITING);
            $this->db->where("o.mode", 1); // auto
            $this->db->where("o.api_order_id", -1);
            $this->db->where("o.is_drip_feed !=", 1);
            $this->db->order_by("o.id", 'ASC');
            $this->db->limit(15, 0);
            $query = $this->db->get();
            $result = $query->result();
        }

        // New order by drip-feed
        if ($option['task'] == 'list-items-dripfeed-new-order') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where('status', ORDER_STATUS_ACTIVE);
            $this->db->where("mode", 1); // auto
            $this->db->where("api_order_id", -1);
            $this->db->where("is_drip_feed", 1);
            $this->db->where('changed <', NOW);
            $this->db->order_by("id", 'ASC');
            $this->db->limit(5, 0);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', [ORDER_STATUS_ACTIVE, ORDER_STATUS_PROCESSING, ORDER_STATUS_INPROGRESS, ORDER_STATUS_PENDING]);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type !=', 'subscriptions');
            $this->db->where('is_drip_feed', 0);
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-dripfeed-status_old') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', [ORDER_STATUS_ACTIVE, ORDER_STATUS_PROCESSING, ORDER_STATUS_INPROGRESS, ORDER_STATUS_PENDING]);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type', 'default');
            $this->db->where('is_drip_feed', 1);
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-subscriptions-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('sub_status', [ORDER_STATUS_ACTIVE, ORDER_STATUS_PAUSED, '']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type', 'subscriptions');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-multiple-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', [ORDER_STATUS_ACTIVE, ORDER_STATUS_PROCESSING, ORDER_STATUS_INPROGRESS, ORDER_STATUS_PENDING]);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type !=', 'subscriptions');
            $this->db->where('is_drip_feed !=', 1);
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item-provider') {
            $result = $this->get('url, key, type, id', $this->tb_api_providers, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        if ($option['task'] == 'item-new-update') {
            $order_log =  "Order ID - ". $params['order_id'];
            if (!$params['response']) {
                $data_item = [
                    "status"      => 'error',
                    "note"        => 'Troubleshooting API requests',
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['order'])) {
                $data_item = [
                    "status"        => ORDER_STATUS_PENDING,
                    "api_order_id"  => $params['response']['order'],
                    "changed"       => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            echo $order_log . '<br>';
        }

        // For single Order
        if ($option['task'] == 'item-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];

            $this->db->trans_start();
            try {
                if (isset($params['response']['error'])) {
                    $order_log .= " : ". $params['response']['error'];
                    $data_item = [
                        "status"      => 'error',
                        "note"        => $params['response']['error'],
                        "changed"     => NOW,
                    ];
                    $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
                }

                if (isset($params['response']['status'])) {
                    $order_log .= " : ". $params['response']['status'];
                    $received_status = order_status_format($params['response']['status']);
                    $rand_time = get_random_time_by_created($item['created']);

                    $response_log_details = [
                        'last_check' => NOW,
                        'data' => $params['response'],
                    ];
                    
                    $data_item = [
                        "start_counter" => $params['response']['start_count'],
                        "remains"       => order_remains_format($params['response']['remains']),
                        "note" 	        => "",
                        "changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                        "status"        => $received_status,
                        "details"        => json_encode($response_log_details),
                    ];

                    if ($params['response']['charge']) {
                        $data_item['formal_charge'] = (float) $params['response']['charge'];
                    }
                    
                    //Check refill order or not
                    if (strtolower($params['response']['status']) == "completed") {
                        $data_item['finished'] = NOW; // for avg time
                        if ($item['refill'] && is_table_exists($this->tb_orders_refill)) {
                            $data_item['refill_status'] = 'completed';
                            $data_item['refill_date'] = date('Y-m-d H:i:s', strtotime(NOW) + 86400); //next refill request 24h
                        }
                    }
                    
                    if (in_array($received_status, ['refunded', 'partial', 'canceled'])) {
                        if($params['response']['remains'] > $item['quantity']) { 
                            $params['response']['remains'] = $item['quantity']; 
                        }
                        $new_order_attr = calculate_order_by_status($item, ['status' => $received_status, 'remains' => $params['response']['remains']]);
                        $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $new_order_attr['refund_money']], ['task' => 'update-balance']);
                        $data_item['charge']        = $new_order_attr['real_charge'];
                        $data_item['profit']        = $new_order_attr['profit'];
                    } else {
                        $data_item['profit']        = $item['charge'] - (float) $params['response']['charge'];
                    }
                    $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
                }

                if ($this->db->trans_status() === FALSE) {
                    $order_log .= " : Transaction failed and rollbacked!";
                } else {
                    $order_log .= " : Transaction completed successfully!";
                }
            
            } catch (Exception $e) {
                $order_log .= " : Exception occurred - " . $e->getMessage();
            }

            $this->db->trans_complete();
            echo $order_log . '<br>';
        }

        // For multi Order
        if ($option['task'] == 'item-multiple_status') {
            $order_log = "";
            $rand_time = get_random_time();
            $data_item = [
                "changed"     => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
            ];
            // For Error orders
            if (isset($params['response']['error'])) {
                $order_log = "ID: " . implode(", ", $params['order_ids']) . " - ". $params['response']['error'];
                $data_item['status'] = 'error';
                $data_item['note'] = $params['response']['error'];
            }
            $this->db->where_in('id', $params['order_ids']);
            $this->db->update($this->tb_main, $data_item);
            echo $order_log . '<br>';
        }

        // For single dripfeed Order
        if ($option['task'] == 'item-dripfeed-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }

            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $rand_time = get_random_time();
                $status_dripfeed = order_status_format($params['response']['status'], 'dripfeed');
                $data_item = [
                    "changed"  => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                    "status"   => $status_dripfeed,
                ];
                if (isset($params['response']['runs'])) {
                    $data_item['sub_response_orders'] = json_encode((object)$params['response']);
                }else{
                    switch ($params['response']['status']) {
                        case 'Completed':
                            $params['response']['status'] = 'Completed';
                            $params['response']['runs']   = $item['runs'];
                            break;

                        case 'In progress':
                            $params['response']['status'] = 'Inprogress';
                            $params['response']['runs']   = 0;
                            break;

                        case 'Canceled':
                            $params['response']['status'] = 'Canceled';
                            $params['response']['runs']   = 0;
                            break;
                    }
                    $data_item['sub_response_orders'] = json_encode((object)$params['response']);
                }
                /*----------  Add new order from reponse Drip-feed Service data  ----------*/
                if (isset($params['response']['orders'])) {
                    $this->create_order_log($params, ['task' => 'dripfeed']);
                }
                // Return back to user balance
                if (in_array($params['response']['status'], ['Partial', 'Canceled'])) {
                    $charge = $item['charge'];
                    $data_item['quantity'] = 0;
                    $data_item['charge']   = 0;
                    $return_funds = $charge;

                    if ($params['response']['status'] == "Partial") {
                        $return_funds     = $charge - ($charge * ($params['response']['runs'] / $item['runs']));
                        $data_item['quantity'] = $params['response']['runs']  * $item['dripfeed_quantity'];
                        $data_item['charge']   = $charge * ($params['response']['runs']  / $item['runs']);
                    }
                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $return_funds], ['task' => 'update-balance']);
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }

        // For single subscriptions Order
        if ($option['task'] == 'item-subscriptions-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            
            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $data_item = array(
                    "status"        		    => order_status_format($params['response']['status'], 'subscriptions'),
                    "sub_status"        		=> $params['response']['status'],
                    "sub_response_orders" 	    => json_encode((object)$params['response']),
                    "sub_response_posts" 	    => $params['response']['posts'],
                    "note" 	                    => "",
                    "changed"           		=> date('Y-m-d H:i:s', strtotime(NOW) + get_random_time()),
                );
                /*----------  Add new order from reponse Drip-feed Service data  ----------*/
                if (isset($params['response']['orders'])) {
                    $this->create_order_log($params, ['task' => 'subscriptions']);
                }
                // Return back to user balance if Expired, Canceled
                if (in_array($params['response']['status'], ['Expired', 'Canceled', 'Paused'])) {
                    $return_funds = $item['charge'];
                    if (in_array($params['response']['status'], ['Expired', 'Paused'])) {
                        $return_funds  = $item['charge'] * (1 - ((int)$params['response']['posts'] / $item['sub_posts']));
                        $data_item['charge']   = $item['charge'] - $return_funds;
                    } else {
                        $data_item['charge'] = 0;
                    }
                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $return_funds], ['task' => 'update-balance']);
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }
    }

    public function save_item_by_dripfeed($params = [])
    {
        $item = $params['item'] ?? '';
        if (!$item) {
            return false;
        }
        $response_orders = $item['sub_response_orders'];
        $dripfeed_delivery = get_value($response_orders, 'runs');
        $minutes_passed = (strtotime(NOW) - strtotime($item['changed'])) / 60;
        if ($dripfeed_delivery >= $item['runs']) {
            $db_item_main = [
                'status' => ORDER_STATUS_COMPLETED,
                'changed' => NOW,
            ];
            $this->db->update($this->tb_main, $db_item_main, ['id' => $item['id']]);
            return true;
        } else {
            $db_item = $item;
            $db_item['ids'] = ids();
            $db_item['is_drip_feed'] = 0;
            $db_item['main_order_id'] = $item['id'];
            $db_item['status']   = ORDER_STATUS_AWAITING;
            $db_item['order_source_type']   = ORDER_SOURCE_DRIPFEED;
            $db_item['created']  = NOW;
            $db_item['changed']  = NOW;
            $db_item['quantity'] = $item['dripfeed_quantity'];
            $db_item['charge']   = $item['charge'] / $item['runs'];
            unset($db_item['id']);
            unset($db_item['sub_response_orders']);
            unset($db_item['runs']);
            unset($db_item['interval']);
            unset($db_item['dripfeed_quantity']);

            $this->db->trans_start();
            $this->db->insert($this->tb_main, $db_item);
            $new_order_id = $this->db->insert_id();

            // Create sub_reponse_orders
            $response_orders = $response_orders ? json_decode($response_orders, true) : [];
            $sub_response_orders = json_decode($item['sub_response_orders'], true);

            if ($sub_response_orders === null) {
                $sub_response_orders = ['runs' => 0];
            }

            $sub_response_orders['runs'] = (int) $sub_response_orders['runs'] + 1;

            if (!isset($sub_response_orders['orders']) || !is_array($sub_response_orders['orders'])) {
                $sub_response_orders['orders'] = [];
            }
            if ($sub_response_orders['runs'] === $item['runs']) {
                $sub_response_orders['status'] = 'Completed'; 
            } else {
                $sub_response_orders['status'] = 'Active'; 
            }
            $sub_response_orders['orders'][] = $new_order_id;
            $db_item_main = [
                'changed'       => date('Y-m-d H:i:s', time() + $item['interval'] * 60),
                'sub_response_orders' => json_encode($sub_response_orders, JSON_UNESCAPED_UNICODE),
            ];
            $this->db->update($this->tb_main, $db_item_main, ['id' => $item['id']]);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return false;
            }
    
            return true;
        } 

    }

    private function create_order_log($params = [], $option = [])
    {
        $item        = $params['item'];
        $item_api    = $params['item_api'];
        $db_orders = json_decode($item['sub_response_orders']);
        $new_orders   = [];
        if (isset($db_orders->orders)) {
            $new_orders = array_diff($params['response']['orders'], $db_orders->orders);
        }else{
            $new_orders = $params['response']['orders'];
        }
        if (empty($new_orders)) return false;
        $data_orders_batch = [];
        foreach ($new_orders as $order_id) {
            $exists_order = $this->get('id', $this->tb_main, ['api_order_id' => $order_id, 'service_id' => $item['service_id'], 'api_provider_id' => $item['api_provider_id']]);
            if (!empty($exists_order)) continue;
            $data_order = [
                "ids" 	        	  => ids(),
                "uid" 	        	  => $item['uid'],
                "cate_id" 	    	  => $item['cate_id'],
                "service_id" 		  => $item['service_id'],
                "main_order_id"       => $item['id'],
                "service_type" 		  => "default",
                "api_provider_id"  	  => $item['api_provider_id'],
                "api_service_id"  	  => $item['api_service_id'],
                "api_order_id"  	  => $order_id,
                "status"  	          => 'awaiting',
                "changed" 	    	  => NOW,
                "created" 	    	  => NOW,
            ];

            if ($option['task'] == 'dripfeed') {
                $data_order['link']          = $item['link'];
                $data_order['quantity']      = $item['dripfeed_quantity'];
                $data_order['charge']        = ($item['charge'] * $item['dripfeed_quantity']) / $item['quantity'];;
            }

            if ($option['task'] == 'subscriptions') {
                $data_order['link']          = "https://www.instagram.com/". $item['username'];
                $data_order['quantity']      = $item['sub_max'];
                $data_order['charge']        = $item['charge'] / $item['sub_posts'];
            }

            $data_orders_batch[] = $data_order;
        }
        if (!empty($data_orders_batch)) {
            $this->db->insert_batch($this->tb_main, $data_orders_batch);
            return true;
        }
    }
    public function get_active_local_subscriptions()
    {
        $this->db->select("o.*, s.name as service_name, s.min as service_min, s.max as service_max");
        $this->db->from(ORDER . ' o');
        $this->db->join(SERVICES . ' s', 'o.service_id = s.id', 'left');
        $this->db->where('o.service_type', 'subscriptions');
        $this->db->where('o.sub_status', 'Active');
        $this->db->where('(o.api_provider_id IS NULL OR o.api_provider_id = 0)');
        // If monthly, check expiry_at. If post-based, check sub_response_posts
        $this->db->where('((o.is_monthly = 0 AND o.sub_response_posts < o.sub_posts) OR (o.is_monthly = 1 AND o.expiry_at > NOW()))');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_subscription_post_history($subscription_id, $media_id, $new_order_id)
    {
        $subscription = $this->get('*', ORDER, ['id' => $subscription_id]);
        if (!$subscription) return false;

        $history = json_decode($subscription->sub_response_orders, true);
        if (!$history) $history = ['orders' => [], 'media_ids' => []];
        
        if (!isset($history['media_ids'])) $history['media_ids'] = [];
        
        $history['orders'][] = $new_order_id;
        $history['media_ids'][] = $media_id;
        
        $data = [
            'sub_response_posts' => $subscription->sub_response_posts + 1,
            'sub_response_orders' => json_encode($history),
            'changed' => NOW
        ];

        // If reached max posts, Mark as completed
        if ($data['sub_response_posts'] >= $subscription->sub_posts) {
            $data['sub_status'] = 'Completed';
            $data['status'] = 'completed';
        }

        return $this->db->update(ORDER, $data, ['id' => $subscription_id]);
    }
}
