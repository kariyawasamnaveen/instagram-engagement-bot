<?php
defined('BASEPATH') or exit('No direct script access allowed');

class order extends My_UserController
{
    public $tb_users;
    public $tb_users_price;
    public $tb_order;
    public $tb_orders_refill;
    public $tb_categories;
    public $tb_services;
    public $tb_staff;
    public $module;
    public $module_name;
    public $module_icon;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        $this->load->model(get_class($this) . '_model', 'model');
        $this->load->model('admin/automation_tasks_model', 'automation_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->columns = [];

        //Config Module
        $this->tb_users = USERS;
        $this->tb_staff = STAFFS;
        $this->tb_users_price = USERS_PRICE;
        $this->tb_order = ORDER;
        $this->tb_orders_refill = ORDERS_REFILL;
        $this->tb_categories = CATEGORIES;
        $this->tb_services = SERVICES;
        $this->module = get_class($this);
        $this->module_name = 'Order';
        $this->module_icon = "fa ft-users";

        $this->columns = array(
            "id" => ['name' => lang("order_id"), 'class' => 'text-center'],
            "order_details" => ['name' => lang("order_basic_details"), 'class' => 'text-center'],
            "created" => ['name' => lang("Created"), 'class' => 'text-center'],
            "status" => ['name' => lang("Status"), 'class' => 'text-center'],
        );

    }

    // New order page
    public function new_order()
    {
        $this->load->model("services/services_model", 'services_model');
        $items_service = $this->services_model->list_items(null, ['task' => 'list-items', 'no_group' => true]);
        $this->load->model('client/client_model', 'client_model');
        $items_category = $this->client_model->list_items(null, ['task' => 'list-items-category-in-services']);

        $data = array(
            "controller_name" => $this->controller_name,
            'items_category' => $items_category,
            'items_service' => $items_service,
        );

        $this->template->set_layout('user');
        $this->template->build('add/add', $data);
    }

    private function _debug_log($msg) {
        $log_file = APPPATH . 'logs/order_debug.log';
        $time = date('Y-m-d H:i:s');
        file_put_contents($log_file, "\033[31m[{$time}] {$msg}\033[0m\n", FILE_APPEND);
    }

