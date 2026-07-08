<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once('cron.php');

class provider extends cron
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cron/provider_model', 'main_model');
        $this->lib_provider = new Smm_api();
    }

    public function sync_services()
    {
        $lock = fopen('_lock_file_sync_services.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))  die('Sync Services already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $params = [
            'pagination' => [
                'limit'  => (get('limit')) ? get('limit') : 4,
                'start'  => 0,
            ],
        ];
        $items_api = $this->main_model->list_items($params, ['task' => 'list-items-for-sync-services-on-cron']);
        
        if ($items_api) {
            $i = 0;
            foreach ($items_api as $key => $item_api) {
                $is_api_availible = is_site_availible($item_api['url']);
                $data_item_api = [
                    'name' => $item_api['name'],
                ];
                $number_current_services = (double)$item_api['no_current_services'];

                $current_provider_services = $this->lib_provider->services($item_api, 'directly');
                if ($is_api_availible && !empty($current_provider_services) && empty($current_provider_services['error'])) {
                    $items_services = $this->main_model->get_items(['api_provider_id' => $item_api['id']], ['task' => 'get-items-services']);
                    if ($items_services) {
                        echo $item_api['name']. "<br>";
                        $params = [
                            'item_api'                  => $item_api,
                            'items_services'            => $items_services,
                            'items_provider_service'    => $current_provider_services,
                        ];
                        $number_current_services = count($items_services);
                        if ($params['items_provider_service'] && $number_current_services > 0) {
                            $response = $this->main_model->save_items($params, ['task' => 'sync-services']);
                            $i++;
                        }
                    }
                    $data_item_api['balance'] = (isset($this->lib_provider->balance($item_api)['balance'])) ? (double)$this->lib_provider->balance($item_api)['balance'] : '';
                    $data_item_api['description'] = 'Last Sync: ' . NOW;
                } else {
                    // $data_item_api['status'] = 0;
                    $data_item_api['description'] = 'API URL does not exits';
                }
                
                $data_item_api['no_current_services'] = $number_current_services;

                if ($number_current_services >= 50) {
                    $data_item_api['last_sync_services']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(30*60, 60*60));
                } else if ($number_current_services >= 10 && $number_current_services < 50) {
                    $data_item_api['last_sync_services']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(2*60*60, 3*60*60));
                } else if ($number_current_services > 0 && $number_current_services < 10) {
                    $data_item_api['last_sync_services']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(3*60*60, 4*60*60));
                } else {
                    $data_item_api['last_sync_services']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(3*24*60*60, 7*24*60*60));
                }
                $this->db->update(API_PROVIDERS, $data_item_api, ['id' => $item_api['id']]);
                if ($i == 2 || $is_api_availible !== TRUE) {
                    break;
                }
            }
        } else {
            echo "There is empty api";
        }
        echo 'Successfully';
    }

}
