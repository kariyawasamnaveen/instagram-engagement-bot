"use strict";
function General() {
    var self = this;
    this.init = function () {
        self.General();
        self.AddFunds();

        if ($("#order_resume").length > 0) {
            self.CalculateOrderCharge();
        }

        if ($(".component-button-refill").length > 0) {
            self.HandleButtonRequest(".component-button-refill .btn-refill");
        }

        if ($(".component-button-cancel").length > 0) {
            self.HandleButtonRequest(".component-button-cancel .btn-cancel");
        }

        if ($(".navbar-side").length > 0) {
            self.MenuOption();
        }

    };

    // For Cancel | Refill
    this.HandleButtonRequest = function (selector) {
        $(document).on("click", selector, function (event) {
            event.preventDefault();
            var element = $(this),
                url = element.attr("href"),
                data = $.param({ token: token });

            $.post(url, data, function (_result) {
                if (_result.status === 'success') {
                    element.replaceWith(_result.btn_text);
                }
            }, 'json');
        });
    };

    this.MenuOption = function () {
        const ps1 = new PerfectScrollbar('.navbar-side .scroll-bar', {
            wheelSpeed: 1,
            wheelPropagation: true,
            minScrollbarLength: 10,
            suppressScrollX: true
        });

        $(document).on("click", ".mobile-menu", function () {
            var _that = $(".navbar.navbar-side");
            if (_that.hasClass('navbar-folded')) {
                _that.removeClass('navbar-folded');
            }
            _that.toggleClass("active");
        });
    }

    this.AddFunds = function () {
        $(document).on("submit", ".actionAddFundsForm", function () {
            pageOverlay.show();
            event.preventDefault();
            var _that = $(this),
                _action = PATH + 'add_funds/process',
                _redirect = _that.data("redirect"),
                _data = _that.serialize();
            _data = _data + '&' + $.param({ token: token });
            $.post(_action, _data, function (_result) {
                setTimeout(function () {
                    pageOverlay.hide();
                }, 1500)
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    if (_result.status == 'success' && typeof _result.redirect_url != "undefined") {
                        window.location.href = _result.redirect_url;
                    }
                    setTimeout(function () {
                        notify(_result.message, _result.status);
                    }, 1500)
                    setTimeout(function () {
                        if (_result.status == 'success' && typeof _redirect != "undefined") {
                            reloadPage(_redirect);
                        }
                    }, 2000)
                } else {
                    setTimeout(function () {
                        $(".add-funds-form-content").html(_result);
                    }, 100)
                }
            })
            return false;
        })
    }

    this.CalculateOrderCharge = function () {

        // callback ajax_custom_lists
        $(document).on("keyup", ".ajax_custom_lists", function () {
            var _quantity = $("#new_order .order-usernames-custom textarea[name=usernames_custom]").val();
            if (_quantity == "") {
                _quantity = 0;
            } else {
                _quantity = _quantity.trim().split("\n").filter(line => line.trim() !== "").length;
            }

            var _service_id = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            var _service_max = $("#order_resume input[name=service_max]").val();
            var _service_min = $("#order_resume input[name=service_min]").val();
            var _service_price = $("#order_resume input[name=service_price]").val();

            var _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price) / 1000 : 0;
            _total_charge = preparePrice(_total_charge);
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })
    }

    this.General = function () {
        /*----------  View User/back to admin----------*/
        $(document).on("click", ".ajaxViewUser", function () {
            event.preventDefault();
            pageOverlay.show();
            var element = $(this),
                url = element.attr("href"),
                data = $.param({ token: token });
            callPostAjax(element, url, data, '');
        })

        // Insert hyper-link
        $(document).on('focusin', function (e) {
            if ($(event.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });

        // load ajax-Modal
        $(document).on("click", ".ajaxModal", function () {
            var element = $(this);
            var url = element.attr("href");
            $('#modal-ajax').load(url, function () {
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        });

        /*----------  ajaxChangeTicketSubject  ----------*/
        $(document).on("change", ".ajaxChangeTicketSubject", function () {
            event.preventDefault();
            var element = $(this);
            var type = element.val();
            switch (type) {

                case "subject_order":
                    $("#add_new_ticket .subject-order").removeClass("d-none");
                    $("#add_new_ticket .subject-payment").addClass("d-none");
                    break;

                case "subject_payment":
                    $("#add_new_ticket .subject-order").addClass("d-none");
                    $("#add_new_ticket .subject-payment").removeClass("d-none");
                    break;

                default:
                    $("#add_new_ticket .subject-order").addClass("d-none");
                    $("#add_new_ticket .subject-payment").addClass("d-none");
                    break;
            }
        })

        // ajaxChangeLanguage (footer top)
        $(document).on("change", ".ajaxChangeLanguage", function () {
            event.preventDefault();
            var element = $(this);
            var pathname = element.data("url") + "?" + "ids=" + element.val() + "&" + "redirect=" + element.data("redirect");
            window.location.href = pathname;
        })

        // ajaxChangeLanguageSecond (header top)
        $(document).on("click", ".ajaxChangeLanguageSecond", function () {
            event.preventDefault();
            var element = $(this);
            var pathname = element.data("url") + "?" + "ids=" + element.data("ids") + "&" + "redirect=" + element.data("redirect");
            window.location.href = pathname;
        })

        // callback ajaxChange
        $(document).on("change", ".ajaxChange", function () {
            pageOverlay.show();
            event.preventDefault();
            var element = $(this);
            var id = element.val();
            if (id == "") {
                pageOverlay.hide();
                return false;
            }
            var url = element.data("url") + id;
            var data = $.param({ token: token });
            $.post(url, data, function (_result) {
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearch").html(_result);
                }, 100);
            });
        })

        // callback ajaxSearch
        $(document).on("submit", ".ajaxSearchItem", function () {
            pageOverlay.show();
            event.preventDefault();
            var _that = $(this),
                _action = _that.attr("action"),
                _data = _that.serialize();

            _data = _data + '&' + $.param({ token: token });
            $.post(_action, _data, function (_result) {
                setTimeout(function () {
                    pageOverlay.hide();
                    $("#result_ajaxSearch").html(_result);
                }, 300);
            });
        })

        // callback ajaxSearchItemsKeyUp with keyup and Submit from
        var typingTimer;                //timer identifier
        $(document).on("keyup", ".ajaxSearchItemsKeyUp", function () {
            $(window).keydown(function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
            event.preventDefault();
            clearTimeout(typingTimer);
            $(".ajaxSearchItemsKeyUp .btn-searchItem").addClass("btn-loading");
            var _that = $(this),
                _form = _that.closest('form'),
                _action = _form.attr("action"),
                _data = _form.serialize();
            _data = _data + '&' + $.param({ token: token });

            // if ( $("input:text").val().length < 2 ) {
            //     $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
            //     return;
            // }

            typingTimer = setTimeout(function () {
                $.post(_action, _data, function (_result) {
                    setTimeout(function () {
                        $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
                        $("#result_ajaxSearch").html(_result);
                    }, 10);
                });
            }, 1500);

        })

        $(document).on("submit", ".ajaxSearchItemsKeyUp", function () {
            event.preventDefault();
        })

        // callback actionForm
        $(document).on("submit", ".actionForm", function () {
            pageOverlay.show();
            event.preventDefault();
            var _that = $(this),
                _action = _that.attr("action"),
                _redirect = _that.data("redirect");

            const btn_submit = _that.find('button.btn-spinner-border');
            if (btn_submit.length) {
                btn_submit.addClass('loading').prop('disabled', true);
            }

            if ($("#mass_order").hasClass("active")) {
                var _data = $("#mass_order").find("input[name!=mass_order]").serialize();
                var _mass_order_array = [];
                var _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }
                var _data = _data + '&' + $.param({ mass_order: _mass_order_array, token: token });
            } else {
                var _token = _that.find("input[name=token]").val();
                var _data = _that.serialize();
                if (typeof _token == "undefined") {
                    _data = _data + '&' + $.param({ token: token });
                }
            }

            $.post(_action, _data, function (_result) {
                setTimeout(function () {
                    if (btn_submit.length) {
                        btn_submit.removeClass('loading').prop('disabled', false);
                    }
                    pageOverlay.hide();
                }, 1500)

                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    if (_result.status == 'success' && _result.notification_type == 'place-order') {
                        setTimeout(function () {
                            show_success_message_place_order(_result);
                        }, 1000);
                    } else {
                        if ($("#order_resume").length > 0) {
                            if (!$(".order-success").hasClass('d-none')) {
                                $(".order-success").addClass('d-none')
                            }
                        }
                        if ($("#result_notification").length == 0) {
                            setTimeout(function () {
                                notify(_result.message, _result.status);
                            }, 1500);
                        }

                        if (_result.message) {
                            setTimeout(function () {
                                $("#result_notification").html('<div class="alert alert-' + (_result.status == 'success' ? 'success' : 'danger') + '">' + _result.message + '</div>');
                            }, 1500);
                        }
                        if (_result.status == 'success' && typeof _redirect != "undefined") {
                            setTimeout(function () {
                                reloadPage(_redirect);
                            }, 2200);
                        } else {
                            // grecaptcha.reset(); //New V4.1
                        }
                    }
                } else {
                    setTimeout(function () {
                        $("#result_notification").html(_result);
                    }, 1500)
                }
            })
            return false;
        })

        // Show success message on place order page
        function show_success_message_place_order(data) {
            var notification_area = $("#order-message-area");

            var order_detail = data.order_detail;
            notification_area.find(".order-success .id span").html(order_detail.id);
            notification_area.find(".order-success .service_name span").html(order_detail.service_name);
            notification_area.find(".order-success .charge span").html(order_detail.charge);
            notification_area.find(".order-success .balance span").html(order_detail.balance);

            if (data.order_type == 'default') {
                notification_area.find(".order-success .username").addClass('d-none');
                notification_area.find(".order-success .posts").addClass('d-none');
                notification_area.find(".order-success .link").removeClass('d-none');
                notification_area.find(".order-success .quantity").removeClass('d-none');

                notification_area.find(".order-success .link span").html(order_detail.link);
                notification_area.find(".order-success .quantity span").html(order_detail.quantity);
            }

            if (data.order_type == 'subscriptions') {
                notification_area.find(".order-success .username").removeClass('d-none');
                notification_area.find(".order-success .posts").removeClass('d-none');
                notification_area.find(".order-success .link").addClass('d-none');
                notification_area.find(".order-success .quantity").addClass('d-none');

                notification_area.find(".order-success .username span").html(order_detail.username);
                notification_area.find(".order-success .posts span").html(order_detail.posts);
            }
            if ($(".order-success").hasClass('d-none')) {
                $(".order-success").removeClass('d-none')
            }
            $(".user-balance").html(data.user_balance);
        }

        // actionFormWithoutToast
        $(document).on("submit", ".actionFormWithoutToast", function () {
            alertMessage.hide();
            event.preventDefault();
            var _that = $(this),
                _action = _that.attr("action"),
                _data = _that.serialize();
            _data = _data + '&' + $.param({ token: token });
            var _redirect = _that.data("redirect");
            _that.find(".btn-submit").addClass('btn-loading');
            $.post(_action, _data, function (_result) {
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    setTimeout(function () {
                        alertMessage.show(_result.message, _result.status);
                    }, 1500)

                    setTimeout(function () {
                        if (_result.status == 'success' && typeof _redirect != "undefined") {
                            reloadPage(_redirect);
                        }
                    }, 2000)
                } else {
                    setTimeout(function () {
                        $("#resultActionForm").html(_result);
                    }, 1500)
                }

                setTimeout(function () {
                    _that.find(".btn-submit").removeClass('btn-loading');
                }, 1500)
            })
            return false;
        })
    }
}

General = new General();
$(function () {
    General.init();
});


