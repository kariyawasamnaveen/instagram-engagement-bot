<div class="col-md-12 col-xl-12">
  <?php
    $class_favorite = 'fa-heart';
    $class_unfavorite = 'fa-heart-o';
    $title_favorite = 'Remove from favorites';
    $title_unfavorite = 'Add to favorites';
    foreach ($items as $key => $item_category) {
  ?>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?php echo $key; ?></h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
          <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="<?= get_table_class(); ?>">
          <?php 
            echo render_table_thead($columns, false, false, false); 
          ?>
          <tbody>
            <?php if (!empty($item_category)) {
              foreach ($item_category as $key => $item) {
                $show_item_view     = show_item_details('services', $item);
                $item_price         = (double) $item['price'];
            ?>
              <tr class="tr_<?php echo esc($item['id']); ?> service-item" data-cate-id="<?= esc($item['cate_id']) ?>" data-id="<?= esc($item['id'])?>" data-name="<?= esc($item['name'])?>">
                <?php if (session('uid')) :?>
                  <td class="text-center w-1p">
                    <i class="favorite-icon fs-16 fa <?= $item['favorite'] ? $class_favorite : $class_unfavorite ?>" 
                      data-service-id="<?= $item['id'] ?>" 
                      data-is-favorite="<?= $item['favorite'] ?>"
                      data-toggle="tooltip"
                      data-placement="top" 
                      title="" 
                      data-original-title="<?= $item['favorite'] ? $title_favorite : $title_unfavorite ?>" 
                      aria-hidden="true"
                      style="cursor: pointer;">
                    </i>
                  </td>
                <?php endif; ?>
                <td class="text-center w-10p">
                  <?=esc($item['id']);?>
                </td>
                <td>
                  <div class="title"><?=esc($item['name']);?></div>
                </td>
                <td class="text-center w-10p"><div><?=$item_price ;?></div></td>
                <td class="text-center w-10p text-muted"><?=esc($item['min']) ?></td>
                <td class="text-center w-10p text-muted"><?=esc($item['max'])?></td>
                <?php if ((get_option("enable_average_time", 0) == 1)) : ?>
                  <td class="text-center w-15p">
                    <div>
                      <?=format_avg_time($item['avg_time']) ;?>
                    </div>
                  </td>
                <?php endif; ?>
                <td class="text-center w-5p"> <?php echo $show_item_view;?></td>
                <?php if (session('uid')) :?>
                <td class="text-center w-5p"> 
                  <a class="btn btn-outline-secondary btn-sm" 
                    href="<?=cn('new_order?service='. $item['id'])?>" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="Order now">
                    <span class="fe fe-shopping-cart"></span>
                  </a>
                </td>
                <?php endif; ?>
              </tr>
            <?php }}?>
          </tbody>
        </table>
      </div>
    </div>
  <?php } ?>
</div>
<script>
  $(document).ready(function() {
    $(document).on("click", ".favorite-icon", function () {
      var element = $(this);
      var serviceId = element.data('service-id');
      var is_favorite = parseInt(element.data('is-favorite'));
      var class_favorite = '<?= esc($class_favorite); ?>';
      var class_unfavorite = '<?= esc($class_unfavorite); ?>';
      var title_favorite = '<?= esc($title_favorite); ?>';
      var title_unfavorite = '<?= esc($title_unfavorite); ?>';
      $.ajax({
          url: PATH + '/services/switch_favorite',
          type: 'POST',
          data: { service_id: serviceId, is_favorite: is_favorite, token: token},
          success: function(response) {
            if (is_favorite) {
              element.removeClass(class_favorite)
                .addClass(class_unfavorite)
                .data('is-favorite', 0)
                .attr('data-original-title', title_unfavorite).tooltip('hide').tooltip('fixTitle');
            } else {  
              element.removeClass(class_unfavorite)
                .addClass(class_favorite)
                .data('is-favorite', 1)
                .attr('data-original-title', title_favorite).tooltip('hide').tooltip('fixTitle');
            }
          },
          error: function() {
            console.log('Opp! Please try again!');
            ;
          }
      });
    });
  });
</script>