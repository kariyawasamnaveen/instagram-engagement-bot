<?php if (!empty($items)) : $i = $from; ?>
    <?php
       $i = $from;
       foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
            $changed            = show_item_datetime($item['changed'], 'long');
            $created            = show_item_datetime($item['created'], 'long');
            $subject            = show_item_ticket_subject($controller_name, $item, $params);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center w-10p text-muted"><?php echo show_high_light(esc($item['id']), $params['search'], 'id'); ?></td>
            <td class="text-center w-20p text-muted"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
            <td><?php echo $subject; ?></td>
            <td class="text-center w-10p"><?php echo $item_status; ?></td>
            <td class="text-center w-10p text-muted"><?php echo $changed; ?></td>
            <td class="text-center w-10p text-muted"><?php echo $created; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>