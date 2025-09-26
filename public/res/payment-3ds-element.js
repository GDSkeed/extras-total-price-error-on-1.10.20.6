
var stripeKey = typeof hfystripedata !== 'undefined' ? hfystripedata.key : null;
var processFlow = typeof hfystripedata !== 'undefined' ? hfystripedata.processFlow : '';
var pageUrlComplete = typeof hfystripedata !== 'undefined' ? hfystripedata.pageUrlComplete : '';
var reservationId = null;
var redirectOnSuccess = typeof hfyRedirectOnSuccess !== 'undefined' ? hfyRedirectOnSuccess : null;

let paymentMethodConfiguration = typeof hfystripedata !== 'undefined' ? hfystripedata.paymentMethodConfiguration : false;
let paymentMethodTypes = typeof hfystripedata !== 'undefined' ? hfystripedata.paymentMethodTypes : [];
let stripeElementOptions = {
    mode: 'payment',
    paymentMethodCreation: 'manual',
    currency: typeof hfystripedata !== 'undefined' ? hfystripedata.curr : '',
    amount: typeof hfystripedata !== 'undefined' ? hfystripedata.amount : ''
}

if (paymentMethodConfiguration) {
    stripeElementOptions.payment_method_configuration = paymentMethodConfiguration;
} else if (paymentMethodTypes.length) {
    stripeElementOptions.payment_method_types = paymentMethodTypes;
}

