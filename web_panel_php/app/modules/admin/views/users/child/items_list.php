<?php if (!empty($items)) : $i = $from; ?>
    <?php
       $i = $from;
       foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['ids']);
            $full_name = show_high_light(esc($item['first_name']), $params['search'], 'first_name') . " " . show_high_light(esc($item['last_name']), $params['search'], 'last_name');
            $email = show_high_light(esc($item['email']), $params['search'], 'email');
            $custom_rate_btn    = show_item_custom_rate_btn($controller_name, $item['ids']);
            $item_status_type = (staff_has_permission($controller_name, 'change_status')) ? 'switch' : 'button';
            $item_status        = show_item_status($controller_name, $item['ids'], $item['status'], $item_status_type);
            $created            = show_item_datetime($item['created'], 'long');
            $show_item_buttons  = show_item_button_action($controller_name, $item['ids']);

            $what_asap = get_value($item['more_information'], 'what_asap');
            if ($what_asap) {
                $what_asap = sprintf('<div class="text-muted small"><i class="icon fe fe-phone-call">: %s</i>', $what_asap);
            }
            $customer_infor = sprintf('<div class="title">
                                            <strong>%s</strong> <br>
                                            %s
                                            %s
                                        </div>
                                    ', $full_name, $email, $what_asap);



    ?>
        <tr class="tr_<?php echo esc($item['ids']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td>
                <?=$customer_infor; ?>
            </td>
            <td class="text-center w-10p"><?= esc((double)$item['balance']); ?></td></td>
            <td class="text-center w-10p"><?= esc($item['history_ip']); ?></td></td>
            <?php
                if (staff_has_permission($controller_name, 'custom_rate')) :
            ?>
                <td class="text-center w-15p"><?php echo $custom_rate_btn; ?></td>
            <?php endif;?>
            <td class="text-center w-10p"><?php echo $created; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>