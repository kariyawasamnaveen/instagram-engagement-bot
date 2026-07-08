<?php
  $class_element = app_config('template')['form']['class_element'];
  $class_element_text_emoji = app_config('template')['form']['class_element_text_emoji'];
  
  $form_status = [
    '0' => 'Deactive',
    '1' => 'Active',
  ];
  
  $form_item_category = array_column($items_category, 'name', 'id');

  $form_service_mode = [
    'manual' => 'Manual',
    'api' => 'API',
  ];
  $current_add_type = @$item['add_type'];
  if ($current_add_type == 'internal') {
    $current_add_type = 'manual';
  }
  $elements_header = [
    [
      'label' => form_label('Service name'),
      'element' => form_input(['name' => 'name', 'value' => @$item['name'], 'type' => 'text', 'class' => $class_element, 'data-emojiable' => 'true']),
      'class_main' => "col-md-12 col-sm-12 col-xs-12 emoji-picker-container",
    ],
    [
      'label' => form_label('Category'),
      'element' => form_dropdown('category', $form_item_category, @$item['cate_id'], ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
    [
      'label' => form_label('Mode'),
      'element' => form_dropdown('add_type', $form_service_mode, $current_add_type, ['class' => $class_element]),
      'class_main' => "col-md-12 col-sm-12 col-xs-12",
    ],
  ];

  $form_service_type = app_config('template')['service_type'];

  $form_dripfeed = $form_status;
  ksort($form_dripfeed);

  if (isset($current_add_type) && $current_add_type == 'api') {
    $class_api_fieldset = '';
    $class_manual_fieldset = 'd-none';
  } else {
    $class_api_fieldset = 'd-none';
    $class_manual_fieldset = '';
  }

  $elements_manual_mode = [
    [
      'label' => form_label('Service Type'),
      'element' => form_dropdown('service_type', $form_service_type, @$item['type'], ['class' => $class_element]),
      'class_main' => "manual-mode service-type " . $class_manual_fieldset,
    ],
  ];
  array_unshift($items_provider, ['id' => 0, 'name' => 'Choose Provider']);
  $form_providers = array_column($items_provider, 'name', 'id');

  $items_provider_service = [];
  array_unshift($items_provider_service, ['id' => 0, 'name' => 'Choose Service']);
  $items_provider_service = array_column($items_provider_service, 'name', 'id');
  $elements_api_mode = [
    [
      'label' => form_label('Provider'),
      'element' => form_dropdown('api_provider_id', $form_providers, @$item['api_provider_id'], ['class' => 'ajaxGetServicesFromAPI ' . $class_element]),
      'class_main' => "api-mode " . $class_api_fieldset,
    ],
    [
      'label' => form_label('Service'),
      'element' => form_dropdown('api_service_id', $items_provider_service, @$item['api_service_id'], ['class' => $class_element . ' ajaxGetServiceDetail select-service-item', 'id' => 'select-service-item']),
      'class_main' => "form-group provider-services-list  api-mode " . $class_api_fieldset,
      'type' => "admin-change-provider-service-list",
    ],
  ];

  $form_min = $item['min'] ?? 100;
  $form_max = $item['max'] ?? 50000;
  $form_price = $item['price'] ?? 1;

  $elements_item_detail = [
    [
      'label' => form_label('Prevent duplicate links ' . render_tooltip_popover_html('Duplicate links are not allowed when an existing order with the same service is still Pending, Processing, or In Progress', 'popover', 'right')),
      'element' => form_dropdown('deny_duplicates', $form_status, esc($item['deny_duplicates'] ?? 0), ['class' => $class_element]),
      'class_main' => "col-md-12" ,
    ],
    [
      'element' => form_input(['name' => 'overflow', 'value' => esc($item['overflow'] ?? 0), 'type' => 'number', 'class' => $class_element]),
      'class_main' => "col-md-12",
    ],
    [
      'label' => form_label('Tags (e.g., female, male, music, action:follow)'),
      'element' => form_input(['name' => 'tags', 'value' => esc($item['tags'] ?? ''), 'type' => 'text', 'class' => $class_element, 'placeholder' => 'Enter tags separated by comma. Add action:follow for follow automation']),
      'class_main' => "col-md-12",
    ],
  ];

  $elements_item_description = [
    [
      'label' => form_label('Description'),
      'element' => form_textarea(['name' => 'desc', 'value' => htmlspecialchars_decode(@$item['desc'], ENT_QUOTES), 'class' => $class_element_text_emoji]),
      'class_main' => "col-md-12",
    ],
  ];

  $class_drip_feed = '';
  if (!empty($item)) {
    if ($item['type'] != 'default') {
      $class_drip_feed = 'd-none';
    }
  }

  $elements_general = [
    [
      'label' => form_label('Dripfeed'),
      'element' => form_dropdown('dripfeed', $form_dripfeed, esc($item['dripfeed'] ?? 0), ['class' => $class_element]),
      'class_main' => "drip-feed-option " . $class_drip_feed,
    ],
  ];

  if (is_table_exists(ORDERS_CANCEL)) {

    $form_cancel_status = [
      1 => 'Active',
      0 => 'Deactive',
    ];

    $elements_general = array_merge($elements_general, [
      [
        'label' => form_label('Cancel Button'),
        'element' => form_dropdown('cancel', $form_cancel_status, esc($item['cancel'] ?? ''), ['class' => $class_element]),
        'class_main' => "cancel-button-option d-none",
      ],
    ]);
  }

  if (!empty($item['id'])) {
    $ids = $item['id'];
    $modal_title = 'Edit Service (ID: ' . $item['id'] . ')';
  } else {
    $modal_title = 'Add new';
  }
  $form_url = admin_url($controller_name . "/store/");
  $redirect_url = '';
  $form_attributes = array('class' => 'form actionForm', 'method' => "POST");
  $form_hidden = [
    'id' => @$item['id'],
    'api_service_id' => @$item['api_service_id'],
    'api_service_data'  => '',
  ];
?>
<style>
  .form-control.select-service-item {
    padding: 0px;
  }
  .form-control.select-service-item .selectize-input{
    font-size: 14px;
    border-radius: 6px;
    margin-bottom: -6px;
  }
</style>

<div id="main-modal-content" class="crud-service-form">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fa fa-edit"></i> <?php echo $modal_title; ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <div class="col-md-12 " id="alert_notification">
              
            </div>
            <?php echo render_elements_form($elements_header); ?>
            <div class="col-md-12">
              <?php
                echo form_fieldset('', ['class' => 'form-fieldset']);
                echo render_elements_form($elements_manual_mode);
                echo render_elements_form($elements_api_mode);
                echo render_elements_form($elements_general);
                echo form_fieldset_close();
              ?>
            </div>
            <?php
              $this->load->view('sync_options', ['item' => $item]);
              echo render_elements_form($elements_item_detail);
              $this->load->view('refill_option', ['item' => $item]);
              echo render_elements_form($elements_item_description);
            ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Save</button>
          <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
        </div>
        <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script>
  $(function() {
    window.emojiPicker = new EmojiPicker({
      emojiable_selector: '[data-emojiable=true]',
      assetsPath: "<?=BASE?>assets/plugins/emoji-picker/lib/img/",
      popupButtonClasses: 'fa fa-smile-o'
    });
    window.emojiPicker.discover();
  });

  $(document).ready(function() {
    $(".text-emoji").emojioneArea({
      pickerPosition: "top",
      tonesStyle: "bullet"
    });
  });
</script>

<script>
  var _token  = '<?php echo strip_tags($this->security->get_csrf_hash()); ?>';
  var pathGetProviderServicesURL  = '<?php echo admin_url($controller_name . '/provider_services/'); ?>';
  /*----------  Load default service with API  ----------*/
  $( document ).ready(function() {
    var percent_input  = $(".percent-input");
    var sync_rate_checkbox  = $(".sync-rate-check-box");
    var sync_min_order_checkbox  = $(".sync-min-order-checkbox");
    var provider_min_value_label  = $(".provider-min-value-label");
    var sync_max_order_checkbox  = $(".sync-max-order-checkbox");
    var provider_max_value_label  = $(".provider-max-value-label");
    var provider_sync_service  = $(".sync_service");
    var original_rate_label  = $(".crud-service-form .original-label");
    var price_input = $(".crud-service-form input[name=price]");
    var min_order_input             = $(".crud-service-form input[name=min]"),
        max_order_input             = $(".crud-service-form input[name=max]");

    if ($('select[name=add_type]').val() == "api") {
      // percent_input.removeClass('d-none');
      sync_rate_checkbox.removeClass('d-none');
      sync_min_order_checkbox.removeClass('d-none');
      provider_min_value_label.removeClass('d-none');
      sync_max_order_checkbox.removeClass('d-none');
      provider_max_value_label.removeClass('d-none');
      provider_sync_service.removeClass('d-none');
      original_rate_label.removeClass('d-none');
      
      $('.provider-services-list').removeClass('d-none');
      $('.provider-services-list .dimmer').addClass('active');
      var id = $('select[name=api_provider_id]').val();
      if (id == "" || id == 0) return;

      var _api_service_id = $('input[name=api_service_id]').val();
      var data        = $.param({token:_token, provider_id:id, provider_service_id:_api_service_id});
      $.post(pathGetProviderServicesURL, data, function(_result) {
        setTimeout(function () {
          $('.provider-services-list .dimmer').removeClass('active');
          $(".provider-services-list select").html(_result);
          
          var _that = $( ".ajaxGetServiceDetail option:selected");
          var api_service_data = _that.attr("data-api_service_infor");
          if (api_service_data) {
            var input_api_service_data = $('input[name=api_service_data]');
            input_api_service_data.val(api_service_data);
            var api_service_data = JSON.parse(api_service_data);
            var _rate = api_service_data.rate;
            var _min =  api_service_data.min;
            var _max =  api_service_data.max;

            //Cancel button
            cancel_form_html(api_service_data, 'auto');

            var _refill = api_service_data.refill,
                provider_min_value_label  = $(".crud-service-form .provider-min-value-label"),
                provider_max_value_label  = $(".crud-service-form .provider-max-value-label"),
                original_price_input  = $(".crud-service-form input[name=original_price]");
            if (_refill == 0) {
              $(".refill-type-option option[value='1']").remove();
            }
            original_price_input.val(_rate);
            provider_min_value_label.html(_min);
            provider_max_value_label.html(_max);
          }

          if (!$('.select-service-item').hasClass('selectize-control')) {
            $('.select-service-item').selectize();
          }

        }, 100);
      });
      return false;
    } else {
      percent_input.addClass('d-none');
      sync_rate_checkbox.addClass('d-none');
      sync_min_order_checkbox.addClass('d-none');
      provider_min_value_label.addClass('d-none');
      sync_max_order_checkbox.addClass('d-none');
      provider_max_value_label.addClass('d-none');
      provider_sync_service.addClass('d-none');
      original_rate_label.addClass('d-none');

      price_input.removeAttr("readonly", "readonly");
      min_order_input.removeAttr("readonly", "readonly");
      max_order_input.removeAttr("readonly", "readonly");
    }
  });
</script>
