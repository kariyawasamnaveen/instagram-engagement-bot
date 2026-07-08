<!DOCTYPE html>
<html lang="en">
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
      'data-redirect' => cn('statistics'), 
      'method'        => "POST"
    ];
  ?>
  <style>
      /* OVERRIDE SMARTPANEL STYLES FOR NATIVE APP LOOK - LUXURY DARK & GOLD */
      body, html { 
          background-color: #0B0B0C !important; /* Obsidian Black */
          color: #fff !important; 
          font-family: 'Inter', 'Segoe UI', sans-serif !important; 
          min-height: 100vh;
      }
      .navbar, .footer, .navbar-custom, footer { display: none !important; }
      
      #app-login-container {
          max-width: 500px; margin: 0 auto;
          display: flex; flex-direction: column; 
          padding: 60px 20px 80px 20px; 
      }
      
      .header-logo {
          display: flex; align-items: center; justify-content: center; margin-bottom: 40px;
      }
      .header-logo h1 { font-size: 22px; font-weight: 700; letter-spacing: 1px; margin: 0; color:#fff;}

      .app-tabs { display: flex; background: #131315; border-radius: 14px; margin-bottom: 35px; padding: 5px; border: 1px solid #242427; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5);}
      .app-tab { flex: 1; text-align: center; padding: 14px 0; border-radius: 10px; font-weight: 600; font-size: 15px; color: #636366; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
      .app-tab.active { background: linear-gradient(135deg, #B8860B 0%, #D4AF37 100%); color: #000; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); transform: scale(1.02);}

      .app-input-group { margin-bottom: 22px; }
      .app-input-group label { display: block; font-size: 13px; color: #8E8E93; margin-bottom: 8px; font-weight: 500; padding-left: 4px; letter-spacing: 0.5px;}
      .app-input-group input {
          width: 100%; background: #131315 !important; border: 1px solid #242427 !important; border-radius: 14px !important;
          padding: 18px 16px !important; color: #fff !important; font-size: 15px !important; outline: none; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5) !important;
          transition: all 0.3s ease;
      }
      .app-input-group input:focus { border-color: #D4AF37 !important; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5), 0 0 0 3px rgba(212, 175, 55, 0.25) !important; }
      .app-input-group input::placeholder { color: #48484A !important; font-weight: 400;}
      
      .app-btn-submit {
          width: 100%; background: linear-gradient(135deg, #B8860B 0%, #D4AF37 100%) !important; color: #000 !important; border: none !important; border-radius: 14px !important;
          padding: 18px !important; font-size: 17px !important; font-weight: 700 !important; cursor: pointer; margin-top: 15px;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4); letter-spacing: 1px; text-transform: uppercase;
      }
      .app-btn-submit:active { transform: scale(0.98); opacity: 0.9 !important; }

      /* Staggered Fade-Up Animation */
      @keyframes fadeUp {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
      }
      .animate-item { opacity: 0; animation: fadeUp 0.6s ease forwards; }
      .delay-1 { animation-delay: 0.1s; }
      .delay-2 { animation-delay: 0.2s; }
      .delay-3 { animation-delay: 0.3s; }
      .delay-4 { animation-delay: 0.4s; }
      .delay-5 { animation-delay: 0.5s; }
  </style>

  <body>
    <div id="app-login-container">
        
        <div class="header-logo animate-item delay-1">
            <svg viewBox="0 0 24 24" style="width:32px; height:32px; fill:#D4AF37; margin-right:10px;"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.36 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/></svg>
            <h1>NEW VIEW ORDER</h1>
        </div>

        <div class="app-tabs animate-item delay-2">
            <div class="app-tab active">Sign in</div>
            <div class="app-tab" onclick="window.location.href='<?=cn('auth/signup')?>'">Sign Up</div>
        </div>

        <?php echo form_open($form_url, $form_attributes); ?>
            
            <div class="app-input-group animate-item delay-3">
                <label>Email Address</label>
                <input type="email" name="email" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : '' ?>" placeholder="Email Address" required>
            </div>
            
            <div class="app-input-group animate-item delay-4">
                <label>Password</label>
                <input type="password" name="password" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" placeholder="Password" required>
            </div>
            
            <?php
            if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") {
            ?>
            <div class="form-group animate-item delay-5" style="margin-top: 15px;">
              <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
            </div>
            <?php } ?> 

            <div class="form-group mt-20 text-center">
                <div id="alert-message" class="alert-message-reponse text-danger font-weight-bold" style="margin-top:10px; font-size:14px;"></div>
            </div>
            
            <button class="app-btn-submit btn-submit animate-item delay-5" type="submit">Sign in</button>
        <?php echo form_close(); ?>
        
    </div>

  </body>
  <?php include_once 'blocks/script.blade.php'; ?>
</html>