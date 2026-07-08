<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 * @param  $controller_name
 * @param array $item - item data
 * @return  affilliates total payment and total commisision
 */
if (!function_exists('count_payments_and_total_value')) {
    function count_payments_and_total_value($controller_name = '', $item = [])
    {
        $result = [
            'count' => 0,
            'total' => 0,
        ];
        $CI = &get_instance();
        $CI->load->model('model', 'model');
        $items_transaction = $CI->model->fetch('id, amount', TRANSACTION_LOGS , ['status' => 1, 'uid' => $item['id']], '', '', true);
        if ($items_transaction) {
            $result = [
                'count' => count($items_transaction),
                'total' => array_sum(array_column($items_transaction,'amount')),
            ];
        }
        return $result;
    }
}
/**
 * 
 * @param  $controller_name
 * @param array $item - item data
 * @return  link detail of payments
 */
if (!function_exists('show_item_payment_detail_link')) {
    function show_item_payment_detail_link($controller_name = '', $item = [], $number_payments = '')
    {
        $xhtml = $number_payments;
        if ($number_payments > 0) {
            $link = admin_url('transactions') . '?' . http_build_query(['query' => $item['email'], 'field' => 'email', 'status' => 1]);
            $xhtml = sprintf('<a href="%s">%s</a>', $link, $number_payments);
        }
        return $xhtml;
    }
}


/**
 * From V3.6
 * @param name $controllerName
 * @param array $item_data - default null
 * @return HTML Show button action for item
 * @author Seji2906
 */
if (!function_exists('show_item_button_action_module')) {
    function show_item_button_action_module($controller_name, $item_data = [])
    {
        $xhtml = null;
        if (in_array($item_data['status'], ['rejected', 'approved'])) {
            return $xhtml;
        }
        $tmpl_buttons = [
            'approve' => ['name' => 'Approve',   'class' => '', 'icon' => 'fe fe-check', 'route-name' => '/payout_update/'],
            'reject' => ['name' => 'Reject',   'class' => '', 'icon' => 'fe fe-x-square', 'route-name' => '/payout_update/'],
        ];
        $curent_btn_area = ['approve', 'reject'];
        $xhtml .='<div class="item-action dropdown">
                    <a href="javascript:void(0)" data-toggle="dropdown" class="icon"><i class="fe fe-more-vertical"></i></a>
                <div class="dropdown-menu">';
        foreach ($curent_btn_area as $item) {
            $current_btn = $tmpl_buttons[$item];
            $link = admin_url($controller_name . $current_btn['route-name'] . '?type=' . $item . '&ids='. $item_data['ids']);
            $xhtml .= sprintf('<a href="%s" class="dropdown-item %s"><i class="dropdown-icon %s"></i> %s</a>', $link, $current_btn['class'], $current_btn['icon'], $current_btn['name']);
        }
        $xhtml .= '</div></div>';
        return $xhtml;
    }
}