<?php
defined('BASEPATH') or exit('No direct script access allowed');

class membership extends My_UserController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
    }

    public function index()
    {
        $user = $this->main_model->get('*', USERS, ['id' => session('uid')], '', '', true);
        $data = [
            'module' => get_class($this),
            'user'   => $user,
        ];
        $this->template->set_layout('user');
        $this->template->build('index/index', $data);
    }

    public function upgrade()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }

        $user = $this->main_model->get('balance', USERS, ['id' => session('uid')], '', '', true);
        $price = 59.99;

        if ($user['balance'] < $price) {
            ms(['status' => 'error', 'message' => 'Insufficient funds. Please add funds first.']);
        }

        $new_balance = $user['balance'] - $price;
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->db->update(USERS, [
            'balance'           => $new_balance,
            'membership_status' => 1,
            'membership_expiry' => $expiry,
            'membership_level'  => 'pro'
        ], ['id' => session('uid')]);

        ms(['status' => 'success', 'message' => 'Upgraded to Pro successfully! Valid until ' . $expiry]);
    }
}
