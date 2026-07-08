
<style>
  .service-price .percent-input,
  .service-price .rate-input {
    width: 46% !important;
  }

  @media (max-width: 768px) {
    .service-price .percent-input,
    .service-price .rate-input  {
      width: 41% !important;
    }
  }


  .service-price .percent-input .percent-label {
    position: absolute;
    top: -1px;
    left: 14px;
    font-size: 11px;
    z-index: 20;
  }

  .service-price .percent-input .percent-input-form {
    padding-bottom: 0;
    padding-top: 9px;
  }

  .service-price .rate-input-form,
  .service-price .percent-input-form {
    border-radius: 0px;
  }
  .service-price .rate-input {
    position: relative;
  }
  .service-price .rate-input .original-label {
    position: absolute;
    top: 23px;
    left: 14px;
    font-size: 11px;
    z-index: 20;
  }

  /* Provider Min, max order */
  .min-order .provider-min-value-label,
  .max-order .provider-max-value-label {
    position: absolute;
    top: 44px;
    left: 26px;
    font-size: 12px;
    z-index: 20;
  }
</style>

<?php
  $sync_min = 0;
  $sync_max = 0;
  $sync_rate = 0;
  $auto_rate_percent = 0;
  $auto_status = 0;
  $auto_sync_name = 0;
  $auto_sync_desc = 0;
  if (isset($item['sync_options']) && $item['sync_options']) {
    $sync_min = get_value($item['sync_options'], 'sync_min');
    $sync_max = get_value($item['sync_options'], 'sync_max');
    $sync_rate = get_value($item['sync_options'], 'sync_rate');
    $auto_rate_percent = get_value($item['sync_options'], 'auto_rate_percent');
    $auto_status = get_value($item['sync_options'], 'auto_status');
    $auto_sync_name = get_value($item['sync_options'], 'auto_sync_name');
    $auto_sync_desc = get_value($item['sync_options'], 'auto_sync_desc');
  }
?>

<!-- Sync Option -->
<div class="col-md-12 col-sm-6 col-xs-6 service-price">
  <div class="form-group">
    <label>Rate Per 1000</label>
    <div class="input-group"> 
      <span class="percent-input <?php echo ($sync_rate) ? '' : 'd-none'; ?>">
        <div class="percent-label text-info">Percent, %</div>
        <input type="number" name="auto_rate_percent" class="form-control percent-input-form" placeholder="0" value="<?php echo $auto_rate_percent?>" >
      </span>
      <span class="rate-input">
        <input type="text" name="price" class="form-control rate-input-form" value="<?php echo (double)@$item['price']; ?>" <?php echo ($sync_rate) ? 'readonly' : ''; ?>>
        <div class="original-label text-info d-none">
          <span class="value"><?php echo (double)@$item['original_price']; ?></span>
          <?php echo ' ' . get_option('currency_code'); ?>
          <input type="hidden" name="original_price" value="<?php echo @$item['original_price']; ?>">
        </div>
      </span>
      <div class="input-group-prepend sync-rate-check-box d-none">
        <label class="input-group-text custom-switch">
          <input type="hidden" name="sync_rate" value="0">
          <input type="checkbox" name="sync_rate" value="1" class="custom-switch-input provider-sync-rate" <?php echo ($sync_rate) ? 'checked' : ''; ?>>
          <span class="custom-switch-indicator" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sync Rate with provider"></span>
        </label>
      </div>
    </div>
  </div>
</div>

<div class="col-md-6 col-sm-6 col-xs-6">
  <div class="form-group min-order">
    <label>Min Order</label>
    <div class="input-group">
      <input type="number" name="min" class="form-control min-order-input" id="min_price_input" value="<?php echo @$item['min']; ?>" <?php echo ($sync_min) ? 'readonly' : ''; ?>>
      <div class="input-group-prepend sync-min-order-checkbox d-none">
        <label class="input-group-text custom-switch">
          <input type="hidden" name="sync_min" value="0">
          <input type="checkbox" name="sync_min" value="1" class="custom-switch-input provider-sync-min-order" <?php echo ($sync_min) ? 'checked' : ''; ?>>
          <span class="custom-switch-indicator" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sync Min with provider"></span>
        </label>
      </div>
    </div>
    <label for="min" class="provider-min-value-label text-info d-none">20</label>
  </div>
</div>

<div class="col-md-6 col-sm-6 col-xs-6">
  <div class="form-group max-order">
    <label>Max Order</label>
    <div class="input-group">
      <input type="number" name="max" class="form-control max-order-input"  value="<?php echo @$item['max']; ?>" <?php echo ($sync_max) ? 'readonly' : ''; ?>>
      <div class="input-group-prepend sync-max-order-checkbox d-none">
        <label class="input-group-text custom-switch">
          <input type="hidden" name="sync_max" value="0">
          <input type="checkbox" name="sync_max" value="1" class="custom-switch-input provider-sync-max-order" <?php echo ($sync_max) ? 'checked' : ''; ?>>
          <span class="custom-switch-indicator" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sync Max with provider" ></span>
        </label>
      </div>
    </div>
    <label for="min" class="provider-max-value-label text-info d-none">1000</label>
  </div>
</div>

<!-- Service Status -->
<div class="col-md-12 sync_service d-none">
  <div class="form-group ">
    <label class="custom-switch">
      <input type="hidden" name="auto_status" value="0">
      <input type="checkbox" name="auto_status" class="custom-switch-input" value="1"  <?php echo ($auto_status) ? 'checked' : ''; ?>>
      <span class="custom-switch-indicator"></span>
      <span class="custom-switch-description"> Enable service status sync with provider <?=render_tooltip_popover_html('When enabled, the service status will be synced with the status from the provider.', 'popover', 'right'); ?></span>
    </label>
  </div>
  <div class="form-group ">
    <label class="custom-switch">
      <input type="hidden" name="auto_sync_name" value="0">
      <input type="checkbox" name="auto_sync_name" class="custom-switch-input" value="1"  <?php echo ($auto_sync_name) ? 'checked' : ''; ?>>
      <span class="custom-switch-indicator"></span>
      <span class="custom-switch-description"> Enable service name sync <?=render_tooltip_popover_html('When enabled, the service name will be synced with the service name from the provider', 'popover', 'right'); ?> </span>
    </label>
  </div>
  <div class="form-group ">
    <label class="custom-switch">
      <input type="hidden" name="auto_sync_desc" value="0">
      <input type="checkbox" name="auto_sync_desc" class="custom-switch-input" value="1"  <?php echo ($auto_sync_desc) ? 'checked' : ''; ?>>
      <span class="custom-switch-indicator"></span>
      <span class="custom-switch-description"> Enable service description sync <?=render_tooltip_popover_html('When enabled, it will only support descriptions from providers like <b>hqsmartpanel.com</b>, or any provider that returns service description data in an API response', 'popover', 'right'); ?></span>
    </label>
  </div>
</div>
