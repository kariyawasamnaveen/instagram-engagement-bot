<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class staffs_activity extends My_AdminController {

    private $tb_main = STAFFS_LOGS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "staffs_activity";
        $this->params            = [];
        $this->tb_main           = STAFFS_LOGS;
        $this->columns     =  array(
            "location"        => ['name' => 'Location', 'class' => 'text-center'],
            "account"         => ['name' => 'Account', 'class' => ''],
            "event"           => ['name' => 'event',    'class' => ''],
            "details"        => ['name' => 'Details',  'class' => ''],
            "ip"            => ['name' => 'IP Address',    'class' => 'text-center'],
            "date"            => ['name' => 'Date',  'class' => 'text-center'],
        );
    }

    public function index()
    {
        $page = (int) get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions', 'refill'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        } else {
            $filter_status = (isset($_GET['status'])) ? (int) get('status') : '3';
        }
        $this->params = [
            'pagination' => [
                'limit' => $this->limit_per_page,
                'start' => $page * $this->limit_per_page,
            ],
            'filter' => ['status' => $filter_status],
            'search' => ['query' => get('query'), 'field' => get('field')],
        ];

        $this->params['uid'] = '';
        if (get('field') == 'email') {
            $this->load->model('model', 'my_model');
            $item_user = $this->my_model->get('id', USERS, ['email' => get('query')], '', '', true);
            if ($item_user) {
                $this->params['uid'] = $item_user['id'];
            }
        }
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $total_rows = $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']);
        unset($this->params['uid']);
        $items_status_count = $this->main_model->count_items($this->params, ['task' => 'count-items-group-by-status']);
        //Delete
        $data = array(
            "controller_name" => $this->controller_name,
            "params" => $this->params,
            "columns" => $this->columns,
            "items" => $items,
            "items_status_count" => $items_status_count,
            "from" => $page * $this->limit_per_page,
            "pagination" => create_pagination([
                'base_url' => admin_url($this->controller_name),
                'per_page' => $this->limit_per_page,
                'query_string' => $_GET, //$_GET
                'total_rows' => $total_rows,
            ]),
        );
        $this->template->build($this->path_views . '/index', $data);
    }
}
