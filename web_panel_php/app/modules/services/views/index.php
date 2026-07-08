<?php
  $items_category = array_column($items_category, 'id', 'name');
  $items_category = array_flip(array_intersect_key($items_category, array_flip(array_keys($items))));
?>
<section class="page-title">
  <div class="row justify-content-between">
    <div class="col-md-6">
      <h1 class="page-title">
        <i class="fe fe-list" aria-hidden="true"> </i> 
        <?=lang("Services")?>
      </h1>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <select  name="status" class="form-control search-by-category">
          <option value="0"> <?=lang("all")?></option>
          <?php 
            if (!empty($items_category)) {
              foreach ($items_category as $key => $category) {
          ?>
          <option value="<?=$key?>"><?=$category?></option>
          <?php }}?>
        </select>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <input type="text" name="query" class="form-control " id="service-search" placeholder="<?= lang("Search_for_") ?>" value="">
      </div>          
    </div>
  </div>
</section>
<div class="row m-t-5" id="result_ajaxSearch">
  <?php 
    if(!empty($items)){
      $data = array(
        "controller_name"     => $controller_name,
        "params"              => $params,
        "columns"             => $columns,
        "items"               => $items,
      );
      $this->load->view('child/index', $data);
    } else {
      echo show_empty_item();
    }
  ?>
</div>

<script>
  $(document).ready(function() {
    function filterServices() {
      const keyword = $('#service-search').val().trim().toLowerCase();
      const selectedCate = $('.search-by-category').val();
      $('.card').each(function() {
        let anyVisible = false;

        $(this).find('tbody tr.service-item').each(function() {
          const name = $(this).data('name').toLowerCase();
          const id = String($(this).data('id')).toLowerCase();
          const cateId = String($(this).data('cate-id'));

          const matchKeyword = name.includes(keyword) || id.includes(keyword);
          const matchCategory = selectedCate === "0" || cateId === selectedCate;
          if (matchKeyword && matchCategory) {
            $(this).show();
            anyVisible = true;
          } else {
            $(this).hide();
          }
        });
        $(this).toggle(anyVisible);
      });
    }
    $('#service-search').on('input', filterServices);
    $('.search-by-category').on('change', filterServices);
  });

</script>