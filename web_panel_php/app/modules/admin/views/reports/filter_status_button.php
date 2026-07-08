<ul class="list-inline mb-0 order_btn_group">
  <?php 
    foreach ($report_filters as $key => $item_field) {
      $class_item = ($task == $key) ? 'btn-primary' : '';
      echo sprintf('<li class="list-inline-item"><a class="btn %s" href="?type=%s">%s</a>', $class_item, $key, $item_field['name']);
    }
  ?>
</ul>