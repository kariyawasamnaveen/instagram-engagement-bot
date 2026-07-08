<?php
defined('BASEPATH') or exit('No direct script access allowed');

class test_cron extends MX_Controller
{
    public function index()
    {
        file_put_contents('/tmp/test_cron_hit.log', date('c') . " index\n", FILE_APPEND);
        $this->output->set_output("Test Cron Controller index called\n");
    }

    public function hello()
    {
        file_put_contents('/tmp/test_cron_hit.log', date('c') . " hello\n", FILE_APPEND);
        $this->output->set_output("Hello from Test Cron Controller\n");
    }
}
