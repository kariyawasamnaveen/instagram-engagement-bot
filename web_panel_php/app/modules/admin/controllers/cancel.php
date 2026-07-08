<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class cancel extends My_AdminController {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));

        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "cancel";
        $this->params            = [];

        $this->columns     =  array(
            "id"                => ['name' => 'Task ID',    'class' => ''],
            "mode"              => ['name' => 'Mode', 'class' => 'text-center'],
            "user"              => ['name' => 'user', 'class' => 'text-center'],
            "order_id"          => ['name' => 'Order ID', 'class' => 'text-center'],
            "order_details"     => ['name' => 'Order Details', 'class' => 'text-center'],
            "created"           => ['name' => 'Created', 'class' => 'text-center'],
            "status"            => ['name' => 'Status',  'class' => 'text-center'],
            "detail"            => ['name' => 'Details',  'class' => 'text-center'],
        );
    }
}