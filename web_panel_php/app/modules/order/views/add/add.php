<!-- NEW ORDER PAGE OVERHAUL (VIP MASTERPIECE ARCHITECTURE) -->

<style>
  @import url('https://fonts.googleapis.com/css2?family=Macondo&display=swap');
  
  :root {
    --premium-gold: #D4AF37;
    --premium-gold-bright: #F9E2AF;
    --premium-dark: #0B0B0C;
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(212, 175, 55, 0.2);
  }

  body {
    background: var(--premium-dark) !important;
  }

  /* Force edge-to-edge native app feel on mobile with precise 10px spacing matching Home page */
  @media (max-width: 768px) {
    #new-order-area {
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

  .bg-premium-dark { background: var(--premium-dark) !important; }
  .text-gold { color: var(--premium-gold) !important; }
  .border-gold-dim { border: 1px solid var(--glass-border) !important; }
  
  .premium-icon-box {
    width: 45px;
    height: 45px;
    background: rgba(212, 175, 55, 0.1);
    border: 1px solid var(--premium-gold);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--premium-gold);
    font-size: 20px;
  }

  .btn-premium-glow {
    background: linear-gradient(45deg, var(--premium-gold), #B8860B) !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(184, 134, 11, 0.3) !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    color: #121212 !important;
    border-radius: 14px !important;
  }

  .btn-premium-glow:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(184, 134, 11, 0.5) !important;
    color: #000 !important;
  }

  /* Instant Niche Filter Pills */
  .niche-filter-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-bottom: 25px;
  }
  .niche-pill {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .niche-pill.active, .niche-pill:hover {
    background: var(--premium-gold);
    color: #0B0B0C;
    border-color: var(--premium-gold);
    box-shadow: 0 4px 15px rgba(212,175,55,0.4);
    transform: translateY(-2px);
  }

  /* Elite Bottom Navigation Bar */
  .elite-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: rgba(11, 11, 12, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid rgba(212, 175, 55, 0.2);
    display: flex;
    justify-content: space-around;
    padding: 12px 0 20px;
    z-index: 1000;
  }
  .elite-nav-item {
    text-align: center;
    color: rgba(255,255,255,0.5);
    text-decoration: none !important;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
  }
  .elite-nav-item i {
    display: block;
    font-size: 20px;
    margin-bottom: 4px;
    transition: all 0.3s ease;
  }
  .elite-nav-item.active, .elite-nav-item:hover {
    color: var(--premium-gold);
  }
  .elite-nav-item.active i, .elite-nav-item:hover i {
    transform: translateY(-2px);
    text-shadow: 0 0 10px rgba(212,175,55,0.5);
  }
  
  /* Custom Premium Dropdown Options */
  .custom-option, .custom-option-platform {
    padding: 18px 20px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: all 0.2s ease;
  }
  .custom-option:hover, .custom-option-platform:hover:not(.coming-soon-item) {
    background: rgba(212,175,55,0.15);
    color: var(--premium-gold) !important;
    padding-left: 25px !important;
  }
  .custom-select-container.open .custom-select-trigger,
  .custom-select-container-platform.open .custom-select-trigger-platform {
    border-color: var(--premium-gold) !important;
    box-shadow: 0 0 15px rgba(212,175,55,0.2) !important;
  }
  .custom-select-container.open .fe-chevron-down,
  .custom-select-container-platform.open .fe-chevron-down {
    transform: rotate(180deg);
  }

  /* Quantity Input */
  .qty-input-wrap {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(212, 175, 55, 0.25);
    border-radius: 12px;
    display: flex;
    align-items: center;
    overflow: hidden;
    height: 55px;
  }
  .qty-btn {
    width: 52px;
    height: 100%;
    background: rgba(212, 175, 55, 0.08);
    border: none;
    color: var(--premium-gold);
    font-size: 22px;
    font-weight: 300;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s;
  }
  .qty-btn:active { background: rgba(212,175,55,0.2); }
  .qty-input {
    flex: 1;
    background: transparent !important;
    border: none !important;
    color: #fff !important;
    font-size: 20px !important;
    font-weight: 700 !important;
    text-align: center !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  .qty-input:focus { box-shadow: none !important; border: none !important; }
</style>

<div class="row justify-content-center m-t-10 m-0" id="new-order-area" style="padding-bottom: 80px;">
  <div class="col-12 col-md-10 col-xl-8 px-1">
    
    <!-- The Royal Header for New Order -->
    <div class="card-header pb-4 pt-0 px-0 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 25px;">
      <div class="d-flex align-items-center">
        <img src="/assets/images/site_logo_gold.png" style="width: 52px; height: 52px; margin-right: 20px; filter: drop-shadow(0 0 15px rgba(212,175,55,0.85)) drop-shadow(0 0 5px rgba(212,175,55,0.4));" alt="Splash Logo">
        <h4 class="card-title mb-0" style="font-family: 'Macondo', cursive; font-weight: 700; font-size: 38px; letter-spacing: 1.5px; color: #F9E2AF; text-shadow: 0 0 18px rgba(212,175,55,0.7), 0 0 8px rgba(212,175,55,0.4);">Initiate Campaign</h4>
      </div>
    </div>

    <form class="form actionForm" action="<?=cn($controller_name . "/ajax_add_order")?>" data-redirect="<?=cn('new_order')?>" method="POST">
      <input type="hidden" name="agree" value="on">
      <!-- HARDCODED VIP ELITE CATEGORY AND SERVICE -->
      <input type="hidden" name="category_id" value="34">
      <input type="hidden" name="service_id" value="1">
      
      <div class="card p-2" style="background: transparent !important; border: none !important; box-shadow: none !important;">
        
        <div class="card-body p-0 pt-2">
          <!-- Premium Platform Selection Dropdown -->
          <div class="form-group premium-platform-selector mb-4">
            <label style="color: var(--premium-gold); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block;">
              <i class="fe fe-globe mr-2"></i>Select Social Platform
            </label>
            
            <div class="custom-select-container-platform" style="position: relative;">
              <!-- Hidden input to store actual value -->
              <input type="hidden" name="platform" id="selected_platform" value="instagram">
              
              <!-- Visible selected box -->
              <div class="custom-select-trigger-platform" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 55px; display: flex; align-items: center; padding: 0 20px; font-size: 15px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
                <span class="selected-text-platform">INSTAGRAM</span>
                <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 18px; transition: transform 0.3s ease;"></i>
              </div>

              <!-- Dropdown options panel -->
              <div class="custom-select-options-platform" style="position: absolute; top: 100%; left: 0; width: 100%; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 16px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); overflow: hidden; backdrop-filter: blur(20px);">
                <div class="custom-option-platform" data-value="instagram" style="padding: 18px 20px; color: #fff; font-weight: 600; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">INSTAGRAM</div>
                <div class="custom-option-platform coming-soon-item" data-value="facebook" style="padding: 18px 20px; color: rgba(255,255,255,0.4); font-weight: 600; cursor: not-allowed; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
                  <span>FACEBOOK</span>
                  <span class="badge badge-dark" style="background: rgba(255,255,255,0.1); color: var(--premium-gold); font-size: 10px;">Coming Soon</span>
                </div>
                <div class="custom-option-platform coming-soon-item" data-value="tiktok" style="padding: 18px 20px; color: rgba(255,255,255,0.4); font-weight: 600; cursor: not-allowed; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
                  <span>TIKTOK</span>
                  <span class="badge badge-dark" style="background: rgba(255,255,255,0.1); color: var(--premium-gold); font-size: 10px;">Coming Soon</span>
                </div>
                <div class="custom-option-platform coming-soon-item" data-value="youtube" style="padding: 18px 20px; color: rgba(255,255,255,0.4); font-weight: 600; cursor: not-allowed; display: flex; justify-content: space-between; align-items: center;">
                  <span>YOUTUBE</span>
                  <span class="badge badge-dark" style="background: rgba(255,255,255,0.1); color: var(--premium-gold); font-size: 10px;">Coming Soon</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Premium Service Selection Dropdown (CUSTOM NATIVE-LOOK UI) -->
          <div class="form-group premium-service-selector mb-4">
            <label style="color: var(--premium-gold); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block;"><i class="fe fe-target mr-2"></i>Select AI Fleet Service</label>
            
            <div class="custom-select-container" style="position: relative;">
              <!-- Hidden input to store actual value -->
              <input type="hidden" id="vipServiceSelector" value="1">
              
              <!-- Visible selected box -->
              <div class="custom-select-trigger" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 55px; display: flex; align-items: center; padding: 0 20px; font-size: 15px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
                <span class="selected-text">VIP LIKES ENGINE</span>
                <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 18px; transition: transform 0.3s ease;"></i>
              </div>

              <!-- Dropdown options panel -->
              <div class="custom-select-options" style="position: absolute; top: 100%; left: 0; width: 100%; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 16px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); overflow: hidden; backdrop-filter: blur(20px);">
                <div class="custom-option" data-value="1" style="padding: 18px 20px; color: #fff; font-weight: 600; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">VIP LIKES ENGINE</div>
                <div class="custom-option" data-value="2" style="padding: 18px 20px; color: #fff; font-weight: 600; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">VIP FOLLOWS ENGINE</div>
                <div class="custom-option" data-value="3" style="padding: 18px 20px; color: #fff; font-weight: 600; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s ease;">VIP COMMENTS ENGINE</div>
                <div class="custom-option" data-value="4" style="padding: 18px 20px; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">VIRAL MULTIPLIER (Repost & Share)</div>
              </div>
            </div>
          </div>

          <!-- QUANTITY INPUT -->
          <div class="form-group mb-4">
            <label style="color: var(--premium-gold); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block;"><i class="fe fe-layers mr-2"></i>Number of Actions</label>
            <div class="qty-input-wrap">
              <button type="button" class="qty-btn" onclick="adjustQty(-1)">−</button>
              <input type="number" class="qty-input form-control" id="qty_field" name="quantity" value="10" min="1" max="100000" step="1">
              <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 8px; padding: 0 4px;">
              <span style="font-size: 10px; color: rgba(255,255,255,0.3);">Min: 1</span>
              <span style="font-size: 10px; color: rgba(255,255,255,0.3);">Max: 100,000</span>
            </div>
          </div>

          <!-- Success Message -->
          <?php $this->load->view('child/order_message_success'); ?>

          <!-- VIP Elite Package ONLY (Master Hero Canvas & Kinetic Grid) -->
          <div class="order-vip_elite">
            <?php $this->load->view('child/order_vip_elite'); ?>
          </div>


          <!-- Submit Button -->
          <div class="form-actions left mt-4">
            <button type="submit" class="btn btn-spinner-border mr-1 mb-1 btn-block btn-lg btn-premium-glow" style="height: 55px; font-size: 13px; font-weight: 800; letter-spacing: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">
              <i class="fe fe-zap mr-2"></i> LAUNCH AI FLEET
            </button>
          </div>
        </div>
      </div>  
    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
      // Cost calculator logic helper
      function updateCost() {
          var qty = parseInt($('#qty_field').val()) || 1000;
          var rate = 59.00; // rate per 1000 actions
          var cost = ((qty / 1000) * rate).toFixed(2);
          $('.charge_number_floating').text(cost);
          $('.charge_number').text(cost);
      }

      // Cost badge sync
      setTimeout(function() {
          updateCost();
      }, 500);

      // Quantity adjustment function
      window.adjustQty = function(delta) {
          var field = document.getElementById('qty_field');
          var val = parseInt(field.value) || 1000;
          val = Math.max(100, Math.min(100000, val + delta));
          field.value = val;
          updateCost();
      };

      $('#qty_field').on('input change', function() {
          updateCost();
      });

      // Custom Select UI Logic for Services
      $('.custom-select-trigger').on('click', function(e) {
          e.stopPropagation();
          $('.custom-select-container-platform').removeClass('open');
          $('.custom-select-options-platform').fadeOut(200);
          $(this).parent('.custom-select-container').toggleClass('open');
          $(this).siblings('.custom-select-options').fadeToggle(200);
      });

      $('.custom-option').on('click', function(e) {
          e.stopPropagation();
          var value = $(this).data('value');
          var text = $(this).text();
          
          // Update Trigger UI
          $(this).closest('.custom-select-container').find('.selected-text').text(text);
          $(this).closest('.custom-select-container').removeClass('open');
          $(this).closest('.custom-select-options').fadeOut(200);
          // Update Hidden Inputs for Backend
          $('#vipServiceSelector').val(value);
          $('input[name="service_id"]').val(value);
          
          // DYNAMIC FORM SWITCHING LOGIC
          $('.dynamic-form-section').hide();
          $('#form-service-' + value).fadeIn(300);
      });

      // Custom Select UI Logic for Platform
      $('.custom-select-trigger-platform').on('click', function(e) {
          e.stopPropagation();
          $('.custom-select-container').removeClass('open');
          $('.custom-select-options').fadeOut(200);
          $(this).parent('.custom-select-container-platform').toggleClass('open');
          $(this).siblings('.custom-select-options-platform').fadeToggle(200);
      });

      $('.custom-option-platform').on('click', function(e) {
          e.stopPropagation();
          if ($(this).hasClass('coming-soon-item')) {
              return; // Ignore Coming Soon clicks
          }
          var value = $(this).data('value');
          var text = $(this).text();
          
          // Update Trigger UI
          $(this).closest('.custom-select-container-platform').find('.selected-text-platform').text(text);
          $(this).closest('.custom-select-container-platform').removeClass('open');
          $(this).closest('.custom-select-options-platform').fadeOut(200);
          // Update Hidden Input for Platform
          $('#selected_platform').val(value);
      });

      // Close dropdown when clicking anywhere else
      $(document).on('click', function() {
          $('.custom-select-container').removeClass('open');
          $('.custom-select-options').fadeOut(200);
          $('.custom-select-container-platform').removeClass('open');
          $('.custom-select-options-platform').fadeOut(200);
      });
  });
</script>
