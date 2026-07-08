<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Get admin url
 * @param string slug to append (Optional)
 * @return url admin
 */
if (!function_exists('admin_url')) {
    function admin_url($slug = "")
    {
        if (!isset($GLOBALS['ADMIN_URL_PREFIX']) || (isset($GLOBALS['ADMIN_URL_PREFIX']) && $GLOBALS['ADMIN_URL_PREFIX'] == null)) {
            $GLOBALS['ADMIN_URL_PREFIX'] = 'admin';
        }
        return PATH . $GLOBALS['ADMIN_URL_PREFIX'] . '/' . $slug;
    };
}

/**
 * Get client url
 * @param string slug to append (Optional)
 * @return url admin
 */
if (!function_exists('client_url')) {
    function client_url($slug = "")
    {
        return PATH . $slug;
    };
}

/**
 * Is admin logged in
 * @return boolean
 */
if (!function_exists('is_admin_logged_in')) {
    function is_admin_logged_in()
    {
        if (session('sid')) {
            return true;
        }
        return false;
    }
}

/**
 * Is client logged in
 * @return boolean
 */
if (!function_exists('is_client_logged_in')) {
    function is_client_logged_in()
    {
        if (session('uid')) {
            return true;
        }
        return false;
    }
}

/**
 * @param string $role_type
 * @return boolean
 */
if (!function_exists('get_role')) {
    function get_role($role_type = "", $id = "")
    {
        if (!session('uid')) {
            return false;
        }
        if (session('uid') && isset($GLOBALS['current_user'])) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * @return array $data logged user information
 */
if (!function_exists("current_logged_user")) {
    function current_logged_user()
    {
        return $GLOBALS['current_user'];
    }
}

if (!function_exists("get_current_user_data")) {
    function get_current_user_data($id = "")
    {
        if ($id == "") {
            $id = session("uid");
        }
        $CI = &get_instance();
        if (empty($CI->help_model)) {
            $CI->load->model('model', 'help_model');
        }
        $user = $CI->help_model->get("*", USERS, ['id' => $id]);
        if (!empty($user)) {
            return $user;
        } else {
            return false;
        }
    }
}

/*----------  Get user price  ----------*/
if (!function_exists('get_user_price')) {
    function get_user_price($uid, $service)
    {
        $CI = &get_instance();
        if (empty($CI->help_model)) {
            $CI->load->model('model', 'help_model');
        }
        $user_price = $CI->help_model->get('service_price', USERS_PRICE, ['uid' => $uid, 'service_id' => $service->id]);
        if (isset($user_price->service_price)) {
            $price = $user_price->service_price;
        } else {
            $price = $service->price;
        }

        /*----------  Apply 10% Pro Membership Discount  ----------*/
        $user = $CI->help_model->get('membership_status, membership_level', USERS, ['id' => $uid]);
        if ($user && $user->membership_status == 1 && $user->membership_level == 'pro') {
            $price = $price * 0.9; // 10% discount
        }

        return $price;
    }
}

if (!function_exists('generate_random_seconds')) {
    function generate_random_seconds($max_hours = 24) {
        if (!is_numeric($max_hours) || $max_hours <= 0) {
            $max_hours = 48;  
        }
        
        $random = rand(1, 100); 
        
        if ($random <= 66) {
            return rand(0, 3600);  
        } elseif ($random <= 86) {
            
            return null;
        } else {
            $max_seconds = $max_hours * 3600; 
            return rand(3600, $max_seconds); 
        }
    }
}


/*----------  Show Success page for avoid lost session after redirect to complete page ----------*/
if (!function_exists('show_html_add_funds_success_page')) {
    function show_html_add_funds_success_page($tnx_id = '', $uid = '')
    {
        $CI = &get_instance();
        $tnx_where = [
            'id' => $tnx_id,
            'uid' => $uid,
            'status' => 1,
        ];
        $item_tnx = $CI->model->get("*", TRANSACTION_LOGS, $tnx_where, '', '', true);
        if ($item_tnx) {
            $data = array(
                "module" => '',
                "transaction" => $item_tnx,
            );
            if (!session('uid')) {
                $item_user = $CI->model->get('*', USERS, ['id' => $uid, 'status' => 1], '', '', true);
                if ($item_user) {
                    set_session('uid', $item_user['id']);
                    $GLOBALS['current_user'] = $item_user;
                    $CI->template->set_layout('user');
                    $CI->template->build('payment_successfully', $data);
                    
                } else {
                    redirect(cn(""));
                }
            }
        } else {
            redirect(cn(""));
        }
    }
}

if (!function_exists('format_avg_time')) {
    function format_avg_time($seconds) {
        
        $lang_hours = lang('hours');
        $lang_minutes = lang('minutes');
        $lang_seconds = lang('seconds');

        if (!is_numeric($seconds) || $seconds <= 0 || floor($seconds) != $seconds) {
            return '--'; 
            // return lang('Not_enough_data');
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60); 
        $remaining_seconds = $seconds % 60;

        if ($hours > 0) {
            return "{$hours} {$lang_hours} {$minutes} {$lang_minutes}";
        } elseif ($minutes > 0) {
            return "{$minutes} {$lang_minutes}";
        } else {
            return "1 {$lang_minutes}";
        }
    }
}


if (! function_exists('user_show_filter_status_button')) {
    function user_show_filter_status_button($controller_name, $params = '')
    {
        $xhtml         = null;
        $config_status = app_config('config')['status'];
        $status_in_controller = (in_array($controller_name, array_keys($config_status))) ? $config_status[$controller_name] : $config_status['order'];
        $status_in_controller = array_diff($status_in_controller, [ORDER_STATUS_ERROR, ORDER_STATUS_FAIL, ORDER_STATUS_AWAITING, 'all', 'All']);
        
        $tmpl_orders_status   = app_config('template')['order_status'];
        $xhtml                = '<div class="nav-pills order-status-pills order_btn_group hide-scrollbar" style="margin-bottom: 20px;">';
        $param_search         = $params['search'];
        $current_search       = array_combine(array_keys($param_search), array_values($param_search));
        
        // Add 'All' pill first
        $current_status = (get('status') != "") ? get('status') : 'all';
        $all_link       = cn($controller_name);
        if ($current_search['query'] != "") {
            $all_link .= '?' . http_build_query($current_search);
        }
        $all_active = ($current_status == 'all') ? 'active' : '';
        $xhtml .= sprintf('<a class="nav-link %s" href="%s">%s</a>', $all_active, $all_link, lang('All'));
        
        foreach ($status_in_controller as $item) {
            $link = cn($controller_name) . '?status=' . $item;
            if ($current_search['query'] != "") {
                $link .= '&' . http_build_query($current_search);
            }
            $current_class  = ($current_status == $item) ? 'active' : '';
            $xhtml .= sprintf(
                '<a class="nav-link %s" href="%s">%s</a>',
                $current_class, $link, $tmpl_orders_status[$item]['name']
            );
        }
        $xhtml .= '</div>';
        return $xhtml;
    }
}