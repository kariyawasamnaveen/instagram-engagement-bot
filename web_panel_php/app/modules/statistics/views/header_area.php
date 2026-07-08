  <?php
    if ($header_area) :
  ?>
      <!-- PREMIUM QUICK ACTIONS GRID (Mobile Only) -->
      <div class="quick-actions-grid mb-4 d-md-none">
          <div class="row row-xs">
              <div class="col-3 text-center">
                  <a href="<?=cn('new_order')?>" class="action-item">
                      <div class="icon-box bg-gold-glow"><i class="fe fe-plus"></i></div>
                      <span>New Order</span>
                  </a>
              </div>
              <div class="col-3 text-center">
                  <a href="<?=cn('add_funds')?>" class="action-item">
                      <div class="icon-box bg-gold-glow"><i class="fe fe-credit-card"></i></div>
                      <span>Deposit</span>
                  </a>
              </div>
              <div class="col-3 text-center">
                  <a href="<?=cn('tickets')?>" class="action-item">
                      <div class="icon-box bg-gold-glow"><i class="fe fe-help-circle"></i></div>
                      <span>Support</span>
                  </a>
              </div>
              <div class="col-3 text-center">
                  <a href="<?=cn('subscriptions')?>" class="action-item">
                      <div class="icon-box bg-gold-glow"><i class="fe fe-zap"></i></div>
                      <span>VIP Sub Logs</span>
                  </a>
              </div>
          </div>
      </div>

      <!-- PREMIUM METRIC CAROUSEL -->
      <div class="premium-carousel-wrapper">
          <div class="row flex-nowrap overflow-auto hide-scrollbar premium-metrics-row">
            <?php
              foreach ($header_area as $key => $item) {
            ?>
              <div class="col-9 col-sm-6 col-md-3 item">
                <div class="card p-3 premium-metric-card">
                  <div class="d-flex align-items-center">
                    <?php
                      $stamp_color = 'premium-stamp-blue';
                      if (strpos($item['class'], 'bg-warning') !== false) $stamp_color = 'premium-stamp-gold';
                      if (strpos($item['class'], 'bg-success') !== false) $stamp_color = 'premium-stamp-green';
                    ?>
                    <span class="premium-stamp <?=$stamp_color;?> mr-3">
                      <i class="<?=$item['icon'];?>"></i>
                    </span>
                    <div class="d-flex order-lg-2 ml-auto">
                      <div class="ml-2 d-lg-block text-right">
                        <h4 class="m-0 text-right number"><?=$item['value'];?></h4>
                        <small class="text-muted "><?=$item['name'];?></small>
                      </div>
                    </div>
                  </div>
                  <!-- Sparkline Placeholder (CSS Only) -->
                  <div class="mini-sparkline mt-2"></div>
                </div>
              </div>
            <?php } ?>
          </div>
      </div>

  <?php endif;?>