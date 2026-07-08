<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_UserController extends MX_Controller
{
    protected $controller_title  = '';
    protected $controller_name   = '';
    protected $path_views        = '';
    protected $params = [];
    protected $columns = [];
    protected $limit_per_page;
    protected $provider;

    protected $tb_main;
    protected $tb_order;
    protected $tb_orders_refill;
    protected $tb_users;
    protected $tb_users_price;
    protected $tb_users_favorite_services;
    protected $tb_services;
    protected $tb_categories;
    protected $tb_tickets;
    protected $tb_ticket_message;
    protected $tb_api_providers;
    protected $tb_language_list;
    protected $tb_news;
    protected $tb_faqs;
    protected $tb_role_permission;
    protected $tb_transaction_logs;
    protected $tb_blog_category;
    protected $tb_blog_posts;
    protected $tb_blog_posts_lang;
    protected $tb_staffs;
    protected $tb_staffs_activity;
    protected $tb_blacklist_ip;
    protected $tb_blacklist_email;
    protected $tb_blacklist_link;

    protected $order_log_controller;
    protected $general_controller;

    public function __construct()
    {
        parent::__construct();

        if (!in_array(segment(2), array('cron', 'set_language')) && !in_array(segment(3), array('cron', 'complete'))) {
            $allowed_controllers = ['auth', 'api', 'client', 'services', 'crons'];
            $allowed_page        = ['logout', 'ipn'];
            if (!session('uid') && !$this->maintenance_mode && !in_array($this->router->fetch_class(), $allowed_controllers) && !in_array($this->router->fetch_method(), $allowed_page)) {
                if(segment(1) != ""  && !in_array(segment(1), ['cron', 'ipn'])){
                    redirect(PATH);
                }
            }
        }
        
        if (session("uid")) {
            $user_allowed_controllers = [];
            $user_allowed_controllers = ['faqs', 'users', 'setting', 'module', 'api_provider', 'category', 'user_mail_logs', 'user_block_ip', 'user_logs', 'payments', 'subscribers','payments_bonuses'];
            if ((in_array($this->router->fetch_class(), $user_allowed_controllers) || in_array(segment(2), ['update']))) {
                redirect(PATH . "statistics");
            }
        }
        $this->limit_per_page = get_option("default_limit_per_page", 10);

        $this->order_log_controller = [
            'order', 'dripfeed', 'subscriptions', 
        ];
        $this->general_controller = [
            'tickets', 'transactions' 
        ];

        // Global Membership Expiry Check
        if (session('uid')) {
            $user = current_logged_user();
            if ($user && $user->membership_status == 1 && !empty($user->membership_expiry)) {
                $current_time = strtotime(NOW);
                $expiry_time = strtotime($user->membership_expiry);
                if ($current_time > $expiry_time) {
                    $this->db->update(USERS, [
                        'membership_status' => 0,
                        'membership_level'  => 'basic',
                    ], ['id' => $user->id]);
                    // Update session/cache if necessary, though current_logged_user usually fetches fresh or from session
                }
            }
        }
    }

    public function index()
    {
        $page = max(0, (int) get("p") - 1);

        // Determine filter status based on controller type
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions', 'refill', 'cancel'])) {
            $filter_status = (isset($_GET['status'])) ? strtolower(get('status')) : 'all';
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
            'table_thead_html'    => render_table_thead($this->columns, false, false, false, false),
        ];

        // Load the correct view template
        $this->template->set_layout('user');
        if (in_array($this->controller_name, $this->general_controller)) {
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
        $table_html = $this->load->view('items_list', [
            "controller_name" => $this->controller_name,
            "params"          => $this->params,
            "items"           => $items,
            "from"            => $page * $this->limit_per_page,
        ], TRUE);

        $pagination = create_pagination([
            'base_url'     => cn($this->controller_name),
            'per_page'     => $this->limit_per_page,
            'query_string' => $_GET,
            'total_rows'   => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
        ]);
        $pagination_html = show_pagination($pagination);
        if (in_array($this->controller_name, $this->order_log_controller)) {
            $btn_filter_group_html = user_show_filter_status_button($this->controller_name, $this->params);
        } else {
            $btn_filter_group_html = null;
        }
        echo json_encode([
            'table_html'            => $table_html,
            'pagination_html'       => $pagination_html,
            'btn_filter_group_html' => $btn_filter_group_html,
        ]);
        exit;
    }

}
