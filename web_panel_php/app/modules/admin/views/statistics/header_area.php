<?php
    if ($header_area) {
  ?>
    <div class="row">
      <?php
        foreach ($header_area as $key => $item) {
      ?>
        <div class="col-sm-6 col-lg-3 item">
          <div class="card p-3">
            <div class="d-flex align-items-center">
              <span class="stamp stamp-md <?=$item['class'];?> text-white mr-3">
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
  <?php
    }
  ?>