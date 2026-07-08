<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = ($item['id'] == 1) ? '' : show_item_check_box('check_item', $item['id']);
            $item_status        = ($item['id'] == 1) ? show_item_status($controller_name, $item['id'], $item['status'], 'button') : show_item_status($controller_name, $item['id'], $item['status'], 'switch');
            $show_item_buttons  = ($item['id'] == 1) ? '' : show_item_button_action($controller_name, $item['id']);
    ?>
        <tr class="tr_<?php echo ($item['id'] == 1) ? '' : esc($item['ids']); ?>" data-id="<?php echo ($item['id'] == 1) ? '' : $item['id']; ?>">
            <td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
            <td>
                <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
            </td>
            <td class="text-center w-20p">
                <div class="title"><?php echo $item['description']; ?></div>
            </td>
            <td class="text-center w-10p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>