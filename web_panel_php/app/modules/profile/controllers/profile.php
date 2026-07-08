<?php
defined('BASEPATH') or exit('No direct script access allowed');

class profile extends My_UserController
{
    public $tb_users;
    public $controller_name;
    public $module_icon;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'model');
        $this->controller_name = get_class($this);
        $this->tb_users        = USERS;
    }

    public function index()
    {
        $data = [
            "module" => get_class($this),
            "item"   => $this->model->get('*', $this->tb_users, ['id' => session('uid')], '', '', true),
        ];
        $this->template->set_layout('user');
        $this->template->build('index', $data);
    }

    public function ajax_update($ids = '')
    {
        if (! $this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }

        $id          = session('uid');
        $first_name  = post('first_name');
        $last_name   = post('last_name');
        $password    = post('password');
        $re_password = post('re_password');
        $timezone    = post('timezone');
        if ($first_name == '' || $last_name == '') {
            ms([
                'status'  => 'error',
                'message' => lang("please_fill_in_the_required_fields"),
            ]);
        }
        $data = [
            "first_name" => $first_name,
            "last_name"  => $last_name,
            "timezone"   => $timezone,
            "changed"    => NOW,
        ];
        if ($password != '') {
            if ($password == '') {
                ms([
                    'status'  => 'error',
                    'message' => lang("Password_is_required"),
                ]);
            }
            if (strlen($password) < 6) {
                ms([
                    'status'  => 'error',
                    'message' => lang("Password_must_be_at_least_6_characters_long"),
                ]);
            }
            if ($re_password != $password) {
                ms([
                    'status'  => 'error',
                    'message' => lang("Password_does_not_match_the_confirm_password"),
                ]);
            }
            is_demo_version();
            $data['password'] = $this->model->app_password_hash($password);
        }
        if (! empty($id)) {
            $checkUser = $this->model->get('id,ids,email', $this->tb_users, ['id' => $id]);
            if (empty($checkUser)) {
                ms([
                    'status'  => 'error',
                    'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
                ]);
            }
            if ($this->db->update($this->tb_users, $data, ['id' => $id])) {
                ms([
                    'status'  => 'success',
                    'message' => lang('Update_successfully'),
                ]);
            }
        } else {
            ms([
                'status'  => 'error',
                'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
            ]);
        }
    }

    public function ajax_update_more_infors($ids = '')
    {
        if (! $this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }

        $id        = session('uid');
        $website   = post('website');
        $phone     = post('phone');
        $skype_id  = post('skype_id');
        $what_asap = post('what_asap');
        $address   = post('address');

        $more_information = [
            "website"   => $website,
            "phone"     => $phone,
            "what_asap" => $what_asap,
            "skype_id"  => $skype_id,
            "address"   => $address,
        ];

        $data = [
            "more_information" => json_encode($more_information),
            "changed"          => NOW,
        ];

        if ($id != '') {
            $checkUser = $this->model->get('id,ids,email', $this->tb_users, ['id' => $id]);

            if (empty($checkUser)) {
                ms([
                    'status'  => 'error',
                    'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
                ]);
            }

            if ($this->db->update($this->tb_users, $data, ['id' => $id])) {
                ms([
                    'status'  => 'success',
                    'message' => lang('Updated_successfully'),
                ]);
            }
        } else {
            ms([
                'status'  => 'error',
                'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
            ]);
        }
    }

    public function ajax_update_api($ids = '')
    {
        if (! $this->input->is_ajax_request()) {
            redirect(cn($this->controller_name));
        }
        $uid                  = session('uid');
        $api_key              = create_random_string_key(32);
        $check_exists_api_key = $this->model->get('id,ids,api_key', $this->tb_users, ['api_key' => $api_key]);
        if (! empty($check_exists_api_key)) {
            ms([
                'status'  => 'error',
                'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
            ]);
        }

        $data = [
            "api_key" => $api_key,
            "changed" => NOW,
        ];

        if (! empty($uid)) {
            $check_user = $this->model->get('id,ids,api_key', $this->tb_users, ['id' => $uid]);
            if (empty($check_user)) {
                ms([
                    'status'  => 'error',
                    'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
                ]);
            }
            if ($this->db->update($this->tb_users, $data, ['id' => $uid])) {
                $success_message = lang('Your_API_Key_is_ready_Save_it_before_refreshing_it_will_be_hidden');
                $xhtml   = sprintf('<div class="form-group" id="result_notification">
                        <label> %s </label>
                        <div class="input-group">
                            <input type="text" name="api_key" class="form-control square" value="%s">
                        </div>
                        <div class="alert alert-info m-t-10" role="alert">
                            %s
                        </div>
                    </div>', lang('Key'), $api_key, $success_message);
                echo $xhtml;
            } else {
                ms([
                    'status'  => 'error',
                    'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
                ]);
            }
        }
    }
}
