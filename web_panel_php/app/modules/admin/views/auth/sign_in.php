
<div class="row h-100 align-items-center auth-form">
  <div class="col-md-5 col-login mx-auto">
    <form class="card actionForm" action="<?=admin_url("login")?>" data-redirect="<?=admin_url('users')?>" method="POST" style="border: none; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
      <div class="card-body p-6">
        <div class="card-title text-center mb-6">
          <div class="site-logo mb-4">
            <a href="<?=cn()?>">
              <img src="<?=BASE?>assets/images/site_logo_gold.png" alt="website-logo" style="max-height: 80px; filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.3));">
            </a>
          </div>
          <h5 class="text-white font-weight-bold" style="letter-spacing: 1px;"><?=lang("login_to_your_account")?></h5>
        </div>
        <div class="form-group">
          <div class="input-icon mb-4">
            <span class="input-icon-addon">
              <i class="fe fe-mail text-primary"></i>
            </span>
            <input type="email" class="form-control" name="email" placeholder="<?=lang("Email")?>" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : ""?>" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 10px; height: 50px;">
          </div>    
                
          <div class="input-icon mb-4">
            <span class="input-icon-addon">
              <i class="fa fa-key text-primary"></i>
            </span>
            <input type="password" class="form-control" name="password" placeholder="<?=lang("Password")?>" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 10px; height: 50px;">
          </div>  
        </div>

        <?php
          if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") {
        ?>
        <div class="form-group">
          <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
        </div>
        <?php } ?>

        <div class="form-footer mt-5">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <button type="submit" class="btn btn-primary btn-block shadow-lg" style="height: 50px; border-radius: 10px; font-weight: 600; font-size: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; transition: all 0.3s ease;"><?=lang("Login")?></button>
        </div>
      </div>
    </form>
    <div id="result_notification" class="mt-4 mx-auto" style="max-width: 400px; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%;"></div>
  </div>
</div>

<style>
  .form-control:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: #667eea !important;
    box-shadow: 0 0 15px rgba(102, 126, 234, 0.3);
  }
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
    filter: brightness(1.1);
  }
  .alert {
    border-radius: 15px;
    border: none;
    background: rgba(15, 23, 42, 0.9);
    color: #fff;
    backdrop-filter: blur(10px);
    text-align: center;
    font-weight: 500;
    padding: 15px 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    animation: slideUp 0.4s ease-out;
  }
  @keyframes slideUp {
    from { transform: translate(-50%, 50px); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
  }
  .alert-success {
    border-left: 4px solid #2bcbba !important;
  }
  .alert-danger {
    border-left: 4px solid #ff7b7b !important;
  }
</style>
