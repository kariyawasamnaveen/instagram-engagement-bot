<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Automation_worker extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cron/Cron_model', 'main_model');
    }

    public function run()
    {
        $cron_key = get_cron_key();
        if ($cron_key != $this->input->get('key') && !is_cli()) {
            die("Cron Key mismatch.");
        }

        // PHP Automation is Disabled. 
        // Tasks are now handled by the Python Playwright Background Service (worker.py).
        echo "Python Worker is active. PHP processing disabled.";
        return true;
    }
}
