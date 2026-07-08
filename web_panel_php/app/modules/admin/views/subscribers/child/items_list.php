<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $created            = show_item_datetime($item['created'], 'long');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td>
                <div class="title"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></div>
            </td>
            <td class="text-center w-10p"><?php echo show_high_light(esc($item['ip']), $params['search'], 'ip'); ?></td>
            <td class="text-center w-10p"><?php echo show_high_light(esc($item['country']), $params['search'], 'country'); ?></td>
            <td class="text-center text-muted w-15p"><?php echo $created; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>