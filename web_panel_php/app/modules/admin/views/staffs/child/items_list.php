<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['ids']);
            $full_name          = show_high_light(esc($item['first_name']), $params['search'], 'first_name') . " " . show_high_light(esc($item['last_name']), $params['search'], 'last_name');
            $email              = show_high_light(esc($item['email']), $params['search'], 'email');
            $item_status        = ($item['id'] != 10) ? show_item_status($controller_name, $item['ids'], $item['status'], 'switch') : show_item_status($controller_name, $item['ids'], $item['status'], 'button');
            $created            = show_item_datetime($item['created'], 'long');
            $show_item_buttons  = show_item_button_action($controller_name, $item['ids']);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td>
            <div class="title"><h6><?php echo $full_name; ?></h6></div>
                <div class="sub text-muted"><?php echo $email; ?></small></div>
            </td>
            <td class="text-center w-10p"><?php echo $item['permission_name']; ?></td></td>
            <td class="text-center w-15p"><?php echo $created; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>