jQuery(document).ready(function($) {

    const script = document.createElement('script');
    script.onload = function() {
        initPayment3ds();
    }
    script.src = 'https://js.stripe.com/v3/';
    document.body.appendChild(script);

    function initPayment3ds()
    {
        var stripe = Stripe(stripeKey, { stripeAccount: typeof hfystripedata !== 'undefined' ? hfystripedata.connectedAccount : null });

        const stripeElements = stripe.elements(stripeElementOptions);

        function handleShowHideDetails()
        {
            let btnShow = $('#details-show');
            let btnHide = $('#details-hide');

            if (btnShow.length && btnHide.length) {
                let btnBoth = btnShow.add(btnHide);

                // Function to toggle details
                function toggleDetails(show) {
                    $('.mob-hide').toggle(show);
                    btnShow.toggle(!show);
                    btnHide.toggle(show);
                }

                // Show details button click event
                btnShow.on('click', function() {
                    toggleDetails(true);
                });

                // Hide details button click event
                btnHide.on('click', function() {
                    toggleDetails(false);
                });

                // Optional: Add touch support for mobile devices
                btnBoth.on('touchstart', function() {
                    $(this).addClass('active');
                }).on('touchend', function() {
                    $(this).removeClass('active');
                });

                // Optional: Keypress accessibility
                btnBoth.on('keypress', function(e) {
                    if (e.which === 13 || e.which === 32) {
                        e.preventDefault();
                        $(this).click();
                    }
                });

                // Add ARIA attributes for better accessibility
                btnBoth.attr('role', 'button');
                btnShow.attr('aria-expanded', 'false');
                btnHide.attr('aria-expanded', 'true');

                // Update ARIA attributes when toggling
                function updateAriaAttributes(show) {
                    btnShow.attr('aria-expanded', !show);
                    btnHide.attr('aria-expanded', show);
                }

                // Combine toggle and ARIA update
                function toggleDetailsWithAria(show) {
                    toggleDetails(show);
                    updateAriaAttributes(show);
                }

                // Update click events to use the new combined function
                btnShow.off('click').on('click', function() {
                    toggleDetailsWithAria(true);
                });

                btnHide.off('click').on('click', function() {
                    toggleDetailsWithAria(false);
                });
            }
        }

        // todo
        // $(document).on("ajaxSend", function(e, xhr, settings) {
        //     settings.timeout = 30000; // 5 min
        // });

        window.intlTelInput(document.querySelector("#hfy-payment-phone"), {
            initialCountry: hfyPhoneLang,
            separateDialCode: true,
            hiddenInput: () => ({ phone: "pphone" }),
        });

        handleShowHideDetails();

        var paymentFormContainerSelector = ".hfy-payment";
        var paymentFormSelector = "#payment-form";
        var $paymentFormSelector = $("#payment-form");
        var countryInputSelector = "#hfy-payment-address-country";
        var feesSelector = "#fees";

        var elements = createCardElements();

        var formContainer = document.querySelector(paymentFormContainerSelector);
        var form = document.querySelector(paymentFormSelector);
        var error = document.querySelector(paymentFormSelector +' .error');
        var errorMessage = error.querySelector('.message');
        var terms = document.querySelector('.terms-row');
        // var paymentContent = document.querySelector('.payment-content');

        var paymentFormMainContainer = document.querySelector("#payment-form-main-container");
        var successContainer = document.querySelector(".hfy-payment-success");
        var errorContainer = document.querySelector(".hfy-payment-error");

        var inputElements = document.querySelectorAll(paymentFormContainerSelector + " .field input:required");
        inputElements.forEach(function(ie) {
            ie.addEventListener('invalid', function (e) {
                if (e.target.validity.valid) {
                    jQuery(e.target).parents('.field').first().removeClass('invalid');
                } else {
                    if (e.target.title) e.target.setCustomValidity(e.target.title);
                    jQuery(e.target).parents('.field').first().addClass('invalid');
                }
            });
            ie.addEventListener('change', function (e) {
                if (e.target.validity.valid) {
                    jQuery(e.target).parents('.field').first().removeClass('invalid');
                } else {
                    jQuery(e.target).parents('.field').first().addClass('invalid');
                }
                e.target.setCustomValidity('');
            });
            ie.addEventListener('change', function (e) {
                jQuery(e.target).parents('.field').first().removeClass('invalid');
            });
        });

        $('.pay-btn', $paymentFormSelector).on('click', function(){
            jQuery('#hfy-payment-address-country, #hfy-payment-zip').removeClass('invalid');
        });

        jQuery(countryInputSelector).on('change', function(){
            let t = $(this);
            if (t != '') t.removeClass('empty')
            else t.addClass('empty')
        });

        $('#hfy-payment-note', $paymentFormSelector).on('focus', function(){
            $(this).parent().addClass('focused')
        });
        $('#hfy-payment-note', $paymentFormSelector).on('blur', function(){
            $(this).parent().removeClass('focused')
        });

        $('.next-btn', $paymentFormSelector).on('click', function(){
            if ($paymentFormSelector[0].checkValidity()) {
                let a = $('.hfy-payment-steps .tab-pane.active', $paymentFormSelector);
                let b = $('+ .tab-pane', a).first().addClass('active');
                a.removeClass('active');
                if (b.hasClass('hfy-payment-step-last')) {
                    $('input[name=zip]', $paymentFormSelector).attr('required', 'required');
                    $('input[name=terms]', $paymentFormSelector).attr('required', 'required');
                }
            } else {
                $paymentFormSelector[0].reportValidity()
            }
        });

        $('.prev-btn', $paymentFormSelector).on('click', function(){
            let a = $('.hfy-payment-steps .tab-pane.active', $paymentFormSelector);
            let b = a.prev().addClass('active');
            a.removeClass('active');
            if (b.hasClass('hfy-payment-step-first')) {
                $('input[name=zip]', $paymentFormSelector).removeAttr('required');
                $('input[name=terms]', $paymentFormSelector).removeAttr('required');
            }
        });

        (function() {
            'use strict';
            addInputsFloatingLabels();
            addInputErrorListener();
            addFormEventListener();
        })();

        function getDetailsElementValue(s) {
            let e = $(s, $paymentFormSelector);
            return e ? e.value : undefined;
        }

        function createCardElements() {
            if (!stripe) return;

            var paymentElement = stripeElements.create('payment', {
                layout: {
                    type: 'tabs',
                    defaultCollapsed: false,
                },
                wallets: {
                    applePay: 'auto',
                    googlePay: 'auto',
                },
                defaultValues: {
                    billingDetails: {
                        name: getDetailsElementValue("#hfy-payment-name"),
                        email: getDetailsElementValue("#hfy-payment-email"),
                        phone: getDetailsElementValue("#hfy-payment-phone"),
                        // address: {
                        //     postal_code: getDetailsElementValue("#hfy-payment-zip"),
                        //     // country: country ? country.value : undefined
                        // }
                    },
                },
            });
            paymentElement.mount('#hfy-payment-element');

            // paymentElement.on('ready', function(event) {
            // 	console.log('payment element loaded');
            // 	$('#payment-submit-button').prop("disabled", false);
            // });

            return [paymentElement];
        }

        function addInputsFloatingLabels() {
            var inputs = document.querySelectorAll(paymentFormSelector + ' .input');
            Array.prototype.forEach.call(inputs, function(input) {
                input.addEventListener('focus', function() {
                    input.classList.add('focused');
                });
                input.addEventListener('blur', function() {
                    input.classList.remove('focused');
                });
                input.addEventListener('keyup', function() {
                    if (input.value && input.value.length === 0) {
                        input.classList.add('empty');
                    } else {
                        input.classList.remove('empty');
                    }
                });
            });
        }

        function addInputErrorListener() {
            elements.forEach(function(element) {
                element.on('change', function(event) {
                    if (event.error) {
                        error.classList.add('visible');
                        errorMessage.innerText = event.error.message;
                        if(terms) terms.classList.add('error-visible');
                    } else {
                        error.classList.remove('visible');
                        if(terms) terms.classList.remove('error-visible');
                    }
                });
            });
        }

        function addFormEventListener() {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!elements) return false;
                if ($('#hfy-payment-step1', $paymentFormSelector).hasClass('active')) return false;

                error.classList.remove('visible');

                formContainer.classList.add('submitting');
                stripeElements.submit().then(function(result) {
                    if (result && result.error) {
                        showError(result.error.message);
                    } else {

                        stripe.createPaymentMethod({
                            elements: stripeElements,
                            params: {
                                billing_details: getAdditionalData(false),
                            },
                        }).then(function(result) {
                            if (result.error) {
                                showError(result.error.message);
                                return;
                            }

                            //If the payment method is created successfully, create a reservation and try to charge it
                            createReservation(result.paymentMethod);
                        });

                    }
                });
            });
        }

        function reservationFailedPayment() {
            if (reservationId) {
                $.ajax({
                    type: 'POST',
                    data: { action: 'payment_fail', data: getAdditionalData() },
                    url: hfyx.url,
                    success: function (result) {}
                });
            }
        }

        function createReservation(paymentMethod) {
            if (reservationId) {
                return stripePaymentMethodHandler(paymentMethod);
            }

            $.ajax({
                type: 'POST',
                data: { action: 'init_reservation', data: getAdditionalData() },
                url: hfyx.url,
                async: true,
                timeout: 180000, // 3 min
                success: function (result) {
                    if (result.success && result.reservation) {
                        reservationId = result.reservation;
                        stripePaymentMethodHandler(paymentMethod);
                    }else if(result.error){
                        showError(result.error + ". Your card was not charged.");
                    }else{
                        console.error({result});
                        showError("There was an error creating your reservation, please try again later. Your card was not charged.");
                    }
                },
                fail: function (request) {
                    showError(request.responseJSON.error + ". Your card was not charged.");
                },
                error: function (request) {
                    showError("There was an error processing your request, please try again later. Your card was not charged.");
                },
            });
        }

        function createPaymentMethod() {
            stripe.createPaymentMethod({
                type: 'card',
                card: elements[0],
                billing_details: getAdditionalData(false),
            }).then(function(result) {
                if (result && result.error) {
                    showError(result.error.message);
                } else {
                    stripePaymentMethodHandler(result.paymentMethod);
                }
            });
        }

        function getAdditionalData(full = true) {
            let name = form.querySelector("#hfy-payment-name");
            let email = form.querySelector("#hfy-payment-email");
            let phone = form.querySelector("#hfy-payment-phone");
            let note = form.querySelector("#hfy-payment-note");
            // let zip = form.querySelector("#hfy-payment-zip");
            let country = form.querySelector(countryInputSelector);
            let discountCode = form.querySelector("#discount-code");
            let dcid = form.querySelector("#dcid");
            let fees = form.querySelector(feesSelector);
            let data = {
                name: name ? name.value : undefined,
                email: email ? email.value : undefined,
                phone: phone ? phone.value : undefined,
                // address: {
                //     postal_code: zip ? zip.value : undefined,
                //     // country: country ? country.value : undefined
                // }
            };
            if(full){
                data.note = note ? note.value : undefined;
                // data.zip = zip ? zip.value : undefined;
                data.start_date = typeof hfystripedata !== 'undefined' ? hfystripedata.start_date : null;
                data.end_date = typeof hfystripedata !== 'undefined' ? hfystripedata.end_date : null;
                data.guests = typeof hfystripedata !== 'undefined' ? hfystripedata.guests : null;
                data.adults = typeof hfystripedata !== 'undefined' ? hfystripedata.adults : null;
                data.children = typeof hfystripedata !== 'undefined' ? hfystripedata.children : null;
                data.infants = typeof hfystripedata !== 'undefined' ? hfystripedata.infants : null;
                data.pets = typeof hfystripedata !== 'undefined' ? hfystripedata.pets : null;
                data.listing_id = typeof hfystripedata !== 'undefined' ? hfystripedata.listing_id : null;
                data.total = typeof hfystripedata !== 'undefined' ? hfystripedata.total : null;
                data.dcid = dcid ? dcid.value : undefined;
                data.fees = fees ? fees.value : undefined;
                data.discount_code = discountCode ? discountCode.value : undefined;
                data.reservationId = reservationId;
            }
            return data;
        }

        function showError(message) {
            formContainer.classList.remove('submitting');
            errorMessage.textContent = message;
            error.classList.add('visible');
            if (terms) terms.classList.add('error-visible');
            setUrlHash('error');
            doGtagEvent('hfy_payment_error', { message: message });
        }

        function stripePaymentMethodHandler(stripeObject) {
            setUrlHash('wait');
            var data = getAdditionalData();

            data.redirect_url = typeof hfystripedata !== 'undefined' ? (hfystripedata.return_url + '?reservation_id=' + data.reservationId) : null;
            data.user_ip = typeof hfystripedata !== 'undefined' ? hfystripedata.userIp : null;
            data.user_agent = typeof hfystripedata !== 'undefined' ? hfystripedata.userAgent : null;
            data.form_type = 'payment_element'; // for internal info

            data.stripeObject = stripeObject;
            $.ajax({
                type: 'POST',
                data: { action: 'payment', data: data },
                url: hfyx.url,
                async: true,
                // timeout: 180000, // 3 min
                success: function (result) {
                    if (result && result.success) {
                        // stripeWaitingProcessingCall(result.id);
                        handleStripeParseResult(result.data);
                    } else {
                        if (result && result.error) {
                            showError(result.error);
                        } else {
                            paymentFormMainContainer.classList.add("hidden");
                            errorContainer.classList.remove("hidden");
                        }
                    }
                },
                error: function (x, status) {
                    console.error('error',status);
                    showError(typeof x.responseJSON !== 'undefined' ? x.responseJSON.error : status);
                },
                // complete: function(res, status) {
                //     // console.warn('complete',status,res);
                //     if (res.responseJSON && res.responseJSON.requires_action && res.responseJSON.requires_action == true) {}
                //     else {
                //         // formContainer.classList.remove('submitting');
                //     }
                // }
            });
        }

        function handleStripeWaitingProcessing(id) {
            setTimeout(function() {
                stripeWaitingProcessingCall(id)
            }, 3000);
        }

        function stripeWaitingProcessingCall(id) {
            $.ajax({
                type: 'POST',
                data: { action: 'payment_processing', data: { id } },
                url: hfyx.url,
                async: true,
                success: function (result) {
                    if (result && result.success) {
                        if (result.state && result.state == 'done') {
                            handleStripeParseResult(result.data);
                        } else {
                            handleStripeWaitingProcessing(id);
                        }
                    } else {
                        if (result && result.error) {
                            showError(result.error);
                        } else {
                            paymentFormMainContainer.classList.add("hidden");
                            errorContainer.classList.remove("hidden");
                        }
                    }
                },
                error: function (x, status) {
                    console.error('error',status);
                    showError(typeof x.responseJSON !== 'undefined' ? x.responseJSON.error : status);
                }
            });
        }

        function setOrHideIfEmpty(selector, text)
        {
            if (text && text.length) {
                $(selector).text(text);
            } else {
                $(selector).parents('.row').first().hide();
            }
        }

        function handleStripeParseResult(result)
        {
            if (!(result && result.success)) {
                formContainer.classList.remove('submitting');
                if (result && result.error) {
                    showError(result.error);
                } else {
                    paymentFormMainContainer.classList.add("hidden");
                    errorContainer.classList.remove("hidden");
                }
            } else {
                if (result && result.requires_action) {
                    stripe.handleNextAction({
                        clientSecret: result.payment_intent_client_secret
                    }).then(handleStripeJsResult);
                } else {
                    formContainer.classList.remove('submitting');
                    $("#transaction-message").text(result.message);

                    try {
                        let rcode = '';
                        if (result.reservationData && result.reservationData.confirmation_code) {
                            rcode = result.reservationData.confirmation_code || '';
                        } else {
                            if (result.reservation && result.reservation.confirmation_code) {
                                rcode = result.reservation.confirmation_code || '';
                            }
                        }

                        $('.hfy-payment-success input#reservation-code').val(rcode);

                        setOrHideIfEmpty("#property-name", typeof hfystripedata !== 'undefined' ? hfystripedata.listingName : '');
                        setOrHideIfEmpty("#reservation-id", rcode)
                        setOrHideIfEmpty("#transaction-id", result.paymentData.id || null)
                        setOrHideIfEmpty("#transaction-date", result.paymentData.created || null);
                        setOrHideIfEmpty("#transaction-payment-method", result.paymentData.card || null);
                        setOrHideIfEmpty("#transaction-amount", result.paymentData.amount || null);

                    } catch(e) {
                        console.warn(e);
                        console.warn('ERR result.paymentData', result);
                    }
                    paymentFormMainContainer.classList.add("hidden");
                    successContainer.classList.remove("hidden");
                    setUrlHash('success');
                    doGtagEvent('hfy_payment_success', {
                        listing_id: result.reservation.listing_id || '',
                        transaction_amount: result.paymentData.amount || ''
                    });
                    doGtagEvent('purchase', {
                        transaction_id: result.reservation.confirmation_code || '',
                        value: result.reservation.payout_price || 0,
                        tax: result.reservation.tax_amount || 0,
                        shipping: 0,
                        currency: result.reservation.currency || '',
                        coupon: result.reservation.discount_code || '',
                        item_id: typeof hfystripedata !== 'undefined' ? hfystripedata.listing_id : '',
                        item_name: typeof hfystripedata !== 'undefined' ? hfystripedata.listingName : '',
                        coupon: result.reservation.discount_code || '',
                        items: [{
                            item_id: typeof hfystripedata !== 'undefined' ? hfystripedata.listing_id : '',
                            item_name: typeof hfystripedata !== 'undefined' ? hfystripedata.listingName : '',
                            // affiliation: "Google Merchandise Store",
                            coupon: result.reservation.discount_code || '',
                            // discount: 2.22,
                            // index: 0,
                            // item_brand: "Google",
                            // item_category: "Apparel",
                            // item_category2: "Adult",
                            // item_category3: "Shirts",
                            // item_category4: "Crew",
                            // item_category5: "Short sleeve",
                            // item_list_id: "related_products",
                            // item_list_name: "Related Products",
                            // item_variant: "green",
                            // location_id: "ChIJIQBpAG2ahYAR_6128GcTUEo",
                            // price: 10.01,
                            quantity: 1
                        }]
                    });
                    if (redirectOnSuccess) {
                        let href = new URL(redirectOnSuccess);
                        href.searchParams.set('reservation_id', result.reservation.id || '');
                        href.searchParams.set('success', '1');
                        window.location.href = href.toString();
                    }
                }
            }
        }

        function handleStripeJsResult(result) {
            if (result && result.error) {
                reservationFailedPayment();
                showError(result.error.message);
            } else {
                stripePaymentMethodHandler(result.paymentIntent);
            }
        }

    }

});
