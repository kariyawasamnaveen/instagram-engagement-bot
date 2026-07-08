<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
/**
 * From V3.6
 * Get app_config
 * @param string #params
 * @return config data
 */

if (!function_exists('app_config')) {
    function app_config($params)
    {
        $CI = &get_instance();
        $value = $CI->config->item($params);
        return $value;
    }
}

/**
 * Escapes data for safe output in different contexts.
 * 
 * This helper function wraps around the Escaper class to provide
 * context-specific escaping (HTML, JavaScript, CSS, URL, HTML attributes).
 * 
 * It supports both strings and arrays (recursively escapes all values in arrays).
 * 
 * Usage:
 *   - esc($data)                    // Escape as HTML by default
 *   - esc($data, 'html')            // Escape for HTML context
 *   - esc($data, 'js')              // Escape for JavaScript context
 *   - esc($data, 'css')             // Escape for CSS context
 *   - esc($data, 'url')             // Escape for URL context
 *   - esc($data, 'attr')            // Escape for HTML attribute context
 *   - esc($data, 'raw')             // No escaping (returns original data)
 * 
 * Parameters:
 *   - $data (string|array): The data to escape. Arrays are recursively escaped.
 *   - $context (string): The escaping context to apply. Defaults to 'html'.
 *   - $encoding (string|null): Optional character encoding for escaping.
 * 
 * Returns:
 *   - string|array: Escaped string or recursively escaped array.
 * 
 * Throws:
 *   - InvalidArgumentException if an invalid context is provided.
 * 
 * Note:
 *   - For array inputs, this function recursively escapes each element.
 *   - Use 'raw' context when you want to output data without escaping.
 * 
 * Performs simple auto-escaping of data for security reasons.
 * Might consider making this more complex at a later date.
 *
 * If $data is a string, then it simply escapes and returns it.
 * If $data is an array, then it loops over it, escaping each
 * 'value' of the key/value pairs.
 *
 * Valid context values: html, js, css, url, attr, raw, null
 *
 * @param array|string $data
 * @param string       $encoding
 *
 * @throws InvalidArgumentException
 *
 * @return array|string
 */
if (!function_exists('esc')) {
    function esc($data, $context = 'html', $encoding = null)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                $value = esc($value, $context);
            }
        }

        if (is_string($data)) {
            $context = strtolower($context);

            // Provide a way to NOT escape data since
            // this could be called automatically by
            // the View library.
            if (empty($context) || $context === 'raw') {
                return $data;
            }

            if (!in_array($context, ['html', 'js', 'css', 'url', 'attr'], true)) {
                throw new InvalidArgumentException('Invalid escape context provided.');
            }

            $method = $context === 'attr' ? 'escapeHtmlAttr' : 'escape' . ucfirst($context);

            static $escaper;
            $CI = &get_instance();
            $CI->load->library('Escaper');
            if (!$escaper) {
                $escaper = new Escaper($encoding);
            }

            if ($encoding && $escaper->getEncoding() !== $encoding) {
                $escaper = new Escaper($encoding);
            }

            $data = $escaper->{$method}($data);
        }

        return $data;
    }
}

function test_esc_function() {
    $testString = '<script>alert("XSS")</script>';
    $testArray = [
        'html' => '<b>bold</b>',
        'js'   => 'var x = "test";',
        'url'  => 'https://example.com/?q=search term',
        'attr' => '" onclick="alert(1)"',
        'css'  => 'body { background: red; }',
    ];

    // HTML escape (default)
    // $escapedHtml = esc($testString);
    // echo "HTML escaped: $escapedHtml\n";

    // JavaScript escape
    // $escapedJs = esc($testString, 'js');
    // echo "JS escaped: $escapedJs\n";

    // // URL escape
    // $escapedUrl = esc($testArray['url'], 'url');
    // echo "URL escaped: $escapedUrl\n";

    // // HTML attribute escape
    // $escapedAttr = esc($testArray['attr'], 'attr');
    // echo "Attr escaped: $escapedAttr\n";

    // // CSS escape
    // $escapedCss = esc($testArray['css'], 'css');
    // echo "CSS escaped: $escapedCss\n";

    // // Array recursive escape
    // $escapedArray = esc($testArray, 'html');
    // print_r($escapedArray);

    // // Raw (no escape)
    // $raw = esc($testString, 'raw');
    // echo "Raw output: $raw\n";
}


/**
 * From V4.0
 * Prevent simultaneous execution
 * @param array #params
 */

if (!function_exists('lock_file')) {
    function lock_file($params = [], $options = [])
    {
        if (isset($params['file_name'])) {
            $file_path = 'assets/tmp/lock_file_'. $params['file_name'] .'.lock';
        } else {
            $file_path = 'assets/tmp/lock_file_default.lock';
        }
        $text_notification = (isset($params['title_message'])) ? $params['title_message'] : 'already running!';
        $lock = fopen($file_path, 'w'); 
        if ( !($lock && flock($lock, LOCK_EX | LOCK_NB)) ) {
            exit($text_notification);
        }
        return true;
    }
}


/**
 * @param $table
 * @return boole
 */
if (!function_exists('is_table_exists')) {
    function is_table_exists($table = '')
    {
        $CI = &get_instance();
        if ($table && $CI->db->table_exists($table)) {
            return true;
        }
        return false;
    }
}

/**
 * @return cron key
 */
if (!function_exists('get_cron_key')) {
    function get_cron_key()
    {
        $cron_key =  mb_substr(ENCRYPTION_KEY, 0, 10);
        return $cron_key;
    }
}

/**
 * @param array $data_current, data_new
 * return json data payment log before saving to transaction log
 */
if (!function_exists('payment_transaction_log')) {
    function payment_transaction_log($data_current = [], $data_new = [])
    {
        $arr_log_new = [];
        $result = [];
        if (is_array($data_new) && $data_new) {
            $arr_log_new = [
                'time' => NOW,
                'data' => $data_new
            ];
            // Exists Data current and new
            if (is_array($data_current) && $data_current) {
                array_push($data_current, $arr_log_new);
                $result = $data_current;
            } else if (!$data_current) {
                $result[] = [
                    'time' => NOW,
                    'data' => $data_new
                ]; 
            }
        }
        return json_encode($result);
    }
}


/**
 * @return base_url
 */
if (!function_exists('get_base_url')) {
    function get_base_url()
    {
        $base_url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? "https" : "http";
        $request_host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $base_url .= "://" . $request_host;
        $base_url .= str_replace(basename($script_name), "", $script_name);
        return $base_url;
    }
}


/**
 * Check Demo Permission
 * @return redirect | validation message
 */
if (!function_exists('is_demo_version')) {
	function is_demo_version()
    {
        $CI = &get_instance();
        if (DEMO_VERSION && $CI->input->is_ajax_request()) {
            _validation('error', "For security reasons, in demo version there have been disabled some features");
            die();
        } else {
            return true;
        }
	}
}



