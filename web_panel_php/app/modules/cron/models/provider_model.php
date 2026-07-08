<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class provider_model extends MY_Model {
    protected $tb_main;

    public function __construct(){
        parent::__construct();
        $this->tb_main     = API_PROVIDERS;
    }

    public function list_items($params = null, $option = null)
    {
        // Get list api for sync services
        if ($option['task'] == 'list-items-for-sync-services-on-cron') {
            $this->db->select('id, ids, name, url, key, type, status, no_current_services');
            $this->db->from($this->tb_main);
            $this->db->where('status', 1);
            $this->db->order_by('last_sync_services', 'ASC');
            $this->db->order_by('no_current_services', 'DESC');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function get_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-items-services') {
            $result = $this->fetch("id, name, status, desc, price, original_price, deny_duplicates, refill, min, max, add_type, type, api_service_id, api_provider_id, dripfeed, status, changed, sync_options, sync_lastcheck", $this->tb_services, ['api_provider_id' => $params['api_provider_id']], '', '', '', '', true);
        }
        return $result;
    }

    public function save_items($params = null, $option = null)
    {
        if ($option['task'] == 'sync-services') {
            $item_api_services = array_sort_by_new_key($params['items_provider_service'], 'service');
            $items_services = $params['items_services'];
            $data_items_batch = [];
            if (empty($items_services)) {
                return false;
            }
            $is_auto_currency_convert = get_option('is_auto_currency_convert', 1);
            $convert_to_new_currency_rate  = 1;
            if ($is_auto_currency_convert) {
                $convert_to_new_currency_rate = get_option('new_currecry_rate', 1);
            }
            foreach ($items_services as $key => $item_db) {
                /*----------  Get Sync Options  ----------*/
                $sync_options = json_decode($item_db['sync_options'], true);

                if (isset($item_api_services[$item_db['api_service_id']])) {
                    $item_exists_provider = $item_api_services[$item_db['api_service_id']];
                    
                    // Dripfeed
                    // $item_db['dripfeed']        = !empty($item_exists_provider['dripfeed']) ? 1 : 0;

                    // Cancel
                    $item_db['cancel_type']     = !empty($item_exists_provider['cancel']) ? 1 : 0;
                    if (!$item_db['cancel_type']) {
                        $item_db['cancel']          = 0;
                    }

                    // Refill
                    $item_db['refill']          = !empty($item_exists_provider['refill']) ? 1 : 0;

                    $item_db['original_price']  = (double) $item_exists_provider['rate'];
                    
                    /*----------  Auto sync_rate  ----------*/
                    if (!empty($sync_options['sync_rate'])) {
                        $price_percentage_increase = (isset($sync_options['auto_rate_percent'])) ? $sync_options['auto_rate_percent'] : 25;
                        $item_db['price'] = import_new_rate($item_exists_provider['rate'], $price_percentage_increase, $convert_to_new_currency_rate);
                    }

                    /*---------- Sync Min   ----------*/
                    if (!empty($sync_options['sync_min'])  && isset($item_exists_provider['min'])) {
                        $item_db['min']      = $item_exists_provider['min'];
                    }

                    /*---------- Sync Max  ----------*/
                    if (!empty($sync_options['sync_max'])  && isset($item_exists_provider['max'])) { 
                        $item_db['max']      = $item_exists_provider['max'];
                    }

                    /*---------- Sync Status  ----------*/
                    if (!empty($sync_options['auto_status'])) {
                        $item_db['status']      = 1;
                    }

                    /*---------- Sync service name  ----------*/
                    if (!empty($sync_options['auto_sync_name']) && isset($item_exists_provider['name'])) {
                        $item_db['name']      = esc($item_exists_provider['name']);
                    }

                    /*---------- Sync service description ----------*/
                    if (!empty($sync_options['auto_sync_desc']) && !empty($item_exists_provider['desc']) ) {
                        $item_db['desc']      = esc($item_exists_provider['desc']);
                    }
                    
                    // Check custom rate
                    $item_custom_rate = $this->get('id', $this->tb_users_price, ['service_id' =>  $item_db['id'], 'service_price <' => $item_db['original_price']], '', '', true);
                    if ($item_custom_rate) {
                        $this->db->delete($this->tb_users_price, ['service_id' =>  $item_db['id']]);
                    }
                } else {
                    /*---------- Sync Status  ----------*/
                    if (!empty($sync_options['auto_status'])) {
                        $item_db['status']      = 0;
                    }
                }
                $data_items_batch[] = $item_db;
            }
            if ($data_items_batch) {
                $this->db->update_batch($this->tb_services, $data_items_batch, 'id');
                return true;
            }
            return $result;
        }

    }


}
