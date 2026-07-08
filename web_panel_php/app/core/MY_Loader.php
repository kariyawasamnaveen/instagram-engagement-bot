<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {

    public function _ci_object_to_array($object) {
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $object[$key] = $this->_ci_object_to_array($value);
            }
        }
        return $object;
    }

}