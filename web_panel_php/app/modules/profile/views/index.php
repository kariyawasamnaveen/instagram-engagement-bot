<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-item delay-1">
  <div class="d-flex align-items-center">
    <a href="<?=cn('statistics')?>" class="btn p-2 mr-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 50%; color: #D4AF37; transition: all 0.2s;">
        <i class="fe fe-arrow-left" style="font-size: 16px;"></i>
    </a>
    <h1 class="page-title mb-0" style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 700; color: #FFFFFF; letter-spacing: 0.5px;">
        <?=lang('Your_account')?>
    </h1>
  </div>
  <a href="<?=cn("auth/logout")?>" class="btn btn-pill px-4" style="border: 1px solid rgba(220, 38, 38, 0.3); background: rgba(220, 38, 38, 0.08); color: #FC8181; font-weight: 600; font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase; border-radius: 30px; transition: all 0.2s;">
    <i class="fe fe-log-out mr-2" style="font-size: 12px;"></i><?=lang('Logout')?>
  </a>
</div>

<?php
  $item_user_timezone = esc($item['timezone'] ?? 'Asia/Ho_Chi_Minh');
  $item_login_type = esc($item['login_type'] ?? '');
  $item_more_infor = esc($item['more_information'] ?? []);
  
  $website = '';
  $phone = '';
  $skype_id = '';
  $what_asap = '';
  $address = '';
  
  if (!empty($item_more_infor)) {
    $more_info_array = json_decode($item['more_information'], true);
    if (is_array($more_info_array)) {
      $website    = get_value($more_info_array, "website");
      $phone      = get_value($more_info_array, "phone");
      $skype_id   = get_value($more_info_array, "skype_id");
      $what_asap  = get_value($more_info_array, "what_asap");
      $address    = get_value($more_info_array, "address");
    }
  }
?>

<style>
/* CYBER-LUXURY GLASS DESIGN */
.profile-card {
    background: rgba(20, 20, 20, 0.75) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(212, 175, 55, 0.15) !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.35);
    margin-bottom: 28px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.profile-card:hover {
    border-color: rgba(212, 175, 55, 0.3) !important;
    box-shadow: 0 12px 40px rgba(212, 175, 55, 0.08);
}
.profile-card .card-header {
    background: transparent !important;
    border-bottom: 1px solid rgba(212, 175, 55, 0.1) !important;
    padding: 20px 24px !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.profile-card .card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #D4AF37 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin: 0;
}
.profile-card .card-body {
    padding: 24px !important;
}

/* Premium Input Fields */
.profile-group {
    margin-bottom: 20px;
}
.profile-label {
    display: block;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 600;
    color: #8A8A93;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}
.profile-input {
    width: 100%;
    background: rgba(10, 10, 11, 0.85) !important;
    border: 1px solid rgba(212, 175, 55, 0.12) !important;
    border-radius: 12px !important;
    color: #FFFFFF !important;
    padding: 12px 16px !important;
    font-size: 14px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    transition: all 0.2s ease-in-out;
}
.profile-input:focus {
    border-color: #D4AF37 !important;
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.25) !important;
    outline: none !important;
}
.profile-input[readonly] {
    background: rgba(18, 18, 20, 0.5) !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
    color: #8A8A93 !important;
    cursor: not-allowed;
}

/* Custom Dropdown select */
select.profile-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23D4AF37' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 16px center !important;
    background-size: 16px !important;
    padding-right: 44px !important;
}

/* Small notes */
.profile-helper {
    display: block;
    font-size: 11px;
    color: #8A8A93;
    margin-top: 6px;
    line-height: 1.4;
}

/* Solid gold glowing action button */
.btn-gold-action {
    background: linear-gradient(45deg, #B8860B, #D4AF37) !important;
    color: #000000 !important;
    font-family: 'Poppins', sans-serif;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 24px !important;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3) !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
}
.btn-gold-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45) !important;
}
.btn-gold-action:active {
    transform: translateY(1px);
}

.profile-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

/* Clipboard click highlight */
.clickable-api-input {
    cursor: pointer !important;
    transition: background-color 0.2s;
}
.clickable-api-input:hover {
    background: rgba(212, 175, 55, 0.05) !important;
}

/* Target AJAX elements for key regeneration */
#result_notification input {
    width: 100% !important;
    background: rgba(10, 10, 11, 0.85) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    border-radius: 12px !important;
    color: #D4AF37 !important;
    padding: 12px 16px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    font-family: monospace !important;
    letter-spacing: 0.8px !important;
    cursor: pointer !important;
}
#result_notification label {
    display: block;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 600;
    color: #8A8A93;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}
#result_notification .alert-info {
    background: rgba(212, 175, 55, 0.08) !important;
    border: 1px solid rgba(212, 175, 55, 0.2) !important;
    border-radius: 12px !important;
    color: #D4AF37 !important;
    font-family: 'Poppins', sans-serif;
    font-size: 12px !important;
    padding: 12px 16px !important;
    margin-top: 16px !important;
    line-height: 1.5 !important;
}
</style>

