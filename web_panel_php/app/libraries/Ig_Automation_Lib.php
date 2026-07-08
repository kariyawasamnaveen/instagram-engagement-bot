<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ig_Automation_Lib
{

    protected $CI;
    protected $cookie_path;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->cookie_path = APPPATH . '../assets/tmp/ig_cookies/';
        if (!is_dir($this->cookie_path)) {
            mkdir($this->cookie_path, 0777, true);
        }
    }

    /**
     * SNEAKY ANTI-BAN: Generate a unique, consistent Browser User-Agent based on the username.
     */
    private function generate_device_info($username)
    {
        $hash = md5($username . "smartpanel_salt_2026");
        $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36";
        
        return [
            'user_agent' => $user_agent,
            'device_id' => md5($username)
        ];
    }

    /**
     * Extracts Media ID from Instagram URL or Shortcode
     */
    public function resolve_media_id($target)
    {
        if (is_numeric($target)) return $target;

        $shortcode = '';
        if (strpos($target, 'instagram.com') !== false) {
            preg_match('/\/p\/([^\/]+)\//', $target, $matches);
            if (isset($matches[1])) {
                $shortcode = $matches[1];
            } else {
                preg_match('/\/reel\/([^\/]+)\//', $target, $matches);
                if (isset($matches[1])) $shortcode = $matches[1];
            }
        } else {
            $shortcode = $target;
        }

        if (!$shortcode) return $target;

        // Convert shortcode to numerical media ID
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
        $media_id = '0';
        for ($i = 0; $i < strlen($shortcode); $i++) {
            $char = $shortcode[$i];
            $media_id = bcmul($media_id, '64');
            $media_id = bcadd($media_id, strpos($alphabet, $char));
        }
        return $media_id;
    }

    /**
     * Extracts numerical User ID from Instagram URL or Username
     */
    public function resolve_user_id($target)
    {
        if (is_numeric($target)) return $target;

        $username = '';
        if (strpos($target, 'instagram.com') !== false) {
            // Match username from URL (e.g., instagram.com/crystal_glam/)
            preg_match('/instagram\.com\/([^\/?#]+)/', $target, $matches);
            if (isset($matches[1])) $username = $matches[1];
        } else {
            $username = $target;
        }

        if (!$username) return $target;

        $device = $this->generate_device_info($username);
        $cookie_file = $this->cookie_path . md5($username) . '.txt';

        // GET profile page to find the ID
        $ch = curl_init("https://www.instagram.com/{$username}/?__a=1&__d=dis"); // Using __a=1 for JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $device['user_agent']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        // Try to find the numerical ID in the JSON response
        preg_match('/"id":"(\d+)"/', $response, $matches);
        if (isset($matches[1])) return $matches[1];

        // Backup plan: search in the whole HTML if __a=1 is blocked
        preg_match('/"profile_id":"(\d+)"/', $response, $matches);
        if (isset($matches[1])) return $matches[1];

        return $target;
    }

    /**
     * Simulates a human delay to prevent API flooding detection.
     */
    private function human_delay($min = 1, $max = 3)
    {
        sleep(rand($min, $max));
    }

    public function login($username, $password, $proxy = null, $force = false)
    {
        $cookie_file = $this->cookie_path . md5($username) . '.txt';
        $device = $this->generate_device_info($username);

        if ($force && file_exists($cookie_file)) {
            unlink($cookie_file);
        }

        if (!$force && file_exists($cookie_file) && (time() - filemtime($cookie_file) < 3600)) {
            return ['status' => true, 'message' => 'Using cached session'];
        }

        $this->human_delay(2, 4);

        // STAGE 1: GET main page to get initial CSRF
        $ch = curl_init('https://www.instagram.com/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $device['user_agent']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
        $resp = curl_exec($ch);
        curl_close($ch);

        // Get CSRF from cookie
        $csrf = $this->get_csrf_from_cookie($cookie_file);

        // STAGE 2: Web AJAX Login
        $ch = curl_init('https://www.instagram.com/accounts/login/ajax/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'enc_password' => "#PWD_INSTAGRAM_BROWSER:0:" . time() . ":{$password}",
            'username' => $username,
            'queryParams' => "{}",
            'optIntoOneTap' => 'false'
        ]));
        curl_setopt($ch, CURLOPT_USERAGENT, $device['user_agent']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-CSRFToken: $csrf",
            "X-Instagram-AJAX: 1",
            "X-Requested-With: XMLHttpRequest",
            "Referer: https://www.instagram.com/accounts/login/"
        ]);
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($http_code == 200 && isset($json['authenticated']) && $json['authenticated'] == true) {
            return ['status' => true, 'message' => 'Logged in successfully'];
        } else {
            return ['status' => false, 'message' => 'Login failed', 'response' => $response];
        }
    }

    private function get_csrf_from_cookie($cookie_file)
    {
        if (!file_exists($cookie_file)) return '';
        $content = file_get_contents($cookie_file);
        preg_match('/csrftoken\s+([^\s]+)/', $content, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }

    public function perform_action($action, $username, $password, $target, $proxy = null, $is_retry = false)
    {
        $login = $this->login($username, $password, $proxy);
        if (!$login['status']) return $login;

        $cookie_file = $this->cookie_path . md5($username) . '.txt';
        $device = $this->generate_device_info($username);
        $csrf = $this->get_csrf_from_cookie($cookie_file);

        $url = '';
        switch ($action) {
            case 'like':
                $url = "https://www.instagram.com/api/v1/web/likes/{$target}/like/";
                break;
            case 'follow':
                $url = "https://www.instagram.com/api/v1/web/friendships/{$target}/follow/";
                break;
        }

        $this->human_delay(1, 3);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $device['user_agent']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-CSRFToken: $csrf",
            "X-Instagram-AJAX: 1",
            "X-Requested-With: XMLHttpRequest",
            "Referer: https://www.instagram.com/"
        ]);
        if ($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($http_code == 200 && isset($json['status']) && $json['status'] == 'ok') {
            return ['status' => true, 'message' => 'Action performed successfully', 'response' => $response];
        }

        if (strpos($response, 'login_required') !== false && !$is_retry) {
            $this->login($username, $password, $proxy, true);
            return $this->perform_action($action, $username, $password, $target, $proxy, true);
        }

        return ['status' => false, 'message' => 'Action failed: ' . $http_code, 'response' => $response];
    }
}
