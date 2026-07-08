<?php 
  $show_search_area = show_search_area($controller_name, $params, 'user');
  
  $ci = &get_instance();
  $uid = session('uid');
  $active_count = 0;
  if ($uid) {
      $active_count = $ci->db->where('uid', $uid)
                            ->where_in('status', ['active', 'processing', 'inprogress', 'pending'])
                            ->count_all_results(ORDER);
  }
?>

<!-- CYBER-LUXURY DESIGN STYLING OVERRIDES -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Macondo&family=Share+Tech+Mono&display=swap');

  :root {
    --velvet-obsidian: #050506;
    --velvet-card-bg: rgba(11, 11, 12, 0.7);
    --liquid-gold: #E5C158;
    --liquid-gold-bright: #FDF5E6;
    --glass-border: rgba(229, 193, 88, 0.15);
    --text-muted: rgba(255, 255, 255, 0.4);
    --text-dim: rgba(255, 255, 255, 0.65);
  }

  body {
    background: var(--velvet-obsidian) !important;
  }

  .lists-index-ajax {
    padding-bottom: 80px;
  }

  @media (max-width: 768px) {
    .lists-index-ajax {
      padding-left: 10px !important;
      padding-right: 10px !important;
    }
  }

  /* Cyber-Luxury Console Header */
  .cyber-luxury-title {
    font-family: 'Macondo', cursive;
    font-size: 34px;
    font-weight: 700;
    color: var(--liquid-gold-bright) !important;
    text-shadow: 0 0 18px rgba(229, 193, 88, 0.6), 0 0 6px rgba(229, 193, 88, 0.3);
    letter-spacing: 1px;
  }

  /* Concentric Active Orbit Telemetry HUD */
  .campaign-telemetry-hud {
    background: radial-gradient(circle at top, rgba(229, 193, 88, 0.08) 0%, rgba(5, 5, 6, 0) 70%), var(--velvet-card-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(35px);
    -webkit-backdrop-filter: blur(35px);
  }

  .telemetry-orbit-wrapper {
    position: relative;
    width: 76px;
    height: 76px;
    margin-right: 20px;
    flex-shrink: 0;
  }

  .orbit-ring-outer {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    border: 1px dashed rgba(229, 193, 88, 0.2);
    border-radius: 50%;
    animation: spin-clockwise 20s linear infinite;
  }

  .orbit-ring-inner {
    position: absolute;
    top: 10px; left: 10px; width: 56px; height: 56px;
    border: 1px solid rgba(229, 193, 88, 0.1);
    border-top-color: var(--liquid-gold);
    border-bottom-color: var(--liquid-gold);
    border-radius: 50%;
    animation: spin-counter-clockwise 8s linear infinite;
  }

  .orbit-core {
    position: absolute;
    top: 24px; left: 24px; width: 28px; height: 28px;
    background: var(--liquid-gold);
    border-radius: 50%;
    box-shadow: 0 0 15px var(--liquid-gold), 0 0 5px rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #050506;
    font-size: 13px;
    font-weight: 900;
    font-family: 'Share Tech Mono', monospace;
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
    0% { transform: scale(0.95); box-shadow: 0 0 10px rgba(229, 193, 88, 0.6); }
    50% { transform: scale(1.05); box-shadow: 0 0 22px rgba(229, 193, 88, 0.9), 0 0 8px rgba(255,255,255,0.7); }
    100% { transform: scale(0.95); box-shadow: 0 0 10px rgba(229, 193, 88, 0.6); }
  }

  /* Velvet Obsidian Filter Pills */
  .order-status-pills.nav-pills {
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 8px !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 25px !important;
    padding-bottom: 8px !important;
  }

  .order-status-pills.nav-pills::-webkit-scrollbar {
    display: none;
  }

  .order-status-pills.nav-pills .nav-link {
    flex: 0 0 auto !important;
    background: rgba(11, 11, 12, 0.8) !important;
    border: 1px solid var(--glass-border) !important;
    color: var(--text-dim) !important;
    border-radius: 30px !important;
    padding: 10px 18px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    white-space: nowrap !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
  }

  .order-status-pills.nav-pills .nav-link:hover,
  .order-status-pills.nav-pills .nav-link.active {
    background: var(--liquid-gold) !important;
    color: #050506 !important;
    border-color: var(--liquid-gold) !important;
    box-shadow: 0 5px 15px rgba(229, 193, 88, 0.35) !important;
    transform: translateY(-2px) scale(1.02) !important;
  }

  .order-status-pills.nav-pills .nav-link:active {
    transform: translateY(0) scale(0.97) !important;
  }

  /* Cyber-Luxury Search Area Overrides */
  .search-area .form-group {
    margin-bottom: 0 !important;
  }
  
  .search-area .input-group {
    background: rgba(11, 11, 12, 0.8) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 30px !important;
    padding: 4px 6px !important;
    align-items: center;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
  }
  
  .search-area .input-group:focus-within {
    border-color: var(--liquid-gold) !important;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.4), 0 0 15px rgba(229, 193, 88, 0.25) !important;
  }
  
  .search-area .form-control {
    background: transparent !important;
    border: none !important;
    color: var(--text-pure) !important;
    font-size: 14px !important;
    padding-left: 15px !important;
    height: 38px !important;
    box-shadow: none !important;
  }
  
  .search-area .form-control::placeholder {
    color: var(--text-muted) !important;
    opacity: 0.8 !important;
  }
  
  .search-area .btn-search {
    background: var(--liquid-gold) !important;
    border: none !important;
    color: #050506 !important;
    width: 38px !important;
    height: 38px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 10px rgba(229, 193, 88, 0.35) !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
  }
  
  .search-area .btn-search:hover {
    background: var(--liquid-gold-bright) !important;
    box-shadow: 0 4px 18px rgba(229, 193, 88, 0.6) !important;
    transform: scale(1.05) !important;
  }
  
  .search-area .btn-search:active {
    transform: scale(0.95) !important;
  }

  .search-area .btn-clear {
    background: transparent !important;
    border: none !important;
    color: var(--text-muted) !important;
    padding: 0 8px !important;
    margin-right: 4px;
    font-size: 14px !important;
    transition: all 0.2s ease !important;
  }
  
  .search-area .btn-clear:hover {
    color: #ff4a5a !important;
  }
</style>

<div class="lists-index-ajax">
  <?php include(APPPATH . 'modules/common_block/views/ajax_index_overplay.php'); ?>

  <!-- Cyber-Luxury Title Section -->
  <div class="page-title m-b-20" style="padding: 10px 4px 0 4px; margin-bottom: 25px;">
    <div class="row align-items-center">
      <div class="col-12 d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <h3 class="cyber-luxury-title mb-0">
          Campaign Console
        </h3>
        <div class="search-area">
          <?php echo $show_search_area; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Concentric Orbit Telemetry HUD -->
  <div class="campaign-telemetry-hud d-flex align-items-center">
    <div class="telemetry-orbit-wrapper">
      <div class="orbit-ring-outer"></div>
      <div class="orbit-ring-inner"></div>
      <div class="orbit-core"><?= $active_count ?></div>
    </div>
    <div>
      <h5 class="text-white mb-1" style="font-weight: 700; letter-spacing: 0.5px;">Active Campaigns Telemetry</h5>
      <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.5;">Currently executing automated AI fleets via secure internal instagram pilot networks.</p>
    </div>
  </div>

  <div id="btn-filter-group">
    <!-- Rendered by AJAX load -->
  </div>
 
  <?php 
    // Use full module path so CI looks in common_block/views/, not the calling controller's views/
    $this->load->view('common_block/card_blade'); 
  ?>
</div>
