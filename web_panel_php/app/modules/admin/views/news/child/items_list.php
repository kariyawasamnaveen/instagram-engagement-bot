<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $description        = show_high_light(show_content(htmlspecialchars_decode($item['description'], ENT_QUOTES), 200), $params['search'], 'description');
            $type               = show_item_news_type($item['type']);
            $start              = show_item_datetime($item['created'], 'short');
            $expiry             = show_item_datetime($item['expiry'], 'short');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
            
            $item_status        = show_item_status($controller_name, $item['id'], $item['status']);
            if ($item['status'] && $item['expiry'] < NOW) {
            $item_status = '<span class="badge bg-red">Expired</span>';
            }
    ?>
        <tr class="tr_<?php echo esc($item['id']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td><?php echo $description; ?></td>
            <td class="text-center w-10p"><?php echo $type; ?></td>
            <td class="text-center text-muted w-10p"><?php echo $start; ?></td>
            <td class="text-center text-muted w-10p"><?php echo $expiry; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>