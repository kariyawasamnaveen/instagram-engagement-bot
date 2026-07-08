<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ig_accounts extends My_AdminController
{
    private $tb_main = 'ig_accounts';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = "Instagram Accounts";
        $this->path_views = "ig_accounts";
        $this->columns = array(
            "username"      => ['name' => 'Username', 'class' => ''],
            "proxy"         => ['name' => 'Proxy', 'class' => ''],
            "gender"        => ['name' => 'Gender', 'class' => 'text-center'],
            "interest_tags" => ['name' => 'Tags', 'class' => 'text-center'],
            "status"        => ['name' => 'Status', 'class' => 'text-center'],
        );
    }

    public function index()
    {
        $items = $this->main_model->list_items(null, ['task' => 'list-items']);
        $data = array(
            "controller_name" => $this->controller_name,
            "params"          => $this->params,
            "columns"         => $this->columns,
            "items"           => $items,
        );
        $this->template->build($this->path_views . '/index', $data);
    }

    public function update($id = null)
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        $item = null;
        if ($id !== null) {
            $item = $this->main_model->get_item(['id' => $id], ['task' => 'get-item']);
        }
        $data = array(
            "controller_name" => $this->controller_name,
            "item"            => $item,
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function bulk_import()
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        $data = array(
            "controller_name" => $this->controller_name,
        );
        $this->load->view($this->path_views . '/bulk_import', $data);
    }

    public function store()
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        
        $task = post('id') ? 'edit-item' : 'add-item';
        if (post('bulk_items')) {
            $task = 'bulk-append';
        } else {
            $this->form_validation->set_rules('username', 'username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                _validation('error', validation_errors());
            }
        }

        $response = $this->main_model->save_item(null, ['task' => $task]);
        ms($response);
    }
}
