"use strict";
$(document).ready(function () {
    const services_list_object = {};

    services_list.forEach(function (service) {
        services_list_object[service.id] = service;
    });

    var searchServiceArea = $('.ajaxSearchService');
    var categorySelect = $('.ajaxChangeCategory');
    var serviceSelect = $('.ajaxChangeService');

    // New Order Form
    if (searchServiceArea.length > 0) {
        searchServiceArea.selectize();

        const new_order_elements1111 = {
            defaultLink: $("#new_order .order-default-link"),
            defaultQuantity: $("#new_order .order-default-quantity"),
            inputQuantity: $("#new_order .order-default-quantity input[name=quantity]"),
            comments: $("#new_order .order-comments"),
            commentsCustomPackage: $("#new_order .order-comments-custom-package"),
            subscriptions: $("#new_order .order-subscriptions"),
            usernamesCustom: $("#new_order .order-usernames-custom"),
            usernames: $("#new_order .order-usernames"),
            hashtags: $("#new_order .order-hashtags"),
            username: $("#new_order .order-username"),
            hashtag: $("#new_order .order-hashtag"),
            media: $("#new_order .order-media"),
            scheduleOption: $('.schedule-option'),
            resultTotalCharge: $("#new_order #result_total_charge"),
            dripFeedOption: $("#new_order .drip-feed-option"),
            totalCharge: $("#new_order input[name=total_charge]"),
            currencySymbol: $("#new_order input[name=currency_symbol]"),
            agree: $('#new_order input[type="checkbox"]'),
        };

        const new_order_elements = {
            defaultLink: $("#new_order .order-default-link"),
            defaultQuantity: $("#new_order .order-default-quantity"),
            comments: $("#new_order .order-comments"),
            commentsCustomPackage: $("#new_order .order-comments-custom-package"),
            subscriptions: $("#new_order .order-subscriptions"),
            usernamesCustom: $("#new_order .order-usernames-custom"),
            usernames: $("#new_order .order-usernames"),
            hashtags: $("#new_order .order-hashtags"),
            username: $("#new_order .order-username"),
            hashtag: $("#new_order .order-hashtag"),
            media: $("#new_order .order-media"),
            scheduleOption: $('.schedule-option'),
            resultTotalCharge: $("#new_order #result_total_charge"),
            dripFeedOption: $("#new_order .drip-feed-option")
        };


        const new_order_inputs = {
            link: $("#new_order input[name=link]"),
            quantity: $("#new_order input[name=quantity]"),
            is_drip_feed: $("#new_order input[name=is_drip_feed]"),
            total_quantity: $("#new_order input[name=total_quantity]"),
            totalCharge: $("#new_order input[name=total_charge]"),
            currencySymbol: $("#new_order input[name=currency_symbol]"),
            agree: $('#new_order input[type="checkbox"]'),
            runs: $("#new_order input[name=runs]"),
            interval: $("#new_order input[name=interval]"),
            customComments: $("#new_order .order-comments textarea[name=comments]"),
            sub_expiry: $("#new_order input[name=sub_expiry]"),
        };


        // order_resume
        const order_resume_elements = {
            serviceId: $("#order_resume .service-id-val"),
            serviceMediaLogo: $("#order_resume .service-media-logo"),
            serviceName: $("#order_resume .service-name"),
            serviceDescription: $("#order_resume .service-details"),
            servicePrice: $("#order_resume .service-price"),
            serviceMin: $("#order_resume .service-min-val"),
            serviceMax: $("#order_resume .service-max-val"),
            serviceAVGTime: $("#order_resume .service-avg-time"),
            proPriceVal: $("#order_resume .pro-price-val"),
            proPriceBadge: $("#order_resume #pro-price-badge"),
        };

        const selectedService = {};

        $(document).on("click", ".brand-category", function () {
            // Reset tag filter when brand category is clicked
            $('input[name="tag_filter"][value="all"]').prop('checked', true).parent().addClass('active').siblings().removeClass('active');

            const category = $(this).data("id");
            if (!Array.isArray(categories)) {
                console.error("categories is not defined or not an array");
                return;
            }
            categorySelect.empty();
            if (category === "favorite") {
                categorySelect.append($('<option>').val('-1').text('Favorite services'));
            }
            categories.forEach(item => {
                const name = item.name || '';
                const lowerName = name.toLowerCase();

                const shouldInclude =
                    category === "everything" ||
                    (category === "other" && isOtherCategory(name)) ||
                    lowerName.includes(category);
                if (shouldInclude) {
                    const $option = $('<option>').val(item.id).text(item.name);
                    categorySelect.append($option);
                }
            });
            categorySelect.trigger('change');
        });

        // Tag Filter Logic
        $(document).on("change", 'input[name="tag_filter"]', function () {
            const selectedTag = $(this).val();
            categorySelect.empty();

            let filteredCategories = [];
            if (selectedTag === "all") {
                filteredCategories = categories;
            } else {
                // Find services matching the tag
                const matchingServiceCateIds = new Set();
                services_list.forEach(service => {
                    if (service.tags && service.tags.toLowerCase().includes(selectedTag)) {
                        matchingServiceCateIds.add(service.cate_id);
                    }
                });

                // Get categories that have matching services
                filteredCategories = categories.filter(cat => matchingServiceCateIds.has(cat.id));
            }

            if (filteredCategories.length > 0) {
                filteredCategories.forEach(item => {
                    const $option = $('<option>').val(item.id).text(item.name);
                    categorySelect.append($option);
                });
            } else {
                categorySelect.append($('<option>').val('').text('No categories found for this tag'));
            }

            categorySelect.trigger('change');
        });

        // Change Service, search service
        $(document).on("change", ".ajaxSearchService", function () {
            var selectedID = $('select[name=search_service_id] option:selected').val();

            if (services_list_object.hasOwnProperty(selectedID)) {
                var serviceData = services_list_object[selectedID];

                // reset category
                renderAllCategories();
                categorySelect.val(serviceData.cate_id).trigger("change");

                setTimeout(function () {
                    serviceSelect.val(selectedID).trigger("change");
                }, 200);

            } else {
                return;
            }
        });

        // ajaxChangeCategory
        $(document).on("change", ".ajaxChangeCategory", function () {
            var cate_id = $('select[name=category_id] option:selected').val();
            if (cate_id == "") {
                return;
            }

            serviceSelect.empty();
            var firstServiceId = null;
            const selectedTag = $('input[name="tag_filter"]:checked').val();

            services_list.forEach(function (item) {
                var is_matched_item = false;
                if (cate_id == -1 && item.favorite == 1) {
                    is_matched_item = true;
                }
                if (item.cate_id == cate_id) {
                    is_matched_item = true;
                }

                // Apply tag filter in services as well
                if (is_matched_item && selectedTag !== "all") {
                    if (!item.tags || !item.tags.toLowerCase().includes(selectedTag)) {
                        is_matched_item = false;
                    }
                }

                if (is_matched_item) {
                    var itemName = item.id + ' - ' + item.name + ' - [' + app_currency_symbol + item.price + ']';
                    var option = $('<option></option>')
                        .val(item.id)
                        .text(itemName);
                    serviceSelect.append(option);

                    if (!firstServiceId) {
                        firstServiceId = item.id;
                    }
                }
            });
            var selectedID = $('select[name=search_service_id] option:selected').val();
            if (firstServiceId != selectedID) {
                serviceSelect.val(firstServiceId).trigger("change");
            }
        })

        // ajaxChangeService
        $(document).on("change", ".ajaxChangeService", function () {
            var selectedID = $(this).val();
            handleServiceChange(selectedID);
        });

        // Input Quantity, Dripfeed, Subscriptions
        $(document).on("input click change", ".ajaxQuantity, .ajaxDripFeedRuns, .is_drip_feed, input[name=sub_posts], input[name=sub_min], input[name=sub_max]", function () {
            var service = getSelectedService('data');
            updateTotal(service);
        });

        // ajax_custom_comments, ajax_custom_lists
        $(document).on("keyup", ".ajax_custom_comments, .ajax_custom_lists", function () {
            var text = $(this).val() || "";
            var lines = text.split(/\r\n|\r|\n/);
            var filteredLines = $.grep(lines, function (line) {
                return $.trim(line) !== "";
            });
            var _quantity = filteredLines.length;
            new_order_inputs.quantity.val(_quantity);

            var service = getSelectedService('data');
            updateTotal(service);
        });

        function isOtherCategory(name) {
            const lowerName = name.toLowerCase();
            return !excludedKeywords.some(keyword => lowerName.includes(keyword));
        }

        function handleServiceChange(selectedID) {
            if (services_list_object.hasOwnProperty(selectedID)) {
                selectedService.data = services_list_object[selectedID];
                selectedService.id = selectedID;
                resetForm();
                updateOrderResume(selectedService.data);
                prepareOrderForm(selectedService.data);
            } else {
                return;
            }
        }

        function renderAllCategories() {
            categorySelect.empty();
            categories.forEach(item => {
                const $option = $('<option>').val(item.id).text(item.name);
                categorySelect.append($option);
            });
        }

        // prepareOrderForm
        function prepareOrderForm(service) {

            var service_type = service.type;

            var service_dripfeed = service.dripfeed;

            new_order_elements.resultTotalCharge.removeClass("d-none");

            switch (service_type) {
                case "subscriptions":
                    new_order_inputs.sub_expiry.val('');

                    new_order_elements.scheduleOption.addClass("d-none");
                    new_order_elements.defaultLink.addClass("d-none");
                    new_order_elements.defaultQuantity.addClass("d-none");
                    new_order_elements.subscriptions.removeClass("d-none");
                    break;

                case "custom_comments":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.comments.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none").find("input[name=quantity]").attr("disabled", true);
                    break;

                case "custom_comments_package":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.commentsCustomPackage.removeClass("d-none");

                    break;
                case "mentions_with_hashtags":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    new_order_elements.usernames.removeClass("d-none");
                    new_order_elements.hashtags.removeClass("d-none");
                    break;

                case "mentions_custom_list":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.usernamesCustom.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none").find("input[name=quantity]").attr("disabled", true);
                    break;

                case "mentions_hashtag":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    new_order_elements.hashtag.removeClass("d-none");
                    break;

                case "mentions_user_followers":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    new_order_elements.username.removeClass("d-none");
                    break;

                case "mentions_media_likers":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    new_order_elements.media.removeClass("d-none");
                    break;

                case "package":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.addClass("d-none");
                    break;

                case "comment_likes":
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    new_order_elements.username.removeClass("d-none");
                    break;

                default:
                    new_order_elements.defaultLink.removeClass("d-none");
                    new_order_elements.defaultQuantity.removeClass("d-none");
                    break;
            }

            if (service_dripfeed == 1) {
                new_order_elements.dripFeedOption.removeClass("d-none");
            }

            if (service_type == "package" || service_type == "custom_comments_package") {
                updateTotalCharge(service.price);
            }
        }

        function updateTotal(service) {
            if (!service) {
                return;
            }
            var _service_price = service.price;
            var _total_charge = 0;

            if (service.type == 'subscriptions') {
                if (service.id == 5) {
                    _total_charge = 59.00;
                } else {
                    var _sub_posts = parseInt($('input[name=sub_posts]').val(), 10);
                    var _sub_max = parseInt($('input[name=sub_max]').val(), 10);
                    if (isNaN(_sub_posts)) _sub_posts = 0;
                    if (isNaN(_sub_max)) _sub_max = 0;
                    _total_charge = (_sub_max * _sub_posts * _service_price) / 1000;
                }
            } else {
                var _quantity = parseInt(new_order_inputs.quantity.val(), 10);
                if (isNaN(_quantity)) {
                    _quantity = 0;
                }
                var _total_quantity = _quantity;
                var _is_drip_feed = $("#new_order input[name=is_drip_feed]:checked").val();
                if (_is_drip_feed) {
                    var _runs = parseInt(new_order_inputs.runs.val(), 10);
                    var _interval = parseInt(new_order_inputs.interval.val(), 10);
                    _total_quantity = _runs * _quantity;
                    if (isNaN(_total_quantity)) {
                        _total_quantity = 0;
                    }
                }
                new_order_inputs.total_quantity.val(_total_quantity);
                _total_charge = (_total_quantity && _service_price) ? (_total_quantity * _service_price) / 1000 : 0;
            }
            updateTotalCharge(preparePrice(_total_charge));
        }

        function getSelectedService(returnData = 'id') {
            if (!selectedService.id) {
                return;
            }
            if (returnData === 'id') {
                return selectedService.id;
            }
            if (returnData === 'data') {
                return selectedService.data;
            }
            return;
        }

        // Hile all hideAllElements
        function hideAllElements() {
            Object.values(new_order_elements).forEach(function ($element) {
                $element.addClass("d-none");
            });
        }

        function updateTotalCharge(inputCharge) {
            inputCharge = parseFloat(inputCharge);
            if (isNaN(inputCharge)) {
                inputCharge = 0;
            }
            inputCharge = preparePrice(inputCharge);
            new_order_inputs.totalCharge.val(inputCharge);
            $("#new_order .charge_number").html(inputCharge);
        }

        // reset form
        function resetForm() {
            hideAllElements();
            updateOrderResume();

            new_order_inputs.agree.prop('checked', false);
            new_order_inputs.quantity.attr("disabled", false).val('');
            new_order_inputs.link.val('');
            updateTotalCharge(0);
        }

        function updateOrderResume(service) {
            if ($("#order_resume").length > 0) {
                var serviceName = service?.name || "";

                var rawDesc = service?.desc || "N/a";
                order_resume_elements.serviceDescription.text(rawDesc);

                order_resume_elements.serviceId.html(service?.id || "--");
                order_resume_elements.serviceAVGTime.html(formatAvgTime(service?.avg_time || 0));
                order_resume_elements.servicePrice.html(service?.price || 0);
                order_resume_elements.serviceMin.html(service?.min || 0);
                order_resume_elements.serviceMax.html(service?.max || 0);

                if (order_resume_elements.proPriceVal.length > 0) {
                    var price = parseFloat(service?.price || 0);
                    var proPrice = preparePrice(price * 0.9);
                    order_resume_elements.proPriceVal.html(proPrice);
                    order_resume_elements.proPriceBadge.show();
                }

                var logoPath = PATH + `/assets/images/media-icon/${getServiceLogo(serviceName)}`;
                order_resume_elements.serviceName.html(serviceName);
                order_resume_elements.serviceMediaLogo
                    .attr("src", logoPath)
                    .attr("alt", serviceName + " logo")
                    .attr("title", serviceName)
                    .on("error", function () {
                        $(this).attr("src", PATH + "/assets/images/media-icon/other.png");
                    });
            }
        }

        function formatAvgTime(seconds) {
            if (seconds <= 0) {
                return 'N/a';
            }
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remaining_seconds = seconds % 60;

            if (hours > 0) {
                return `${hours} ${lang.hours} ${minutes} ${lang.minutes}`;
            } else if (minutes > 0) {
                return `${minutes} ${lang.minutes} ${remaining_seconds} ${lang.seconds}`;
            } else {
                return `${remaining_seconds} ${lang.seconds}`;
            }
        }

        function getServiceLogo(serviceName) {
            if (!serviceName) return 'other.png';
            const lowerName = serviceName.toLowerCase();
            for (const keyword of excludedKeywords) {
                if (lowerName.includes(keyword.toLowerCase())) {
                    return `${keyword}.png`;
                }
            }
            return 'other.png';
        }

        // load default service and Category
        function initializeSelectedCategoryAndService() {
            var urlParams = new URLSearchParams(window.location.search);
            var serviceIdFromUrl = urlParams.get('service');

            var selectedCategoryId = null;
            var selectedServiceId = null;
            var item_service = null;

            if (serviceIdFromUrl && services_list_object.hasOwnProperty(serviceIdFromUrl)) {
                item_service = services_list_object[serviceIdFromUrl];
                selectedServiceId = serviceIdFromUrl;
                selectedCategoryId = item_service.cate_id;
            } else {
                selectedCategoryId = categorySelect.val();
                selectedServiceId = serviceSelect.val();
            }

            if (selectedCategoryId) {
                categorySelect.val(selectedCategoryId).trigger("change");
                if (selectedServiceId) {
                    serviceSelect.val(selectedServiceId).trigger("change");
                }
            } else {
                return;
            }
        }
        initializeSelectedCategoryAndService();
    }

});
