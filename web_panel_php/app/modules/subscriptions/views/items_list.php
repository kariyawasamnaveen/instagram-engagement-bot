<?php if (!empty($items)) : $i = $from; ?>
    <?php
        foreach ($items as $key => $item) :
            $i++;
           
            
            $params['search']['field'] = 'id';
            $item_id = show_high_light(esc($item['id']), $params['search'], 'id');    

            $item_service_name = $item['service_id'] ." - ". $item['service_name'];
            $item_details       = show_item_order_details($controller_name, $item, $params, 'user');
            $item_status = (in_array($item['status'], ['error'])) ? 'pending' : $item['status'];
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <td class="text-center"><?=$item_id; ?></td>
            <td>
                <div class="title"><?php echo $item_details; ?></div>
            </td>
            <td class="text-center w-10p"><?=convert_timezone($item['created'], "user")?></td>
            <td class="text-center w-10p"><?php echo show_item_status($controller_name, $item['id'], $item_status, '', 'user');?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>