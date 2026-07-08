<?php
defined('BASEPATH') or exit('No direct script access allowed');

class statistics extends My_UserController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'model');
        $this->load->library('user_agent');
        $this->controller_name = 'statistics';
    }

    public function index()
    {
        $data = array(
            "controller_name" => $this->controller_name,
            "module" => get_class($this),
        );
        $chart_and_orders_area = $this->model->chart_and_orders_statistics();
        $this->template->set_layout('user');
        $this->template->build("index", $data);
    }

    public function load_header_area() {

        if (!is_ajax_call()) redirect(cn());

        $header_area = $this->model->header_statistics();
    
        $data['header_area'] = $header_area;
        $html = $this->load->view('header_area', $data, true); 
        echo json_encode(array('html' => $html));
    }

    public function load_chart_and_orders_area() {
        if (!is_ajax_call()) redirect(cn());

        $chart_and_orders_area = $this->model->chart_and_orders_statistics();
        $data['chart_and_orders_area'] = $chart_and_orders_area;

        $html = $this->load->view('chart_and_orders_area', $data, true);
        echo json_encode(array(
            'html' => $html,
            'chart_spline' => $chart_and_orders_area['chart_spline'],  
            'chart_pie' => $chart_and_orders_area['chart_pie']  
        ));
    }

    public function load_items_top_best_seller() {
        if (!is_ajax_call()) redirect(cn());
        
        $this->load->model('order/order_model');
        $items_top_best_seller = $this->order_model->list_items_best_seller(['limit' => 10], ['task' => 'user']);
    
        $data['items_top_best_seller'] = $items_top_best_seller;
        $html = $this->load->view('top_bestsellers', $data, true); 
        echo json_encode(array('html' => $html));
    }
}
