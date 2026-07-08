<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once('cron.php');

class services extends cron
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cron/services_model', 'main_model');
    }

    public function update_avg_time() {
        
        $lock = fopen('_lock_file_avg_time.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Avg ime already running');
        $success = $this->main_model->update_avg_time();
        if ($success) {
            echo "Update Successfull!";
        } else {
            echo "Falied";
        }
    }

}
