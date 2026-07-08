
  <!-- Chart Area VIP Style -->
  <style>
    .vip-chart-card {
        background: var(--glass-bg, rgba(255,255,255,0.03)) !important;
        border: 1px solid var(--glass-border, rgba(212, 175, 55, 0.2)) !important;
        border-radius: 15px;
        box-shadow: none;
    }
    .vip-chart-card .card-header {
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .vip-chart-card .card-title {
        color: var(--premium-gold, #D4AF37) !important;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 14px;
    }
    .vip-stat-box {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        transition: transform 0.2s ease;
    }
    .vip-stat-box:hover {
        border-color: rgba(212, 175, 55, 0.3);
        transform: translateY(-2px);
    }
  </style>



  <!-- Realtime Logs -->
  <?php if ($chart_and_orders_area) : ?>
    <div class="row mt-4">
      <?php foreach ($chart_and_orders_area['orders_statistics'] as $key => $item) : ?>
        <div class="col-6 col-sm-6 col-lg-3 item mb-3">
          <div class="card p-3 vip-stat-box h-100 m-0">
            <div class="d-flex align-items-center flex-column text-center">
              <span class="premium-stamp" style="color: var(--premium-gold); font-size: 24px; margin-bottom: 10px;">
                <i class="<?=$item['icon'];?>"></i>
              </span>
              <div>
                <h4 class="m-0 number text-white" style="font-weight: 700; font-size: 20px;"><?=$item['value'];?></h4>
                <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;"><?=$item['name'];?></small>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
