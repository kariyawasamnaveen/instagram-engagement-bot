<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class users_banned_ip extends My_AdminController {

    private $tb_main = USER_LOGS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "users_banned_ip";
        $this->params            = [];
        $this->tb_main           = USER_BLOCK_IP;
        $this->columns     =  array(
            "ip"          => ['name' => 'IP Address', 'class' => 'text-center'],
            "description" => ['name' => 'Description', 'class' => 'text-center'],
            "created"     => ['name' => 'Created',  'class' => 'text-center'],
        );
    }

    public function store()
    {
        if (!is_ajax_call()) redirect(admin_url($this->controller_name));
        $this->form_validation->set_rules('description', 'description', 'trim|xss_clean');
        $ids = post('ids');
        $ip_address_unique = "|edit_unique[$this->tb_main.ip.$ids]";
        if ($ids) {
            $task   = 'edit-item';
        } else {
            $task = 'add-item';
            $ip_address_unique = "|is_unique[$this->tb_main.ip]";
        }
        $this->form_validation->set_rules('ip_address', 'IP address', 'trim|required|xss_clean'. $ip_address_unique, [
            'is_unique' => 'The IP address already exists.',
        ]);

        if (!$this->form_validation->run()) _validation('error', validation_errors());
        $response = $this->main_model->save_item( $this->params, ['task' => $task]);

        ms($response);
    }
}
