<?php
  $form_url = admin_url($controller_name."/store/");
  $form_attributes = array('class' => 'form actionForm', 'data-redirect' => get_current_url(), 'method' => "POST");
?>
<div class="card content">
  <div class="card-header">
    <h3 class="card-title"><i class="fe fe-settings"></i> Default Setting</h3>
  </div>
  <?php echo form_open($form_url, $form_attributes); ?>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 col-lg-12">

          <div class="form-group">
            <div class="form-label"><i class="fa fa-arrow-circle-right"></i> Admin Account</div>
            <label class="custom-switch">
              <input type="hidden" name="admin_auto_logout_when_change_ip" value="0">
              <input type="checkbox" name="admin_auto_logout_when_change_ip" class="custom-switch-input" <?=(get_option("admin_auto_logout_when_change_ip", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
            <br>
            <small class="text-danger"><strong><?=lang("note")?></strong> Log admin out when there is a change of IP address</a></small>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-6">
              <h5><i class="fa fa-arrow-circle-right"></i> Pagination</h5>
              <div class="form-group">
                <label>Maximum Number of Rows per Page Limit</label>
                <select name="default_limit_per_page" class="form-control square">
                  <?php
                    for ($i = 1; $i <= 100; $i++) {
                      if ($i%5 == 0) {
                  ?>
                  <option value="<?=$i?>" <?=(get_option("default_limit_per_page", 10) == $i)? "selected" : ''?>><?=$i?></option>
                  <?php }} ?>
                </select>
              </div>
            </div> 
          </div>

          <h5><i class="fa fa-arrow-circle-right"></i> Tickets log (Auto clear ticket lists)</h5>
          <div class="form-group">
            <label class="custom-switch">
              <input type="hidden" name="is_clear_ticket" value="0">
              <input type="checkbox" name="is_clear_ticket" class="custom-switch-input" <?=(get_option("is_clear_ticket", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><?=lang("clear_ticket_lists_after_x_days_without_any_response_from_user")?></label>
                <select  name="default_clear_ticket_days" class="form-control square">
                  <?php
                    $default_clear_ticket_days =  get_option('default_clear_ticket_days', 30); 
                    for ($i = 1; $i <= 90; $i++) { 
                  ?>
                  <option value="<?=$i?>" <?=($default_clear_ticket_days == $i)? 'selected': ''?>> <?=$i?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Max pending tickets per user</label>
                <select  name="default_pending_ticket_per_user" class="form-control square">
                  <?php
                    $default_pending_ticket_per_user =  get_option('default_pending_ticket_per_user', 2);
                    for ($i = 1; $i <= 9; $i++) {
                        $number_ticket_title =  $i . ' ticket';
                      if ($i > 1) {
                        $number_ticket_title =  $i . ' tickets';
                      }
                  ?>
                  <option value="<?=$i?>" <?=($default_pending_ticket_per_user == $i) ? 'selected' : ''?>> <?=$number_ticket_title?></option>
                  <?php } ?>
                  <option value="0" <?=($default_pending_ticket_per_user == 0) ? 'selected' : ''?>> Unlimited</option>
                </select>
              </div>
            </div>
          </div>

          <h5><i class="fa fa-arrow-circle-right"></i> Average time <?= render_tooltip_popover_html('Average completion time is based on the most recent orders', 'Tooltip', 'top')?></h5>
          <div class="form-group">
            <label class="custom-switch">
              <input type="hidden" name="enable_average_time" value="0">
              <input type="checkbox" name="enable_average_time" class="custom-switch-input" <?=(get_option("enable_average_time", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Number of Most Recent Orders for Average Time Calculation  <?= render_tooltip_popover_html('Select the number of most recently completed orders used to calculate the average completion time', 'Tooltip', 'top')?></label>
                
                <select  name="default_orders_for_avg_time" class="form-control square">
                  <?php
                    $default_orders_for_avg_time  =  get_option('default_orders_for_avg_time ', 10); 
                  ?>
                    <?php for ($i = 1; $i <= 90; $i++) : ?>
                      <option value="<?=$i?>" <?=($default_orders_for_avg_time == $i)? 'selected': ''?>> <?=$i?></option>
                    <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>
          
          
          <h5><i class="fa fa-arrow-circle-right"></i> <?=lang("notification_popup_at_home_page")?></h5>
          <div class="form-group">
            <label class="custom-switch">
              <input type="hidden" name="enable_notification_popup" value="0">
              <input type="checkbox" name="enable_notification_popup" class="custom-switch-input" <?=(get_option("enable_notification_popup", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
              <label class="form-label"><?=lang("Content")?></label>
              <textarea rows="3" name="notification_popup_content" id="notification_popup_content" class="form-control plugin_editor"><?=get_option('notification_popup_content', "<p><strong>Lorem Ipsum</strong></p><p>Lorem ipsum dolor sit amet, in eam consetetur consectetuer. Vivendo eleifend postulant ut mei, vero maiestatis cu nam. Qui et facer mandamus, nullam regione lucilius eu has. Mei an vidisse facilis posidonium, eros minim deserunt per ne.</p><p>Duo quando tibique intellegam at. Nec error mucius in, ius in error legendos reformidans. Vidisse dolorum vulputate cu ius. Ei qui stet error consulatu.</p><p>Mei habeo prompta te. Ignota commodo nam ei. Te iudico definitionem sed, placerat oporteat tincidunt eu per, stet clita meliore usu ne. Facer debitis ponderum per no, agam corpora recteque at mel.</p>")?>
              </textarea>
            </div>
            </div>
          </div>

          <hr>
          <h5><i class="fa fa-arrow-circle-right"></i> Note at User Order page</h5>
          <div class="form-group">
            <label>Title attentions at order page </label>
            <input class="form-control" name="title_attentions_orderpage" value="<?=get_option('title_attentions_orderpage', "Guides & Descriptions");?>">
          </div>  

          <div class="form-group">
            <label class="custom-switch">
              <input type="hidden" name="enable_attentions_orderpage" value="0">
              <input type="checkbox" name="enable_attentions_orderpage" class="custom-switch-input" <?=(get_option("enable_attentions_orderpage", 0) == 1) ? "checked" : ""?> value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Active</span>
            </label>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
              <label class="form-label">Content</label>
              <textarea rows="3" name="guides_and_desc" class="form-control plugin_editor"><?=get_option('guides_and_desc', "<p><strong>Lorem Ipsum</strong></p><p>Lorem ipsum dolor sit amet, in eam consetetur consectetuer. Vivendo eleifend postulant ut mei, vero maiestatis cu nam. Qui et facer mandamus, nullam regione lucilius eu has. Mei an vidisse facilis posidonium, eros minim deserunt per ne.</p><p>Duo quando tibique intellegam at. Nec error mucius in, ius in error legendos reformidans. Vidisse dolorum vulputate cu ius. Ei qui stet error consulatu.</p><p>Mei habeo prompta te. Ignota commodo nam ei. Te iudico definitionem sed, placerat oporteat tincidunt eu per, stet clita meliore usu ne. Facer debitis ponderum per no, agam corpora recteque at mel.</p>")?>
              </textarea>
            </div>
            </div>
          </div>
          <hr>

          <div class="row">
            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Disable Home page (Langding page)</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_disable_homepage" value="0">
                  <input type="checkbox" name="enable_disable_homepage" class="custom-switch-input" <?=(get_option("enable_disable_homepage", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i>  Disable Signup Page</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="disable_signup_page" value="0">
                  <input type="checkbox" name="disable_signup_page" class="custom-switch-input" <?=(get_option("disable_signup_page", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>
            
            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i>  Displays the service lists without login or register</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_service_list_no_login" value="0">
                  <input type="checkbox" name="enable_service_list_no_login" class="custom-switch-input" <?=(get_option("enable_service_list_no_login", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Displays News & Announcement feature</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_news_announcement" value="0">
                  <input type="checkbox" name="enable_news_announcement" class="custom-switch-input" <?=(get_option("enable_news_announcement", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Displays API tab in header</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_api_tab" value="0">
                  <input type="checkbox" name="enable_api_tab" class="custom-switch-input" <?=(get_option("enable_api_tab", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Displays the required Skype field for the signup page</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_signup_skype_field" value="0">
                  <input type="checkbox" name="enable_signup_skype_field" class="custom-switch-input" <?=(get_option("enable_signup_skype_field", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-6">
              <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Displays the required WhatsApp field for the signup page</h5>
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_signup_whatsapp_field" value="0">
                  <input type="checkbox" name="enable_signup_whatsapp_field" class="custom-switch-input" <?=(get_option("enable_signup_whatsapp_field", 1) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

          </div>
          <hr>
          <h5 class="m-t-10"><i class="fa fa-arrow-circle-right"></i> Displays Google reCAPTCHA</h5>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="enable_goolge_recapcha" value="0">
                  <input type="checkbox" name="enable_goolge_recapcha" class="custom-switch-input" <?=(get_option("enable_goolge_recapcha", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Google reCAPTCHA site key</label>
                <input class="form-control" name="google_capcha_site_key" value="<?=get_option('google_capcha_site_key', '')?>">
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Google reCAPTCHA serect key</label>
                <input class="form-control" name="google_capcha_secret_key" value="<?=get_option('google_capcha_secret_key', '')?>">
              </div>
            </div>

          </div>
          
        </div> 
      </div>
    </div>
    <div class="card-footer text-end">
      <button class="btn btn-primary btn-min-width text-uppercase"><?=lang("Save")?></button>
    </div>
  <?php echo form_close(); ?>
</div>

<script>
  $(document).ready(function() {
    plugin_editor('.plugin_editor', {height: 200});
  });
</script>