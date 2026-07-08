<!-- CYBER-LUXURY MONOLITH CAMPAIGN CARD STYLE OVERRIDES -->
<style>
  .order-activity-item {
    background: radial-gradient(135deg, rgba(255, 255, 255, 0.02) 0%, rgba(255, 255, 255, 0) 100%), var(--velvet-card-bg) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 20px !important;
    padding: 22px !important;
    margin-bottom: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
    backdrop-filter: blur(25px) !important;
    -webkit-backdrop-filter: blur(25px) !important;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative;
    overflow: hidden;
  }

  .order-activity-item::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 1px;
    background: linear-gradient(90deg, rgba(229,193,88,0) 0%, rgba(229,193,88,0.25) 50%, rgba(229,193,88,0) 100%);
  }

  .order-activity-item:hover {
    border-color: rgba(229, 193, 88, 0.35) !important;
    box-shadow: 0 15px 40px rgba(229, 193, 88, 0.08), 0 12px 35px rgba(0, 0, 0, 0.6) !important;
    transform: translateY(-4px);
  }

  .order-id-pill {
    background: rgba(229, 193, 88, 0.1) !important;
    border: 1px solid var(--glass-border) !important;
    color: var(--liquid-gold-bright) !important;
    font-family: 'Share Tech Mono', monospace !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    letter-spacing: 0.5px;
  }

  .order-date {
    font-family: 'Share Tech Mono', monospace !important;
    font-size: 11px !important;
    color: var(--text-muted) !important;
  }

  .order-service-name {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #fff !important;
    letter-spacing: 0.5px !important;
  }

  /* Glowing Luxury Badges */
  .badge-completed {
    background: rgba(40, 167, 69, 0.1) !important;
    border: 1px solid #28a745 !important;
    color: #28a745 !important;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.25) !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .badge-processing {
    background: rgba(229, 193, 88, 0.1) !important;
    border: 1px solid var(--liquid-gold) !important;
    color: var(--liquid-gold) !important;
    box-shadow: 0 0 10px rgba(229, 193, 88, 0.25) !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .badge-pending {
    background: rgba(255, 193, 7, 0.1) !important;
    border: 1px solid #ffc107 !important;
    color: #ffc107 !important;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.25) !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .badge-danger {
    background: rgba(220, 53, 69, 0.1) !important;
    border: 1px solid #dc3545 !important;
    color: #dc3545 !important;
    box-shadow: 0 0 10px rgba(220, 53, 69, 0.25) !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  /* Recessed Liquid Progress Tubes */
  .order-progress-container .progress {
    height: 8px !important;
    background: rgba(0, 0, 0, 0.6) !important;
    border-radius: 10px !important;
    border: 1px solid rgba(255,255,255,0.03) !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.8) !important;
    overflow: hidden !important;
  }

  .order-progress-container .progress-bar {
    background: linear-gradient(90deg, #B8860B, var(--liquid-gold)) !important;
    box-shadow: 0 0 10px var(--liquid-gold), 0 0 4px rgba(255,255,255,0.6) !important;
    border-radius: 10px !important;
    height: 100% !important;
  }

  .order-pill {
    background: rgba(229, 193, 88, 0.06) !important;
    border: 1px solid rgba(229, 193, 88, 0.15) !important;
    color: var(--liquid-gold) !important;
    font-size: 9px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    padding: 5px 12px !important;
    border-radius: 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    font-weight: 700 !important;
    margin-right: 8px !important;
    margin-bottom: 8px !important;
  }

  .metric-label {
    font-size: 9px !important;
    color: var(--text-muted) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    font-weight: 600;
  }

  .metric-value {
    font-family: 'Share Tech Mono', monospace !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #FFF !important;
    margin-top: 3px !important;
  }

  .metric-value-charge {
    font-family: 'Share Tech Mono', monospace !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    color: var(--liquid-gold) !important;
    margin-top: 3px !important;
  }

  /* Ultra-Premium Action Trigger Buttons */
  .order-activity-item .d-flex.justify-content-end .btn,
  .order-activity-item .d-flex.justify-content-end a {
    background: transparent !important;
    border: 1px solid var(--liquid-gold) !important;
    color: var(--liquid-gold) !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    border-radius: 10px !important;
    padding: 8px 16px !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .order-activity-item .d-flex.justify-content-end .btn:hover,
  .order-activity-item .d-flex.justify-content-end a:hover {
    background: var(--liquid-gold) !important;
    color: #050506 !important;
    box-shadow: 0 4px 15px rgba(229, 193, 88, 0.4) !important;
    transform: translateY(-1px);
  }
</style>

<?php if (!empty($items)) : $i = $from; ?>
    <?php
        foreach ($items as $key => $item) :
            $i++;
            $params['search']['field'] = 'id';
            $item_id = show_high_light(esc($item['id']), $params['search'], 'id');    
            $item_service_name = esc($item['service_name']);
            
            // Format link
            $link = esc($item['link']);
            if (filter_var($item['link'], FILTER_VALIDATE_URL)) {
                $link = '<a href="' . esc($item['link']) . '" target="_blank" style="color: var(--liquid-gold); text-decoration: none; word-break: break-all; font-weight: 600; font-family: \'Share Tech Mono\', monospace;"><i class="fe fe-link" style="margin-right: 4px;"></i>' . truncate_string(esc($item['link']), 45) . '</a>';
            }
            
            // Format Status
            $item_status = (in_array($item['status'], [ORDER_STATUS_FAIL, ORDER_STATUS_ERROR, ORDER_STATUS_AWAITING])) ? 'pending' : $item['status'];
            $status_class = 'badge-pending';
            $status_name = ucfirst($item_status);
            switch(strtolower($item_status)) {
                case 'completed':
                    $status_class = 'badge-completed';
                    break;
                case 'processing':
                case 'inprogress':
                    $status_class = 'badge-processing';
                    break;
                case 'pending':
                    $status_class = 'badge-pending';
                    break;
                case 'fail':
                case 'failed':
                case 'error':
                case 'canceled':
                    $status_class = 'badge-danger';
                    break;
            }
            
            // Progress Bar Computation
            $quantity = (int)$item['quantity'];
            $remains = (int)$item['remains'];
            $completed = max(0, $quantity - $remains);
            $percent = ($quantity > 0) ? round(($completed / $quantity) * 100) : 0;
            $percent = min(100, max(0, $percent));
            if (strtolower($item_status) == 'completed') {
                $percent = 100;
                $remains = 0;
                $completed = $quantity;
            }
            
            // Attribute pills
            $pills = [];
            if (!empty($item['delivery_speed'])) {
                $pills[] = '<span class="order-pill"><i class="fe fe-zap" style="color: var(--liquid-gold); margin-right: 4px; font-size: 10px;"></i>' . ucfirst(str_replace('_', ' ', $item['delivery_speed'])) . '</span>';
            }
            if (!empty($item['custom_tags'])) {
                $pills[] = '<span class="order-pill"><i class="fe fe-users" style="color: var(--liquid-gold); margin-right: 4px; font-size: 10px;"></i>' . ucfirst($item['custom_tags']) . '</span>';
            }
            
            // Action buttons
            $btn_refill = '';
            if (is_table_exists(ORDERS_REFILL)) {
                $btn_refill = show_item_refill_button($controller_name, $item);
            }
            $btn_cancel = '';
            if (is_table_exists(ORDERS_CANCEL)) {
                $btn_cancel = show_item_cancel_button($controller_name, $item);
            }
    ?>
        <div class="order-activity-item tr_<?php echo esc($item['ids']); ?>">
            <!-- Card Header -->
            <div class="order-item-header d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <span class="order-id-pill mr-2">#<?= $item_id ?></span>
                    <span class="order-date"><?= convert_timezone($item['created'], "user") ?></span>
                </div>
                <span class="badge <?= $status_class ?>"><?= lang($status_name) ?></span>
            </div>
            
            <!-- Service Name -->
            <div class="order-service-name mt-2 mb-2">
                <?= $item_service_name ?>
            </div>
            
            <!-- Target Link -->
            <div class="mt-2 mb-3" style="font-size: 13px;">
                <?= $link ?>
            </div>
            
            <!-- Progress Bar -->
            <div class="order-progress-container mt-3 mb-3">
                <div class="d-flex justify-content-between mb-2" style="font-size: 11px; color: var(--text-dim);">
                    <span>Campaign Telemetry Progress</span>
                    <span class="tactical-mono" style="color: var(--liquid-gold);"><?= $completed ?> / <?= $quantity ?> (<?= $percent ?>%)</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            
            <!-- Detail Pills -->
            <?php if (!empty($pills)) : ?>
                <div class="order-details-pills mt-3 mb-3">
                    <?php foreach ($pills as $pill) echo $pill; ?>
                </div>
            <?php endif; ?>
            
            <!-- Metrics Row -->
            <div class="row text-center mt-3 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                <div class="col-4" style="border-right: 1px solid rgba(255, 255, 255, 0.03);">
                    <div class="metric-label">Target Actions</div>
                    <div class="metric-value"><?= number_format($quantity) ?></div>
                </div>
                <div class="col-4" style="border-right: 1px solid rgba(255, 255, 255, 0.03);">
                    <div class="metric-label">Remains</div>
                    <div class="metric-value"><?= number_format($remains) ?></div>
                </div>
                <div class="col-4">
                    <div class="metric-label">Campaign Cost</div>
                    <div class="metric-value-charge"><?= get_option("currency_symbol", "$") . number_format($item['charge'], 2) ?></div>
                </div>
            </div>
            
            <!-- Action Triggers -->
            <?php if ($btn_refill || $btn_cancel) : ?>
                <div class="d-flex justify-content-end mt-3 pt-3" style="gap: 8px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
                    <?= $btn_refill ?>
                    <?= $btn_cancel ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="card p-5 text-center mt-3" style="background: rgba(11,11,12,0.7); border: 1px dashed rgba(229,193,88,0.25); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="font-size: 32px; color: var(--liquid-gold); margin-bottom: 12px;"><i class="fe fe-folder"></i></div>
        <h5 class="text-white mb-1" style="font-weight: 700;">No Campaigns Placed Yet</h5>
        <p class="text-dim mb-0" style="font-size: 13px;">When you launch a campaign, it will appear here in real-time.</p>
    </div>
<?php endif; ?>