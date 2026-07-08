<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Google/autoload.php'; 

class Googleauth {

    private $client;

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setClientId(get_option('social_login_google_app_id')); 
        $this->client->setClientSecret(get_option('social_login_google_secret_key')); 
        $this->client->setRedirectUri(cn('auth/callback/google')); 
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function createLoginUrl() {
        return $this->client->createAuthUrl();
    }

    public function handleCallback() {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                return false;
            }
            set_session('access_token', $token);
            $this->client->setAccessToken($token['access_token']);
            $oauth2Service = new Google_Service_Oauth2($this->client);
            $userInfo = $oauth2Service->userinfo->get();
            return $userInfo;
        } else {
            return false;
        }
    }
}
