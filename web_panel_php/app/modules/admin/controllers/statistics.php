<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class statistics extends My_AdminController {

    private $tb_main = CATEGORIES;

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = 'statistics';
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "statistics";
        $this->params            = [];
        staff_check_role_permission($this->controller_name, 'index');
    }

    public function index()
    {
        $data = [
            "controller_name"         => $this->controller_name,
        ];
        $this->template->build($this->path_views . "/index", $data);
    }

    
    public function load_header_area() {

        if (!is_ajax_call()) redirect(cn());

        $header_area = $this->main_model->header_statistics();
        $data = [
            "controller_name"         => $this->controller_name,
            "header_area"         => $header_area,
        ];
        $html = $this->load->view($this->path_views. '/header_area', $data, true); 
        echo json_encode(array('html' => $html));
    }

    public function load_chart_and_orders_area() {
        if (!is_ajax_call()) redirect(cn());

        $chart_and_orders_area = $this->main_model->chart_and_orders_statistics();
        $data = [
            "controller_name"         => $this->controller_name,
            "chart_and_orders_area"         => $chart_and_orders_area,
        ];
        $html = $this->load->view($this->path_views. '/chart_and_orders_area', $data, true);
        echo json_encode(array(
            'html' => $html,
            'chart_spline' => $chart_and_orders_area['chart_spline'],  
            'chart_pie' => $chart_and_orders_area['chart_pie']  
        ));
    }

    public function load_last_users() {
        if (!is_ajax_call()) redirect(cn());
        
        $this->load->model('Users_model');
        $items_last_users = $this->Users_model->list_items(['limit' => 10], ['task' => 'items-last-users']);
        $data = [
            'controller_name'  => $this->controller_name,
            'items_last_users' => $items_last_users,
        ];
        $html = $this->load->view($this->path_views. '/last_users', $data, true); 
        echo json_encode(array('html' => $html));
    }

    public function load_items_top_best_seller() {
        if (!is_ajax_call()) redirect(cn());
        
       $this->load->model('order_model');
        $items_best_seller = $this->order_model->list_items(['limit' => 10], ['task' => 'best-seller-in-statistics']);
        
        $data = [
            'controller_name'  => $this->controller_name,
            'items_best_seller' => $items_best_seller,
        ];

        $html = $this->load->view($this->path_views. '/items_top_best_seller', $data, true); 
        echo json_encode(array('html' => $html));
    }
}