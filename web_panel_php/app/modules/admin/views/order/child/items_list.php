<?php if (!empty($items)) : $i = $from; ?>
    <?php
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_id            = show_item_order_id($controller_name, $item, $params);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], '');
            $created            = show_item_datetime($item['created'], 'long');
            $item_details       = show_item_order_details($controller_name, $item, $params);
            $item_buttons       = show_item_button_action($controller_name, $item['id'], '', $item);
            $item_mode_class   = $item['mode'] && $item['api_service_id'] ? 'badge-default' : 'badge-default';
            $item_mode_title   = $item['mode'] && $item['api_service_id'] ? 'Auto' : 'Manual';
            
    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="w-10p text-center"><?php echo $item_id; ?></td>
            <td class="text-muted w-15p">
                <div>
                    <?php echo show_high_light(esc($item['email']), $params['search'], 'email'); ?>
                </div>
                <?php if ($item['order_source_type'] != ORDER_SOURCE_DEFAULT) : ?>
                <div>
                    <span class="badge badge-default">
                        <?= ucfirst(esc($item['order_source_type'])) ?>
                    </span>
                </div>
                <?php endif; ?>
            </td>
            <td>
                <div class="title"><?php echo $item_details; ?></div>
            </td>
            <td class="text-center w-10p text-muted"><?=$created;?></td>
            <td class="text-center w-10p text-danger"><?=esc($item['note'])?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p text-muted">
                <span class="badge <?= $item_mode_class ?>"><?= $item_mode_title ?></span>
            </td>
            <td class="text-center w-5p"><?php echo $item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>