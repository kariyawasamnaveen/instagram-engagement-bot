<!DOCTYPE html>
<html lang="en">
  <?php 
    include_once 'blocks/head.blade.php';
    $form_url        = cn("auth/ajax_sign_up");
    $form_attributes = [
      'id'            => 'signUpForm', 
      'data-focus'    => 'false', 
      'class'         => 'actionFormWithoutToast', 
      'data-redirect' => cn('new_order'), 
      'method'        => "POST"  
    ];
  ?>
  <style>
      /* OVERRIDE SMARTPANEL STYLES FOR NATIVE APP LOOK - LUXURY DARK MIDNIGHT SAPPHIRE */
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
          display: flex; align-items: center; justify-content: center; margin-bottom: 30px;
      }
      .header-logo h1 { font-size: 22px; font-weight: 700; letter-spacing: 1px; margin: 0; color:#fff;}

      .app-tabs { display: flex; background: #131315; border-radius: 14px; margin-bottom: 35px; padding: 5px; border: 1px solid #242427; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5);}
      .app-tab { flex: 1; text-align: center; padding: 14px 0; border-radius: 10px; font-weight: 600; font-size: 15px; color: #636366; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
      .app-tab.active { background: linear-gradient(135deg, #B8860B 0%, #D4AF37 100%); color: #000; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); transform: scale(1.02);}

      .app-input-group { margin-bottom: 22px; }
      .app-input-group label { display: block; font-size: 13px; color: #8E8E93; margin-bottom: 8px; font-weight: 500; padding-left: 4px; letter-spacing: 0.5px;}
      .app-input-group input, 
      .app-input-group select {
          width: 100%; background: #131315 !important; border: 1px solid #242427 !important; border-radius: 14px !important;
          padding: 18px 16px !important; color: #fff !important; font-size: 15px !important; outline: none; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5) !important;
          transition: all 0.3s ease; height: auto !important; appearance: none;
      }
      .app-input-group input:focus,
      .app-input-group select:focus { border-color: #D4AF37 !important; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5), 0 0 0 3px rgba(212, 175, 55, 0.25) !important; }
      .app-input-group input::placeholder { color: #48484A !important; font-weight: 400;}
      
      .app-input-group select option { background: #18181A; color: #fff; }

      .custom-checkbox-wrapper {
          display: flex; align-items: center; margin-bottom: 20px; padding-left: 5px;
      }
      .custom-checkbox-wrapper input[type="checkbox"] { width: 18px; height: 18px; margin-right: 12px; accent-color: #D4AF37; cursor: pointer; }
      .custom-checkbox-wrapper label { font-size: 13px; color: #8E8E93; cursor: pointer; margin: 0;}
      .custom-checkbox-wrapper a { color: #D4AF37; font-weight: 600; text-decoration: none; }
      
      .app-btn-submit {
          width: 100%; background: linear-gradient(135deg, #B8860B 0%, #D4AF37 100%) !important; color: #000 !important; border: none !important; border-radius: 14px !important;
          padding: 18px !important; font-size: 17px !important; font-weight: 700 !important; cursor: pointer; margin-top: 10px; margin-bottom: 30px;
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
            <div class="app-tab" onclick="window.location.href='<?=cn('auth/login')?>'">Sign in</div>
            <div class="app-tab active">Sign Up</div>
        </div>

        <?php echo form_open($form_url, $form_attributes); ?>
            
            <div style="display: flex; gap: 15px;">
                <div class="app-input-group animate-item delay-3" style="flex: 1;">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                </div>
                <div class="app-input-group animate-item delay-3" style="flex: 1;">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                </div>
            </div>

            <div class="app-input-group animate-item delay-3">
                <label>Email Address</label>
                <input type="email" name="email" id="email" placeholder="Email Address" required>
            </div>
            
            <?php if (get_option('enable_signup_skype_field')) : ?>
            <div class="app-input-group animate-item delay-4">
                <label>Skype ID</label>
                <input type="text" name="skype_id" id="skype_id" placeholder="Skype ID">
            </div>
            <?php endif; ?>

            <?php if (get_option('enable_signup_whatsapp_field')) : ?>
            <div class="app-input-group animate-item delay-4">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" id="whatsapp" placeholder="WhatsApp Number" required>
            </div>
            <?php endif; ?>
            
            <div class="app-input-group animate-item delay-4">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>

            <div class="app-input-group animate-item delay-4">
                <label>Confirm Password</label>
                <input type="password" name="re_password" id="re_password" placeholder="Confirm Password" required>
            </div>

            <div class="app-input-group animate-item delay-5">
                <label>Timezone</label>
                <select name="timezone">
                    <?php $time_zones = tz_list(); ?>
                    <?php if (!empty($time_zones)) :
                        $location = get_location_info_by_ip(get_client_ip());
                        $user_timezone = $location->timezone;
                        if ($user_timezone == "" || $user_timezone == 'Unknow') {
                        $user_timezone = get_option("default_timezone", 'UTC');
                        }
                        foreach ($time_zones as $key => $time_zone) :
                    ?>
                    <option value="<?=$time_zone['zone']?>" <?=($user_timezone == $time_zone["zone"])? 'selected': ''?>><?=$time_zone['time']?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="custom-checkbox-wrapper animate-item delay-5">
                <input type="checkbox" id="customCheck1" name="terms" required>
                <label for="customCheck1">I agree to the <a href="<?=cn('terms')?>">Terms & Policy</a></label>
            </div>

            <?php if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") : ?>
            <div class="app-input-group animate-item delay-5" style="margin-top: 15px;">
                <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
            </div>
            <?php endif; ?>

            <div class="form-group mt-20 text-center">
                <div id="alert-message" class="alert-message-reponse text-danger font-weight-bold" style="margin-top:10px; font-size:14px;"></div>
            </div>
            
            <button class="app-btn-submit btn-submit animate-item delay-5" type="submit">Create Account</button>
        <?php echo form_close(); ?>
        
    </div>

  </body>
  <?php include_once 'blocks/script.blade.php'; ?>
</html>
