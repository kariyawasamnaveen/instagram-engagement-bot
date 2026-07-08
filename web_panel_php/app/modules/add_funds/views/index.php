<!-- RECHARGE CENTER OVERHAUL -->
<div class="row justify-content-center m-t-20">
    <div class="col-md-10 col-xl-8">
        <!-- Balance Card -->
        <div class="recharge-balance-card">
            <h4><?=lang('Total_Balance')?></h4>
            <div class="balance-amount"><?=get_option('currency_symbol', '$')?><?=number_format((double)get_user_balance(), 2)?></div>
        </div>

        <?php if ($active_payments): ?>
        <div class="card p-4">
            <div class="card-header pb-4 pt-0 px-0 border-bottom-0">
                <h4 class="card-title text-white mb-0"><?=lang('method')?></h4>
            </div>
            <div class="card-body p-0">
                <!-- Payment Methods Grid -->
                <div class="payment-methods-grid nav nav-pills" role="tablist">
                    <?php
                        $i = 0;
                        foreach ($active_payments as $key => $row):
                            if ($row):
                                $i++;
                                $method_icon = BASE . "assets/images/payments/".strtolower($row['type']).".png";
                                // Fallback icon logic if needed, but we'll trust the images or use text
                    ?>
                    <div class="payment-method-item nav-item <?=($i == 1) ? 'active' : ''?>" data-toggle="pill" href="#<?=$row['type']?>">
                        <span><?=esc($row['name'])?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>

                <!-- Payment Forms Container -->
                <div class="tab-content mt-4">
                    <?php
                        $i = 0;
                        foreach ($active_payments as $key => $row):
                            $i++;
                    ?>
                    <div id="<?=$row['type']?>" class="tab-pane fade <?=($i == 1) ? 'show active' : ''?>">
                        <div class="add-funds-form-content">
                            <?php
                                $this->load->view($row['type'].'/index', ['payment_id' => $row['id'], 'payment_params' => $row['params']]);
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
  .page-title h1{
    margin-bottom: 5px; }
    .page-title .border-line {
      height: 5px;
      width: 270px;
      background: #eca28d;
      background: -webkit-linear-gradient(45deg, #eca28d, #f98c6b) !important;
      background: -moz- oldlinear-gradient(45deg, #eca28d, #f98c6b) !important;
      background: -o-linear-gradient(45deg, #eca28d, #f98c6b) !important;
      background: linear-gradient(45deg, #eca28d, #f98c6b) !important;
      position: relative;
      border-radius: 30px; }
    .page-title .border-line::before {
      content: '';
      position: absolute;
      left: 0;
      top: -2.7px;
      height: 10px;
      width: 10px;
      border-radius: 50%;
      background: #fa6d7e;
      -webkit-animation-duration: 6s;
      animation-duration: 6s;
      -webkit-animation-timing-function: linear;
      animation-timing-function: linear;
      -webkit-animation-iteration-count: infinite;
      animation-iteration-count: infinite;
      -webkit-animation-name: moveIcon;
      animation-name: moveIcon; }

  @-webkit-keyframes moveIcon {
    from {
      -webkit-transform: translateX(0);
    }
    to { 
      -webkit-transform: translateX(215px);
    }
  }
</style>
<?php if (get_option("is_active_manual")): ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-10 col-xl-8">
        <div class="card p-4">
            <div class="card-header pb-4 pt-0 px-0 border-bottom-0">
                <h4 class="card-title text-white mb-0"><?=lang('manual_payment')?></h4>
            </div>
            <div class="card-body p-0 text-dim" style="font-size: 14px; line-height: 1.6;">
                <?=htmlspecialchars_decode(get_option('manual_payment_content', ''), ENT_QUOTES)?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


