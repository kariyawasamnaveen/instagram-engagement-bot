<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cancel_model extends MY_Model
{
    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->get_class();
        $this->tb_main = ORDERS_CANCEL;
    
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->get("*", $this->tb_main, ['ids' => $params['ids']], '', '', true);
        }
       
        if ($option['task'] == 'get-item-provider') {
            $result = $this->get('url, key, type, id', $this->tb_api_providers, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'item-refill-status') {
            $item = $params['item'];
            $order_log = "ID " . $item['id'];
            if (isset($params['response']['error'])) {
                $order_log .= " : " . $params['response']['error'];
                $data_item = [
                    "status" => 'error',
                    'details' => json_encode($params['response']),
                    "changed" => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            if (isset($params['response']['status'])) {
                $order_log .= " : " . $params['response']['status'];

                $data_item_refill = [
                    'changed' => date('Y-m-d H:i:s', strtotime(NOW) + rand(600, 3600)),
                    'last_updated' => NOW,
                    'details' => json_encode($params['response']),
                    'status' => order_status_format($params['response']['status']),
                ];

                $data_item_order = [
                    'refill_status' => order_status_format($params['response']['status']),
                ];

                //Check refill order or not
                $this->db->update($this->tb_main, $data_item_refill, ['id' => $item['id']]);
                $this->db->update($this->tb_order, $data_item_order, ['id' => $item['order_id']]);

            }
            echo $order_log . '<br>';
        }
        // create
        if ($option['task'] == 'item-cancel-create') {
            $item_order = null;
            if ($option['request'] == 'api') {
                if (empty($params['order_id'])) {
                    return ['error' => 'Incorrect order ID'];
                }
                $item_where = ['id' => $params['order_id'], 'uid' => $params['uid']];
            } else {
                if (empty($params['ids'])) {
                    return ['error' => 'Incorrect order ID'];
                }

                $item_where = ['ids' => $params['ids'], 'uid' => session('uid')];
            }
            $item_order = $this->get("id, ids, cancel, uid, api_provider_id, api_order_id, status, created", $this->tb_order, $item_where, '', '', true);
            if (!$item_order) {
                return ['error' => 'Incorrect order ID'];
            }

            if (in_array($item_order['status'], ['completed', 'canceled', 'partial' ])) {
                return ['error' => 'The order can not send request for cancellation, please raise ticket'];
            }
            if (!$item_order['cancel']) {
                return ['error' => 'Do not allow cancel this order'];
            }
            
            //Cancel task exists or not
            $item_cancel_log_exists = $this->get("id", $this->tb_main, ['order_id' => $item_order['id']], '', '', true);
            if ($item_cancel_log_exists) {
                return ['error' => 'Order cancellation request has been sent already'];
            }
            // Collect Item cancellation data
            $data_item_cancel = [
                'ids' => ids(),
                'uid' => $item_order['uid'],
                'order_id' => $item_order['id'],
                'request' => 0, //0: web, 1: API
                'cancel_type' => 0, //0: manual, 1: API
                'api_id' => ($item_order['api_provider_id']) ? $item_order['api_provider_id'] : '',
                'last_updated' => NOW,
                'changed' => NOW,
                'created' => NOW,
            ];
           
            if ($item_order['cancel']) {
                if ($item_order['api_order_id'] > 1) {
                    $item_api = $this->get('name, id, url, key, type', $this->tb_api_providers, ['id' => $item_order['api_provider_id']], '', '', true);
                    if ($item_api) {
                        $provider = new Smm_api();
                        $response = $provider->cancel($item_api, [$item_order['api_order_id']]);
                        if ($response && !isset($response['error'])) {
                            $response = array_sort_by_new_key($response, 'order');
                            if (isset($response[$item_order['api_order_id']])) {
                                $response_item_api = $response[$item_order['api_order_id']];
                                $data_item_cancel['api_cancel_id'] = (is_array($response_item_api['cancel']) && isset($response_item_api['cancel']['error'])) ? '00' : $response_item_api['cancel'];
                                $data_item_cancel['cancel_type'] = 1;
                                $data_item_cancel['status'] = (is_array($response_item_api['cancel']) && isset($response_item_api['cancel']['error'])) ? ORDER_STATUS_REJECTED : ORDER_STATUS_PENDING;
                                $data_item_cancel['details'] = json_encode(['status' => 'Successfully sent to API']);
                            }
                        }
                    }
                } else {
                    $data_item_cancel['cancel_type'] = 0;
                    $data_item_cancel['status'] = 'pending';
                }
            }
            $this->db->insert($this->tb_main, $data_item_cancel);
            $this->db->update($this->tb_order, ['cancel' => 0], ['id' => $item_order['id']]);
            $cancel_id = $this->db->insert_id();
            if ($option['request'] == 'api') {
                $result = ['cancel' => $cancel_id];
            } else {
                $result = ['status' => 'success', 'message' => 'Sent Cancelation successfully', 'btn_text' => 'Cancel requested'];
            }
        }
        return $result;
    }
}
