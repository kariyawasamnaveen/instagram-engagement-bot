<?php
defined('BASEPATH') or exit('No direct script access allowed');

class subscriptions extends My_UserController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "dripfeed";
        $this->params = [];

        $this->columns = array(
            "id" => ['name' => lang("order_id"), 'class' => 'text-center'],
            "order_details" => ['name' => lang("order_basic_details"), 'class' => 'text-center'],
            "created" => ['name' => lang("Created"), 'class' => 'text-center'],
            "status" => ['name' => lang("Status"), 'class' => 'text-center'],
        );
    }
}
