<?php
defined('BASEPATH') or exit('No direct script access allowed');

class proxies extends My_AdminController
{
    private $tb_main = 'general_proxies';

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "proxies";
        $this->columns = array(
            "proxy"  => ['name' => 'Proxy', 'class' => ''],
            "type"   => ['name' => 'Type', 'class' => 'text-center'],
            "status" => ['name' => 'Status', 'class' => 'text-center'],
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

    public function update($ids = null)
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        $item = null;
        if ($ids !== null) {
            $item = $this->main_model->get_item(['ids' => $ids], ['task' => 'get-item']);
        }
        $data = array(
            "controller_name" => $this->controller_name,
            "item"            => $item,
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        $this->form_validation->set_rules('proxy', 'proxy', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }
        $response = $this->main_model->save_item(null, ['task' => post('ids') ? 'edit-item' : 'add-item']);
        ms($response);
    }
}
