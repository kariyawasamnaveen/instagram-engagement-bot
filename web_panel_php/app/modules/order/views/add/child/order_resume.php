<!-- PROFESSIONAL ORDER RESUME -->
<div class="col-md-5" id="order_resume">
    <div class="card p-4 h-100">
        <div class="card-header pb-4 pt-0 px-0 border-bottom-0">
            <div class="d-flex align-items-start mb-3">
                <div class="badge bg-soft-primary text-primary px-3 py-2 mr-3" style="border-radius: 8px; font-weight: 700;">
                    ID: <span class="service-id-val">687</span>
                </div>
                <h5 class="service-name text-white mb-0" style="font-size: 16px; line-height: 1.4; font-weight: 600;">[𝐒𝐮𝐩𝐞𝐫 𝐂𝐡𝐞𝐚𝐩] Twitter Followers [5K] [5K/D - R30] [Instant - No Drop]</h5>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="row g-3 mb-4">
                <div class="col-12 mb-3">
                    <div class="p-3 bg-dark-dim" style="border-radius: 12px; border: 1px solid var(--border-dim);">
                        <p class="text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;"><?= lang('rate_per_1000') ?></p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="text-white mb-0" style="font-weight: 700;"><?=get_option('currency_symbol', '$')?><span class="service-price">3.88</span></h4>
                            <?php if (current_logged_user()->membership_status != 1 || current_logged_user()->membership_level != 'pro') : ?>
                                <span class="badge" id="pro-price-badge" style="background: rgba(255, 215, 0, 0.1); color: #FFD700; border: 1px solid rgba(255, 215, 0, 0.3); font-size: 10px; display: none;">
                                    PRO: <?=get_option('currency_symbol', '$')?><span class="pro-price-val">0.00</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="p-3 bg-dark-dim" style="border-radius: 12px; border: 1px solid var(--border-dim);">
                        <p class="text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;"><?= lang('min') ?></p>
                        <p class="text-white mb-0 service-min-val" style="font-weight: 600;">1,000</p>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 bg-dark-dim" style="border-radius: 12px; border: 1px solid var(--border-dim);">
                        <p class="text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;"><?= lang('max') ?></p>
                        <p class="text-white mb-0 service-max-val" style="font-weight: 600;">1,000,000</p>
                    </div>
                </div>

                <?php if ((get_option("enable_average_time", 0) == 1)) : ?>
                <div class="col-12 mt-3">
                    <div class="d-flex align-items-center text-muted">
                        <svg class="mr-2" style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        <span style="font-size: 12px; font-weight: 500;"><?= lang('Average_time') ?>: <span class="service-avg-time text-white ml-1">-:-</span></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="description mt-4">
                <p class="text-muted mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;"><?=lang("Description")?></p>
                <div class="service-details p-3 bg-dark-dim text-dim" style="border-radius: 12px; border: 1px solid var(--border-dim); font-size: 13px; min-height: 150px; line-height: 1.6;">
                    Select a service to view details.
                </div>
            </div>
        </div>
    </div>
</div>