<style>
  @import url('https://fonts.googleapis.com/css2?family=Macondo&family=Share+Tech+Mono&display=swap');

  :root {
    --velvet-obsidian: #050506;
    --velvet-card-bg: rgba(11, 11, 12, 0.85);
    --liquid-gold: #E5C158;
    --liquid-gold-bright: #FDF5E6;
    --glass-border: rgba(229, 193, 88, 0.15);
    --text-muted: rgba(255, 255, 255, 0.4);
    --text-dim: rgba(255, 255, 255, 0.65);
    --neon-green: #39FF14;
    --neon-green-glow: rgba(57, 255, 20, 0.4);
  }

  .membership-container {
    padding: 30px 10px 80px 10px;
    min-height: 80vh;
    font-family: 'Inter', -apple-system, sans-serif;
  }

  .membership-card {
    background: var(--velvet-card-bg) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 24px !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6) !important;
    overflow: hidden;
    backdrop-filter: blur(35px);
    -webkit-backdrop-filter: blur(35px);
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
  }

  .membership-header {
    background: radial-gradient(circle at top, rgba(229, 193, 88, 0.07) 0%, rgba(5, 5, 6, 0) 75%);
    padding: 45px 20px;
    text-align: center;
    border-bottom: 1px solid var(--glass-border);
    position: relative;
  }

  /* Concentric Orbit Portal License Telemetry */
  .license-orbit-wrapper {
    position: relative;
    width: 90px;
    height: 90px;
    margin: 0 auto 20px auto;
  }

  .orbit-ring-outer {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    border: 1.5px dashed rgba(229, 193, 88, 0.25);
    border-radius: 50%;
    animation: spin-clockwise 20s linear infinite;
  }

  .orbit-ring-inner {
    position: absolute;
    top: 12px; left: 12px; width: 66px; height: 66px;
    border: 1.5px solid rgba(229, 193, 88, 0.15);
    border-top-color: var(--liquid-gold);
    border-bottom-color: var(--liquid-gold);
    border-radius: 50%;
    animation: spin-counter-clockwise 8s linear infinite;
  }

  .orbit-core {
    position: absolute;
    top: 27px; left: 27px; width: 36px; height: 36px;
    background: var(--liquid-gold);
    border-radius: 50%;
    box-shadow: 0 0 20px var(--liquid-gold), 0 0 8px rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #050506;
    font-size: 16px;
    animation: pulse-beacon 2s ease-in-out infinite;
  }

  @keyframes spin-clockwise {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @keyframes spin-counter-clockwise {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
  }

  @keyframes pulse-beacon {
    0% { transform: scale(0.95); box-shadow: 0 0 15px rgba(229, 193, 88, 0.6); }
    50% { transform: scale(1.05); box-shadow: 0 0 25px rgba(229, 193, 88, 0.9), 0 0 10px rgba(255, 255, 255, 0.8); }
    100% { transform: scale(0.95); box-shadow: 0 0 15px rgba(229, 193, 88, 0.6); }
  }

  .title-gold {
    font-family: 'Macondo', cursive;
    font-size: 28px;
    font-weight: 700;
    color: var(--liquid-gold-bright) !important;
    text-shadow: 0 0 15px rgba(229, 193, 88, 0.5);
    letter-spacing: 1.5px;
  }

  .status-badge-active {
    display: inline-flex;
    align-items: center;
    background: rgba(57, 255, 20, 0.08);
    color: var(--neon-green);
    border: 1px solid rgba(57, 255, 20, 0.25);
    padding: 6px 18px;
    border-radius: 30px;
    font-family: 'Share Tech Mono', monospace;
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 1px;
    box-shadow: 0 0 15px var(--neon-green-glow);
  }

  .pro-expiry {
    font-size: 12px;
    color: var(--text-dim);
    line-height: 1.6;
  }

  .countdown-container {
    font-family: 'Share Tech Mono', monospace;
    font-size: 13px;
    color: var(--liquid-gold);
    background: rgba(229, 193, 88, 0.05);
    border: 1px solid rgba(229, 193, 88, 0.1);
    padding: 4px 12px;
    border-radius: 8px;
    display: inline-block;
    margin-top: 8px;
  }

  .price-gold {
    font-family: 'Share Tech Mono', monospace;
    color: var(--liquid-gold-bright);
    font-size: 36px;
    font-weight: 700;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
  }

  .price-gold span {
    font-size: 14px;
    font-weight: 400;
    color: var(--text-muted);
  }

  .benefit-item {
    padding: 16px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    display: flex;
    align-items: center;
  }

  .benefit-icon {
    width: 36px;
    height: 36px;
    background: rgba(229, 193, 88, 0.08);
    border: 1px solid rgba(229, 193, 88, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 18px;
    color: var(--liquid-gold);
    flex-shrink: 0;
    box-shadow: 0 0 8px rgba(229, 193, 88, 0.1);
  }

  .benefit-text-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--liquid-gold-bright);
    margin-bottom: 2px;
  }

  .benefit-text-desc {
    font-size: 11px;
    color: var(--text-dim);
    line-height: 1.4;
  }

  .btn-upgrade-gold {
    background: var(--liquid-gold) !important;
    border: none !important;
    color: #050506 !important;
    font-weight: 900 !important;
    font-size: 13px !important;
    padding: 15px 30px !important;
    border-radius: 30px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    box-shadow: 0 5px 15px rgba(229, 193, 88, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    animation: button-pulse 2s infinite;
  }

  .btn-upgrade-gold:hover {
    background: var(--liquid-gold-bright) !important;
    color: #050506 !important;
    box-shadow: 0 8px 25px rgba(229, 193, 88, 0.6) !important;
    transform: translateY(-2px) scale(1.01) !important;
  }

  .btn-upgrade-gold:active {
    transform: translateY(0) scale(0.98) !important;
  }

  @keyframes button-pulse {
    0% { box-shadow: 0 5px 15px rgba(229, 193, 88, 0.3); }
    50% { box-shadow: 0 5px 25px rgba(229, 193, 88, 0.6); }
    100% { box-shadow: 0 5px 15px rgba(229, 193, 88, 0.3); }
  }

  .btn-active-status {
    border: 1px solid rgba(229, 193, 88, 0.2) !important;
    background: rgba(229, 193, 88, 0.04) !important;
    color: var(--liquid-gold) !important;
    border-radius: 30px !important;
    font-weight: 700 !important;
    padding: 15px 30px !important;
    font-size: 13px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    opacity: 0.8;
  }
</style>

<div class="membership-container row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="membership-card">
      <div class="membership-header">
        
        <!-- Concentric Active Orbit Telemetry HUD -->
        <div class="license-orbit-wrapper">
          <div class="orbit-ring-outer"></div>
          <div class="orbit-ring-inner"></div>
          <div class="orbit-core"><i class="fe fe-shield"></i></div>
        </div>

        <h2 class="title-gold mb-1">PORTAL LICENSE</h2>
        <p class="text-muted mb-0" style="font-size: 12px; font-weight: 500;">Campaign Console Authorization Gateway</p>
        
        <?php if ($user['membership_status']): ?>
          <div class="mt-4">
            <span class="status-badge-active"><i class="fe fe-check-circle mr-1"></i> ACTIVE LICENSE</span>
            <p class="pro-expiry mt-3 mb-0">Elite routing clearance is active until:<br>
              <span class="text-white font-weight-bold" style="font-size: 13px;"><?= date('M d, Y - H:i', strtotime($user['membership_expiry'])) ?></span>
            </p>
            <div class="countdown-container">
              <i class="fe fe-clock mr-1"></i> <span id="portal-countdown">Calculating...</span>
            </div>
          </div>
        <?php else: ?>
          <div class="mt-4">
            <h1 class="price-gold mb-0">$59.99<span style="font-size: 15px;">/month</span></h1>
          </div>
        <?php endif; ?>
      </div>

      <div class="card-body p-4 p-md-5">
        <h5 class="text-white mb-4" style="font-weight: 700; font-size: 14px; letter-spacing: 0.5px;">SYSTEM PRIVILEGES</h5>
        
        <div class="benefit-item">
          <div class="benefit-icon"><i class="fe fe-key"></i></div>
          <div>
            <div class="benefit-text-title">Portal Access Authorization</div>
            <div class="benefit-text-desc">Mandatory system-wide gateway required to launch, schedule, and execute automated AI campaigns.</div>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon"><i class="fe fe-percent"></i></div>
          <div>
            <div class="benefit-text-title">100% Free Campaign Automation</div>
            <div class="benefit-text-desc">Place unlimited automated campaigns for a whole month without any deductions from your main balance.</div>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon"><i class="fe fe-zap"></i></div>
          <div>
            <div class="benefit-text-title">High-Delivery Server Routing</div>
            <div class="benefit-text-desc">Your automation packets are routed through high-priority backend pilot network channels for maximized execution speeds.</div>
          </div>
        </div>

        <div class="benefit-item border-bottom-0 pb-0">
          <div class="benefit-icon"><i class="fe fe-headphones"></i></div>
          <div>
            <div class="benefit-text-title">Dedicated Telemetry Support</div>
            <div class="benefit-text-desc">Support tickets are instantly marked for fast-track queue escalation and routed directly to core administrators.</div>
          </div>
        </div>

        <?php if (!$user['membership_status']): ?>
          <div class="mt-5 text-center">
            <button class="btn btn-upgrade-gold btn-block ajaxUpgradeMembership" data-action="<?= cn('membership/upgrade') ?>">
              ACTIVATE PORTAL LICENSE NOW
            </button>
            <p class="mt-3 text-muted small" style="font-size: 10px;">License auto-renews every 30 days using platform funds. Deactivate at any time.</p>
          </div>
        <?php else: ?>
          <div class="mt-5 text-center">
            <button class="btn btn-active-status btn-block" disabled>
              PORTAL LICENSE ACTIVE
            </button>
            <p class="mt-3 text-muted small" style="font-size: 10px;">Your automation console is fully authorized. Thank you for your subscription.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // AJAX Upgrade Handler
  $(document).on('click', '.ajaxUpgradeMembership', function(e) {
    e.preventDefault();
    const action = $(this).data('action');
    if (confirm('Authorize $59.99 monthly platform fee to activate your automated Campaign Console Portal License?')) {
        pageOverlay.show();
        $.post(action, {token: token}, function(res) {
            pageOverlay.hide();
            if (res.status == 'success') {
                notify(res.message, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                notify(res.message, 'error');
            }
        }, 'json');
    }
  });

  // Dynamic Countdown Timer
  <?php if ($user['membership_status']): ?>
  function startPortalCountdown() {
      const expiryTime = new Date("<?= date('Y-m-d H:i:s', strtotime($user['membership_expiry'])) ?>").getTime();
      
      const interval = setInterval(function() {
          const now = new Date().getTime();
          const difference = expiryTime - now;
          
          if (difference <= 0) {
              clearInterval(interval);
              document.getElementById("portal-countdown").innerHTML = "LICENSE EXPIRED";
              setTimeout(() => location.reload(), 3000);
              return;
          }
          
          const days = Math.floor(difference / (1000 * 60 * 60 * 24));
          const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
          const seconds = Math.floor((difference % (1000 * 60)) / 1000);
          
          document.getElementById("portal-countdown").innerHTML = 
              days + "D " + hours + "H " + minutes + "M " + seconds + "S REMAINING";
      }, 1000);
  }
  
  if (document.getElementById("portal-countdown")) {
      startPortalCountdown();
  }
  <?php endif; ?>
</script>
