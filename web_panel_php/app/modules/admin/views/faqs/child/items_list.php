<?php if (!empty($items)) : $i = $from; ?>
    <?php
         $i = $from;
        foreach ($items as $key => $item) :
            $i++;
            $item_checkbox      = show_item_check_box('check_item', $item['id']);
            $item_sort          = show_item_sort($controller_name, $item['id'], $item['sort']);
            $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
            $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
            $answer  = show_high_light(show_content(html_entity_decode($item['answer'], ENT_QUOTES), 100), $params['search'], 'answer');
    ?>
        <tr class="tr_<?php echo esc($item['id']); ?>">
            <th class="text-center w-1"><?php echo $item_checkbox; ?></th>
            <td class="text-center text-muted"><?=$i?></td>
            <td>
                <h6>Q: <?php echo show_high_light(esc($item['question']), $params['search'], 'question'); ?></h6>
                <p class="text-muted"><strong>Answer:</strong> <?php echo $answer; ?></p>
            </td>
            <td class="text-center"><?php echo $item_sort; ?></td>
            <td class="text-center w-5p"><?php echo $item_status; ?></td>
            <td class="text-center w-10p"><?php echo $item['changed'] ?></td>
            <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo render_tr_no_item(); ?>
<?php endif; ?>