    public function ajax_add_order()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }

        $this->_debug_log("=== NEW ORDER REQUEST ===");
        $this->_debug_log("POST Data: " . json_encode($_POST));

        $this->main_model->check_blacklist();

        $service_id = post("service_id");

        $quantity = post("quantity");
        $link = post("link");
        $follows_username = post("follows_username");
        if (empty($link) && !empty($follows_username)) {
            $link = "https://www.instagram.com/" . ltrim(trim($follows_username), '@ ') . "/";
        }
        $comments_url = post("comments_url");
        if (empty($link) && !empty($comments_url)) {
            $link = trim($comments_url);
        }
        $viral_url = post("viral_url");
        if (empty($link) && !empty($viral_url)) {
            $link = trim($viral_url);
        }
        
        $runs = post("runs");
        $interval = post("interval");
        $is_drip_feed = isset($_POST["is_drip_feed"]) && $_POST["is_drip_feed"] == "on" ? 1 : 0;
        $speed = post("speed");
        $tag_filter = post("tag_filter");

        $agree = isset($_POST["agree"]) && $_POST["agree"] == "on" ? 1 : 0;

        /*----------  Handling Speed Logic (Auto Drip-feed)  ----------*/
        if ($speed != "instant" && !$is_drip_feed) {
            $check_service = $this->main_model->check_record("*", $this->tb_services, $service_id, false, true);
            if ($check_service && $check_service->dripfeed) {
                $is_drip_feed = 1;
                if ($speed == "slow") {
                    $runs = 24; // 24 chunks
                    $interval = 120; // Every 2 hours
                    $quantity = ceil($quantity / $runs);
                } else if ($speed == "organic") {
                    $runs = 10; // 10 chunks
                    $interval = 60; // Every hour
                    $quantity = ceil($quantity / $runs);
                }
            }
        }

        if (!$service_id) {
            _validation('error', lang("please_choose_a_service"));
        }

        $check_service = $this->main_model->check_record("*", $this->tb_services, $service_id, false, true);
        if (!$check_service) {
            _validation('error', lang("service_does_not_exists"));
        }

        $check_category = $this->main_model->check_record("id, name", $this->tb_categories, $check_service->cate_id, false, true);
        if (!$check_category) {
            _validation('error', lang("category_does_not_exists"));
        }

        // Check agree removed for Mobile App UI compatibility

        $cate_id = $check_category->id;

        /*----------  Add all order without quantity  ----------*/
        $service_type = $check_service->type;
        $api_provider_id = $check_service->api_provider_id;
        $api_service_id = $check_service->api_service_id;
        if ($service_type == "subscriptions") {
            $this->add_order_subscriptions($_POST, $check_service, $check_category);
            exit();
        }
        if (!$link) {
            _validation('error', lang("invalid_link"));
        }
        $link = str_replace(' ', '', strip_tags($link));

        // check duplicate order
        if ($check_service->deny_duplicates) {
            $check_deny_duplicate_order = $this->main_model->get_item(['service_id' => $service_id, 'link' => $link], ['task' => 'check-duplicate-order']);
            if ($check_deny_duplicate_order) {
                _validation('error', lang('deny_duplicates_error'));
            }
        }

        switch ($service_type) {
            case 'custom_comments':
                $comments = strip_tags(trim($_POST['comments']));
                if (!$comments) {
                    _validation('error', lang("comments_field_is_required"));
                }
                $lines = preg_split('/\r\n|\r|\n/', $comments);
                $filtered_lines = array_filter($lines, function ($line) {
                    return trim($line) !== '';
                });
                $quantity = count($filtered_lines);
                break;

            case 'mentions_custom_list':
                $usernames_custom = post("usernames_custom");
                if (!$usernames_custom) {
                    _validation('error', lang("username_field_is_required"));
                }
                $lines = preg_split('/\r\n|\r|\n/', $usernames_custom);
                $filtered_lines = array_filter($lines, function ($line) {
                    return trim($line) !== '';
                });
                $quantity = count($filtered_lines);
                break;

            case 'package':
                $quantity = 1;
                break;

            case 'custom_comments_package':
                $comments = strip_tags($_POST['comments_custom_package']);
                if (!$comments) {
                    _validation('error', lang("comments_field_is_required"));
                }
                $quantity = 1;
                break;
        }

        if (!$quantity) {
            _validation('error', lang("quantity_is_required"));
        }

        /*----------  Check dripfeed  ----------*/
        if ($is_drip_feed && !$check_service->dripfeed) {
            _validation('error', lang("service_does_not_support_dripfeed"));
        }

        if ($is_drip_feed && $check_service->dripfeed) {
            if (!$runs) {
                _validation('error', lang("runs_is_required"));
            }
            if (!$interval) {
                _validation('error', lang("interval_time_is_required"));
            }
            if ($interval > 1440) {
                _validation('error', 'Invalid interval time');
            }
            $total_quantity = $runs * $quantity;
        } else {
            $total_quantity = $quantity;
        }

        /*----------  Handling Tagging Logic (Gender & Interests)  ----------*/
        $tag_filter = post('tag_filter');
        if ($tag_filter && $tag_filter != 'all') {
            $inv_params = [];
            if (in_array($tag_filter, ['male', 'female'])) {
                $inv_params['tag_filter'] = $tag_filter;
                $inv_params['gender'] = $tag_filter;
            } else {
                // For niche interest categories or custom suggestions, check total active accounts
                $inv_params['tag_filter'] = 'all';
            }
            $inventory = $this->main_model->check_account_inventory($inv_params);
            if ($inventory < $total_quantity) {
                _validation('error', sprintf(lang("not_enough_tagged_accounts_in_inventory"), ucfirst($tag_filter)));
            }
        }

        /*----------  Check quantity  ----------*/
        $min = $check_service->min;
        $max = $check_service->max;
        $price = get_user_price(session('uid'), $check_service);

        if ($service_type == "package" || $service_type == "custom_comments_package") {
            $total_charge = $price;
        } else {
            $total_charge = ($price * $total_quantity) / 1000;
        }

        if ($total_quantity <= 0 || ($total_quantity < $min) || $quantity < $min) {
            _validation('error', lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount"));
        }

        if ($total_quantity > $max) {
            _validation('error', lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount"));
        }

        /*----------  Get balance & Membership ----------*/
        $user = $this->main_model->get("balance, membership_status, membership_expiry", $this->tb_users, ['id' => session('uid')]);

        /*----------  Membership Check  ----------*/
        if ($user->membership_status == 1) {
            if (strtotime($user->membership_expiry) < time()) {
                $this->db->update($this->tb_users, ['membership_status' => 0, 'membership_level' => 'basic'], ['id' => session('uid')]);
                _validation('error', "Your Portal License has expired. Please upgrade to continue using premium features.");
            } else {
                // Active Subscription! Make internal automated orders 100% FREE
                if ($check_service->add_type == 'internal') {
                    $total_charge = 0;
                }
            }
        }

        // check balance
        if ($user->balance < $total_charge) {
            _validation('error', lang("not_enough_funds_on_balance"));
        }
        
        $this->_debug_log("Balance check passed! Deducting {$total_charge} from {$user->balance}.");

        $automation_action = null;
        if ($check_service->add_type == 'internal' && $service_type == 'default') {
            $automation_action = $this->resolve_local_automation_action($check_service);
            if (!$this->is_valid_local_automation_target($automation_action, $link)) {
                $target_message = ($automation_action == 'follow')
                    ? 'Follow services require an Instagram profile URL'
                    : 'Like services require an Instagram post, reel, or video URL';
                _validation('error', $target_message);
            }
        }

        /*----------  Collect data import to database  ----------*/
        $data = [
            "ids" => ids(),
            "uid" => session("uid"),
            "cate_id" => $cate_id,
            "service_id" => $service_id,
            "service_type" => $service_type,
            "mode" => ($check_service->add_type == 'api') ? 1 : ($check_service->add_type == 'internal' ? 2 : 0),
            "link" => $link,
            "quantity" => $total_quantity,
            "remains" => ($check_service->add_type == 'internal') ? $total_quantity : 0,
            "charge" => $total_charge,
            "api_provider_id" => $api_provider_id,
            "api_service_id" => $api_service_id,
            "is_drip_feed" => $is_drip_feed,
            "custom_tags" => $tag_filter ? $tag_filter : null,
            "delivery_speed" => $speed ? $speed : 'instant',
            "status" => ($check_service->add_type == 'internal') ? 'pending' : ORDER_STATUS_AWAITING,
            "changed" => NOW,
            "created" => NOW,
        ];

        /*----------  get the different required paramenter for each service type  ----------*/
        switch ($service_type) {
            case 'mentions_with_hashtags':
                $hashtags = post("hashtags");
                $usernames = post("usernames");
                $usernames = strip_tags($usernames);
                if (!$usernames) _validation('error', lang("username_field_is_required"));
                if (!$hashtags) _validation('error', lang("hashtag_field_is_required"));
                $data["usernames"] = $usernames;
                $data["hashtags"] = $hashtags;
                break;

            case 'mentions_hashtag':
                $hashtag = post("hashtag");
                if (!$hashtag) _validation('error', lang("hashtag_field_is_required"));
                $data["hashtag"] = $hashtag;
                break;

            case 'comment_likes':
                $username = post("username");
                $username = strip_tags($username);
                if (!$username) _validation('error', lang("username_field_is_required"));
                $data["username"] = $username;
                break;

            case 'mentions_user_followers':
                $username = post("username");
                $username = strip_tags($username);
                if (!$username) _validation('error', lang("username_field_is_required"));
                $data["username"] = $username;
                break;

            case 'mentions_media_likers':
                $media_url = post("media_url");
                if ($media_url == "" || !filter_var($media_url, FILTER_VALIDATE_URL)) _validation('error', lang("invalid_link"));
                $data["media"] = $media_url;
                break;

            case 'custom_comments':
                $data["comments"] = json_encode($comments);
                break;

            case 'custom_comments_package':
                $data["comments"] = json_encode($comments);
                break;

            case 'mentions_custom_list':
                $data["usernames"] = json_encode($usernames_custom);
                break;
        }

        if ($is_drip_feed) {
            $data['runs'] = $runs;
            $data['interval'] = $interval;
            $data['dripfeed_quantity'] = $quantity;
            $data['status'] = ORDER_STATUS_ACTIVE;
        }

        if (!empty($api_provider_id) && !empty($api_service_id)) {
            $data['api_order_id'] = -1;
        }

        if ($check_service->refill && is_table_exists($this->tb_orders_refill)) $data['refill'] = 1;
        if ($check_service->cancel && is_table_exists(ORDERS_CANCEL)) $data['cancel'] = 1;
        
        $more_params['service_name'] = $check_service->name;
        if ($automation_action) $more_params['automation_action'] = $automation_action;

        $this->_debug_log("Prepared order data for saving: Service [{$check_service->name}] Link [{$link}] Quantity [{$total_quantity}]");
        $this->save_order($this->tb_order, $data, $user->balance, $total_charge, $more_params);
    }

    private function add_order_subscriptions($post, $check_service, $item_category)
    {
        $api_provider_id = $check_service->api_provider_id;
        $api_service_id = $check_service->api_service_id;
        $service_id = $check_service->id;
        $cate_id = $check_service->cate_id;
        $agree = (isset($post['agree']) && $post["agree"] == "on") ? 1 : 0;
        $service_type = $check_service->type;

        $data = [
            "ids" => ids(),
            "uid" => session("uid"),
            "cate_id" => $cate_id,
            "service_id" => $service_id,
            "service_type" => $service_type,
            "mode" => ($check_service->add_type == 'api') ? 1 : ($check_service->add_type == 'internal' ? 2 : 0),
            "api_provider_id" => $api_provider_id,
            "api_service_id" => $api_service_id,
            "sub_status" => ORDER_STATUS_ACTIVE,
            "status" => ORDER_STATUS_AWAITING,
            "changed" => NOW,
            "created" => NOW,
        ];

        switch ($service_type) {
            case 'subscriptions':
                $username = $post["sub_username"];
                $posts = (int) $post["sub_posts"];
                $min = (int) $post["sub_min"];
                $max = (int) $post["sub_max"];
                $delay = (int) $post["sub_delay"];
                $expiry = $post["sub_expiry"];

                if ($username == "") _validation('error', lang("username_field_is_required"));
                if ($min == "" || $min < $check_service->min) _validation('error', lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount"));
                if ($max < $min) _validation('error', lang("min_cannot_be_higher_than_max"));
                if ($max > $check_service->max) _validation('error', lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount"));
                if (!in_array($delay, array(0, 5, 10, 15, 30, 60, 90))) _validation('error', lang("incorrect_delay"));
                if ($posts <= 0 || $posts == "") _validation('error', lang("new_posts_future_posts_must_to_be_greater_than_or__equal_to_1"));
                if (!$agree) _validation('error', lang("you_must_confirm_to_the_conditions_before_place_order"));

                $price = get_user_price(session('uid'), $check_service);
                $charge = ($max * $posts * $price) / 1000;
                
                if (in_array($service_id, [1, 2, 3, 5]) || $cate_id == 34) {
                    $charge = 59.00;
                    $data["is_monthly"] = 1;
                    $data["auto_renew"] = 1;
                    $data["expiry_at"] = date("Y-m-d H:i:s", strtotime("+30 days"));
                    $posts = 10; $min = 100; $max = 100;
                    $data['delivery_speed'] = post('sub_speed') ? post('sub_speed') : 'organic';
                    $data['gender'] = post('sub_gender') ? post('sub_gender') : 'mixed';
                }

                $user = $this->main_model->get("balance", $this->tb_users, ['id' => session('uid')]);
                if (($user->balance != 0 && $user->balance < $charge) || $user->balance == 0) _validation('error', lang("not_enough_funds_on_balance"));
                
                if ($expiry != "") {
                    $expiry = str_replace('/', '-', $expiry);
                    $expiry = date("Y-m-d", strtotime($expiry));
                } else {
                    $expiry = "";
                }

                $data["username"] = $username;
                $data["sub_posts"] = ($posts == "") ? -1 : $posts;
                $data["sub_min"] = $min;
                $data["sub_max"] = $max;
                $data["sub_delay"] = $delay;
                $data["sub_expiry"] = $expiry;
                $data["charge"] = $charge;

                if (!empty($api_provider_id) && !empty($api_service_id)) $data['api_order_id'] = -1;
                $more_params['service_name'] = $check_service->name;
                $more_params['order_type'] = 'subscriptions';
                $this->save_order($this->tb_order, $data, $user->balance, $charge, $more_params);
                break;
        }
    }

    private function save_order($table, $data_orders, $user_balance = "", $total_charge = "", $more_params = [])
    {
        $this->db->trans_start();
        try {
            $service_mode = $data_orders['mode'];
            $new_balance = $user_balance - $total_charge;
            $new_balance = ($new_balance > 0) ? $new_balance : 0;
            $update_status = $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);

            if ($update_status) {
                $this->db->insert($table, $data_orders);
                $order_id = $this->db->insert_id();

                if ($data_orders['mode'] == 2) {
                    $action = $more_params['automation_action'] ?? 'like';
                    if ($data_orders['service_type'] != 'subscriptions') {
                        // Extract speed preference (app sends follows_speed for follows, or falls back to speed)
                        if ($action === 'follow') {
                            $delivery_speed = post('follows_speed') ? post('follows_speed') : ($data_orders['delivery_speed'] ?? 'instant');
                        } else {
                            $delivery_speed = post('speed') ? post('speed') : ($data_orders['delivery_speed'] ?? 'instant');
                        }
                        
                        $task_data = ['tag_filter' => $data_orders['custom_tags'] ?? 'all', 'delivery_speed' => $delivery_speed];
                        if (post('comments_strategy')) {
                            $task_data['comment_strategy'] = (post('comments_strategy') == 'custom') ? 'custom_list' : 'ai_generated';
                            $task_data['custom_comments'] = post('custom_comments');
                        }
                        if (post('viral_url')) {
                            $task_data['algo_saves'] = (post('viral_saves') == 'on');
                            $task_data['story_reposts'] = (post('viral_reposts') == 'on');
                            $task_data['push_strategy'] = post('viral_speed') ? post('viral_speed') : '24h';
                        }
                        
                        $this->automation_model->create_tasks(
                            $order_id,
                            $data_orders['service_id'],
                            $action,
                            $data_orders['link'],
                            $data_orders['quantity'],
                            $task_data,
                            [
                                'delivery_speed' => $delivery_speed,
                                'runs' => $data_orders['runs'] ?? 0,
                                'interval' => $data_orders['interval'] ?? 0,
                                'dripfeed_quantity' => $data_orders['dripfeed_quantity'] ?? 0,
                            ]
                        );
                    }
                }

                $this->db->trans_complete();
                $this->_debug_log("Order successfully saved to database! Order ID: {$order_id}");
                ms(['status' => 'success', 'order_id' => $order_id]);
            } else {
                $this->db->trans_rollback();
                _validation('error', lang("error_processing_request"));
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            _validation('error', lang("error_processing_request"));
        }
    }

    private function resolve_local_automation_action($service)
    {
        $service_profile = strtolower(trim(implode(' ', array_filter([$service->name ?? '', strip_tags($service->desc ?? ''), $service->tags ?? '']))));
        if (strpos($service_profile, 'follows engine') !== false) return 'follow';
        if (strpos($service_profile, 'comments engine') !== false) return 'comment';
        if (strpos($service_profile, 'viral multiplier') !== false) return 'share';
        return 'like';
    }

    private function is_valid_local_automation_target($action, $link)
    {
        if (!preg_match('#instagram\.com/#i', $link)) return false;
        
        $path = trim((string) parse_url($link, PHP_URL_PATH), '/');
        $segments = array_values(array_filter(explode('/', $path)));
        
        if (count($segments) == 0) return false; // Prevent empty URLs like instagram.com//
        
        if ($action == 'follow') {
            if (count($segments) !== 1) return false;
            return !in_array(strtolower($segments[0]), ['p', 'reel', 'tv', 'explore', 'stories', 'accounts', 'direct']);
        }
        return preg_match('#instagram\.com/(p|reel|tv)/#i', $link) === 1;
    }

    public function ajax_mass_order()
    {
        // Mass order logic...
    }
}
