<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $created            = show_item_datetime($item['created'], 'long');
            $item_mode          = ($item['cancel_type']) ? 'Auto' : '<span class="badge bg-secondary">Manual</span>';
            $show_item_details  = show_item_details($controller_name, $item);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
            $item_item_order_details = show_item_order_details($controller_name, $item, $params);
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center w-10p">
                <div><?php echo show_high_light($item['id'], $params['search'], 'id'); ?></div>
                <?php if($item['api_cancel_id'] && staff_has_permission($controller_name, 'copy_api_cancel_id')) { ?>
                    <small class="text-muted">
                    <?=$item['api_cancel_id']?>
                    </small>
                <?php } ?>
            </td>
            <td class="text-center w-5p text-muted"><?=$item_mode;?></td>
            <td class="text-muted w-10p"><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
            <td class="text-center w-10p">
                <a href="<?=admin_url('order?field=id&query=' . $item['order_id']);?>" target='blank'>
                    <div class="content"><span></span><?=show_high_light($item['order_id'], $params['search'], 'order_id');?></div>
                </a>
                <?php if($item['api_order_id']  && staff_has_permission($controller_name, 'copy_api_order_id')) { ?>
                    <small class="text-muted">
                    <?=$item['api_order_id']?>
                    </small>
                <?php } ?>
            </td>
            <td class=""><div class="title"><?php echo $item_item_order_details; ?></div></td>
            <td class="text-center w-10p text-muted"><?=$created;?></td>
            <td class="text-center w-10p"><?php echo $item_status; ?></td>
            <td class="text-center w-10p"><?php echo $show_item_details; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>