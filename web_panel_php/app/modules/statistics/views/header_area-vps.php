<?php if ($header_area) : ?>

<style>
  .kinetic-card:hover {
    transform: translateY(-3px); border-color: var(--premium-gold) !important;
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25) !important; background: rgba(255,255,255,0.05) !important;
  }
  .premium-segmented-control {
    display: flex; background: rgba(255,255,255,0.05); border-radius: 12px;
    padding: 5px; border: 1px solid rgba(255,255,255,0.1);
  }
  .premium-segmented-control input[type="radio"] { display: none; }
  .premium-segmented-control label {
    flex: 1; text-align: center; padding: 10px 5px; margin: 0; cursor: pointer;
    border-radius: 8px; color: rgba(255,255,255,0.6); font-weight: 600; font-size: 13px; transition: all 0.3s ease;
  }
  .premium-segmented-control input[type="radio"]:checked + label {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.05));
    color: var(--premium-gold); border: 1px solid rgba(212, 175, 55, 0.5); box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  }
  .premium-input {
    background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.1) !important;
    color: white !important; border-radius: 12px !important; padding: 12px 15px !important; transition: all 0.3s ease;
  }
  .premium-input:focus {
    border-color: var(--premium-gold) !important; box-shadow: 0 0 10px rgba(212, 175, 55, 0.2) !important;
    background: rgba(255,255,255,0.08) !important;
  }
  .vip-credit-card {
    background: linear-gradient(135deg, #1f1f1f, #0a0a0a); border: 1px solid var(--glass-border);
    border-radius: 16px; padding: 18px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    aspect-ratio: 1.586 / 1; display: flex; flex-direction: column; justify-content: space-between;
  }
  .vip-credit-card::before {
    content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 60%); opacity: 0.5; pointer-events: none;
  }
  .chip-icon {
    width: 40px; height: 30px; background: linear-gradient(135deg, #FFD700, #D4AF37); border-radius: 6px; opacity: 0.8;
  }
  .vip-card-text { text-transform: uppercase; letter-spacing: 2px; color: var(--premium-gold); font-size: 10px; font-weight: 700; }
  .vip-balance { font-size: 34px; font-weight: 800; color: #fff; margin: 0; letter-spacing: 1px; text-shadow: 0 2px 10px rgba(255,255,255,0.1); line-height: 1; }
  .vip-footer-metric { text-align: center; border-right: 1px solid rgba(255,255,255,0.05); }
  .vip-footer-metric:last-child { border-right: none; }
  .vip-footer-value { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
  .vip-footer-label { font-size: 9px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
  
  /* Bottom Sheet Modal Styles - FIXED */
  .modal-dialog-bottom {
    position: fixed !important;
    bottom: 0 !important;
    margin: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    pointer-events: auto !important;
  }
  .glass-morphism-modal {
    background: #0f0f0f !important;
    background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)) !important;
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(212, 175, 55, 0.3); 
    border-bottom: none;
    border-radius: 30px 30px 0 0 !important; 
    box-shadow: 0 -10px 40px rgba(0,0,0,0.8);
  }
  .modal.fade .modal-dialog-bottom {
    transition: transform 0.3s ease-out;
    transform: translateY(100%);
  }
  .modal.show .modal-dialog-bottom {
    transform: translateY(0);
  }
  /* Force modal to cover bottom navigation bar */
  .modal { z-index: 99999 !important; }
  .modal-backdrop { z-index: 99998 !important; }
  
  .drag-handle { width: 40px; height: 5px; background: rgba(255,255,255,0.2); border-radius: 10px; margin: 10px auto; }
  .modal-icon-wrapper {
    width: 80px; height: 80px; background: rgba(212, 175, 55, 0.15); border: 2px solid var(--premium-gold);
    border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 35px;
    box-shadow: 0 0 25px rgba(212, 175, 55, 0.4); margin: -40px auto 20px auto;
  }
  .text-gold { color: var(--premium-gold) !important; }
</style>

<!-- STRICT VIP CREDIT CARD UI (PRESERVED FOR SMARTPANEL STATISTICS LOGIC) -->
<div class="row justify-content-center mb-4 m-0">
  <div class="col-12 p-0">
    <div class="vip-credit-card mx-auto" style="max-width: 400px;">
      <div class="d-flex justify-content-between align-items-center">
        <div class="chip-icon"></div>
        <div class="vip-card-text">VIP ELITE MEMBER</div>
      </div>
      
      <div>
        <div class="vip-card-text" style="color: rgba(255,255,255,0.5); font-size: 10px; margin-bottom: 2px;">AVAILABLE BALANCE</div>
        <?php 
            $balance = "0.00"; $orders = "0"; $spent = "0.00"; $tickets = "0";
            $ai_stats = isset($header_area['ai_stats']) ? $header_area['ai_stats'] : [
                'followers_today' => 0, 'likes_today' => 0,
                'active_likes' => 0, 'active_follows' => 0, 'active_comments' => 0, 'active_viral' => 0
            ];
            foreach($header_area as $key => $item) {
               if ($key === 'ai_stats') continue;
               $name = strtolower($item['name']);
               if (strpos($name, 'balance') !== false) $balance = $item['value'];
               else if (strpos($name, 'order') !== false) $orders = $item['value'];
               else if (strpos($name, 'spent') !== false) $spent = $item['value'];
               else if (strpos($name, 'ticket') !== false) $tickets = $item['value'];
            }
            
            function format_ai_number($num) {
                if ($num == 0) return '0';
                if ($num >= 1000) return '+' . round($num / 1000, 1) . 'k';
                return '+' . $num;
            }
        ?>
        <div class="vip-balance"><?= $balance ?></div>
      </div>
      
      <div class="row pt-2" style="border-top: 1px solid rgba(255,255,255,0.05);">
         <div class="col-4 vip-footer-metric">
            <div class="vip-footer-value"><?= $orders ?></div>
            <div class="vip-footer-label">Orders</div>
         </div>
         <div class="col-4 vip-footer-metric">
            <div class="vip-footer-value"><?= $spent ?></div>
            <div class="vip-footer-label">Spent</div>
         </div>
         <div class="col-4 vip-footer-metric">
            <div class="vip-footer-value"><?= $tickets ?></div>
            <div class="vip-footer-label">Tickets</div>
         </div>
      </div>
    </div>
  </div>
</div>

<!-- AI ENGINE LIVE STATUS & SUMMARY -->
<style>
@keyframes pulse-green {
  0% { box-shadow: 0 0 0 0 rgba(0, 255, 0, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(0, 255, 0, 0); }
  100% { box-shadow: 0 0 0 0 rgba(0, 255, 0, 0); }
}
</style>
<div class="row m-0">
<div class="col-12 p-0">
  <div class="premium-vip-card glass-morphism mb-4 p-3 p-md-4 mx-auto" style="max-width: 400px; border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 20px; background: linear-gradient(145deg, rgba(18,18,18,0.9), rgba(0,0,0,0.95)); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    
    <!-- Live Status -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
      <div class="d-flex align-items-center">
        <div class="mr-3" style="width: 12px; height: 12px; border-radius: 50%; background: #00ff00; animation: pulse-green 2s infinite;"></div>
        <div>
          <div style="font-size: 10px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px;">System Status</div>
          <div style="font-size: 14px; color: #fff; font-weight: 700; letter-spacing: 0.5px;">AI ENGINE ACTIVE</div>
        </div>
      </div>
      <div class="badge p-2" style="background: rgba(212,175,55,0.1); color: var(--premium-gold); border: 1px solid var(--premium-gold); font-size: 9px; letter-spacing: 1px;">OPTIMAL</div>
    </div>

    <!-- Today's Summary -->
    <div class="row text-center align-items-center">
      <div class="col-6" style="border-right: 1px solid rgba(255,255,255,0.05);">
        <div style="font-size: 26px; font-weight: 800; color: #fff; text-shadow: 0 0 15px rgba(255,255,255,0.2); line-height: 1;"><?= format_ai_number($ai_stats['followers_today']) ?></div>
        <div style="font-size: 9px; color: var(--premium-gold); text-transform: uppercase; letter-spacing: 1px; margin-top: 8px;">Followers Today</div>
      </div>
      <div class="col-6">
        <div style="font-size: 26px; font-weight: 800; color: #fff; text-shadow: 0 0 15px rgba(255,255,255,0.2); line-height: 1;"><?= format_ai_number($ai_stats['likes_today']) ?></div>
        <div style="font-size: 9px; color: var(--premium-gold); text-transform: uppercase; letter-spacing: 1px; margin-top: 8px;">Likes Today</div>
      </div>
    </div>
    
  </div>
</div>
</div>

<!-- THE KINETIC AUTOMATION GRID (Thumb-Optimized 2x2 Grid) -->
<div class="kinetic-automation-grid mb-5">
  <h5 class="text-white mb-3 px-2" style="font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 12px; color: var(--premium-gold) !important;"><i class="fe fe-grid mr-2"></i>Autonomous AI Fleet Status</h5>
  
  <div class="row row-xs m-0">
    <!-- Pillar 1: VIP Likes -->
    <div class="col-6 mb-3 px-1">
      <div class="kinetic-card p-3 h-100" data-title="VIP LIKES ENGINE" data-icon="fe-heart" data-desc="Our autonomous AI fleet uses premium private accounts to engage with your posts in real-time. This algorithmic injection signals high engagement to the platform, boosting your content onto the explore page naturally." style="cursor: pointer; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px; transition: all 0.3s ease; backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center mb-2">
          <div class="premium-icon-box mr-2" style="width: 35px; height: 35px; font-size: 16px; background: rgba(212,175,55,0.15); border-color: var(--premium-gold);">
            <i class="fe fe-heart"></i>
          </div>
          <div>
            <h6 class="text-white mb-0" style="font-weight: 700; font-size: 13px;">VIP LIKES</h6>
            <span class="text-success small" style="font-size: 10px; font-weight: 600;">● Active AI Liking</span>
          </div>
        </div>
        <p class="text-white-50 small mb-2" style="font-size: 11px; line-height: 1.3;">Autonomous post liking via curated private account fleet.</p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
          <span class="text-white-50" style="font-size: 10px;">Active Campaigns</span>
          <span class="badge badge-pill" style="background: rgba(212,175,55,0.2); color: var(--premium-gold); font-weight: 700;"><?= $ai_stats['active_likes'] ?> Active</span>
        </div>
      </div>
    </div>

    <!-- Pillar 2: VIP Follows -->
    <div class="col-6 mb-3 px-1">
      <div class="kinetic-card p-3 h-100" data-title="VIP FOLLOWS ENGINE" data-icon="fe-users" data-desc="Real active follower injection targeting your profile URL. We deploy our highest quality tier of AI-managed accounts to follow your page, building a massive, credible audience overnight." style="cursor: pointer; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px; transition: all 0.3s ease; backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center mb-2">
          <div class="premium-icon-box mr-2" style="width: 35px; height: 35px; font-size: 16px; background: rgba(212,175,55,0.15); border-color: var(--premium-gold);">
            <i class="fe fe-users"></i>
          </div>
          <div>
            <h6 class="text-white mb-0" style="font-weight: 700; font-size: 13px;">VIP FOLLOWS</h6>
            <span class="text-success small" style="font-size: 10px; font-weight: 600;">● Active Growth</span>
          </div>
        </div>
        <p class="text-white-50 small mb-2" style="font-size: 11px; line-height: 1.3;">Real active follower injection targeting your profile URL.</p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
          <span class="text-white-50" style="font-size: 10px;">Active Campaigns</span>
          <span class="badge badge-pill" style="background: rgba(212,175,55,0.2); color: var(--premium-gold); font-weight: 700;"><?= $ai_stats['active_follows'] ?> Active</span>
        </div>
      </div>
    </div>

    <!-- Pillar 3: VIP Comments -->
    <div class="col-6 mb-3 px-1">
      <div class="kinetic-card p-3 h-100" data-title="VIP COMMENTS ENGINE" data-icon="fe-message-square" data-desc="Custom AI-generated & niche-specific comment posting. Our bots analyze your content and leave highly relevant, positive comments that encourage real human users to join the conversation." style="cursor: pointer; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px; transition: all 0.3s ease; backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center mb-2">
          <div class="premium-icon-box mr-2" style="width: 35px; height: 35px; font-size: 16px; background: rgba(212,175,55,0.15); border-color: var(--premium-gold);">
            <i class="fe fe-message-square"></i>
          </div>
          <div>
            <h6 class="text-white mb-0" style="font-weight: 700; font-size: 13px;">VIP COMMENTS</h6>
            <span class="text-success small" style="font-size: 10px; font-weight: 600;">● Smart AI Niche</span>
          </div>
        </div>
        <p class="text-white-50 small mb-2" style="font-size: 11px; line-height: 1.3;">Custom AI-generated & niche-specific comment posting.</p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
          <span class="text-white-50" style="font-size: 10px;">Active Campaigns</span>
          <span class="badge badge-pill" style="background: rgba(212,175,55,0.2); color: var(--premium-gold); font-weight: 700;"><?= $ai_stats['active_comments'] ?> Active</span>
        </div>
      </div>
    </div>

    <!-- Pillar 4 & 5: Repost & Share (Viral Multiplier) -->
    <div class="col-6 mb-3 px-1">
      <div class="kinetic-card p-3 h-100" data-title="VIRAL MULTIPLIER" data-icon="fe-share-2" data-desc="The ultimate algorithm hack. Our fleet autonomously executes story reposts, DM shares, and bookmark saves. This triggers the platform's 'virality' sensors, pushing your content to massive new audiences." style="cursor: pointer; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px; transition: all 0.3s ease; backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center mb-2">
          <div class="premium-icon-box mr-2" style="width: 35px; height: 35px; font-size: 16px; background: rgba(212,175,55,0.15); border-color: var(--premium-gold);">
            <i class="fe fe-share-2"></i>
          </div>
          <div>
            <h6 class="text-white mb-0" style="font-weight: 700; font-size: 13px;">VIRAL MULTIPLIER</h6>
            <span class="text-info small" style="font-size: 10px; font-weight: 600;">● Repost & Share</span>
          </div>
        </div>
        <p class="text-white-50 small mb-2" style="font-size: 11px; line-height: 1.3;">Autonomous story reposts, DM shares & bookmark saves.</p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
          <span class="text-white-50" style="font-size: 10px;">Active Campaigns</span>
          <span class="badge badge-pill" style="background: rgba(212,175,55,0.1); color: rgba(255,255,255,0.5); font-weight: 700;"><?= $ai_stats['active_viral'] ?> Active</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- AI INFO BOTTOM SHEET MODAL -->
<div class="modal fade" id="aiInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-bottom" role="document">
    <div class="modal-content glass-morphism-modal">
      <div class="modal-header border-0 pb-0 justify-content-center" style="position: relative;">
        <div class="drag-handle"></div>
        <button type="button" class="close text-white" data-dismiss="modal" style="position: absolute; right: 20px; top: 15px; opacity: 0.5; text-shadow: none;">
          <span aria-hidden="true" style="font-size: 28px;">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center pt-0 pb-5 px-4">
        <div class="modal-icon-wrapper">
          <i id="aiModalIcon" class="fe fe-heart text-gold"></i>
        </div>
        <h3 id="aiModalTitle" class="text-white font-weight-bold mb-3" style="letter-spacing: 1px; font-family: 'Anubis Mythical', sans-serif;">TITLE</h3>
        <p id="aiModalDesc" class="text-white-50 mb-4" style="font-size: 13px; line-height: 1.6; font-weight: 500;"></p>
        <a href="<?=cn('new_order')?>" class="btn btn-block py-3" style="font-weight: 800; letter-spacing: 1px; background: linear-gradient(90deg, #D4AF37, #FFDF73, #D4AF37); color: #000; border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(212,175,55,0.4);">
          <i class="fe fe-zap mr-2"></i> LAUNCH NEW CAMPAIGN
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // FIX: Move modal to body to escape z-index trapping in AJAX loaded content
    if($('#aiInfoModal').length) {
      $('#aiInfoModal').appendTo('body');
    }

    // Use event delegation for dynamically loaded content
    $(document).on('click', '.kinetic-card', function() {
      var title = $(this).data('title');
      var icon = $(this).data('icon');
      var desc = $(this).data('desc');
      
      $('#aiModalTitle').text(title);
      $('#aiModalIcon').attr('class', 'fe ' + icon + ' text-gold');
      $('#aiModalDesc').text(desc);

      // Manually trigger modal to bypass Bootstrap data-api issues
      $('#aiInfoModal').modal('show');
    });
  });
</script>

<?php endif;?>
