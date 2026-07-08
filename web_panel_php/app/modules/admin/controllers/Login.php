<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends My_AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staffs_model', 'model');
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {
        if (is_admin_logged_in()) {
            redirect(admin_url('statistics'));
        }

        if ($this->input->post()) {
            if (!$this->input->is_ajax_request()) {
                redirect(admin_url('login'));
            }
            $email = post("email");
            $password = post("password");

            if ($email == "") {
                ms(array(
                    "status" => "error",
                    "message" => lang("email_is_required"),
                ));
            }

            if ($password == "") {
                ms(array(
                    "status" => "error",
                    "message" => lang("Password_is_required"),
                ));
            }

            $user = $this->model->get("*", STAFFS, ['email' => $email]);

            $error = false;
            if (!$user) {
                log_message('error', "DEBUG LOGIN: User not found for email: $email");
                $error = true;
            } else {
                $check = $this->model->app_password_verify($password, $user->password);
                if ($check) {
                    log_message('error', "DEBUG LOGIN: Password Match SUCCESS for email: $email");
                    $error = false;
                } else {
                    log_message('error', "DEBUG LOGIN: Password Match FAILED for email: $email. Input: $password, Hash in DB: " . $user->password);
                    $error = true;
                }
            }

            if (!$error) {
                if ($user->status != 1) {
                    ms(array(
                        "status" => "error",
                        "message" => lang("your_account_has_not_been_activated"),
                    ));
                }
                set_session("sid", $user->id);
                $data_session = array(
                    'email'      => $user->email,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'timezone'   => $user->timezone,
                );
                set_session('staff_current_info', $data_session);
                
                ms(array(
                    "status" => "success",
                    "message" => lang("Login_successfully"),
                    "redirect_url" => admin_url('statistics'),
                ));
            } else {
                ms(array(
                    "status" => "error",
                    "message" => lang("email_address_and_password_that_you_entered_doesnt_match_any_account_please_check_your_account_again"),
                ));
            }
        } else {
            $data = array();
            $this->template->set_layout('auth');
            $this->template->build('auth/sign_in', $data);
        }
    }

    public function logout()
    {
        unset_session("sid");
        unset_session("staff_current_info");
        $this->session->sess_destroy();
        redirect(admin_url('login'));
    }
}
