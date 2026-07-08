<!-- Chart Area -->
<div class="charts">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Recent orders</h3>
    </div>
    <div class="row">
      <div class="col-sm-8">
        <div class="p-4 card">
          <div id="orders_chart_spline" style="height: 20rem;"></div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="p-4 card">
          <div id="orders_chart_pie" style="height: 20rem;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if ($chart_and_orders_area) :  ?>
  <div class="row">
    <?php
      foreach ($chart_and_orders_area['orders_statistics'] as $key => $item) {
    ?>
      <div class="col-sm-6 col-lg-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md text-primary mr-3">
              <i class="<?=$item['icon'];?>"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?=$item['value'];?></h4>
                <small class="text-muted "><?=$item['name'];?></small>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
<?php endif;  ?>