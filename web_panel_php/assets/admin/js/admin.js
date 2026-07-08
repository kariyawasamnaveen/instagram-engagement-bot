"use strict";
function Admin() {
    var self = this;
    this.init = function () {
        self.ScriptLicense();
        self.Item();
        self.Users();
        self.FileManager();
        self.Provider();
        self.Services();
        if ($(".sortable-content").length > 0) {
            self.sortItems();
        }
        if ($(".service-sortable").length > 0) {
            self.sortItems();
        }
    };

    // Sort items by Sort-table - plugin
    this.sortItems = function () {
        //for category
        $(".sortable-content tbody").sortable({
            handle: '.sort-handler',
            update: function (event, ui) {
                var array = [];
                $(this).find('tr').each(function (i) {
                    $(this).attr('data-sort', i + 1);
                    var params = {};
                    params['id'] = $(this).attr('data-id');
                    params['sort'] = $(this).attr('data-sort');
                    array.push(params);
                });
                var _url = $(".sortable-content table").data('sort_table_url');
                var data = {
                    action: 'sort_table',
                    token: token,
                    items: array
                };
                callPostAjax($(this), _url, data, 'sort-table');
            }
        });

        //for Services page
        $(".service-sortable tbody").sortable({
            handle: '.sort-handler',
            update: function (event, ui) {
                var array = [];
                $(this).find('tr').each(function (i) {
                    $(this).attr('data-sort', i + 1);
                    var params = {};
                    params['id'] = $(this).attr('data-id');
                    params['sort'] = $(this).attr('data-sort');
                    params['cate_id'] = $(this).attr('data-cate_id');
                    array.push(params);
                });
                var _url = PATH + 'admin/services/sort_table';
                var data = {
                    action: 'sort_table',
                    token: token,
                    items: array
                };
                callPostAjax($(this), _url, data, 'sort-table');
            }
        });
    }

    this.Services = function () {
        // Check post type
        var pathGetProviderServicesURL = PATH + 'admin/services/provider_services/';
        // Min Order
        var sync_min_order_input  = '.crud-service-form input[name=sync_min]',
            sync_max_order_input  = '.crud-service-form input[name=sync_max]';

        $(document).on("change", "select[name=add_type]", function () {
            var element = $(this),
                mode = element.val();
            var provider_mode = $('.api-mode'),    
                manual_mode = $('.manual-mode');    
            if (mode == 'api') {
                provider_mode.removeClass('d-none');
                manual_mode.addClass('d-none');
            } else {
                manual_mode.removeClass('d-none');
                provider_mode.addClass('d-none');
                // Refill option
                refill_form_html();
                sync_params_with_provider_html('manual');
            }
        });

        // Service Type
        $(document).on("change", ".manual-mode select[name=service_type]", function () {
            dripfeed_form_html($(this).val());
        });

        function dripfeed_form_html(service_type) {
            var dripfeed_option = $('.drip-feed-option');    
            if (service_type.toLowerCase() != 'default') {
                dripfeed_option.addClass('d-none');
            } else {
                dripfeed_option.removeClass('d-none');
            }
        }
        
        /*----------  Get Services list from API  ----------*/
        $(document).on("change", ".ajaxGetServicesFromAPI", function () {
            event.preventDefault();
            $('.provider-services-list').removeClass('d-none');
            $('.provider-services-list .dimmer').addClass('active');

            var element = $(this),
                id = element.val();
            if (id == "" || id == 0) return;
            var data = $.param({ token: token, provider_id: id });
            $.post(pathGetProviderServicesURL, data, function (_result) {
                $('#select-service-item').selectize()[0].selectize.destroy();
                setTimeout(function () {
                    reset_service_attr();
                    $('.provider-services-list .dimmer').removeClass('active');
                    $(".provider-services-list select").html(_result);
                    $('#select-service-item').selectize(); 
                }, 100);
            });
        })

        $(document).on("change", ".ajaxGetServicesFromAPI_old", function () {
            event.preventDefault();
            $('.provider-services-list').removeClass('d-none');
            $('.provider-services-list .dimmer').addClass('active');

            var element = $(this),
                id = element.val();
            if (id == "" || id == 0) return;
            var data = $.param({ token: token, provider_id: id });
            $.post(pathGetProviderServicesURL, data, function (_result) {
                $('#select-service-item').selectize()[0].selectize.destroy();
                setTimeout(function () {
                    reset_service_attr();

                    $('.provider-services-list .dimmer').removeClass('active');
                    $(".provider-services-list select").html(_result);
                    $('#select-service-item').selectize(); 
                }, 100);
            });
        })

        // Get Provider Service detail
        $(document).on("change", ".ajaxGetServiceDetail", function () {
            var service_id = $(".ajaxGetServiceDetail .selectize-input.has-items .item").attr('data-value');
            var api_id = $('select[name=api_provider_id]').val();
            var data = $.param({
                token:_token, 
                provider_id:api_id, 
                provider_service_id:service_id,
                action: 'get-service-detail',
            }); 
            reset_service_attr();
            
            $.post(pathGetProviderServicesURL, data, function(_result) {
                if (is_json(_result)) {
                    var result = JSON.parse(_result);
                    var api_service_data = $('input[name=api_service_data]');
                    api_service_data.val(_result);

                    // Dripfeed Option
                    dripfeed_form_html(result.type);
                    // Dripfeed Option
                    cancel_form_html(result);
                    // Paste all value to input
                    sync_params_with_provider_html('api', result);
                    // auto check sync min, max, rate
                    sync_min_max_order_check_box(this, 'auto-check', 'min');
                    sync_min_max_order_check_box(this, 'auto-check', 'max');
                    sync_rate_html('', 'auto-check');
                    // Refill Option
                    refill_form_html(result.refill);
                    $("#alert_notification").html('');
                } else {
                    // console.log(_result);
                    // $("#alert_notification").html('<div class="alert alert-warning" role="alert">The Service field is required</div>');
                }
            });
            return false;
        })

        function reset_service_attr() {
            $('select[name="dripfeed"]').val('0').trigger('change');
            $('select[name="cancel"]').val('0');
            $('.cancel-button-option').addClass('d-none');

            // Refill Option
            $(".refill-type-option").html('<option value="0"> Manual </option>');
            $("#refill-option").prop('checked', false);
            $("#refill-from").removeClass('show');

            // Reset option null
            $(".crud-service-form input[name=original_price]").val('');
            $(".crud-service-form input[name=api_service_type]").val('');
            $(".crud-service-form input[name=api_service_dripfeed]").val('');
            $(".crud-service-form input[name=api_service_refill]").val('');
            $(".crud-service-form input[name=api_service_id]").val('');
        };
        
        $(document).on('click', sync_min_order_input, function () {
            sync_min_max_order_check_box(this, 'click', 'min');
        })
        // Max Order
        $(document).on('click', sync_max_order_input, function () {
            sync_min_max_order_check_box(this, 'click', 'max');
        })
        // Sync Rate Check box
        $(document).on('click', '.provider-sync-rate', function () {
            sync_rate_html(this, 'click');
        })
        
        // Auto Rate Percent Input
        $(document).on("input", ".crud-service-form input[name=auto_rate_percent]" , function() {
            auto_sync_rate();
        })

        function refill_form_html(is_refill = null) {
            var refill_options = $(".refill-type-option");
            refill_options.html('<option value="0"> Manual </option>');
            $("#refill-option").prop('checked', false);
            $("#refill-from").removeClass('show');
            if (is_refill) {
                refill_options.append('<option value="1"> Provider </option>');
            }
        }

        function sync_rate_html(element, type) {
            var percent_input_form = $(".crud-service-form .percent-input"),
                price_input = $(".crud-service-form input[name=price]"),
                sync_rate_checkbox = $(".crud-service-form input[name=sync_rate]");

            if (type == 'click') {
                if (element.checked) {
                    percent_input_form.removeClass('d-none');
                    price_input.attr("readonly","readonly");
                    auto_sync_rate();
                } else {
                    percent_input_form.addClass('d-none');
                    price_input.removeAttr("readonly","readonly");
                }
            } else if (type == 'auto-check') {
                sync_rate_checkbox.prop('checked', true);
                auto_sync_rate();
            }
        }

        function auto_sync_rate() {
            var auto_rate_percent_input = $(".crud-service-form input[name=auto_rate_percent]"),
                price_input = $(".crud-service-form input[name=price]"),
                original_rate_input = $(".crud-service-form input[name=original_price]");
            var auto_rate_percent_val = auto_rate_percent_input.val(),
                original_rate_val = original_rate_input.val();
            if (auto_rate_percent_val > 0) {
                var new_rate = original_rate_val * (1 + (auto_rate_percent_val / 100));
            } else {
                var new_rate = original_rate_val;
            }
            new_rate = preparePrice(new_rate);
            price_input.val(parseFloat(new_rate));
        }

        function sync_min_max_order_check_box(element, type, name_input, value) {
            var sync_input = $(".crud-service-form input[name=sync_" + name_input + "]"),
                input = $(".crud-service-form input[name=" + name_input + "]"),
                label = $(".provider-" + name_input + "-value-label");
            switch (type) {
                case 'click':
                        if (!element.checked) {
                            input.val(value);
                            input.removeAttr("readonly","readonly");
                        } else {
                            input.attr("readonly","readonly");
                            input.val(label.text());
                        }
                    break;
                case 'auto-check':
                        sync_input.prop('checked', true);
                        input.attr("readonly","readonly");
                    break;
            }
        }

        function sync_params_with_provider_html(type, inputs_value = []) {
            // Form
            var percent_input  = $(".crud-service-form .percent-input"),
                sync_rate_checkbox  = $(".crud-service-form .sync-rate-check-box"),
                sync_min_order_checkbox  = $(".crud-service-form .sync-min-order-checkbox"),
                sync_max_order_checkbox  = $(".crud-service-form .sync-max-order-checkbox"),
                provider_min_value_label  = $(".crud-service-form .provider-min-value-label"),
                provider_max_value_label  = $(".crud-service-form .provider-max-value-label"),
                provider_sync_service  = $(".crud-service-form .sync_service"),
                original_rate_label  = $(".crud-service-form .original-label");

            // value
            var price_input                 = $(".crud-service-form input[name=price]"),
                original_price_input        = $(".crud-service-form input[name=original_price]"),
                original_price_label        = $(".crud-service-form .original-label .value"),
                min_order_input             = $(".crud-service-form input[name=min]"),
                max_order_input             = $(".crud-service-form input[name=max]"),
                api_service_type_input      = $(".crud-service-form input[name=api_service_type]"),
                api_service_dripfeed_input  = $(".crud-service-form input[name=api_service_dripfeed]"),
                api_service_refill_input    = $(".crud-service-form input[name=api_service_refill]");

            if (type == 'api') {
                // Prepare HTML
                percent_input.removeClass('d-none');
                sync_rate_checkbox.removeClass('d-none');
                sync_min_order_checkbox.removeClass('d-none');
                provider_min_value_label.removeClass('d-none');
                sync_max_order_checkbox.removeClass('d-none');
                provider_max_value_label.removeClass('d-none');
                provider_sync_service.removeClass('d-none');
                original_rate_label.removeClass('d-none');
                // Paste value to all input
                original_price_input.val(inputs_value.rate);
                api_service_type_input.val(inputs_value.type);
                api_service_dripfeed_input.val(inputs_value.dripfeed);
                api_service_refill_input.val(inputs_value.refill);
                
                min_order_input.val(inputs_value.min);
                max_order_input.val(inputs_value.max);
                provider_min_value_label.html(inputs_value.min);
                provider_max_value_label.html(inputs_value.max);
                price_input.val(inputs_value.rate);
                original_price_label.html(inputs_value.rate);
            } else {
                price_input.val('');
                min_order_input.val('');
                max_order_input.val('');

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

                $(".crud-service-form input[name=min]").removeAttr("disabled");
                $(".crud-service-form input[name=max]").removeAttr("disabled");
            }
        }

        // Filter Services
        function filter_services() {
            const keyword = $('#search-input-with-select input[type="text"]').val().trim().toLowerCase();
            const selectedCate = $('.search-by-category').val();
            const searchField = $('#search-input-with-select select').val();
      
            $('.card').each(function () {
              let anyVisible = false;
              $(this).find('tbody tr.service-item').each(function () {
                const cateId = ($(this).data('cate-id') || '').toString();
                const data = {};
                searchFieldsConfig.forEach(field => {
                  data[field.key] = ($(this).data(field.dataKey) || '').toString().toLowerCase();
                });
                let matchKeyword = false;
                if (!keyword) {
                  matchKeyword = true;
                } else if (searchField === 'all') {
                  matchKeyword = searchFieldsConfig.some(field => data[field.key].includes(keyword));
                } else {
                  matchKeyword = data[searchField] && data[searchField].includes(keyword);
                }
      
                const matchCategory = selectedCate === "0" || cateId === selectedCate;
      
                if (matchKeyword && matchCategory) {
                  $(this).show();
                  anyVisible = true;
                } else {
                  $(this).hide();
                }
              });
              $(this).toggle(anyVisible);
            });

            let anyCardVisible = $('.card:visible').length > 0;
            if (!anyCardVisible) {
                $('.data-empty').removeClass('d-none');
            } else {
                $('.data-empty').addClass('d-none');
            }
        }

        $(document).on("change", ".search-by-category", function () {
            filter_services();
        })

        $(document).on("change", '#search-input-with-select select', function () {
            filter_services();
        })

        $(document).on("input", '#search-input-with-select input[type="text"]', function () {
            filter_services();
        })

        // Services collapse
        $(document).on("click", ".btn-services-collapse", function () {
            var element = $(this),
                items_by_category_area = $(".items-by-category .card");
            if (items_by_category_area.hasClass('card-collapsed')) {
                element.html('<span class="fe fe-chevrons-up"></span> Hide All');
                items_by_category_area.removeClass('card-collapsed');
            } else {
                element.html('<span class="fe fe-chevrons-down"></span> Show All');
                items_by_category_area.addClass('card-collapsed');
            }
        })
    }

    this.Provider = function () {
        // Update balance
        $(document).on("click", ".ajaxUpdateApiProvider", function () {
            pageOverlay.show();
            event.preventDefault();
            var element = $(this),
                url = element.attr("href"),
                redirect_url = element.data("redirect"),
                data = $.param({ token: token });
            callPostAjax(element, url, data, '');
        })
    }

    this.Users = function () {
        $(document).on("click", ".btnEditCustomRate", function () {
            var element = $(this),
                url = element.data("action");
            $('#customRate').load(url, function () {
                $('#customRate').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#customRate').modal('show');
            });
            return false;
        });
    }

    this.ScriptLicense = function () {
        $(document).on("click", ".ajaxUpgradeVersion", function () {
            pageOverlay.show();
            event.preventDefault();
            var element = $(this),
                url = element.attr("href"),
                data = $.param({ token: token });
            callPostAjax(element, url, data, '');
        })
    }

    // Upload media on Settings page
    this.FileManager = function () {
        var url = PATH + "upload_files";
        $(document).on('click', '.settings_fileupload', function () {
            var element = $(this);
            var _closest_div = element.closest('div');
            $('.settings .settings_fileupload').fileupload({
                url: url,
                formData: { token: token },
                dataType: 'json',
                done: function (e, data) {
                    if (data.result.status == "success") {
                        var _img_link = data.result.link;
                        _closest_div.children('input').val(_img_link);
                    }
                },
            });
        });
    }

    this.Item = function () {
        // Count items secleted with check all
        var cardHeaderTitle = $('.massAction .card-title'),
            cardHeaderBtnActions = $('.massAction .btnActions'),
            cardHeaderActionOptionArea = $('.action-options'),
            btnActionsDropdownMenu = $('.massAction .btnActions .action-options .dropdown-menu');

        function setItemsSecletedText() {
            // show the number of selected items
            cardHeaderTitle.addClass('d-none');
            cardHeaderBtnActions.removeClass('d-none');
            btnActionsDropdownMenu.removeClass('dropdown-menu-right');
            btnActionsDropdownMenu.addClass('dropdown-menu-left');

            var $count = 0;
            $(".check-item:checked").each(function () {
                ++$count;
            });
             
            if ($count > 0) {
                cardHeaderActionOptionArea.removeClass('d-none');
            } else {
                cardHeaderActionOptionArea.addClass('d-none');
            }

            $('.btnActions .number-items-selected').html($count + ' items ' + ' secleted');
        }

        function toggleActionLinksByStatus() {
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');
            
            // Check if there is at least one element with data-type
            if ($('.dropdown-menu-left [data-type]').length) {
                // Loop through all elements with data-type
                $('.dropdown-menu-left [data-type]').each(function () {
                    const type = $(this).data('type');
                    
                    // If status is 'error'
                    if (status === 'error') {
                        // Show 'resend' and hide 'copy_api_order_id'
                        if (type === 'resend') {
                            $(this).removeClass('d-none');  // Show resend
                        } else if (type === 'copy_api_order_id') {
                            $(this).addClass('d-none');     // Hide copy_api_order_id
                        } else {
                            $(this).removeClass('d-none');  // Show all other links
                        }
                    } else {
                        // If status is not 'error', hide 'resend' and show all other links
                        if (type === 'resend') {
                            $(this).addClass('d-none');     // Hide resend
                        } else {
                            $(this).removeClass('d-none');  // Show all other links
                        }
                    }
                });
            }
        }

        // Change the text when click each check item
        $(document).on('click', '.check-item', function () {
            setItemsSecletedText();
        })

        // check all
        $(document).on('click', '.check-all', function () {
            var element = $(this),
                _checkName = element.data('name');
            $('.' + _checkName + '').prop('checked', this.checked);
            // show action button
            if (element.is(":checked")) {
                //Count items secleted
                setItemsSecletedText();
                toggleActionLinksByStatus();
            } else {
                cardHeaderTitle.removeClass('d-none');
                cardHeaderBtnActions.addClass('d-none');
            }
        })

        // ajaxChangeCurrencyCode - Payment update form
        $(document).on("change", ".ajaxChangeCurrencyCode", function () {
            var element = $(this),
                currency_code = element.val();
            $(".new-currency-code").html(currency_code);
        });

        // ajaxToggleItemStatus
        $(document).on("click", ".ajaxToggleItemStatus", function () {
            var element = $(this),
                id = element.data('id'),
                url = element.data('action') + id;
            var status = 0;
            if (element.is(":checked")) status = 1;
            var data = $.param({ token: token, status: status });
            callPostAjax(element, url, data, 'status');
        });

        // ajaxChangeSort
        $(document).on("change", ".ajaxChangeSort", function () {
            var element = $(this),
                id = element.data('id'),
                url = element.data('url') + id,
                sort = element.val();
            var data = $.param({ token: token, sort: sort });
            callPostAjax(element, url, data, 'sort');
        });

        // callback Delete item
        $(document).on("click", ".ajaxDeleteItem", function () {
            event.preventDefault();
            var element = $(this),
                confirm_message = element.data('confirm_ms');
            if (!confirm_notice(confirm_message)) {
                return;
            }
            var url = element.attr("href"),
                data = $.param({ token: token });
            callPostAjax(element, url, data, 'delete-item');
        });

        $(document).on("click", ".ajaxActionOptions", function () {
            event.preventDefault();
            var element = $(this),
                type = element.data("type");
            if ((type == 'delete' || type == 'all_deactive' || type == 'clear_all' || type == 'empty')) {
                if (!confirm_notice('deleteItems')) {
                    return;
                }
            }
            var url = element.attr("href");
            var selected_ids = [];
            $(".check-item:checked").each(function () {
                selected_ids.push($(this).val());
            });
            if (selected_ids.length <= 0 && type != 'empty') {
                alert("Please choose at least one item");
            } else {
                selected_ids = selected_ids.join(",");
                var data = 'ids=' + selected_ids + '&' + $.param({ token: token });
                pageOverlay.show();
                var type_post = '';
                var array_type_copy_clipboard = ['copy_id', 'copy_order_id', 'copy_api_refill_id', 'copy_api_order_id', 'copy_api_order_id'];
                if (array_type_copy_clipboard.includes(type) === true) {
                    type_post = 'copy-to-clipboard';
                }
                callPostAjax(element, url, data, type_post);
            }
        })

        // callback ajaxChange Sort By
        $(document).on("change", ".ajaxListServicesSortByCateogry", function () {
            pageOverlay.show();
            event.preventDefault();
            var element = $(this),
                id = element.val();

            if (id == "") {
                pageOverlay.hide();
                return false;
            }
            var pathname = element.data("url") + "?" + "sort_by=" + id;
            window.location.href = pathname;
        })

        // callback ajaxChange
        $(document).on("change", ".ajaxGetServicesChangeByProvider", function () {
            event.preventDefault();
            pageOverlay.show();
            var element = $(this),
                id = element.val();
            if (id == 0) {
                pageOverlay.hide();
                return false;
            }
            var url = element.data("url") + id;
            var data = $.param({ token: token });
            callPostAjax(element, url, data, 'get-result-html');
        })
    }
}

Admin = new Admin();
$(function () {
    Admin.init();
});


function cancel_form_html(service, select_type = 'manual') {
    var cancel_button_option = $('.cancel-button-option');
    var cancelSelect = $('.cancel-button-option select[name="cancel"]');
    
    if (service.cancel) {
        if (cancelSelect.val() !== '1' && select_type == 'manual') {
            cancelSelect.val('1'); 
        }
        cancel_button_option.removeClass('d-none');
    } else {
        cancel_button_option.addClass('d-none');
    }
}


