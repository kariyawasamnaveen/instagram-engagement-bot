
<?php if (!empty($items)) : $i = $from; ?>
    <?php
      foreach ($items as $key => $item) :
          $i++;
          $item_payment_type  = show_item_transaction_type($item['type']);
          $created            = show_item_datetime($item['created'], 'long');
            
    ?>
        <tr class="tr_<?php echo esc($item['id']); ?>">
          <td class="text-center w-5p text-muted"><?=$item['id']?></td>
          <td class="text-center w-10p"><?php echo $item_payment_type ; ?></td>
          <td class="text-center w-10p"><?=(double)$item['amount']; ?></td>
          <td class="text-center w-5p text-muted"><?=(double)$item['txn_fee']; ?></td>
          <td class="text-center w-5p text-muted"><?php echo $created; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>
