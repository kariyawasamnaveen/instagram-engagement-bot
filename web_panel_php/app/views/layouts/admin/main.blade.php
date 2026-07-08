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
    <!-- Start Header Vertical -->
    <?php 
      include_once 'blocks/header_vertical.php';
    ?>
    <div class="d-flex flex-row h-100p">
      <?php include 'blocks/sidebar.php'; ?>
      <div class="layout-main d-flex flex-column flex-fill max-w-full">
        <main class="app-content">
          <?php echo $template['body']; ?>
        </main>
      </div>
    </div>
    <!-- modal -->
    <div id="modal-ajax" class="modal fade" tabindex="-1"></div>
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
