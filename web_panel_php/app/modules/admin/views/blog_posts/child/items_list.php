<?php 
$thead_params = [
    'checkbox_data_name' => 'check_' . $items_category[0]['cate_id']
];
echo render_table_thead($columns, true, false, true, $thead_params); 
?>
<?php if (!empty($items_category)) {
foreach ($items_category as $key => $item) {
    $item_checkbox      = show_item_check_box('check_item', $item['id'], '', 'check_' . $items_category[0]['cate_id']);
    $item_status        = show_item_status($controller_name, $item['id'], $item['status'], 'switch');
    $show_item_buttons  = show_item_button_action($controller_name, $item['id']);

    $language_name = '';
    foreach ($items_lang as $key => $item_lang) {
    if ($item_lang['is_default']) {
        $link = admin_url($controller_name . '/update/'. $item['id']);
        $language_name .= sprintf('<a class="m-r-10" href="%s" data-toggle="tooltip" title="Edit"><i class="fa fa-check text-success"></i></a>', $link);
    } else {
        $link = admin_url($controller_name . '/update/'. $item['id'] .'?ref_lang='.$item_lang['code']);
        $language_name .= sprintf('<a href="%s" data-toggle="tooltip" title="Edit related language for this item"><span class="fe fe-edit"></span></a>', $link);
    }
    }
?>
<tr class="tr_<?php echo esc($item['ids']); ?>" data-id="<?php echo $item['id']; ?>">
    <td class="text-center w-1p"><?php echo $item_checkbox; ?></td>
    <td>
    <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
    </td>
    <td class="text-center w-10p"><?php echo $item_status; ?></td>
    <td class="text-center w-10p"><?php echo $language_name; ?></td>
    <td class="text-center w-10p"><?php echo show_item_datetime($item['released'], 'short'); ?></td>
    <td class="text-center w-10p"><?php echo show_item_datetime($item['changed'], 'long'); ?></td>
    <td class="text-center w-10p"><?php echo show_item_datetime($item['created'], 'long'); ?></td>
    <td class="text-center w-10p"><?php echo $show_item_buttons; ?></td>
</tr>
<?php }}?>