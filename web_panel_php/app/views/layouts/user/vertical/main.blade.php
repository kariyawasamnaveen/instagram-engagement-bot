<!doctype html>
<html lang="en" dir="ltr">
  <head>
    <?php 
      include 'elements/head.blade.php';
    ?>
  </head>
  <body class="antialiased vertical-menu">

    <!-- Start page_overplay -->
    <?php
      include_once(APPPATH . 'views/layouts/common/page_overplay.php');
    ?>
    <!-- Start header_vertical -->
    <?php 
      include_once 'blocks/header_vertical.php';
    ?>
    <div class="d-flex flex-row h-100p">
      <?php include 'blocks/sidebar.php'; ?>
      <div class="layout-main d-flex flex-column flex-fill max-w-full">
        <main class="app-content">
          <div class="<?php (segment(1) != 'statistics' ? 'container-xl' : '')?>">
            <?php echo $template['body']; ?>
          </div>
        </main>
      </div>
    </div>

    <div id="modal-ajax" class="modal fade" tabindex="-1"></div>
    
    <!-- iOS PWA Install Guide Modal -->
    <div id="ios-install-guide-modal">
        <div class="ios-modal-content">
            <span class="close-ios-modal">&times;</span>
            <h3 style="color: #D4AF37; margin-bottom: 20px; font-size: 18px;">Install Application</h3>
            <p style="color: #AEAEB2; font-size: 15px; margin-bottom: 12px; line-height: 1.6;">1. Tap the <strong>Share</strong> button <span style="display: inline-block; width: 24px; text-align: center; margin: 0 4px;"><i class="fa fa-share-square-o"></i></span> at the bottom of Safari.</p>
            <p style="color: #AEAEB2; font-size: 15px; margin-bottom: 12px; line-height: 1.6;">2. Scroll down and select <strong>'Add to Home Screen'</strong> <span style="display: inline-block; width: 24px; text-align: center; margin: 0 4px;"><i class="fa fa-plus-square-o"></i></span>.</p>
            <p style="color: #AEAEB2; font-size: 15px; margin-bottom: 12px; line-height: 1.6;">3. Tap <strong>'Add'</strong> in the top right corner.</p>
        </div>
    </div>

    <!-- Theme Settings -->
    <?php
      include 'blocks/theme_settings.php';
    ?>
    <!-- Scripts -->
    <?php 
      include 'elements/script.blade.php';
    ?>
  </body>
</html>
