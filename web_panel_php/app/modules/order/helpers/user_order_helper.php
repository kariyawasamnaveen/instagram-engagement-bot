<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *  From V4.0
 * @param array $controller_name
 * @param array $item - item data
 * @return  refill button html
 */
if (!function_exists('show_item_refill_button')) {
    function show_item_refill_button_old($controller_name = '', $item = [])
    {
        $xhtml = null;

        $limit_refill_date_time = strtotime($item['created']) + 30 * 24 * 60 * 60; //limit under 30days
        if ($item['refill'] && strtotime(NOW) < $limit_refill_date_time && $item['status'] == 'completed') {
            $xhtml .= '<div class="component-button-refill">';
            $limit_refill_day = strtotime($item['refill_date']);
            if (strtotime($item['refill_date']) < strtotime(NOW) && strtotime(NOW) > $limit_refill_day ) {
                if (strtotime($item['refill_date']) < (24 * 3600)) {
                    $title = 'Refill';
                    $link  = cn('/refill/add/' . $item['ids']);
                    $xhtml .= sprintf('<btn class="btn btn-sm btn-info btn-refill" data-href="%s">%s</btn>', $link, $title);
                } else {
                    $title       = 'Refill';
                    $refill_note = sprintf('Refill will be available in approximately %s', estimated_time_arrival_string($item['refill_date']));
                    $xhtml .= sprintf('<span class="btn btn-sm  btn-gray" data-toggle="tooltip" data-placement="right" title="" data-original-title="%s">%s</span>', $refill_note, $title);
                }
            } elseif (strtotime($item['refill_date']) >= strtotime(NOW)) {
                $title       = 'Refill';
                $refill_note = sprintf('Refill will be available in approximately %s', estimated_time_arrival_string($item['refill_date']));
                $xhtml .= sprintf('<span class="btn btn-sm btn-gray" data-toggle="tooltip" data-placement="right" title="" data-original-title="%s">%s</span>', $refill_note, $title);
            }
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
}

// Check if the function 'show_item_refill_button' does not exist to avoid redeclaration
if (!function_exists('show_item_refill_button')) {
    // Define the function 'show_item_refill_button'
    function show_item_refill_button($controller_name = '', $item = [])
    {
        // Initialize the variable to hold the HTML output
        $xhtml = null;
        
        // Calculate the deadline for refill (30 days after the order is created)
        $limit_refill_date_time = strtotime($item['created']) + 30 * 24 * 60 * 60;

        // Check if the item status is 'completed' and the order is within the 30-day period
        if ($item['status'] == 'completed' && strtotime(NOW) < $limit_refill_date_time) {
            
            // Create a wrapper for the refill button
            $xhtml .= '<div class="component-button-refill">';
            
            // Get the timestamp for the refill date
            $limit_refill_day = strtotime($item['refill_date']);
            
            // If the refill time has passed and it's within the 24-hour window
            if (strtotime(NOW) > $limit_refill_day && strtotime(NOW) < ($limit_refill_day + 24 * 3600)) {
                // Set the title and link for the refill button
                $title = 'Refill';
                $link  = cn('/refill/add/' . $item['ids']);
                // Generate the refill button as a link
                $xhtml .= sprintf('<a class="btn btn-sm btn-info btn-refill" href="%s">%s</a>', $link, $title);
            } 
            // If the refill time has not arrived yet
            elseif (strtotime(NOW) < $limit_refill_day) {
                // Set the title and a note indicating when refill will be available
                $title       = 'Refill';
                $refill_note = sprintf('Refill will be available in approximately %s', estimated_time_arrival_string($item['refill_date']));
                // Generate a gray button with a tooltip showing the refill availability
                $xhtml .= sprintf('<span class="btn btn-sm btn-gray" data-toggle="tooltip" data-placement="right" title="" data-original-title="%s">%s</span>', $refill_note, $title);
            } 
            // If more than 24 hours have passed since the last refill attempt but still within 30 days
            elseif (strtotime(NOW) > ($limit_refill_day + 24 * 3600)) {
                // Set the title and a note stating the refill is available after 24 hours
                $title       = 'Refill';
                $refill_note = 'You can refill again after 24 hours.';
                // Generate a gray button with a tooltip showing when refill will be available
                $xhtml .= sprintf('<span class="btn btn-sm btn-gray" data-toggle="tooltip" data-placement="right" title="" data-original-title="%s">%s</span>', $refill_note, $title);
            }

            // Close the wrapper for the refill button
            $xhtml .= '</div>';
        }

        return $xhtml;
    }
}




/**
 *  From V4.0
 * @param array $controller_name
 * @param array $item - item data
 * @return  cancel button html
 */
if (!function_exists('show_item_cancel_button')) {
    function show_item_cancel_button($controller_name = '', $item = [])
    {
        $xhtml = null;
        if ($item['cancel'] && in_array($item['status'], [ORDER_STATUS_AWAITING, ORDER_STATUS_INPROGRESS, ORDER_STATUS_PENDING, ORDER_STATUS_PROCESSING])) {
            $xhtml .= '<div class="component-button-cancel">';
            $title = 'Cancel';
            $link  = cn('order/cancel/index/' . $item['ids']);
            $xhtml .= sprintf('<a class="btn btn-sm btn-outline-blue btn-cancel" href="%s">%s</a>', $link, $title);
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
}

/**
 *  From V4.0
 * @param input $datatime
 * @return  Get different estimated time arrival hours and minutes
 */
if (!function_exists('estimated_time_arrival_string')) {
    function estimated_time_arrival_string($datetime)
    {
        $now = new DateTime;
        $next = new DateTime($datetime);
        $string = array(
            'h' => 'hour',
            'i' => 'minute',
        );
        $interval = $next->diff($now);

        foreach ($string as $k => $v) {
            if ($interval->d > 0) {
                $interval->h = $interval->h + 24 * $interval->d;
            }
            $string[$k] = $interval->$k . ' ' . $v . ($interval->$k > 1 ? 's' : '');
        }

        if ($interval->h > 0) {
            $result = $string['h'] . ' ' . $string['i'];
        } else {
            $result = $string['i'];
        }
        return $result;
    }
}

/**
 *  From V4.1
 * @param input $data Message place order successfullty
 * @return  Get message
 */
if (!function_exists('get_message_order_success')) {
    function get_message_order_success($data = [], $params = [])
    {
        $result = [];
        $title = lang('order_received');
        $message = lang('thank_you_your_order_has_been_received');
        $details = null;
        if ($data) {
            $details = '<ul>';
            foreach ($data as $key => $row) {
                $details .= '<li>' . lang($key) .': '. $row.'</li>';
            }
            $details .= '</ul>';
        }
        $result = sprintf(
            '<div class="alert alert-info order-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>
                <h3><span class="fe fe-check"></span> %s</h3>
                <div class="p-b-20"> %s</div>
                <small>
                    %s
                </small>
            </div>', $title, $message, $details
        );
        return $result;
    }
}

if (!function_exists('filter_categories_by_keyword')) {
function filter_categories_by_keyword($categories) {
    $config_social_media = app_config('social_media');
    if (empty($categories) || !is_array($categories)) {
      return ['everything' => $config_social_media['everything']];
    }
    $category_filter = [
      'everything'  => $config_social_media['everything'],
      'favorite'    => $config_social_media['favorite'],
    ];
    $has_other = false;
    foreach ($categories as $cat) {
      if (!isset($cat['name'])) continue;

      $cat_name_lower = strtolower($cat['name']);
      $found_group = false;

      foreach ($config_social_media as $key => $info) {
        if (in_array($key, ['everything', 'other'])) continue;

        if (strpos($cat_name_lower, $key) !== false) {
          $category_filter[$key] = $info;
          $found_group = true;
          break;
        }
      }

      if (!$found_group) {
        $has_other = true;
      }
    }
    if ($has_other) {
        $category_filter['other'] = $config_social_media['other'];
        // Đảm bảo 'other' là phần tử cuối cùng
        $other = $category_filter['other'];
        unset($category_filter['other']);
        $category_filter['other'] = $other;
    }
    return $category_filter;
  }
}


/**
 * Filters the categories based on the cate_id values in items_service.
 * The function removes duplicates in cate_ids and returns items_category 
 * that match the cate_id in the items_service.
 *
 * @param array $items_category The list of categories with 'id' and 'name'.
 * @param array $items_service The list of services with 'cate_id'.
 * @return array Filtered list of categories with 'id' and 'name'.
 */
if (!function_exists('filter_items_by_category')) {
    function filter_items_by_category($items_category = [], $items_service = [])
    {
        // Get the cate_id from items_service and remove duplicates
        $cate_ids = array_flip(array_unique(array_column($items_service, 'cate_id')));

        // Step 2: Filter the items_category array based on the unique cate_id
        $activated_items_category = array_filter($items_category, function($item) use ($cate_ids) {
            // Check if the item's id exists in the cate_ids array
            return isset($cate_ids[$item['id']]);
        });

        return array_values($activated_items_category);
    }
}
