<?php
if (!defined('WPINC')) die;

$fill_name1 = HfyHelper::getUserMeta('first_name');
$fill_name2 = HfyHelper::getUserMeta('last_name');
$fill_name = empty($reserveInfo->name) ? trim("$fill_name1 $fill_name2") : $reserveInfo->name;
$fill_email = empty($reserveInfo->email) ? HfyHelper::getUserMeta('user_email') : $reserveInfo->email;
$fill_phone = empty($reserveInfo->phone) ? HfyHelper::getUserMeta('phone_number') : $reserveInfo->phone;

?>

<style>
    .payment-successful {
        opacity: 0;
    }
</style>

<div class="cell hfy-payment">
    <form id="payment-form" role="form" method="post">

        <div class="tab-content hfy-payment-steps">
            <div role="tabpanel" class="tab-pane active hfy-payment-step-first">
                <?php include hfy_tpl('payment/paypal-form-step-1'); ?>
            </div>

            <?php if ($sliderStepsCount > 2): ?>
                <div role="tabpanel" class="tab-pane">
                    <?php include hfy_tpl('payment/stripe-form-3ds-step-2'); ?>
                </div>
            <?php endif; ?>

            <div role="tabpanel" class="tab-pane hfy-payment-step-last">
                <div class='payment-info-content'>
                    <div>
                        <script src="https://www.paypal.com/sdk/js?currency=<?= $currency ?>&client-id=<?= $paymentSettings->services->account_id ?? '' ?>" data-sdk-integration-source="button-factory"></script>
                        <div id="paypal-button-container"></div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col">
                        <button class='prev-btn prev-btn-pay' type="button">&lsaquo; &nbsp; <?= __('Back', 'hostifybooking') ?></button>
                    </div>
                </div>

            </div>
        </div>

        <?php include hfy_tpl('payment/additional-info'); ?>

    </form>
</div>



<div class="img" id="payment_load" style="height: 140px; display: none; justify-content: center; align-items: center; height: 50px; background: transparent;">
    <img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/loading.svg" style="height: 300px;" alt="" />
</div>

<div class="payment-successful" id="success">
    <div class="text-center">
        <svg id="successAnimation" class="animated" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 70 70">
            <path id="successAnimationResult" fill="#24b47e" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"></path>
            <circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="#24b47e" stroke-width="2" stroke-linecap="round" fill="transparent"></circle>
            <polyline id="successAnimationCheck" stroke="#ffff" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"></polyline>
        </svg>
    </div>
    <div class=" text-center text-uppercase h4">
        <?= __('Payment successful', 'hostifybooking') ?>
    </div>
</div>

<div id="xerror" class="alert alert-danger" style="display:none"></div>

