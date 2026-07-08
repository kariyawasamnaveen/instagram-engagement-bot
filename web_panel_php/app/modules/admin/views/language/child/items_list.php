<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_created       = show_item_datetime($item['created'], 'short');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status']);
            $item_default       = show_item_status($controller_name, $item['id'], $item['is_default']);
    ?>
        <tr class="tr_<?php echo esc($item['id']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td><?php echo language_codes($item['code']); ?></td>
            <td class="text-center w-5p"><?php echo $item['code']; ?></td>
            <td class="text-center w-10p"><span class="flag-icon flag-icon-<?=strtolower($item['country_code'])?>"></span></td>
            <td class="text-center w-5p"><?php echo $item_default; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-10p"><?php echo $item_created; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>