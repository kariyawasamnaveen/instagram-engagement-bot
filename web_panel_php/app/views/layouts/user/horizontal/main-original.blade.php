<!doctype html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta name="description" content="<?=get_option('website_desc', "SmartPanel - #1 SMM Reseller Panel - Best SMM Panel for Resellers. Also well known for TOP SMM Panel and Cheap SMM Panel for all kind of Social Media Marketing Services. SMM Panel for Facebook, Instagram, YouTube and more services!")?>">
    <meta name="keywords" content="<?=get_option('website_keywords', "smm panel, SmartPanel, smm reseller panel, smm provider panel, reseller panel, instagram panel, resellerpanel, social media reseller panel, smmpanel, panelsmm, smm, panel, socialmedia, instagram reseller panel")?>">
    <title><?=get_option('website_title', "SmartPanel - SMM Panel Reseller Tool")?></title>

    <link rel="shortcut icon" type="image/x-icon" href="<?=get_option('website_favicon', BASE."assets/images/favicon.png")?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="manifest" href="<?=BASE?>manifest.json">
    <meta name="theme-color" content="#D4AF37">
    <link rel="apple-touch-icon" href="<?=BASE?>assets/images/logo.png">

    <link rel="stylesheet" href="<?php echo BASE; ?>assets/plugins/font-awesome/css/font-awesome.min.css">
    
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery-3.2.1.min.js"></script>

    <!-- flag icon -->
    <?php if (segment('1') == 'language') {
    ?>
    <link href="<?php echo BASE; ?>assets/plugins/flags/css/flag-icon.css" rel="stylesheet">
    <?php }?>
    <!-- Core -->
    <link href="<?php echo BASE; ?>assets/css/core.css?v=2.0" rel="stylesheet">
      
    <!-- c3.js Charts Plugin -->
    <?php if(segment('1') == 'statistics'){ ?>
    <link href="<?php echo BASE; ?>assets/plugins/charts-c3/c3.css" rel="stylesheet">
    <script src="<?php echo BASE; ?>assets/plugins/charts-c3/d3.v3.min.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/charts-c3/c3.min.js"></script>
    <?php }?>
    <!-- toast -->
    
    <link rel="stylesheet" type="text/css" href="<?php echo BASE; ?>assets/plugins/jquery-toast/css/jquery.toast.css">

    <link rel="stylesheet" href="<?php echo BASE; ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" id="theme-stylesheet">
    <link rel="stylesheet" href="<?php echo BASE; ?>assets/plugins/boostrap-datetimepicket/bootstrap-datetimepicker.min.css" id="theme-stylesheet">
    
    <link href="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/css/emoji.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>assets/css/util.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>assets/css/footer.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>assets/css/layout.css" rel="stylesheet">
    <link href="<?php echo BASE; ?>themes/nico/assets/css/app-dashboard.css?v=3.2" rel="stylesheet">
    <style>
      /* OBSIDIAN GOLD BRUTE FORCE OVERRIDE */
      :root {
          --accent-blue: #D4AF37 !important;
          --bg-obsidian: #050505 !important;
          --bg-card: rgba(20, 20, 20, 0.9) !important;
          --premium-gold: #D4AF37 !important;
      }
      body.app-dashboard-active { background-color: #050505 !important; }
      .app-user-header .balance-badge {
          background: #D4AF37 !important;
          color: #000000 !important;
          box-shadow: 0 4px 15px rgba(212, 175, 55, 0.5) !important;
      }
      .app-dashboard-active .btn-primary.btn-block {
          background: linear-gradient(45deg, #B8860B, #D4AF37) !important;
          color: #000000 !important;
          box-shadow: 0 10px 20px -5px rgba(212, 175, 55, 0.4) !important;
      }
      .app-nav-item.active { color: #D4AF37 !important; }
      .app-nav-item .active-dot { background: #D4AF37 !important; box-shadow: 0 0 10px #D4AF37 !important; }
      .total_charge { border-color: rgba(212, 175, 55, 0.3) !important; background: rgba(212, 175, 55, 0.05) !important; }
      .charge_number { color: #D4AF37 !important; }
      .premium-upsell-card { background: linear-gradient(135deg, #B8860B, #D4AF37) !important; }
    </style>
    <script type="text/javascript">
      var token = '<?php echo $this->security->get_csrf_hash(); ?>',
          PATH  = '<?php echo PATH; ?>',
          BASE  = '<?php echo BASE; ?>';
    </script>
    <?=htmlspecialchars_decode(get_option('embed_head_javascript', ''), ENT_QUOTES)?>
  </head>
  <?php
    $theme_name = get_option('default_header_skin', 'default');
    if ($theme_name == "") {
      $theme_name = 'default';
    }
  ?>
  <body class="theme-<?php echo $theme_name; ?> app-dashboard-active">

    <!-- Start page_overplay -->
    <?php
      include_once(APPPATH . 'views/layouts/common/page_overplay.php');
    ?>
    <!-- Start Header Vertical -->

    <div class="page">
      <div class="page-main">
        <!-- header -->
        <?php 
          include_once 'blocks/header.php';
        ?>
        <div class="my-3 my-md-5 premium-fade-in-up">
          <div class="container">
            <!-- MODERN APP HEADER -->
            <?php
                $balance = current_logged_user()->balance;
                $balance_display = get_option('currency_symbol',"$") . (empty($balance) ? "0.00" : currency_format($balance));
                $first_name = current_logged_user()->first_name;
            ?>
            <div class="app-user-header animate-item delay-1">
                <div class="greeting">
                    <p class="text-dim mb-0" style="font-size: 13px; font-weight: 500;">Welcome back,</p>
                    <h2 class="text-white"><?= $first_name ?>!</h2>
                </div>
                <div class="balance-badge" onclick="window.location.href='<?=cn('add_funds')?>'">
                    <?= $balance_display ?>
                </div>
            </div>
            <div class="d-md-none">
              <?php
                if ( allowed_search_bar(segment(1)) || allowed_search_bar(segment(2)) ) {
                  echo Modules::run("blocks/search_box");
                }
              ?>
            </div>
            <main class="app-content">  
              <?=$template['body']?>
            </main>
          </div>
        </div>
        
      </div>
      <!-- modal -->
      <div id="modal-ajax" class="modal fade" tabindex="-1"></div>
      <div id="modal-ajax-notification" class="modal fade" tabindex="-1"></div>
    </div>
    <?php 
      include_once 'blocks/footer.php';
    ?>
    
    <script src="<?php echo BASE; ?>assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery.sparkline.min.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/selectize.min.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery.tablesorter.min.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery-jvectormap-2.0.3.min.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery-jvectormap-de-merc.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/jquery-jvectormap-world-mill.js"></script>
    <script src="<?php echo BASE; ?>assets/js/vendors/circle-progress.min.js"></script>

    <script src="<?php echo BASE; ?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    
    <!-- Datetime picker -->
    <script src="<?php echo BASE; ?>assets/plugins/boostrap-datetimepicket/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo BASE; ?>assets/plugins/boostrap-datetimepicket/bootstrap-datetimepicker.min.js"></script>

    <script src="<?php echo BASE; ?>assets/js/core.js"></script>
    <!-- toast -->
    <script type="text/javascript" src="<?php echo BASE; ?>assets/plugins/jquery-toast/js/jquery.toast.js"></script>

    <!-- emoji picker -->
    <script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/config.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/util.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/jquery.emojiarea.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/emoji-picker.js"></script>
    <!-- flags icon -->
    <script src="<?php echo BASE; ?>assets/plugins/flags/js/docs.js"></script>

    <?php if(segment('1') == 'statistics'){ ?>
    <script src="<?php echo BASE; ?>assets/js/chart_template.js"></script>
    <?php }?>
    
    <!-- general JS -->
    <script src="<?php echo BASE; ?>assets/js/process.js"></script>
    <script src="<?php echo BASE; ?>assets/js/general.js"></script>

    <?php if (segment(1) == 'new_order') : ?>
      <script type="text/javascript" src="<?=BASE ?>/assets/js/client.js"></script>
    <?php endif; ?>
    
    <?=htmlspecialchars_decode(get_option('embed_javascript', ''), ENT_QUOTES)?>

    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelectorAll('.lists-index-ajax').length > 0) {
          loadTableData(window.location.href);
        }
      });

      // PWA Logic for Horizontal Layout
      let deferredPrompt;
      const pwaItem = document.getElementById('pwa-install-item');
      
      if (pwaItem) {
          const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
          const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

          window.addEventListener('beforeinstallprompt', (e) => {
              e.preventDefault();
              deferredPrompt = e;
              pwaItem.style.display = 'block';
          });

          if (!isIOS && !isStandalone) {
              pwaItem.style.display = 'block';
          }

          if (isIOS && !isStandalone) {
              pwaItem.style.display = 'block';
          }

          pwaItem.addEventListener('click', async () => {
              if (deferredPrompt) {
                  deferredPrompt.prompt();
                  const { outcome } = await deferredPrompt.userChoice;
                  deferredPrompt = null;
                  pwaItem.style.display = 'none';
              } else if (isIOS) {
                  alert("To install, tap the Share icon and then 'Add to Home Screen'.");
              } else {
                  alert("Please check your browser menu and select 'Install App' or 'Add to Home screen'.");
              }
          });
      }
    </script>
    
    <!-- PREMIUM BOTTOM NAVIGATION -->
    <div class="app-bottom-nav">
        <a href="<?=cn('statistics')?>" class="app-nav-item <?=(segment(1)=='statistics')?'active':''?>">
            <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            <span>Home</span>
            <span class="active-dot"></span>
        </a>
        <a href="<?=cn('new_order')?>" class="app-nav-item <?=(segment(1)=='new_order')?'active':''?>">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
            <span>New Order</span>
            <span class="active-dot"></span>
        </a>
        <a href="<?=cn('order')?>" class="app-nav-item <?=(segment(1)=='order' && segment(2)!='add')?'active':''?>">
            <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            <span>Orders</span>
            <span class="active-dot"></span>
        </a>
        <a href="<?=cn('tickets')?>" class="app-nav-item <?=(segment(1)=='tickets')?'active':''?>">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
            <span>Support</span>
            <span class="active-dot"></span>
        </a>
        <a href="<?=cn('profile')?>" class="app-nav-item <?=(segment(1)=='profile')?'active':''?>">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Account</span>
            <span class="active-dot"></span>
        </a>
    </div>

      <canvas id="confetti-canvas" class="confetti-canvas"></canvas>

    <script>
    // LIGHTWEIGHT CONFETTI ENGINE (Performance Optimized for 2GB RAM)
    function shootConfetti() {
        const canvas = document.getElementById('confetti-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let pieces = [];
        const colors = ['#D4AF37', '#F9E272', '#B8860B', '#FFFFFF'];

        for (let i = 0; i < 50; i++) {
            pieces.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                rotation: Math.random() * 360,
                color: colors[Math.floor(Math.random() * colors.length)],
                size: Math.random() * 10 + 5,
                speed: Math.random() * 5 + 2
            });
        }

        function update() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            pieces.forEach(p => {
                p.y += p.speed;
                p.rotation += p.speed;
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rotation * Math.PI / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.size/2, -p.size/2, p.size, p.size);
                ctx.restore();
            });
            pieces = pieces.filter(p => p.y < canvas.height);
            if (pieces.length > 0) requestAnimationFrame(update);
        }
        update();
    }

    // Listen for SMM Panel AJAX Success (actionForm)
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (settings.url.indexOf('ajax_add_order') !== -1) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                shootConfetti();
            }
        }
    });
    </script>
</body>
</html>
