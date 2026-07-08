<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cancel_model extends MY_Model
{
    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;
    protected $bulk_actions_copy_clipboard_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = ORDERS_CANCEL;
        // Search
        $this->filter_accepted = array_values(app_config('config')['status']['cancel']);
        if (($key = array_search('all', $this->filter_accepted)) !== false) {
            unset($this->filter_accepted[$key]);
        }
        //Copy to clipboard
        $this->bulk_actions_copy_clipboard_accepted = app_config('config')['bulk_action']['cancel'];
        $this->bulk_actions_copy_clipboard_accepted = array_diff($this->bulk_actions_copy_clipboard_accepted, ['completed', 'rejected']);

        $this->field_search_accepted = app_config('config')['search']['cancel'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $this->db->select('co.id, co.ids, co.api_cancel_id, co.api_id, co.order_id, co.cancel_type, co.status, co.details, co.changed, co.last_updated, co.created, co.request');
            $this->db->select('o.type, o.api_order_id, o.link, o.quantity, o.start_counter, o.service_id, o.api_service_id');
            $this->db->select('u.email');
            $this->db->select('s.name as service_name');
            $this->db->select('api.name as api_name');
            $this->db->from($this->tb_main . ' co');
            $this->db->join($this->tb_order . " o", "co.order_id = o.id", 'left');
            $this->db->join($this->tb_users . " u", "u.id = co.uid", 'left');
            $this->db->join($this->tb_services . " s", "s.id = o.service_id", 'left');
            $this->db->join($this->tb_api_providers . " api", "api.id = co.api_id", 'left');

            // filter
            if ($params['filter']['status'] != 'all' && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('co.status', $params['filter']['status']);
            }

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'email') ? 'u.' . $column : 'o.' . $column;
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                if ($params['search']['field'] == 'id') {
                    $this->db->where_in('`co`.`id`', convert_str_number_list_to_array($params['search']['query']));
                } else if ($params['search']['field'] == 'order_id') {
                    $this->db->where_in('`co`.`order_id`', convert_str_number_list_to_array($params['search']['query']));
                } else {
                    switch ($params['search']['field']) {
                        case 'email':
                            $column = 'u.' . $params['search']['field'];
                            break;
                        case 'link':
                            $column = 'o.' . $params['search']['field'];
                            break;
                        default:
                            $column = 'co.' . $params['search']['field'];
                            break;
                    }
                    $this->db->like($column, $params['search']['query']);
                }
            }

            $this->db->order_by('id', 'desc');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-copy-to-clip-board') {
            if (in_array($params['get-type'], ['order_id', 'api_refill_id'])) {
                $this->db->select('co.' . $params['get-type']);
            }
            if ($params['get-type'] == 'api_order_id') {
                $this->db->select('o.' . $params['get-type']);
            }
            $this->db->from($this->tb_main . ' co');
            $this->db->join($this->tb_order . " o", "co.order_id = o.id", 'left');
            $this->db->where_in('co.id', $params['arr_ids']);
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {
            $result = $this->get("id, ids, cate_id, service_id, service_type, api_provider_id, api_service_id, charge, uid, quantity, status, formal_charge, profit, start_counter, remains, link", $this->tb_main, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'count-items-by-status') {
            $this->db->select("id");
            $this->db->from($this->tb_main);
            $this->db->where("status", $params['status']);
            $this->db->where("service_type !=", "subscriptions");
            $this->db->where("is_drip_feed !=", 1);
            $query = $this->db->get();
            return $query->num_rows();
        }

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            // get uid Array
            if ($params['search']['field'] == 'email') {
                $items_uid = $this->fetch_search_items('id', $this->tb_users, '', ['field' => $params['search']['field'], 'query' => $params['search']['query']]);
                if (!$items_uid) {
                    return null;
                }

            }
            $this->db->select('co.id');
            $this->db->from($this->tb_main . ' co');
            $this->db->join($this->tb_order . " o", "co.order_id = o.id", 'left');
            // filter
            if ($params['filter']['status'] != 'all' && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('co.status', $params['filter']['status']);
            }
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'email') ? 'u.' . $column : 'o.' . $column;
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                if ($params['search']['field'] == 'id') {
                    $this->db->where_in('`co`.`id`', convert_str_number_list_to_array($params['search']['query']));
                } else if ($params['search']['field'] == 'order_id') {
                    $this->db->where_in('`co`.`order_id`', convert_str_number_list_to_array($params['search']['query']));
                } else {
                    switch ($params['search']['field']) {
                        case 'email':
                            $this->db->where_in("co.uid", array_column($items_uid, 'id'));
                            break;
                        case 'link':
                            $column = 'o.' . $params['search']['field'];
                            $this->db->like($column, $params['search']['query']);
                            break;
                        default:
                            $column = 'co.' . $params['search']['field'];
                            $this->db->like($column, $params['search']['query']);
                            break;
                    }
                }
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        switch ($option['task']) {
            case 'bulk-action':
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status" => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'completed':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 'completed', 'changed' => NOW]);
                        return ["status" => "success", "message" => 'Update successfully'];
                        break;
                    case 'rejected':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 'rejected', 'changed' => NOW]);
                        return ["status" => "success", "message" => 'Update successfully'];
                        break;

                    default:
                        //copy to clipboard
                        if (in_array($params['type'], $this->bulk_actions_copy_clipboard_accepted)) {
                            $params_tmp = [
                                'arr_ids' => $arr_ids,
                                'get-type' => str_replace('copy_', '', $params['type']),
                            ];
                            $order_ids = $this->list_items($params_tmp, ['task' => 'list-items-copy-to-clip-board']);
                            if ($order_ids) {
                                $order_ids = array_column($order_ids, $params_tmp['get-type']);
                                $order_ids = implode($order_ids, ',');
                                return ["status" => "success", "value" => $order_ids];
                            } else {
                                return ["status" => "error", "value" => 'There was issue with your request'];
                            }
                        }
                        break;
                }
                break;
        }
    }

}
