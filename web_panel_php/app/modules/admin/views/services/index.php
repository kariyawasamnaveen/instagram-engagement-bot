<?php 
  $show_search_area = show_search_area($controller_name, $params);
  $search_fields = get_search_fields_config(['id', 'name', 'api_service_id', 'provider_name']);
?>
<script>
  const searchFieldsConfig = <?php echo json_encode($search_fields); ?>;
</script>

<div class="page-title m-b-20">
  <div class="row justify-content-between">
    <div class="col-md-6">
      <h1 class="page-title">
          <span class="fa fa-list-ul"></span> Services
      </h1>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <div class="input-group" id="search-input-with-select">
            <input type="text" name="query" class="form-control" placeholder="Search for…" value="">
            <select name="search-field" class="form-control">
              <option value="all">All</option>
              <?php foreach ($search_fields as $field): ?>
                <option value="<?= $field['key'] ?>"><?= $field['label'] ?></option>
              <?php endforeach; ?>
            </select>
        </div>
      </div>    
    </div>
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="col-md-6">
          <div class="btn-group" role="group" aria-label="Basic example">
            <?php if (staff_has_permission($controller_name, 'add')) : ?>
              <a href="<?=admin_url($controller_name . "/update"); ?>" class="btn btn-outline-primary ajaxModal"><span class="fe fe-plus"></span> Add new</a>
            <?php endif; ?>
            <?php if (staff_has_permission($controller_name, 'import')) : ?>
              <a href="<?=admin_url('provider/services');?>" class="btn btn-outline-primary"><span class="fe fe-folder-plus"></span> Import</a>
            <?php endif; ?>
            <a href="#" class="btn btn-outline-primary btn-services-collapse"><span class="fe fe-chevrons-up"></span> Hide All</a>
          </div>
          
        </div>
        <div class="col-md-6 d-flex">
          <div class="form-group mr-2">
            <select  name="status" class="form-select search-by-category">
              <option value="0"> <?=lang("all")?></option>
              <?php 
                if (!empty($items_category)) {
                  foreach ($items_category as $key => $category) {
              ?>
              <option value="<?=$key?>"><?=$category?></option>
              <?php }}?>
            </select>
          </div>
          <?php echo show_bulk_btn_action($controller_name); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php
    $is_empty_item =  false;
  ?>
  <?php if (!empty($items)) : ?>
    <div class="col-md-12 col-xl-12 items-by-category">
      <?php foreach ($items as $key => $items_category) : ?>
        <?php 
          $item_category_id = $items_category[0]['cate_id'];
          $link_edit_category = admin_url('category/update/' . $item_category_id);
        ?>
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">
                <?=$key;?>
                <a href="<?php echo $link_edit_category; ?>" class="badge badge-dark ajaxModal" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Category"><i class="fe fe-edit-2"></i></a>
              </h4>
              <div class="card-options">
                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
              </div>
            </div>
            <div class="table-responsive service-sortable">
              <table class="table table-hover table-vcenter table-striped card-table">
                <?php 
                  $thead_params = [
                    'sort-table' => true,
                    'checkbox_data_name' => 'check_' . $items_category[0]['cate_id'],
                  ];
                  if (!staff_has_permission($controller_name, 'see_provider')) {
                    unset($columns['provider']);
                  }
                  echo render_table_thead($columns, true, false, true, $thead_params); 
                ?>
                <tbody>
                  <?php if (!empty($items_category)) : ?>
                    <?php foreach ($items_category as $key => $item) : ?>
                      <?php
                        $item_checkbox      = show_item_check_box('check_item', $item['id'], '', 'check_' . $item['cate_id']);
                        $item_status_type = (staff_has_permission($controller_name, 'change_status')) ? 'switch' : 'button';
                        $item_status        = show_item_status($controller_name, $item['id'], $item['status'], $item_status_type);
                        $show_item_buttons  = show_item_button_action($controller_name, $item['id']);
                        $show_item_view     = show_item_details($controller_name, $item);
                        $show_item_attr     = show_item_service_attr($item);

                        $sync_min = 0;
                        $sync_max = 0;
                        $sync_rate = 0;
                        $auto_status = 0;
                        if (isset($item['sync_options']) && $item['sync_options']) {
                          $sync_min = get_value($item['sync_options'], 'sync_min');
                          $sync_max = get_value($item['sync_options'], 'sync_max');
                          $sync_rate = get_value($item['sync_options'], 'sync_rate');
                          $auto_status = get_value($item['sync_options'], 'auto_status');
                        }
                      ?>
                      <tr class="tr_<?php echo esc($item['ids']); ?> service-item" data-id="<?php echo $item['id']; ?>" data-cate_id="<?php echo $item['cate_id']; ?>" data-cate-id="<?php echo $item['cate_id']; ?>" data-name="<?= esc($item['name'])?>" data-api-service-id="<?= esc($item['api_service_id'])?>"  data-provider-name="<?= esc($item['api_name'])?>">
                        <td class="sort-handler w-1p"><i class="fe fe-grid"></i></td>
                        <th class="text-center w-1p"><?php echo $item_checkbox; ?></th>
                        <td class="text-center w-5p text-muted"><?=show_high_light(esc($item['id']), $params['search'], 'id');?></td>
                        <td>
                          <div class="title"><?php echo show_high_light(esc($item['name']), $params['search'], 'name'); ?></div>
                        </td>
                        <?php if (staff_has_permission($controller_name, 'see_provider')) : ?>
                          <td class="text-center w-10p  text-muted">
                            <?php
                              $provider_type_name = ($item['add_type'] == "api") ?  render_service_attr_html(truncate_string($item['api_name'], 13), $auto_status, 'Sync status with provider') : 'manual';
                              echo $provider_type_name;
                            ?>
                            <div class="text-muted small">
                              <?=(!empty($item['api_service_id'])) ? show_high_light(esc($item['api_service_id']), $params['search'], 'api_service_id') : ""?>
                            </div>
                          </td>
                        <?php endif;?>
                        <td class="text-center w-10p">
                          <?php 
                            echo $item['type'];
                            echo $show_item_attr;
                          ?>
                        </td>
                        <td class="text-center w-5p">
                          <div><?= render_service_attr_html((double)$item['price'], $sync_rate, 'Sync rate with provider'); ?> </div>
                          <?php 
                            if (isset($item['original_price']) && staff_has_permission($controller_name, 'see_provider')) {
                              $text_color = ($item['original_price'] > $item['price']) ? "text-danger" : "text-muted";
                              echo '<small class="'.$text_color.'">'.(double)$item['original_price'].'</small>';
                            }
                          ?>
                        </td>
                        <td class="text-center w-10p text-muted"><?= render_service_attr_html($item['min'], $sync_min, 'Sync min with provider')?></td>
                        <td class="text-center w-10p text-muted"><?=render_service_attr_html($item['max'], $sync_max, 'Sync max with provider')?></td>
                        <td class="text-center w-1p"> <?php echo $show_item_view;?></td>
                        <td class="text-center w-1p"><?php echo $item_status; ?></td>
                        <td class="text-center w-5p"><?php echo $show_item_buttons; ?></td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?> 
                </tbody>
              </table>
            </div>
          </div>
      <?php endforeach ?>
    </div>
  <?php else : $is_empty_item = true; endif?>
  <?php echo show_empty_item(); ?>

</div>

<script>
  $(document).ready(function() {
    var is_empty_item = '<?= $is_empty_item ?>';
    if (!is_empty_item) {
      $('.data-empty').addClass('d-none');
    } else {
      $('.data-empty').addClass('d-none');
    }
  })
</script>

