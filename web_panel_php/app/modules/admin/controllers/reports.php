<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class reports extends My_AdminController {

    private $tb_main = STAFFS_LOGS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = get_class($this);
        $this->params            = [];
        $this->tb_main           = STAFFS_LOGS;
        $this->columns     =  array(
            "no"            => ['name' => '#', 'class' => 'text-center text-muted w-5p'],
            "January"         => ['name' => 'January', 'class' => 'text-center text-muted w-5p'],
            "February"         => ['name' => 'February', 'class' => 'text-center text-muted w-5p'],
            "March"         => ['name' => 'March', 'class' => 'text-center text-muted w-5p'],
            "April"         => ['name' => 'April', 'class' => 'text-center text-muted w-5p'],
            "May"         => ['name' => 'May', 'class' => 'text-center text-muted w-5p'],
            "June"         => ['name' => 'June', 'class' => 'text-center text-muted w-5p'],
            "July"         => ['name' => 'July', 'class' => 'text-center text-muted w-5p'],
            "August"         => ['name' => 'August', 'class' => 'text-center text-muted w-5p'],
            "September"         => ['name' => 'September', 'class' => 'text-center text-muted w-5p'],
            "October"         => ['name' => 'October', 'class' => 'text-center text-muted w-5p'],
            "November"         => ['name' => 'November', 'class' => 'text-center text-muted w-5p'],
            "December"         => ['name' => 'December', 'class' => 'text-center text-muted w-5p'],
        );
    }

    public function index1()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $type = get('type');
        $report_filters = app_config('template')['reports'];
        if (!in_array($type, array_keys($report_filters))) {
            $type = 'payments';
        }
        $data_reports = $this->main_model->get_data_analytic([], ['task' => $type]);
        $data = [
            "columns"             => $this->columns,
            "controller_name"     => $this->controller_name,
            "params"              => $this->params,
            "task"                => $type,
            "report_filters"      => $report_filters,
            "data_reports"        => $data_reports,
        ];
        $this->template->build($this->path_views . '/index', $data);
    }

    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        
        if (is_ajax_call()) {
            $this->handle_ajax_request();
        } else {
            $this->template->build($this->path_views . '/index', ['controller_name' => $this->controller_name]);
        }
    }

    private function handle_ajax_request()
    {
        $type = get('type');
        $report_filters = app_config('template')['reports'];
        if (!in_array($type, array_keys($report_filters))) {
            $type = 'payments';
        }

        $btn_filter_group_html = $this->load->view($this->path_views . '/filter_status_button', [
            "controller_name"     => $this->controller_name,
            "params"              => $this->params,
            "report_filters"      => $report_filters,
            "task"                => $type,
        ], TRUE);

        $data_reports = $this->main_model->get_data_analytic([], ['task' => $type]);
        $data_content = [
            "columns"             => $this->columns,
            "controller_name"     => $this->controller_name,
            "params"              => $this->params,
            "data_reports"        => $data_reports,
        ];

        $report_html = $this->load->view($this->path_views . '/content', $data_content, true);
        echo json_encode([
            'table_html'            => $report_html,
            'btn_filter_group_html' => $btn_filter_group_html,
        ]);
        exit;
    }

}
