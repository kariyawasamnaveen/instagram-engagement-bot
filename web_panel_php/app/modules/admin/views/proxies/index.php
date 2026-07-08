<?php 
  // Page header
  $is_add_new_btn = 'add-new';
  echo show_page_header($controller_name, ['page-options' => $is_add_new_btn, 'page-options-type' => 'ajax-modal']);
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Proxies List</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
          <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter table-stripped card-table">
          <thead>
            <tr>
              <th class="text-center w-1">No.</th>
              <?php foreach ($columns as $key => $column) : ?>
                <th class="<?= $column['class'] ?>"><?= $column['name'] ?></th>
              <?php endforeach; ?>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($items)) : ?>
              <?php $i = 0; foreach ($items as $key => $item) : $i++; ?>
                <tr class="tr_<?= $item['ids'] ?>">
                  <td class="text-center text-muted"><?= $i ?></td>
                  <td><?= $item['proxy'] ?></td>
                  <td class="text-center"><?= $item['type'] ?></td>
                  <td class="text-center">
                    <?php if ($item['status'] == 1) : ?>
                      <span class="badge badge-info">Active</span>
                    <?php else : ?>
                      <span class="badge badge-warning">Deactive</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <div class="item-action dropdown">
                      <a href="javascript:void(0)" data-toggle="dropdown" class="icon"><i class="fe fe-more-vertical"></i></a>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= admin_url($controller_name . '/update/' . $item['ids']) ?>" class="dropdown-item ajaxModal"><i class="dropdown-icon fe fe-edit"></i> Edit</a>
                        <a href="<?= admin_url($controller_name . '/delete/' . $item['ids']) ?>" class="dropdown-item ajaxDeleteItem"><i class="dropdown-icon fe fe-trash-2"></i> Delete</a>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="5" class="text-center">No results found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
