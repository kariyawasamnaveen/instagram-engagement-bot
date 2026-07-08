<?php
defined('BASEPATH') or exit('No direct script access allowed');

class services extends My_UserController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->columns = [
            "id" => ['name' => 'ID', 'class' => 'text-center'],
            "name" => ['name' => lang('Name'), 'class' => 'text-center'],
            "price" => ['name' => lang("rate_per_1000") . "(" . get_option("currency_symbol", "") . ")", 'class' => 'text-center'],
            "min" => ['name' => lang("min"), 'class' => 'text-center'],
            "max" => ['name' => lang("max"), 'class' => 'text-center'],
            "average_time" => ['name' => lang("Average_time") . ' ' . render_tooltip_popover_html(lang("avegrage_time_details"), 'Tooltip', 'top'), 'class' => 'text-center'],
            "desc" => ['name' => lang("Description"), 'class' => 'text-center'],
        ];

        if ((get_option("enable_average_time", 0) != 1)) unset($this->columns['average_time']);
    }

    public function index()
    {
        if (!session('uid') && get_option("enable_service_list_no_login") != 1) {
            redirect(cn());
        }
        if (session('uid')) {
            $fav_column = [
                "fav" => ['name' => '#', 'class' => 'text-center'],
            ];
            $order_button_column = [
                "order_btn" => ['name' => '', 'class' => 'text-center'],
            ];
            $this->columns = $fav_column + $this->columns + $order_button_column;
        }
        $this->params = [
            'cate_id' => 0,
        ];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items', 'no_group' => false]);
        $this->load->model('client/client_model', 'client_model');
        $items_category = $this->client_model->list_items($this->params, ['task' => 'list-items-category-in-services']);
        $data = array(
            "controller_name" => $this->controller_name,
            "params" => $this->params,
            "columns" => $this->columns,
            "items" => $items,
            "items_category" => $items_category,
        );
        if (session('uid')) {
            $this->template->set_layout('user');
            $this->template->build("index", $data);
        } else {
            $this->template->set_layout('general_page');
            $this->template->build("index", $data);
        }
    }

    public function index_old()
    {
        if (!session('uid') && get_option("enable_service_list_no_login") != 1) {
            redirect(cn());
        }
        if (session('uid')) {
            $fav_column = [
                "fav" => ['name' => '#', 'class' => 'text-center'],
            ];
            $order_button_column = [
                "order_btn" => ['name' => '', 'class' => 'text-center'],
            ];
            $this->columns = $fav_column + $this->columns + $order_button_column;
        }
        $this->params = [
            'cate_id' => 0,
        ];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $this->load->model('client/client_model', 'client_model');
        $items_category = $this->client_model->list_items($this->params, ['task' => 'list-items-category-in-services']);
        $data = array(
            "controller_name" => $this->controller_name,
            "params" => $this->params,
            "columns" => $this->columns,
            "items" => $items,
            "items_category" => $items_category,
        );

        if (session('uid')) {
            $this->template->set_layout('user');
            $this->template->build("index", $data);
        } else {
            $this->template->set_layout('general_page');
            $this->template->build("index", $data);
        }
    }

    public function switch_favorite() {
        $service_id = (int) post('service_id');
        $is_favorite = (int) post('is_favorite');
        if (session('uid') && $service_id && in_array($is_favorite, [0, 1])) {
            $params = [
                'service_id' => $service_id,
                'target' => $is_favorite ? 'remove' : 'add',
            ];
            $result = $this->main_model->save_item($params, ['task' => 'switch-favorite']);
            echo json_encode($result);
            
            
        }
    }
}
