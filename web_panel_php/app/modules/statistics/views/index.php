<style>
  @import url('https://fonts.googleapis.com/css2?family=Macondo&display=swap');
  
  :root {
    --premium-gold: #D4AF37;
    --premium-gold-bright: #F9E2AF;
    --premium-dark: #0B0B0C;
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(212, 175, 55, 0.2);
  }

  /* Force edge-to-edge native app feel on mobile */
  /* Force true edge-to-edge full-bleed native app feel on mobile */
  @media (max-width: 768px) {
    #statistics-area {
      width: 100vw !important;
      max-width: 100vw !important;
      position: relative !important;
      left: 50% !important;
      right: 50% !important;
      margin-left: -50vw !important;
      margin-right: -50vw !important;
      padding-left: 10px !important;
      padding-right: 10px !important;
    }
    .col-12 { padding-left: 0 !important; padding-right: 0 !important; }
    .card { padding-left: 0 !important; padding-right: 0 !important; }
    body { overflow-x: hidden !important; }
  }

  body { background: var(--premium-dark) !important; }
  .bg-premium-dark { background: var(--premium-dark) !important; }
  .text-gold { color: var(--premium-gold) !important; }
  
  .premium-icon-box {
    width: 45px; height: 45px;
    background: rgba(212, 175, 55, 0.1);
    border: 1px solid var(--premium-gold);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: var(--premium-gold); font-size: 20px;
  }

  .btn-premium-glow {
    background: linear-gradient(45deg, var(--premium-gold), #B8860B) !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(184, 134, 11, 0.3) !important;
    font-weight: 700 !important; text-transform: uppercase !important;
    letter-spacing: 1px !important; transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    color: #121212 !important; border-radius: 14px !important;
  }
  .btn-premium-glow:hover {
    transform: translateY(-2px); box-shadow: 0 6px 20px rgba(184, 134, 11, 0.5) !important;
    color: #000 !important;
  }

  /* Instant Niche Filter Pills */
  .niche-filter-pills {
    display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-bottom: 25px;
  }
  .niche-pill {
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7); padding: 8px 16px; border-radius: 30px;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
    cursor: pointer; transition: all 0.3s ease;
  }
  .niche-pill.active, .niche-pill:hover {
    background: var(--premium-gold); color: #0B0B0C; border-color: var(--premium-gold);
    box-shadow: 0 4px 15px rgba(212,175,55,0.4); transform: translateY(-2px);
  }

  /* Elite Bottom Navigation Bar */
  .elite-bottom-nav {
    position: fixed; bottom: 0; left: 0; width: 100%;
    background: rgba(11, 11, 12, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid rgba(212, 175, 55, 0.2); display: flex; justify-content: space-around;
    padding: 12px 0 20px; z-index: 1000;
  }
  .elite-nav-item {
    text-align: center; color: rgba(255,255,255,0.5); text-decoration: none !important;
    font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    transition: all 0.3s ease;
  }
  .elite-nav-item i { display: block; font-size: 20px; margin-bottom: 4px; transition: all 0.3s ease; }
  .elite-nav-item.active, .elite-nav-item:hover { color: var(--premium-gold); }
  .elite-nav-item.active i, .elite-nav-item:hover i {
    transform: translateY(-2px); text-shadow: 0 0 10px rgba(212,175,55,0.5);
  }
</style>

<?php
$sections = [
  [
    'id' => 'header_area',
    'url' => cn($controller_name . '/load_header_area'),
  ],
  [
    'id' => 'chart_and_orders_area',
    'url' => cn($controller_name . '/load_chart_and_orders_area'),
    'callback' => 'chartCallback',
  ],
];
?>

<div class="row justify-content-center m-t-10 statistics m-0" id="statistics-area" style="padding-bottom: 80px;">
  <div class="col-12 col-md-10 col-xl-8 px-1">
    <div class="card p-0 py-3 p-md-4" style="background: transparent !important; border: none !important; box-shadow: none !important;">
      <!-- The Royal Header -->
      <div class="card-header pb-4 pt-0 px-0 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div class="d-flex align-items-center">
          <img src="/assets/images/site_logo_gold.png" style="width: 52px; height: 52px; margin-right: 20px; filter: drop-shadow(0 0 15px rgba(212,175,55,0.85)) drop-shadow(0 0 5px rgba(212,175,55,0.4));" alt="Splash Logo">
          <h4 class="card-title mb-0" style="font-family: 'Macondo', cursive; font-weight: 700; font-size: 38px; letter-spacing: 1.5px; color: #F9E2AF; text-shadow: 0 0 18px rgba(212,175,55,0.7), 0 0 8px rgba(212,175,55,0.4);">New View Order</h4>
        </div>
      </div>
      

      <div class="card-body p-0 pt-4">
        <!-- Dynamic Sections Loader (Loads Campaign Accelerator & Kinetic Grid from header_area) -->
        <?php foreach ($sections as $section): ?>
          <div class="col-sm-12 p-0" id="<?= $section['id']; ?>">
            <?= render_component_loader(); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>


<script>
  const sectionCallbacks = {
    chartCallback: function(response) {
      Chart_template.chart_spline('#orders_chart_spline', JSON.parse(response.chart_spline));
      Chart_template.chart_pie('#orders_chart_pie', JSON.parse(response.chart_pie));
    }
  };

  const sections = <?= json_encode($sections); ?>;

  $(document).ready(function() {
    sections.forEach(section => {
      const cb = section.callback ? sectionCallbacks[section.callback] : null;
      loadSection(section.url, '#' + section.id, cb);
    });

    // Niche Filter Pill Toggles E.g. visual micro-interaction
    $('.niche-pill').on('click', function() {
        $('.niche-pill').removeClass('active');
        $(this).addClass('active');
    });
  });
</script>
