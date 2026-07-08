<?php if (!empty($items)) : $i = $from; ?>
    <?php
       $i = $from;
       foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_id            = show_item_order_id($controller_name, $item, $params);
            $item_status        = show_item_status($controller_name, $item['id'], strtolower($item['sub_status']), '');
            $created            = show_item_datetime($item['created'], 'long');
            $updated            = show_item_datetime($item['changed'], 'long');
            $item_details       = show_item_order_details($controller_name, $item, $params);
            $item_buttons       = show_item_button_action($controller_name, $item['id'], '', $item);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="w-5p"><?php echo $item_id; ?></td>
            <td class="text-muted w-10p"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
            <td><div class="title"><?php echo $item_details; ?></div></td>
            <td class="text-center w-10p text-muted"><?=$created;?></td>
            <td class="text-center w-10p text-danger"><?=esc($item['note'])?></td>
            <td class="text-center w-10p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p"><?php echo $item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>