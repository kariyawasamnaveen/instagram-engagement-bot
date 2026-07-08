<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class order extends My_AdminController {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));

        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "order";
        $this->params            = [];

        $this->columns     =  array(
            "id"                => ['name' => 'Order ID',    'class' => 'text-center'],
            "user"              => ['name' => 'user', 'class' => ''],
            "order_details"     => ['name' => 'Order Details', 'class' => 'text-center'],
            "created"           => ['name' => 'Created', 'class' => 'text-center'],
            "response"          => ['name' => 'API Response', 'class' => 'text-center'],
            "status"            => ['name' => 'Status',  'class' => 'text-center'],
            "mode"              => ['name' => 'Mode', 'class' => 'text-center'],
        );
    }

    public function store() {
        if (!is_ajax_call()) redirect(admin_url($this->controller_name));
        $this->form_validation->set_rules('link', 'link', 'trim|required|xss_clean');
        $this->form_validation->set_rules('start_counter', 'start counter', 'trim|is_natural|xss_clean');
        $this->form_validation->set_rules('remains', 'remains', 'trim|is_natural|xss_clean');
        if (in_array(post('status'), ['canceled'])) {
            staff_check_role_permission($this->controller_name, 'cancel');
        }
        if (post('status') == 'partial') {
            staff_check_role_permission($this->controller_name, 'partial');
            $this->form_validation->set_rules('remains', 'remains', 'trim|required|is_natural|xss_clean');
        }
        if (!$this->form_validation->run()) _validation('error', validation_errors());
        $task   = 'edit-item';
        $response = $this->main_model->save_item( $this->params, ['task' => $task]);
        ms($response);
    }

    public function resend($id = ""){
        $item = $this->main_model->get_item(['id' => (int)$id], ['task' => 'get-item']);
        staff_check_role_permission($this->controller_name, 'resend');
        if (!$item || (!in_array($item['status'], ['error', 'fail']))) redirect(admin_url($this->controller_name));
        $task   = 'resend-item';
        $this->params = [
            'item' => $item
        ];
        $response = $this->main_model->save_item( $this->params, ['task' => $task]);
        redirect(admin_url($this->controller_name));
    }

    public function update_from_provider($id = "")
    {
        staff_check_role_permission($this->controller_name, 'update_from_provider');
        $item = $this->main_model->get_item(['id' => (int) $id], ['task' => 'get-item']);
        if (!$item || (!in_array($item['status'], [ORDER_STATUS_COMPLETED]))) {
            redirect(admin_url($this->controller_name));
        }
        $task   = 'update-item-from-provider';
        $this->params = [
            'item' => $item
        ];
        $response = $this->main_model->save_item( $this->params, ['task' => $task]);
        $query_search = [
            'status' => 'all',
            'query' => $id,
            'field' => 'id',
        ];
        redirect(admin_url($this->controller_name) . '?' .http_build_query($query_search) );
    }

}