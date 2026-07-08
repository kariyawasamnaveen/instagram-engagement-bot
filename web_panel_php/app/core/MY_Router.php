<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH."third_party/MX/Router.php";

class MY_Router extends MX_Router {
    public function locate($segments)
    {
        $result = parent::locate($segments);
        @file_put_contents(
            '/tmp/router_debug.log',
            date('c') . ' locate in=' . json_encode($segments) .
            ' result=' . json_encode($result) .
            ' module=' . json_encode($this->module) .
            ' directory=' . json_encode($this->directory) .
            ' class=' . json_encode($this->class) .
            ' method=' . json_encode($this->method) . "\n",
            FILE_APPEND
        );
        return $result;
    }

    protected function _set_request($segments = array())
    {
        @file_put_contents(
            '/tmp/router_debug.log',
            date('c') . ' set_request in=' . json_encode($segments) . "\n",
            FILE_APPEND
        );

        parent::_set_request($segments);

        @file_put_contents(
            '/tmp/router_debug.log',
            date('c') . ' set_request out module=' . json_encode($this->module) .
            ' directory=' . json_encode($this->directory) .
            ' class=' . json_encode($this->class) .
            ' method=' . json_encode($this->method) .
            ' rsegments=' . json_encode($this->uri->rsegments ?? null) . "\n",
            FILE_APPEND
        );
    }
}
