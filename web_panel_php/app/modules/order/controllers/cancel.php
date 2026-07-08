<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cancel extends My_UserController
{
    public $tb_users;
    public $tb_users_price;
    public $tb_order;
    public $tb_orders_refill;
    public $tb_categories;
    public $tb_services;
    public $tb_staff;
    public $module;
    public $module_name;
    public $module_icon;

    public function __construct()
    {

        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->columns = [];

        //Config Module
        $this->tb_users = USERS;
        $this->tb_staff = STAFFS;
        $this->tb_users_price = USERS_PRICE;
        $this->tb_order = ORDER;
        $this->tb_orders_refill = ORDERS_REFILL;
        $this->tb_categories = CATEGORIES;
        $this->tb_services = SERVICES;
        $this->module = get_class($this);
        $this->module_name = 'Order';
        $this->module_icon = "fa ft-users";

    }

    public function index($ids = "")
    {
        if (!session('uid')) redirect(cn(''));
        $response = $this->main_model->save_item(['ids' => $ids], ['task' => 'item-cancel-create', 'request' => 'web']);
        ms($response);
    }
}
