<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cronjobs extends My_AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->controller_name = strtolower(get_class($this));
        $this->path_views = "cronjobs";
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
    }
    
    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $cron_key = get_cron_key();
        // $link_format = 'curl -s %s';
        $link_format = 'curl -s %s >/dev/null 2>&1';

        $cron_links = [
            'order'           => cn('cron/order?key=' . $cron_key),
            'multiple_status' => cn('cron/multiple_status?key=' . $cron_key),
            'dripfeed'        => cn('cron/dripfeed?key=' . $cron_key),
            'subscriptions'   => cn('cron/subscriptions?key=' . $cron_key),
            'automation'      => cn('cron/run?key=' . $cron_key),
            'automation_dry_run' => cn('cron/run?key=' . $cron_key . '&dry_run=1'),
            'sync_services'             => cn('cron/sync_services?key=' . $cron_key),
            'multiple_refill_status'    => cn('cron/multiple_refill_status?key=' . $cron_key),
        ];

        if (!is_table_exists(ORDERS_REFILL)) unset($cron_links['multiple_refill_status']);


        $data = array(
            "link_format" => $link_format,
            "cron_links"     => $cron_links,
        );
        $this->template->build($this->path_views . '/index', $data);
    }

}