<script>
window.onload = function() {
    var $ = jQuery;

    var paymentFormSelector = "#payment-form";
    var $paymentFormSelector = $("#payment-form");
    var countryInputSelector = "#hfy-payment-address-country";
    var form = document.querySelector(paymentFormSelector);

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

    function pp_getAdditionalData(x) {
        let name = form.querySelector("#hfy-payment-name");
        let email = form.querySelector("#hfy-payment-email");
        let phone = form.querySelector("#hfy-payment-phone");

        return {
            // name: x.payer.name.given_name + ' ' + x.payer.name.surname,
            // phone: (x.payer.phone && x.payer.phone.phone_number && x.payer.phone.phone_number.national_number) || x.payer.phone || '',
            // email: x.payer.email_address || '',
            name: name ? name.value : (x.payer.name.given_name + ' ' + x.payer.name.surname),
            phone: phone ? phone.value : ((x.payer.phone && x.payer.phone.phone_number && x.payer.phone.phone_number.national_number) || x.payer.phone || ''),
            email:  email ? email.value : (x.payer.email_address || ''),

            id: x.id,
            payer_id: x.payer.payer_id,
            country_code: (x.payer.address && x.payer.address.country_code) || '',
            city: (x.payer.address && x.payer.address.admin_area_2) || '',
            start_date: '<?= $reserveInfo->start_date ?>',
            end_date: '<?= $reserveInfo->end_date ?>',
            guests: '<?= $reserveInfo->guests ?>',
            adults: '<?= $reserveInfo->adults ?>',
            children: '<?= $reserveInfo->children ?>',
            infants: '<?= $reserveInfo->infants ?>',
            pets: '<?= $reserveInfo->pets ?>',
            listing_id: '<?= $reserveInfo->listing_id ?>',
            total: '<?= $totalPrice ?>',
            dcid: '<?= $reserveInfo->dcid ?>',
            discount_code: '<?= $reserveInfo->discount_code ?? '' ?>',
            fees: '<?= $reserveInfo->fees_ids ?? '' ?>',
            listing_name: '<?= esc_attr($listingInfo->name ?? '') ?>',
            listing_curr: '<?= esc_attr($listingData->currency ?? '') ?>',
        }
    }

    function pp_getStep1Data() {
        let name = form.querySelector("#hfy-payment-name");
        let email = form.querySelector("#hfy-payment-email");
        // let phone = form.querySelector("#hfy-payment-phone");
        let phone = form.querySelector("input[name=pphone]");
        let country = form.querySelector(countryInputSelector);
        let zip = form.querySelector("#hfy-payment-zip");

        let namefull = name ? name.value : '';
        let chunks = namefull.split(/\s+/);
        let names = [chunks.shift(), chunks.join(' ')];

        let phone_number = {
            national_number: phone ? (phone.value).replace(/\D/g,'') : undefined,
        }

        if (iti) {
            let x = iti.getSelectedCountryData();
            console.log({x});
            let pc = x && typeof x.dialCode !== 'undefined' ? x.dialCode : '';
            if (pc.length) {
                phone_number.country_code = pc;
            }
        }

        return {
            payment_source: {
                paypal: {
                    name: {
                        given_name: names[0],
                        surname: names[1]
                    },
                    email_address: email ? email.value : undefined,
                    phone: {
                        phone_number: phone_number
                    },
                    address: {
                        postal_code: zip ? zip.value : undefined,
                        country_code: country ? country.value : undefined,
                    },
                }
            },
        };
    }

    paypal.Buttons({
        createOrder: function(data, actions) {
            hideError();
            let dataCreate = pp_getStep1Data();
            dataCreate.purchase_units = [{
                amount: {
                    value: '<?= $totalPrice ?>',
                    currency_code: '<?= $currency ?>'
                }
            }];
            dataCreate.application_context = {
                shipping_preference: 'NO_SHIPPING'
            };
            return actions.order.create(dataCreate);
        },
        onApprove: function(data, actions) {
            $('#paypal-button-container, #success, .prev-btn-pay').hide();
            $('#payment_load').show();
            return actions.order.capture().then(function(details) {
                var pdata = pp_getAdditionalData(details);
                $.ajax({
                    type: 'POST',
                    data: { action: 'payment', data: pdata },
                    url: hfyx.url,
                    success: function(result) {
                        $('.payment-info-content').html(result);
                        doGtagEvent('hfy_payment_success', {
                            listing_id: pdata.listing_id || '',
                            transaction_amount: pdata.total || ''
                        });
                        doGtagEvent('purchase', {
                            // transaction_id: result.reservation.confirmation_code || '',
                            value: pdata.total || 0,
                            // tax: result.reservation.tax_amount || 0,
                            shipping: 0,
                            currency: pdata.listing_curr,
                            // coupon: result.reservation.discount_code || '',
                            items: [{
                                item_id: pdata.listing_id,
                                item_name: pdata.listing_name,
                                // affiliation: "Google Merchandise Store",
                                // coupon: result.reservation.discount_code || '',
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

                        var redirectOnSuccess = typeof hfyRedirectOnSuccess !== 'undefined' ? hfyRedirectOnSuccess : null;
                        if (redirectOnSuccess) {
                            console.log({pdata, details});
                            let href = new URL(redirectOnSuccess);
                            href.searchParams.set('reservation_id', pdata.id || '');
                            href.searchParams.set('success', '1');
                            window.location.href = href.toString();
                        }

                        // if (!(result && result.success)) {
                        //     if (typeof result.error !== 'undefined') {
                        //         showError(result.error || 'Payment failed');
                        //     } else if (result && result.success != true ) {
                        //         showError('Payment failed');
                        //     } else {
                        //         $('.payment-info-content').html(result);
                        //     }
                        // } else {
                        //     $('.payment-info-content').html(result);
                        // }
                    },
                    fail: function(request) {
                        console.warn(request.responseJSON.error);
                        showError(request.responseJSON.error);
                    },
                    complete: function(data) {
                        $('#payment_load').hide();
                    }
                });
            })
            // .then(function(captureData) {
            //     if (captureData.error) throw new Error(captureData.error);
            // });
        },
        onCancel: function(data) {
            hideError();
            $('#paypal-button-container').show();
        },
        onError: function (err) {
            $('#payment_load').hide();
            $('#paypal-button-container').show();
            showError(err);
        }
    }).render('#paypal-button-container');

    function showError(message) {
        $('#xerror').text(message).show();
        doGtagEvent('hfy_payment_error', { message: message });
    }
    function hideError() {
        $('#xerror').hide().text('');
    }

    const hpp = document.querySelector("#hfy-payment-phone");

    const iti = window.intlTelInput(hpp, {
        initialCountry: hfyPhoneLang,
        separateDialCode: true,
    });

    // hpp.addEventListener("countrychange", function() {
    //     let scd = iti.getSelectedCountryData();
    //     if (scd && typeof scd.iso2 !== 'undefined') {
    //         let sel = $('select#hfy-payment-address-country option[value='+(''+scd.iso2.toUpperCase())+']');
    //         if (sel.length) sel.prop('selected', true);
    //     }
    // });

}
</script>
