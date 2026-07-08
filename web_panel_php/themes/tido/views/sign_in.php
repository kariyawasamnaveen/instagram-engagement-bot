<?php 
  include_once 'blocks/head.blade.php';
  $cookie_email = '';
  $cookie_pass = '';
  if (isset($_COOKIE["cookie_email"])) {
    $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
  }
  if (isset($_COOKIE["cookie_pass"])) {
    $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
  }
  $form_url        = cn("auth/ajax_sign_in");
  $form_attributes = [
    'id'            => 'signUpForm', 
    'data-focus'    => 'false', 
    'class'         => 'actionFormWithoutToast', 
    'data-redirect' => cn('home'), 
    'method'        => "POST"
  ];
?>
<section class="sign-up-form">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h1><?=lang("login_to_your_account")?></h1>
        <div class="form-container">
          <?php echo form_open($form_url, $form_attributes); ?>
            <div class="form-group">
              <input type="email" class="form-control-input" name="email" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : '' ?>"  required>
              <label class="label-control" for="semail"><?php echo lang("Email"); ?></label>
            </div>
            <div class="form-group">
              <input type="password" class="form-control-input" name="password" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>"  required>
              <label class="label-control" for="spassword"><?php echo lang("Password"); ?></label>
            </div>
            <div class="form-group mt-20">
                <div id="alert-message" class="alert-message-reponse"></div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <?php 
                  if (!session('uid')) {
                ?>
                <div class="form-group checkbox">
                  <input type="checkbox"  name="remember" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
                  <label class=""><?=lang("remember_me")?></label>
                </div>
                <?php } ?>
              </div>
              <?php if (!session('uid')) : ?>
                <div class="col-md-6">
                  <a class="checkbox text-right" href="<?=cn("auth/forgot_password")?>"><?=lang("forgot_password")?>?</a>
                </div>
              <?php endif; ?>
            </div>
            
            <?php
              if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") {
            ?>
            <div class="form-group">
              <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
            </div>
            <?php } ?> 

            <div class="form-group">
              <button type="submit" class="form-control-submit-button btn-submit"><?=lang("Login")?></button>
            </div>

            <?php echo $social_login_html; ?>      

          <?php echo form_close(); ?>
          <?php if (!get_option('disable_signup_page') && !session('uid')): ?>
            <div class="text-center text-muted">
              <?=lang("dont_have_account_yet")?> <a href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php 
  include_once 'blocks/script.blade.php';
?>