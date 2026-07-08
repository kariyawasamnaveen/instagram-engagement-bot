<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id'], 'btn-group');
            $api_url_base = explode("/api", $item['url']);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td>
                <a href="<?=$api_url_base[0]?>" target="_blank"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></a>
            </td>
            <td class="text-center w-10p">
                <span class="badge bg-dark"><?php echo (double) $item['balance']; ?></span>
            </td>
            <td class="text-center w-10p">
                <span class="badge bg-secondary"><?php echo $item['no_current_services']; ?></span>
            </td>
            <td class="text-center w-15p"><?php echo $item['description']; ?></td>
            <td class="text-center w-10p"><?php echo $item_status; ?></td>
            <td class="text-center w-20p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>