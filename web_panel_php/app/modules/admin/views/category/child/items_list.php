<?php if (!empty($items)) : $i = $from; ?>
    <?php
       $i = $from;
       foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_status_type = (staff_has_permission($controller_name, 'change_status')) ? 'switch' : 'button';
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], $item_status_type);
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>" data-id="<?php echo $item['id']; ?>">
            <td class="sort-handler w-1p"><i class="fe fe-grid"></i></td>
            <td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
            <td>
                <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
            </td>
            <td class="text-center w-10p"><?php echo $item_status; ?></td>
            <td class="text-center w-10p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>