<?php
defined('BASEPATH') or exit('No direct script access allowed');

class services_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tb_main = SERVICES;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = app_config('config')['search']['services'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'list-items') {
            if (is_table_exists(ORDERS_CANCEL)) {
                $this->db->select('s.cancel, s.cancel_type');
            }
            $this->db->select('s.id, s.ids, s.name, s.cate_id, s.price, s.original_price, s.min, s.max, s.type, s.add_type, s.api_service_id, s.api_provider_id, s.status, s.desc, s.refill, s.refill_type, s.dripfeed, s.sync_options, s.tags');
            $this->db->select('api.name as api_name, c.name as category_name');
            $this->db->from($this->tb_main . " s");
            $this->db->join($this->tb_categories . " c", "c.id = s.cate_id", 'left');
            $this->db->join($this->tb_api_providers . " api", "s.api_provider_id = api.id", 'left');

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'name') ? 's.' . $column : 's.' . $column;
                        if ($i == 1) {
                            $this->db->like($column, $params['search']['query']);
                        } elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']);
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $column = ($params['search']['field'] == 'name') ? 's.' . $params['search']['field'] : 's.' . $params['search']['field'];
                $this->db->like($column, $params['search']['query']);
            }

            // Sort By
            if ($params['sort_by']['cate_id'] != "") {
                $this->db->where('s.cate_id', $params['sort_by']['cate_id']);
            }
            $this->db->order_by("c.sort", 'ASC');
            $this->db->order_by("s.status", 'DESC');
            $this->db->order_by("s.sort", 'ASC');
            $this->db->order_by("s.price", 'ASC');
            $this->db->order_by("s.name", 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result) {
                $result = group_by_criteria($result, 'category_name');
            }
        }

        if ($option['task'] == 'user-custom-rate-list-items') {
            $result = $this->fetch('id, price, name, original_price', $this->tb_services, ['status' => 1], '', '', 'id', 'ASC', true);
        }

        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item') {

            $result = $this->get('*', $this->tb_main, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        is_demo_version();
        if ($option['task'] == 'delete-item') {
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["id" => $params['id']]);
                $this->db->delete($this->tb_services, ["cate_id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted successfully',
                    "ids" => $item->ids,
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }

        if ($option['task'] == 'delete-custom-rate-item') {
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_users_price, ["service_id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted custom rates successfully',
                    "ids" => '',
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }

        return $result;
    }

    public function save_item($params = null, $option = null)
    {
       
        if (in_array($option['task'], ['add-item', 'edit-item'])) {
           
            $service_type = post("service_type");
            $service_add_type = post('add_type');
            if ($service_add_type == 'manual') {
                $service_add_type = 'internal';
            }
            $data = [
                "name"             => post('name'),
                "add_type"         => $service_add_type,
                "cate_id"          => post('category'),
                "desc"             => $this->input->post('desc'),
                "min"              => post('min'),
                "max"              => post('max'),
                "price"            => (double) post('price'),
                "dripfeed"         => 0,
                "overflow"         => (double) post('overflow'),
                "deny_duplicates"  => (double) post('deny_duplicates'),
                "tags"             => post('tags'),
            ];

            if ($service_add_type == 'api') {
                $api_service_data  = $_POST['api_service_data'];
                if (empty($api_service_data)) {
                    return [
                        "status"  => "error", 
                        "message" => 'Error: Unable to fetch this API Service ID',
                    ];
                }
                $api_service_data = json_decode( $api_service_data, true);
                
                $data['api_provider_id'] = post("api_provider_id");
                $data['api_service_id']  = esc($api_service_data['service']);
                $data['original_price']  = esc($api_service_data['rate']);
                $data['type']            = esc($api_service_data['type']);
                $data['refill']          = (int)post("refill");
				$data['refill_type']     = (int)post("refill_type");

                if (is_table_exists(ORDERS_CANCEL)) {
                    $has_cancel_api = !empty($api_service_data['cancel']);
                    $data['cancel'] = $has_cancel_api ? (int) post('cancel') : 0;
                    $data['cancel_type'] = $has_cancel_api ? 1 : 0;
                }
                // sync_options
                $sync_options = [
                    'sync_rate'         => (int)post("sync_rate"),      
                    'auto_rate_percent' => ((int)post("sync_rate")) ? post("auto_rate_percent") : 0,
                    'sync_min'          => (int)post("sync_min"),
                    'sync_max'          => (int)post("sync_max"),
                    'auto_status'       => (int)post("auto_status"),
                    'auto_sync_name'    => (int)post("auto_sync_name"),
                    'auto_sync_desc'    => (int)post("auto_sync_desc"),
                ];
                $data['sync_options'] = json_encode($sync_options);
                $data['api_service_details'] = json_encode($api_service_data);

            } else {

                $data['api_provider_id'] = null;
                $data['api_service_id']  = null;
                $data['original_price']  = null;
                $data['type']            = $service_type;
                $data['refill']          = (int) post("refill");
				$data['refill_type']     = 0;
                $data['sync_options'] = '';
                if (is_table_exists(ORDERS_CANCEL)) {
                    $data['cancel'] = (int) post("cancel");
                    $data['cancel_type'] = 0;
                }
            }
            $config_service_types = array_keys(app_config('template')['service_type']);
            if (!in_array($data['type'], $config_service_types)) {
                return [
                    "status"  => "error", 
                    "message" => 'The panel platform still does not support this service type. "'. $data['type'] . '"',
                ];
            }
            $data['dripfeed']  = ($data['type'] === 'default') ? (int)post("dripfeed") : 0;
        }


        switch ($option['task']) {
            case 'add-item':
                $data["ids"]     = ids();
                $data["changed"] = NOW;
                $data["created"] = NOW;
                $data["status"]  = ($service_add_type == 'api') ? 1 : (int)post('status');
                $this->db->insert($this->tb_main, $data);
                return ["status"  => "success", "message" => 'Add successfully'];
                break;
                
            case 'edit-item':
                if ($service_add_type != 'api') {
                    $data["status"]  = 1;
                }
                $data["changed"] = NOW;
                $this->db->update($this->tb_main, $data, ["id" => post('id')]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'sort-table':
                $this->db->update_batch($this->tb_main, $params['services'], 'id');
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'change-status':
                $item = $this->get('sync_options, status, add_type, api_provider_id, id', $this->tb_main, ["id" => $params['id']], '', '', true);
                if (!$item) {
                    return ["status"  => "error", "message" => 'Item does not exists!'];
                }
                $data_item = [
                    'status' => $params['status'],
                    'changed' => NOW
                ];
                if (!$params['status']) {
                    $sync_options = json_decode($item['sync_options'], true);
                    $sync_options['auto_status'] = 0;
                    $data_item['sync_options'] = json_encode($sync_options);
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $params['id']]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'bulk-action':
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status"  => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->delete($this->tb_main);
                        return ["status"  => "success", "message" => 'Delete successfully'];
                        break;
                    case 'delete_custom_rates':
                        $this->db->where_in('service_id', $arr_ids);
                        $this->db->delete($this->tb_users_price);
                        return ["status"  => "success", "message" => 'Delete custom rates successfully'];
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
                break;
        }
    }

}