<div class="row">
  <!-- PERSONAL & SECURITY CARD -->
  <div class="col-md-6 animate-item delay-2">
    <div class="card profile-card">
      <div class="card-header">
        <h3 class="card-title"><?=lang("basic_information")?></h3>
      </div>
      <div class="card-body">
        <form class="form actionForm" action="<?=cn($module."/ajax_update")?>" data-redirect="<?=cn($module)?>" method="POST">
          <div class="form-body">
            <div class="row">

              <div class="col-md-6 col-6">
                <div class="form-group profile-group">
                  <label class="profile-label"><?=lang("first_name")?></label>
                  <input class="profile-input" name="first_name" type="text" value="<?=esc($item['first_name'] ?? '')?>">
                </div>
              </div>

              <div class="col-md-6 col-6">
                  <div class="form-group profile-group">
                    <label class="profile-label"><?=lang("last_name")?></label>
                    <input class="profile-input" name="last_name" type="text" value="<?=esc($item['last_name'] ?? '')?>">
                  </div>
              </div> 

              <div class="col-md-12">
                <div class="form-group profile-group">
                  <label class="profile-label"><?=lang('Email')?></label>
                  <input class="profile-input" name="email" type="email" value="<?=esc($item['email'] ?? '')?>" readonly>
                </div>
              </div>
              

              <div class="col-md-12">
                <div class="form-group profile-group">
                  <label class="profile-label"><?=lang('Timezone')?></label>
                  <select name="timezone" class="profile-input">
                    <?php $time_zones = tz_list();
                      if (!empty($time_zones)) {
                        foreach ($time_zones as $key => $time_zone) {
                    ?>
                    <option value="<?=$time_zone['zone']?>" <?= ($item_user_timezone== $time_zone["zone"]) ? 'selected': ''?>><?=$time_zone['time']?></option>
                    <?php }}?>
                  </select>
                </div>
              </div>
              
              <?php if ($item_login_type != 'google_login') : ?>
                <div class="col-md-6 col-6">
                  <div class="form-group profile-group">
                    <label class="profile-label"><?=lang('Password')?></label>
                    <input class="profile-input" name="password" type="password">
                  </div>
                </div> 

                <div class="col-md-6 col-6">
                  <div class="form-group profile-group">
                    <label class="profile-label"><?=lang('Confirm_password')?></label>
                    <input class="profile-input" name="re_password" type="password">
                  </div>
                </div>
                
                <div class="col-md-12 mb-3">
                  <small class="profile-helper"><?=lang("note_if_you_dont_want_to_change_password_then_leave_these_password_fields_empty")?></small>
                </div>
              <?php endif; ?>
              
              <div class="col-md-12 profile-actions">
                <button type="submit" class="btn-gold-action"><?=lang('Save')?></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div> 

  <!-- CONTACT & API DETAILS -->
  <div class="col-md-6 animate-item delay-3">
    <!-- CONTACT VERIFICATION CARD -->
    <div class="card profile-card">
      <div class="card-header">
        <h3 class="card-title">WhatsApp Credentials</h3>
      </div>
      <div class="card-body">
        <form class="form actionForm" action="<?=cn($module."/ajax_update_more_infors")?>" data-redirect="<?=cn($module)?>" method="POST">
          <div class="form-body">
            <div class="row">
              <!-- SAFE HIDDEN INPUTS TO PROTECT DB INTEGRITY -->
              <input type="hidden" name="website" value="<?= esc($website) ?>">
              <input type="hidden" name="phone" value="<?= esc($phone) ?>">
              <input type="hidden" name="skype_id" value="<?= esc($skype_id) ?>">
              <input type="hidden" name="address" value="<?= esc($address) ?>">
              
              <div class="col-md-12">
                <div class="form-group profile-group">
                  <label class="profile-label"><?=lang("whatsapp_number")?></label>
                  <input class="profile-input" name="what_asap" type="text" placeholder="e.g. +947XXXXXXXX" value="<?= esc($what_asap) ?>">
                  <small class="profile-helper">Used by automated telemetry dispatch nodes for campaign status alerts.</small>
                </div>
              </div>
              
              <div class="col-md-12 profile-actions">
                <button type="submit" class="btn-gold-action"><?=lang("Save")?></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- API CONSOLE CARD -->
    <div class="card profile-card">
      <div class="card-header">
        <h3 class="card-title"><?=lang('your_api_key')?></h3>
      </div>
      <div class="card-body">
        <form class="form actionForm" action="<?=cn($module . "/ajax_update_api")?>" method="POST">
          <div class="form-group profile-group" id="result_notification">
            <label class="profile-label"><?=lang('Key')?></label>
            <div class="input-group">
              <input type="text" readonly id="active_api_key" class="profile-input clickable-api-input" style="font-family: monospace; letter-spacing: 0.8px; color: #D4AF37 !important;" value="<?= hide_api_key(esc($item['api_key'] ?? ''))?>">
            </div>
            <small class="profile-helper">Click the key above to copy. Generate a new key if compromised.</small>
          </div>
          <div class="profile-actions">
            <button type="submit" class="btn-gold-action"><?=lang("Generate_new")?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Dynamic delegated click to copy handler for API key (supports original and AJAX replacement inputs)
$(document).ready(function() {
    $(document).on('click', '#result_notification input', function() {
        var keyVal = $(this).val();
        
        if (!keyVal) return;
        
        if (keyVal.indexOf('***') !== -1) {
            $.toast({
                heading: 'Telemetry Key Masked',
                text: 'Click "Generate New" to create and copy a fresh unmasked API key.',
                icon: 'warning',
                loader: true,
                loaderBg: '#D4AF37',
                position: 'top-right',
                textColor: '#fff',
                bgColor: '#101014'
            });
            return;
        }
        
        // Select and Copy to clipboard
        this.select();
        this.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(keyVal);
        
        $.toast({
            heading: 'Copied Success',
            text: 'Telemetry API Key copied to clipboard successfully.',
            icon: 'success',
            loader: true,
            loaderBg: '#D4AF37',
            position: 'top-right',
            textColor: '#fff',
            bgColor: '#101014'
        });
    });
});
</script>
