    <?php 
      include_once 'blocks/head.blade.php';
    ?>
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand logo-image" href="<?php echo cn(); ?>"><img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="Website logo"></a> 
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-awesome fas fa-bars"></span>
                <span class="navbar-toggler-awesome fas fa-times"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <?php echo render_header_nav_ul(); ?>
                <span class="nav-item">
                  <?php 
                    if (!session('uid')) {
                  ?>
                    <a class="nav-link page-scroll link btn-login" href="<?=cn('auth/login')?>"><?=lang("Login")?></a>
                    <?php if (!get_option('disable_signup_page')) { ?>
                        <a class="btn-outline-sm" href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
                    <?php }; ?>
                  <?php } else {?>
                    <a class="btn-outline-sm" href="<?=cn('statistics')?>"><?=lang("dashboard")?></a>
                  <?php } ?>
                </span>
            </div>
        </div>
    </nav>

    <?php
        if (isset($_COOKIE["cookie_email"])) {
          $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
        }

        if (isset($_COOKIE["cookie_pass"])) {
          $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
        }
    ?>

    <section id="home" class="header-top mheader">
      <div class="intro_wrapper">
          <div class="container">  
              <div class="row">        
                  <div class="col-sm-12 col-md-12 col-lg-6">
                      <div class="intro_text">
                          <h1 class=""><?=lang("resellers_1_destination_for_smm_services")?></h1>
                          <p class=""><?=lang("save_time_managing_your_social_account_in_one_panel_where_people_buy_smm_services_such_as_facebook_ads_management_instagram_youtube_twitter_soundcloud_website_ads_and_many_more")?></p>
                          <div class="btn">
                            <a href="<?=cn('auth/signup')?>" class="btn btn-pill btn-gradient btn-signin btn-submit btn-lg"><?=lang("get_start_now")?></a>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-12 col-md-12 col-lg-6" data-aos="fade-up" data-aos-duration="500">
                    <div class="intro_banner">
                       <img src="<?php echo BASE; ?>themes/regular/assets/images/header-top.png" alt="About us">
                    </div>
                  </div>
              </div>
          </div> 
      </div>  
    </section>

    <section id="services" class="services">
      <div class="container">
          <div class="row justify-content-center m-b-20">
            <div class="col-lg-10">
              <div class="section-title text-center">
                <div class="line m-auto"></div>
                <h3 class="title"><?php echo lang("reasons_why_you_should_try_our_panel"); ?></h3>
                <p><?php echo lang("let_us_help_you_build_your_online_presence_quickly_and_efficiently"); ?></p>
              </div>
            </div>
          </div>
          <div class="row justify-content-center">
              <div class="col-lg-4 col-md-7 col-sm-8" data-aos="fade-up" data-aos-duration="500">
                  <div class="sigle-service text-center m-t-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.2s; animation-name: fadeIn;">
                      <div class="services-icon">
                        <img class="shape" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape.svg" alt="shape" />
                        <img class="shape-1" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape-1.svg" alt="shape" />
                        <i class="fe fe-award"></i>
                      </div>
                      <div class="services-content m-t-30">
                        <h4 class="services-title"><?php echo lang("best_quality"); ?></h4>
                        <p class="text"><?php echo lang("the_highest_quality_smm_services_to_meet_your_needs"); ?></p>
                      </div>
                  </div>
              </div>
              <div class="col-lg-4 col-md-7 col-sm-8" data-aos="fade-up" data-aos-duration="1000">
                  <div class="sigle-service text-center m-t-30">
                      <div class="services-icon">
                        <img class="shape" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape.svg" alt="shape" />
                        <img class="shape-1" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape-2.svg" alt="shape" />
                        <i class="fe fe-truck"></i>
                      </div>
                      <div class="services-content m-t-30">
                        <h4 class="services-title"><?php echo lang("diverse_payment_options"); ?></h4>
                        <p class="text"><?php echo lang("we_have_a_good_amount_of_different_payment_options"); ?></p>
                      </div>
                  </div>
              </div>
              <div class="col-lg-4 col-md-7 col-sm-8" data-aos="fade-up" data-aos-duration="1500">
                  <div class="sigle-service text-center m-t-30">
                      <div class="services-icon">
                        <img class="shape" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape.svg" alt="shape" />
                        <img class="shape-1" src="<?php echo BASE; ?>themes/regular/assets/images/services-shape-3.svg" alt="shape" />
                        <i class="fe fe-clock"></i>
                      </div>
                      <div class="services-content m-t-30">
                        <h4 class="services-title"><?php echo lang("super_quick_delivery"); ?></h4>
                        <p class="text"><?php echo lang("we_provide_automated_services_with_quick_delivery"); ?></p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </section>

    <section class="social-icon-area pt-90" data-aos="fade-up" data-aos-duration="1000">
      <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center header-top m-b-20" data-aos="fade-up" data-aos-duration="500">
                <h1><?php echo lang("service_items"); ?></h1>
            </div> 
          <div class="col-lg-12">
            <div class="brand-logo d-flex align-items-center justify-content-center justify-content-md-between">
              <div class="single-logo m-t-30" data-aos="zoom-in" data-aos-duration="1500">
                <img src="<?php echo BASE; ?>themes/regular/assets/images/fb.png" alt="FB" />
              </div>
              <div class="single-logo m-t-30" data-aos="zoom-in" data-aos-duration="1500">
                <img src="<?php echo BASE; ?>themes/regular/assets/images/ig.png" alt="IG" />
              </div>
              <div class="single-logo m-t-30" data-aos="zoom-in" data-aos-duration="1500">
                <img src="<?php echo BASE; ?>themes/regular/assets/images/yt.png" alt="YT" />
              </div>
              <div class="single-logo m-t-30" data-aos="zoom-in" data-aos-duration="1500">
                <img src="<?php echo BASE; ?>themes/regular/assets/images/tw.png" alt="TW" />
              </div>
              <div class="single-logo m-t-30" data-aos="zoom-in" data-aos-duration="1500">
                <img src="<?php echo BASE; ?>themes/regular/assets/images/sc.png" alt="SC" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="features" class="our-features" data-aos="fade-up" data-aos-duration="300">
      <div class="container custom-container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <div class="section-title pb-20">
              <h1><?php echo lang("What_we_offer"); ?></h1>
              <p><?php echo lang("comes_with_all_the_essential_features_and_elements_you_need_here_are_the_key_features_of_our_services_you_must_know"); ?></p>
            </div>
          </div>
        </div>
        <div class="row features-wrapper">
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-calendar"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("Resellers")?></h3>
                  <p class="text-muted"><?=lang("you_can_resell_our_services_and_grow_your_profit_easily_resellers_are_important_part_of_smm_panel")?></p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-phone-call"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("Supports")?></h3>
                  <p class="text-muted"><?=lang("technical_support_for_all_our_services_247_to_help_you")?></p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-star"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("high_quality_services")?></h3>
                  <p class="text-muted"><?=lang("get_the_best_high_quality_services_and_in_less_time_here")?></p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-upload-cloud"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("Updates")?></h3>
                  <p class="text-muted"><?=lang("services_are_updated_daily_in_order_to_be_further_improved_and_to_provide_you_with_best_experience")?></p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-share-2"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("api_support")?></h3>
                  <p class="text-muted"><?=lang("we_have_api_support_for_panel_owners_so_you_can_resell_our_services_easily")?></p>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6 features-col" data-aos="fade-up" data-aos-duration="1000">
              <div class="feature-content m-t-30 m-b-30">
                <div class="features-icon">
                  <i class="fe fe-dollar-sign"></i>
                </div>
                <div class="features-content">
                  <h3><?=lang("secure_payments")?></h3>
                  <p class="text-muted"><?=lang("we_have_a_popular_methods_as_paypal_and_many_more_can_be_enabled_upon_request")?></p>
                </div>
              </div>
            </div>
        </div>
      </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <div class="col-lg-12 text-center header-top" data-aos="fade-up" data-aos-duration="500">
                <h1><?php echo lang("how_it_works"); ?></h1>
                <p><?php echo lang("by_following_the_processes_below_you_can_make_any_order_you_want"); ?></p>
            </div> 
            <div class="row how-it-works-row justify-content-start">
                <div class="col-md-3 how-it-works-col" data-aos="fade-up" data-aos-duration="800">
                    <div class="how-it-works-card">
                        <div class="how-it-works-arrow-top style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-942.000000, -1387.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M889.516523,26.5080119 L891.910644,20.9496585 L902,32.9164837 L886.372927,33.807873 L888.723185,28.3469617 C871.347087,21.9210849 854.507984,19.7125409 838.195168,21.7129851 C818.169006,24.1687976 798.907256,32.9719131 780.398868,48.1424468 L779.638673,48.7694781 L778.869195,49.4081513 L777.591849,47.8691952 L778.361327,47.2305219 C797.38492,31.4407805 817.252224,22.2662407 837.951732,19.7278557 C854.622929,17.6834632 871.814783,19.9463129 889.516523,26.5080119 Z" id="Line3"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="how-it-works-arrow-bottom style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-657.000000, -1461.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M493.869195,93.5918487 L494.638673,94.2305219 C513.37968,109.785715 532.894675,118.797561 553.195168,121.287015 C569.507984,123.287459 586.347087,121.078915 603.723185,114.653038 L601.372927,109.192127 L617,110.083516 L606.910644,122.050341 L604.516523,116.491988 C586.814783,123.053687 569.622929,125.316537 552.951732,123.272144 C532.528218,120.767604 512.914862,111.802694 494.12272,96.3975396 L493.361327,95.7694781 L492.591849,95.1308048 L493.869195,93.5918487 Z" id="Line2"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="d-flex justify-content-center how-it-works-preview">
                            <div class="how-it-works-number style-box-shadow-default style-bg-color-light"> 1 </div>
                        </div>
                        <div class="how-it-works-title">
                            <p class="text-center"><span><strong><?php echo lang("register_and_log_in"); ?></strong></span></p>
                        </div>
                        <div class="how-it-works-description">
                            <p class="text-center"><span><?php echo lang("creating_an_account_is_the_first_step_then_you_need_to_log_in"); ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 how-it-works-col" data-aos="fade-up" data-aos-duration="1600">
                    <div class="how-it-works-card">
                        <div class="how-it-works-arrow-top style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-942.000000, -1387.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M889.516523,26.5080119 L891.910644,20.9496585 L902,32.9164837 L886.372927,33.807873 L888.723185,28.3469617 C871.347087,21.9210849 854.507984,19.7125409 838.195168,21.7129851 C818.169006,24.1687976 798.907256,32.9719131 780.398868,48.1424468 L779.638673,48.7694781 L778.869195,49.4081513 L777.591849,47.8691952 L778.361327,47.2305219 C797.38492,31.4407805 817.252224,22.2662407 837.951732,19.7278557 C854.622929,17.6834632 871.814783,19.9463129 889.516523,26.5080119 Z" id="Line3"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="how-it-works-arrow-bottom style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-657.000000, -1461.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M493.869195,93.5918487 L494.638673,94.2305219 C513.37968,109.785715 532.894675,118.797561 553.195168,121.287015 C569.507984,123.287459 586.347087,121.078915 603.723185,114.653038 L601.372927,109.192127 L617,110.083516 L606.910644,122.050341 L604.516523,116.491988 C586.814783,123.053687 569.622929,125.316537 552.951732,123.272144 C532.528218,120.767604 512.914862,111.802694 494.12272,96.3975396 L493.361327,95.7694781 L492.591849,95.1308048 L493.869195,93.5918487 Z" id="Line2"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="d-flex justify-content-center how-it-works-preview">
                            <div class="how-it-works-number style-box-shadow-default style-bg-color-light"> 2 </div>
                        </div>
                        <div class="how-it-works-title">
                            <p class="text-center"><span><strong><?php echo lang("add_funds"); ?></strong></span></p>
                        </div>
                        <div class="how-it-works-description">
                            <p class="text-center"><span><?php echo lang("next_pick_a_payment_method_and_add_funds_to_your_account"); ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 how-it-works-col" data-aos="fade-up" data-aos-duration="2400">
                    <div class="how-it-works-card">
                        <div class="how-it-works-arrow-top style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-942.000000, -1387.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M889.516523,26.5080119 L891.910644,20.9496585 L902,32.9164837 L886.372927,33.807873 L888.723185,28.3469617 C871.347087,21.9210849 854.507984,19.7125409 838.195168,21.7129851 C818.169006,24.1687976 798.907256,32.9719131 780.398868,48.1424468 L779.638673,48.7694781 L778.869195,49.4081513 L777.591849,47.8691952 L778.361327,47.2305219 C797.38492,31.4407805 817.252224,22.2662407 837.951732,19.7278557 C854.622929,17.6834632 871.814783,19.9463129 889.516523,26.5080119 Z" id="Line3"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="how-it-works-arrow-bottom style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-657.000000, -1461.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M493.869195,93.5918487 L494.638673,94.2305219 C513.37968,109.785715 532.894675,118.797561 553.195168,121.287015 C569.507984,123.287459 586.347087,121.078915 603.723185,114.653038 L601.372927,109.192127 L617,110.083516 L606.910644,122.050341 L604.516523,116.491988 C586.814783,123.053687 569.622929,125.316537 552.951732,123.272144 C532.528218,120.767604 512.914862,111.802694 494.12272,96.3975396 L493.361327,95.7694781 L492.591849,95.1308048 L493.869195,93.5918487 Z" id="Line2"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="d-flex justify-content-center how-it-works-preview">
                            <div class="how-it-works-number style-box-shadow-default style-bg-color-light"> 3 </div>
                        </div>
                        <div class="how-it-works-title">
                            <p class="text-center"><span><strong><?php echo lang("select_a_service"); ?></strong></span></p>
                        </div>
                        <div class="how-it-works-description">
                            <p class="text-center"><span><?php echo lang("select_the_services_you_want_and_get_ready_to_receive_more_publicity"); ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 how-it-works-col" data-aos="fade-up" data-aos-duration="3000">
                    <div class="how-it-works-card">
                        <div class="how-it-works-arrow-top style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-942.000000, -1387.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M889.516523,26.5080119 L891.910644,20.9496585 L902,32.9164837 L886.372927,33.807873 L888.723185,28.3469617 C871.347087,21.9210849 854.507984,19.7125409 838.195168,21.7129851 C818.169006,24.1687976 798.907256,32.9719131 780.398868,48.1424468 L779.638673,48.7694781 L778.869195,49.4081513 L777.591849,47.8691952 L778.361327,47.2305219 C797.38492,31.4407805 817.252224,22.2662407 837.951732,19.7278557 C854.622929,17.6834632 871.814783,19.9463129 889.516523,26.5080119 Z" id="Line3"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="how-it-works-arrow-bottom style-svg-g-primary">
                            <svg width="125px" height="31px" viewBox="0 0 125 31" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Landing" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-657.000000, -1461.000000)" fill="#1E79E4" id="Group-10">
                                        <g transform="translate(165.000000, 1368.000000)">
                                            <path d="M493.869195,93.5918487 L494.638673,94.2305219 C513.37968,109.785715 532.894675,118.797561 553.195168,121.287015 C569.507984,123.287459 586.347087,121.078915 603.723185,114.653038 L601.372927,109.192127 L617,110.083516 L606.910644,122.050341 L604.516523,116.491988 C586.814783,123.053687 569.622929,125.316537 552.951732,123.272144 C532.528218,120.767604 512.914862,111.802694 494.12272,96.3975396 L493.361327,95.7694781 L492.591849,95.1308048 L493.869195,93.5918487 Z" id="Line2"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="d-flex justify-content-center how-it-works-preview">
                            <div class="how-it-works-number style-box-shadow-default style-bg-color-light"> 4 </div>
                        </div>
                        <div class="how-it-works-title">
                            <p class="text-center"><span><strong><?php echo lang("enjoy_popularity"); ?></strong></span></p>
                        </div>
                        <div class="how-it-works-description">
                            <p class="text-center"><span><?php echo lang("you_can_enjoy_incredible_results_when_your_order_is_complete"); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="we-offer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right" data-aos-offset="500" data-aos-easing="ease-in-sine">
                    <div class="text-container">
                        <h3><?php echo lang("what_we_offer_for_your_succes_brand"); ?></h3>
                        <p><?php echo lang("we_are_active_for_support_only_24_hours_a_day_and_seven_times_a_week_with_all_of_your_demands_and_services_around_the_day_dont_go_anywhere_else___we_are_here_ready_to_serve_you_and_help_you_with_all_of_your_smm_needs_users_or_clients_with_smm_orders_and_in_need_of_cheap_smm_services_are_more_then_welcome_in_our_smm_panel"); ?></p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-right" data-aos-offset="500" data-aos-easing="ease-in-sine">
                    <div class="image-container">
                        <img class="img-fluid" src="<?=BASE?>themes/monoka/assets/images/presentation-2.png" alt="Website icon">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonial" data-aos="fade-up" data-aos-duration="2000">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="above-heading"><?php echo lang("what_people_say_about_us"); ?></div>
                    <p class="header-title"><?php echo lang("our_service_has_an_extensive_customer_roster_built_on_years_worth_of_trust_read_what_our_buyers_think_about_our_range_of_service"); ?></p>
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-12">      
                    <div class="slider-container">
                        <div class="swiper-container text-slider">
                            <div class="swiper-wrapper">
                                
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?=BASE?>themes/monoka/assets/images/testimonial-1.jpg" alt="Website icon">
                                    </div> 
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_one_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_one'); ?> - <?php echo lang('client_one_jobname'); ?></div>
                                    </div>
                                </div>
                                
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?=BASE?>themes/monoka/assets/images/testimonial-2.jpg" alt="Website icon">
                                    </div>
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_two_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_two'); ?> - <?php echo lang('client_two_jobname'); ?></div>
                                    </div> 
                                </div>
                               
                                <div class="swiper-slide">
                                    <div class="image-wrapper">
                                        <img class="img-fluid" src="<?=BASE?>themes/monoka/assets/images/testimonial-3.jpg" alt="Website icon">
                                    </div> 
                                    <div class="text-wrapper">
                                        <div class="testimonial-text"><?php echo lang("client_three_comment"); ?></div>
                                        <div class="testimonial-author"><?php echo lang('client_three'); ?> - <?php echo lang('client_three_jobname'); ?></div>
                                    </div>
                                </div>

                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>
    </section>

    <section class="faqs" data-aos="fade-up" data-aos-duration="2000">
        <div class="faq">
            <div class="container">
                <div class="row text-center">
                    <div class="col-lg-12">
                        <div class="above-heading"><?php echo lang("FAQs"); ?></div>
                        <p class="header-desc"><?php echo lang("we_answered_some_of_the_most_frequently_asked_questions_on_our_panel"); ?></p>
                    </div> 
                </div> 
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="faq-block__card">
                                    <div class="card">
                                        <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-1" aria-expanded="false" aria-controls="#faq-block-10-1">
                                            <div class="faq-block__header-title">
                                                <h4><i class="far fa-question-circle"></i> <?php echo lang("smm_panels__what_are_they"); ?></h4>
                                            </div>
                                            <div class="faq-block__header-icon">
                                                <i class="fas fa-chevron-circle-down fa-icon"></i>
                                            </div>
                                        </div>
                                        <div class="faq-block__body collapse" id="faq-block-10-1">
                                            <div class="faq-block__body-description">
                                                <p><?php echo lang("an_smm_panel_is_an_online_shop_that_you_can_visit_to_puchase_smm_services_at_great_prices"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="faq-block__card">
                                    <div class="card">
                                        <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-2" aria-expanded="false" aria-controls="#faq-block-10-2">
                                            <div class="faq-block__header-title">
                                                <h4><i class="far fa-question-circle"></i> <?php echo lang("what_smm_services_can_i_find_on_this_panel"); ?></h4>
                                            </div>
                                            <div class="faq-block__header-icon">
                                                <i class="fas fa-chevron-circle-down fa-icon"></i>
                                            </div>
                                        </div>
                                        <div class="faq-block__body collapse" id="faq-block-10-2">
                                            <div class="faq-block__body-description">
                                                <p><?php echo lang("we_sell_different_types_of_smm_services__likes_followers_views_etc"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="faq-block__card">
                                    <div class="card">
                                        <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-3" aria-expanded="false" aria-controls="#faq-block-10-3">
                                            <div class="faq-block__header-title">
                                                <h4><i class="far fa-question-circle"></i> <?php echo lang("are_smm_services_on_your_panel_safe_to_buy"); ?></h4>
                                            </div>
                                            <div class="faq-block__header-icon">
                                                <i class="fas fa-chevron-circle-down fa-icon"></i>
                                            </div>
                                        </div>
                                        <div class="faq-block__body collapse" id="faq-block-10-3">
                                            <div class="faq-block__body-description">
                                                <p><?php echo lang("sure_your_accounts_wont_get_banned"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="faq-block__card">
                                    <div class="card">
                                        <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-4" aria-expanded="false" aria-controls="#faq-block-10-4">
                                            <div class="faq-block__header-title">
                                                <h4><i class="far fa-question-circle"></i> <?php echo lang("how_does_a_mass_order_work"); ?></h4>
                                            </div>
                                            <div class="faq-block__header-icon">
                                                <i class="fas fa-chevron-circle-down fa-icon"></i>
                                            </div>
                                        </div>
                                        <div class="faq-block__body collapse" id="faq-block-10-4">
                                            <div class="faq-block__body-description">
                                                <p><?php echo lang("its_possible_to_place_multiple_orders_with_different_links_at_once_with_the_help_of_the_mass_order_feature"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="faq-block__card">
                                    <div class="card">
                                        <div class="faq-block__header collapsed" data-toggle="collapse" data-target="#faq-block-10-5" aria-expanded="false" aria-controls="#faq-block-10-5">
                                            <div class="faq-block__header-title">
                                                <h4><i class="far fa-question-circle"></i> <?php echo lang("what_does_dripfeed_mean"); ?></h4>
                                            </div>
                                            <div class="faq-block__header-icon">
                                                <i class="fas fa-chevron-circle-down fa-icon"></i>
                                            </div>
                                        </div>
                                        <div class="faq-block__body collapse" id="faq-block-10-5">
                                            <div class="faq-block__body-description">
                                                <p><?php echo lang("grow_your_accounts_as_fast_as_you_want_with_the_help_of_dripfeed_how_it_works_lets_say_you_want_2000_likes_on_your_post_instead_of_getting_all_2000_at_once_you_can_get_200_each_day_for_10_days"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>                
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="subscriber">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-container">
                        <div class="above-heading text-uppercase"><?php echo lang("newsletter"); ?></div>
                        <h2><?php echo lang("fill_in_the_ridiculously_small_form_below_to_receive_our_ridiculously_cool_newsletter"); ?></h2>
                        <form id="newsletterForm" class="actionFormWithoutToast" action="<?php echo cn("client/subscriber"); ?>"  method="POST">
                            <div class="form-group">
                                <input type="email" class="form-control-input" id="nemail" name="email" required>
                                <label class="label-control" for="nemail"><?php echo lang("email"); ?></label>
                                <div class="help-block with-errors"></div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="form-control-submit-button btn-submit"><?php echo lang("subscribe_now"); ?></button>
                            </div>
                            <div class="form-group mt-20">
                                <div id="alert-message" class="alert-message-reponse"></div>
                            </div>
                        </form>
                    </div> 
                </div> 
            </div> 
        </div>
    </section>

    <style>
        .footer-lang-selector{
            min-width: 130px;
        }
    </style>
    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-col first">
                        <h4><?php echo get_option('website_name'); ?></h4>
                        <p class="p-small"><?php echo lang("all_user_information_is_kept_100_private_and_will_not_be_shared_with_anyone_always_remember_you_are_protected_with_our_panel__most_trusted_smm_panel"); ?></p>

                        <?php
                            $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        ?>
                        <?php 
                            if (!empty($languages)) {
                        ?>
                        <select class="form-custom footer-lang-selector ajaxChangeLanguage" name="ids" data-url="<?=cn('set-language')?>" data-redirect="<?php echo $redirect; ?>">
                            <?php 
                              foreach ($languages as $key => $row) {
                            ?>
                            <option value="<?=$row->ids?>" <?=(!empty($lang_current) && $lang_current->code == $row->code) ? 'selected' : '' ?> ><?=language_codes($row->code)?></option>
                            <?php }?>
                        </select>
                        <?php }?>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="footer-col middle">
                        <h4><?=lang("Quick_links")?></h4>
                        <ul class="list-unstyled li-space-lg p-small">
                            <?php 
                                if (!session('uid')) {
                            ?>
                            <li class="media">
                                <i class="fas fa-chevron-right"></i>
                                <div class="media-body"> <a class="white" href="<?php echo cn()?>"><?php echo lang("Login"); ?></a></div>
                            </li>
                            <li class="media">
                                <i class="fas fa-chevron-right"></i>
                                <div class="media-body"> <a class="white" href="<?php echo cn('auth/signup'); ?>"><?php echo lang("Sign_Up"); ?></a></div>
                            </li>
                            <?php }?>
                            
                        </ul>
                    </div>
                </div> 
                <div class="col-md-4">
                    <div class="footer-col last">
                        <h4>&nbsp;</h4>
                        <ul class="list-unstyled li-space-lg p-small">
                            
                            <li class="media">
                                <i class="fas fa-chevron-right"></i>
                                <div class="media-body"> <a class="white" href="<?php echo cn('terms'); ?>"><?php echo lang("terms__conditions"); ?></a></div>
                            </li>
                            
                            <li class="media">
                                <i class="fas fa-chevron-right"></i>
                                <div class="media-body"> <a class="white" href="<?php echo cn('faq'); ?>"><?php echo lang("FAQs"); ?></a></div>
                            </li>
                            
                            <?php 
                                if (get_option('enable_api_tab')) {
                            ?>
                            <li class="media">
                                <i class="fas fa-chevron-right"></i>
                                <div class="media-body"> <a class="white" href="<?php echo cn('api/docs')?>"><?php echo lang("api_documentation"); ?></a></div>
                            </li>
                            <?php }?>
                            
                        </ul>
                        
                    </div> 
                </div>
            </div>
        </div>
    </div>

    <footer class="footer copyright">
      <div class="container">
        <div class="row align-items-center flex-row-reverse">
            <div class="col-auto ml-lg-auto">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <div class="icon-container">
                        <?php 
                        if (get_option('social_facebook_link')) {
                        ?>
                        <span class="social-icon">
                            <a href="<?php echo get_option('social_facebook_link'); ?>">
                                <i class="fab fa-facebook-square"></i>
                            </a>
                        </span>
                        <?php }?>

                        <?php 
                        if (get_option('social_twitter_link')) {
                        ?>
                        <span class="social-icon">
                            <a href="<?php echo get_option('social_twitter_link'); ?>">
                                <i class="fab fa-twitter-square"></i>
                            </a>
                        </span>
                        <?php }?>

                        <?php 
                        if (get_option('social_pinterest_link')) {
                        ?>
                        <span class="social-icon">
                            <a href="<?php echo get_option('social_pinterest_link'); ?>">
                                <i class="fab fa-pinterest-square"></i>
                            </a>
                        </span>
                        <?php }?> 

                        <?php 
                            if (get_option('social_instagram_link')) {
                        ?>
                        <span class="social-icon">
                            <a href="<?php echo get_option('social_instagram_link'); ?>">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </span>
                        <?php }?>

                        <?php 
                            if (get_option('social_youtube_link')) {
                        ?>
                        <span class="social-icon">
                            <a href="<?php echo get_option('social_youtube_link'); ?>">
                                <i class="fab fa-youtube-square"></i>
                            </a>
                        </span>
                        <?php }?>
                    </div>
                  </div>
                </div>
            </div>
            <?php
                $version = get_field(PURCHASE, ['pid' => 23595718], 'version');
                $version = 'Ver'.$version;
            ?>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0 text-center small">
                <?=get_option('copy_right_content', "Copyright &copy; 2022 - SmartPanel");?> <?=(get_role("admin")) ? $version : "" ?> 
            </div>
        </div>
      </div>
    </footer>

    <div class="modal-infor">
      <div class="modal" id="notification">
        <div class="modal-dialog">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title"><i class="fe fe-bell"></i> <?=lang("Notification")?></h4>
              <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <?=get_option('notification_popup_content')?>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal"><?=lang("Close")?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php 
      include_once 'blocks/script.blade.php';
    ?>