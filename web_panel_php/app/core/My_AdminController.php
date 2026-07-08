<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_AdminController extends MX_Controller
{

    protected $controller_title;
    protected $controller_name;

    protected $path_views;

    protected $params           = [];
    protected $columns          = [];
    protected $table_thead_html = [];
    protected $limit_per_page;
    protected $order_log_controller;

    

    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();

        if (! is_admin_logged_in() && ! in_array($this->router->fetch_class(), ['login']) && ! in_array($this->router->fetch_method(), ['login', 'logout'])) {
            redirect(admin_url('login'));
        }
        if (is_admin_logged_in() && segment(2) == "users") {
            //$CI->db->query("DELETE FROM general_sessions WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 60 MINUTE))");
        }
        if (is_admin_logged_in() && get_option('admin_auto_logout_when_change_ip', 0)) {
            if (current_logged_staff()->history_ip !== get_client_ip()) {
                unset_session("sid");
                unset_session("staff_current_info");
                $CI->session->sess_destroy();
                redirect(admin_url('logout'));
            }
        }
        $this->limit_per_page = get_option("default_limit_per_page", 10);
        $this->order_log_controller = [
            'order', 'dripfeed', 'subscriptions', 'refill', 'cancel',
        ];
    }


    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $page = max(0, (int) get("p") - 1);

        // Determine filter status based on controller type
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions', 'refill', 'cancel'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        } else {
            $filter_status = (isset($_GET['status'])) ? (int)get('status') : '3';
        }
        $this->params = [
            'pagination' => [
                'limit' => $this->limit_per_page,
                'start' => $page * $this->limit_per_page,
            ],
            'filter' => ['status' => $filter_status],
            'search' => ['query' => get('query'), 'field' => get('field')],
        ];
        if (is_ajax_call()) {
            $this->handle_ajax_request($page);
        } else {
            $this->handle_normal_request();
        }
    }

    private function handle_normal_request()
    {
        // Prepare data for view rendering
        $data = [
            "controller_name"     => $this->controller_name,
            "params"              => $this->params,
            "items_status_count"  => [],
            'table_thead_html'    => $this->get_table_thead_html_for_index(),
        ];

        // Define controller groups for choosing correct template
        $general_controller = [
            'staffs', 'role_permission', 'provider', 'payments', 'payments_bonuses',
            'news', 'faqs', 'blog_category', 'language', 'category', 'subscribers',
            'transactions', 'users_activity', 'users', 'tickets',
        ];

        // Load the correct view template
        if (in_array($this->controller_name, $general_controller)) {
            $this->template->build('common_block/index_general', $data);
        } else if (in_array($this->controller_name, $this->order_log_controller)) {
            $this->template->build('common_block/index_order', $data);
        } else {
            $this->template->build($this->path_views . '/index', $data);
        }
    }
    

    private function handle_ajax_request($page)
    {
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $table_html = $this->load->view($this->path_views . '/child/items_list', [
            "controller_name" => $this->controller_name,
            "params"          => $this->params,
            "items"           => $items,
            "from"            => $page * $this->limit_per_page,
        ], TRUE);
        $pagination = create_pagination([
            'base_url'     => admin_url($this->controller_name),
            'per_page'     => $this->limit_per_page,
            'query_string' => $_GET,
            'total_rows'   => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
        ]);
        $pagination_html = show_pagination($pagination);

        $items_status_count = [];
        if (!in_array($this->controller_name, $this->order_log_controller)) {
            $items_status_count = $this->main_model->count_items($this->params, ['task' => 'count-items-group-by-status']);
        }
        $btn_filter_group_html = show_filter_status_button($this->controller_name, $items_status_count, $this->params);
        echo json_encode([
            'table_html'            => $table_html,
            'pagination_html'       => $pagination_html,
            'btn_filter_group_html' => $btn_filter_group_html,
        ]);

        exit;
    }

    private function get_table_thead_html_for_index()
    {
        $configMap = [
            'order'           => [true, false],
            'dripfeed'        => [true, false],
            'subscriptions'   => [true, false],
            'refill'          => [true, false, false],
            'cancel'          => [true, false, false],
            'tickets'         => [true, false],
            'transactions'    => [false],
            'users_activity'  => [true, true, false],
            'role_permission' => [true, false, true],
            'category'        => [true, false, true, ['sort-table' => true]],
            'payments'        => [true, false, true, ['sort-table' => true]],
            'blog_category'   => [true, false, true, ['sort-table' => true]],
        ];

        $config = $configMap[$this->controller_name] ?? [];
        return render_table_thead($this->columns, ...$config);
    }


    public function index_noajax()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $page = (int) get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions', 'refill', 'cancel'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        } else {
            $filter_status = (isset($_GET['status'])) ? (int) get('status') : '3';
        }
        $this->params = [
            'pagination' => [
                'limit' => $this->limit_per_page,
                'start' => $page * $this->limit_per_page,
            ],
            'filter'     => ['status' => $filter_status],
            'search'     => ['query' => get('query'), 'field' => get('field')],
        ];

        $items              = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $items_status_count = $this->main_model->count_items($this->params, ['task' => 'count-items-group-by-status']);
        $data               = [
            "controller_name"    => $this->controller_name,
            "params"             => $this->params,
            "columns"            => $this->columns,
            "items"              => $items,
            "items_status_count" => $items_status_count,
            "from"               => $page * $this->limit_per_page,
            "pagination"         => create_pagination([
                'base_url'     => admin_url($this->controller_name),
                'per_page'     => $this->limit_per_page,
                'query_string' => $_GET, //$_GET
                'total_rows'   => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ]),
        ];
        $this->template->build($this->path_views . '/index', $data);
    }

    // Edit form
    public function update($id = null)
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }

        $item = null;
        if ($id !== null) {

            $this->params = [
                'id'  => $id,
                'ids' => $id,
            ];
            $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
        }
        $data = [
            "controller_name" => $this->controller_name,
            "item"            => $item,
        ];
        $this->load->view($this->path_views . '/update', $data);
    }

    // Change status
    public function change_status($id = "")
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }

        is_demo_version();
        staff_check_role_permission($this->controller_name, 'change_status');
        $params = [
            'id'     => $id,
            'status' => (int) post('status'),
        ];
        $response = $this->main_model->save_item($params, ['task' => 'change-status']);
        ms($response);
    }

    // Bulk action
    public function bulk_action($type = "")
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }

        is_demo_version();
        $params = [
            'ids'  => post('ids'),
            'type' => $type,
        ];
        $response = $this->main_model->save_item($params, ['task' => 'bulk-action']);
        ms($response);
    }

    // sort table
    public function sort_table()
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }
        is_demo_version();
        $items = post('items');
        if ($items) {
            $filtered = array_filter($items, function ($item) {
                return isset($item['id']) && isset($item['sort']);
            });
            usort($filtered, function($a, $b) {
                return $a['sort'] <=> $b['sort'];
            });
            $response = $this->main_model->save_items(['items' => $filtered], ['task' => 'sort-table']);
            ms($response);
        }
    }

    // Delete Item
    public function delete($id = "")
    {
        if (!is_ajax_call()) {
            redirect(admin_url($this->controller_name));
        }

        is_demo_version();
        staff_check_role_permission($this->controller_name, 'delete');
        $params['id'] = $id;
        $response     = $this->main_model->delete_item($params, ['task' => 'delete-item']);
        ms($response);
    }

    // export data
    public function export($type = "")
    {
        is_demo_version();
        $items = $this->main_model->list_items(null, ['task' => 'export-list-items']);
        staff_check_role_permission($this->controller_name, 'export');
        if (empty($items)) {
            redirect(admin_url($this->controller_name));
        }
        $columns  = array_keys((array) $items[0]);
        $filename = $this->controller_title . '-' . date("d-m-Y", strtotime(NOW));
        switch ($type) {
            case 'excel':
                if (! empty($items)) {
                    $filename .= ".xlsx";
                    $this->load->library('phpspreadsheet_lib');
                    $phpexel = new Phpspreadsheet_lib();
                    $phpexel->export_excel($columns, $items, $filename);
                }
                break;
            case 'csv':
                if (! empty($items)) {
                    $filename .= ".csv";
                    $this->load->library('phpspreadsheet_lib');
                    $phpexel = new Phpspreadsheet_lib();
                    $phpexel->export_csv($columns, $items, $filename);
                }
                break;

            default:
                $filename .= ".csv";
                export_csv($filename, $this->tb_subscribers);
                break;
        }
    }
}
