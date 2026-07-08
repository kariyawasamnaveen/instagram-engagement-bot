<?php if (!empty($items)) : $i = $from; ?>
    <?php
       $i = $from;
       foreach ($items as $key => $item) :
            $i++;
            $item_payment_type  = show_item_transaction_type($item['type']);
            $created            = show_item_datetime($item['created'], 'long');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '', '');
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <td class="text-center text-muted w-10p"><?=$item['id']?></td>
            <td><?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?></td>
            <td class="text-center w-5p"><?php if ($item['old_balance']) echo (double)$item['old_balance'] ; ?></td>
            <td class="text-center w-5p"><?php echo (double)$item['amount']; ?></td>
            <td class="text-center w-10p"><?php echo $item_payment_type ; ?></td>
            <td class="text-center"><?php echo show_high_light(esc($item['transaction_id']), $params['search'], 'transaction_id'); ?></td>
            <td class="text-center w-5p text-muted"><?php echo $item['txn_fee']; ?></td>
            <td class="text-center w-10p"><?php echo show_high_light(esc($item['note']), $params['search'], 'note'); ?></td>
            <td class="text-center w-10p text-muted"><?php echo $created; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>