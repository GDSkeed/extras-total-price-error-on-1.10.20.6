<?php if (!defined('WPINC')) die; ?>
<script src="https://docs.netpay.mx/cdn/js/latest/checkout.plus.js"></script>
<link href="<?= HOSTIFYBOOKING_URL ?>public/res/stripe-form.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .payment-successful {
        opacity: 0;
    }

    .payment-transaction-details {
        display: grid;
        font-size: 16px;
        padding: 9px 20px;
        border: 1px solid #d5d5d5;
        width: fit-content;
        margin: auto;
    }

    .payment-transaction-details .col-md-4,
    .payment-transaction-details .col-md-8 {
        width: fit-content;
    }

    .payment-transaction-details .col-md-4 {
        min-width: 160px;
        margin-right: 20px;
    }

    .payment-transaction-details .row {
        padding: 5px;
    }

    .payment-transaction-details .row:not(:last-of-type) {
        border-bottom: 1px solid #d5d5d5;
    }

    .netpay-note {
        color: #0C71FF;
        text-align: end;
    }

    #netpay-back {
        background-color: #fff;
        border: 3px solid #0C71FF;
        border-radius: 3px;
        color: #0C71FF;
        font-size: 14px;
        height: 45px;
        margin-top: 0;
        transition: 1s ease;
    }

    #netpay-back:hover {
        background-color: #0C71FF;
        border: 1px solid lightgrey;
        color: #fff;
        margin-top: 0;
    }

    #netpay-checkout {
        background-color: #0C71FF;
        border-radius: 3px !important;
        margin-top: 0;
    }

    #address-button {
        background-color: #0C71FF;
        border-radius: 3px;
    }
</style>
<div class="col-12" style="padding: 0">
    <main>
        <section class="container-lg">
            <div class="cell stripe-form-container">
                <div id="netpay-address" style="width: 100%">
                    <div class="row" data-locale-reversible>
                        <div class="field">
                            <input id="payment-name" name="email" class="input empty" type="text" placeholder="Name" required>
                            <label id="payment-name-label" for="payment-name"><?= __('Name', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row" data-locale-reversible>
                        <div class="field">
                            <input id="payment-email" name="email" class="input empty" type="email" placeholder="Email" required>
                            <label id="payment-email-label" for="payment-email"><?= __('Email', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row" data-locale-reversible>
                        <div class="field">
                            <input id="payment-phone" name="phone" class="input empty" type="tel" placeholder="Phone" required>
                            <label id="payment-phone-label" for="payment-phone"><?= __('Phone', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="field">
                            <input id="payment-address" name="address" class="input empty" type="text" placeholder="Address" required>
                            <label id="payment-address-label" for="payment-address"><?= __('Address', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="field">
                            <select name="country" class="input empty" id="payment-country" required>
                                <option value="" disabled selected><?= __('Country', 'hostifybooking'); ?></option>
                                <?php foreach (HFY_COUNTRY_CODES_ALPHA2 as $c_code => $c_name) : ?>
                                    <option value="<?= $c_code; ?>"><?= __($c_name, 'hostifybooking'); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="field" id="payment-state-field" style="display: none">
                            <input id="payment-state" name="state" class="input empty" placeholder="State">
                            <label id="payment-state-label" for="payment-state"><?= __('State', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                        <div id="payment-state-us-field" class="field" style="display: none">
                            <select name="state-us" class="input empty" id="payment-state-us">
                                <option value="" disabled selected><?= __('State', 'hostifybooking'); ?></option>
                                <?php foreach (HFY_USA_STATE_CODES_ALPHA2 as $code => $full_name) : ?>
                                    <option value="<?= $code; ?>"><?= __($full_name, 'hostifybooking'); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="baseline"></div>
                        </div>
                        <div id="payment-state-mx-field" class="field" style="display: none">
                            <select name="state-mx" class="input empty" id="payment-state-mx">
                                <option value="" disabled selected><?= __('State', 'hostifybooking'); ?></option>
                                <?php foreach (HFY_MEXICO_STATE_CODES_ALPHA3 as $code => $full_name) : ?>
                                    <option value="<?= $code; ?>"><?= __($full_name, 'hostifybooking'); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="field">
                            <input id="payment-city" name="city" class="input empty" type="text" placeholder="City" required>
                            <label id="payment-city-label" for="payment-city"><?= __('City', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="field">
                            <input id="payment-zip" name="zip" class="input empty" placeholder="ZIP" required>
                            <label id="payment-zip-label" for="payment-zip"><?= __('ZIP', 'hostifybooking'); ?></label>
                            <div class="baseline"></div>
                        </div>
                    </div>
                    <div class="row">
                        <button id="address-button" type="submit"><?= __('Continue', 'hostifybooking'); ?></button>
                    </div>
                </div>
                <div id="netpay-submission" class="col-sm-12" style="display: none; padding: 0">
                    <button id="netpay-back" onClick="window.location.reload();">
                        <?= __('Back', 'hostifybooking'); ?>
                    </button>
                    <button id='netpay-checkout' type="button"
                            data-button-title='<?= __('Pay', 'hostifybooking'); ?> <?= $reserveInfo->prices->iso_code . " " . $totalPrice ?>'
                            data-street1=''
                            data-country=''
                            data-city=''
                            data-postal-code=''
                            data-state=''
                            data-token=''
                            data-phone-number=''
                            data-email=''
                            data-merchant-reference-code=''
                            data-onsuccess='onNetpayPaymentSuccess'
                            data-onerror='onNetpayPaymentError'
                            data-product-count='1'
                            data-commerce-name='Hostify'
                    >
                        <?= __('Pay', 'hostifybooking'); ?> <?= ListingHelper::withSymbol($totalPrice, $reserveInfo->prices, $listingInfo->currency_symbol) ?>
                    </button>
                    <p class="netpay-note" style="margin-bottom: 0"><?= __('The amount will be converted to mexican pesos', 'hostifybooking'); ?></p>
                    <div style="text-align: end;">
                        <img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/netpay.png" id="paydollar-powered" style="height: 45px;"/>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
    var hfyReservationData = {
        listing_id: '<?= $reserveInfo->listing_id ?>',
        start_date: '<?= $reserveInfo->start_date ?>',
        end_date: '<?= $reserveInfo->end_date ?>',
        guests: '<?= $reserveInfo->guests ?>',
        adults: '<?= $reserveInfo->adults ?>',
        children: '<?= $reserveInfo->children ?>',
        infants: '<?= $reserveInfo->infants ?>',
        pets: '<?= $reserveInfo->pets ?>',
        total: '<?= $totalPrice ?>',
        discount_code: '<?= $reserveInfo->discount_code ?>',
        dcid: '<?= $reserveInfo->dcid ?>',
        integration_id: '<?= $settings->data->payment_service_integration_id ?>',
        customer_id: '<?= $settings->customer_id ?>',
        currency: '<?= $reserveInfo->prices->iso_code ?>',
    }
    const nameField = document.getElementById('payment-name');
    const emailField = document.getElementById('payment-email');
    const phoneField = document.getElementById('payment-phone');
    const addressField = document.getElementById('payment-address');
    const countryField = document.getElementById('payment-country');
    const cityField = document.getElementById('payment-city');
    const zipField = document.getElementById('payment-zip');

    var paymentFormMainContainer = document.querySelector('#payment-form-main-container');
    var successContainer = document.querySelector('#payment-form-success');
    var errorContainer = document.querySelector('.hfy-payment-error');

    var reservationId;
    var transactionId;
    var tokenizedAmount;


    jQuery(document).ready(function() {
        jQuery("#address-button").on("click", async function () {
            let stateField;
            if (countryField.value === 'US') {
                stateField = document.getElementById('payment-state-us');
            } else if (countryField.value === 'MX') {
                stateField = document.getElementById('payment-state-mx');
            } else {
                stateField = document.getElementById('payment-state');
            }

            if (nameField.value.length > 0
                && emailField.value.length > 0
                && phoneField.value.length > 0
                && addressField.value.length > 0
                && countryField.value.length > 0
                && cityField.value.length > 0
                && stateField.value.length > 0
                && zipField.value.length > 0
            ) {
                hfyReservationData.name = nameField.value;
                hfyReservationData.email = emailField.value;
                hfyReservationData.phone = phoneField.value;
                paymentSetup(stateField);
                jQuery(this).html('<i class="fa fa-spinner fa-pulse"></i>');
            } else {
                alert('Please fill all fields!')
            }
        });
    });

    function paymentSetup(stateField) {
        jQuery.ajax({
            type: 'POST',
            data: { action: 'netpay_payment_setup', data: hfyReservationData },
            url: "<?= admin_url('admin-ajax.php') ?>",
            timeout: 180000, // 3 min
            success: function (result) {
                if (result.success) {
                    reservationId = result.reservation_id;
                    transactionId = result.transaction_id;
                    tokenizedAmount = result.tokenized_amount_data.tokenAmount;
                    jQuery("#address-button").hide();

                    let netpayCheckout = document.getElementById('netpay-checkout');
                    netpayCheckout.setAttribute('data-street1', addressField.value);
                    netpayCheckout.setAttribute('data-country', countryField.value);
                    netpayCheckout.setAttribute('data-city', cityField.value);
                    netpayCheckout.setAttribute('data-state', stateField.value);
                    netpayCheckout.setAttribute('data-postal-code', zipField.value);
                    netpayCheckout.setAttribute('data-phone-number', phoneField.value);
                    netpayCheckout.setAttribute('data-email', emailField.value);

                    netpayCheckout.setAttribute('data-token', tokenizedAmount);
                    netpayCheckout.setAttribute('data-merchant-reference-code', transactionId);

                    jQuery("#netpay-address").hide();
                    emailField.setAttribute("disabled", "");
                    phoneField.setAttribute("disabled", "");
                    addressField.setAttribute("disabled", "");
                    countryField.setAttribute("disabled", "");
                    cityField.setAttribute("disabled", "");
                    stateField.setAttribute("disabled", "");
                    zipField.setAttribute("disabled", "");
                    jQuery('#netpay-submission').show();

                    NetPay.init('<?= $paymentSettings->services->account_id ?>');
                    NetPay.setSandboxMode(false);
                    // used for testing TODO: add Sandbox mode
                    // NetPay.setSandboxMode(true);
                }else if(result.message){
                    console.log(result.message + ". Error setting up reservation. Your card was not charged");
                }else{
                    console.error({result});
                    console.log("There was an error creating your reservation, please try again later. Your card was not charged.");
                }
            },
            fail: function (request) {
                console.log(request.responseJSON.error + ". Your card was not charged.");
                jQuery("#address-button").html('<?= __('Pay', 'hostifybooking'); ?> <?= ListingHelper::withSymbol($totalPrice, $reserveInfo->prices, $listingInfo->currency_symbol) ?>');
            },
            error: function (request) {
                console.log("There was an error processing your request, please try again later. Your card was not charged.");
            },
        });
    }

    function onNetpayPaymentSuccess(r) {
        jQuery.ajax({
            type: 'POST',
            data: { action: 'netpay_payment_success', data: {
                reservation_id: reservationId,
                transaction_id: transactionId
            } },
            url: "<?= admin_url('admin-ajax.php') ?>",
            timeout: 180000, // 3 min
            success: function(response) {
                if (response.success) {
                    doGtagEvent('hfy_payment_success', {
                        listing_id: hfyReservationData.listing_id || '',
                        transaction_amount: hfyReservationData.total || ''
                    });

                    var redirectOnSuccess = typeof hfyRedirectOnSuccess !== 'undefined' ? hfyRedirectOnSuccess : null;
                    if (redirectOnSuccess) {
                        let href = new URL(redirectOnSuccess);
                        href.searchParams.set('reservation_id', reservationId);
                        href.searchParams.set('success', '1');
                        window.location.href = href.toString();
                    } else {
                        window.location.href = ['<?= HFY_PAGE_CHARGE_URL . '?reservation_id=' ?>', reservationId].join('');
                    }
                } else {
                    if (response.message)
                        console.log(response.message);
                }
            }
            //,error: function(xhr, status, error) {}
        });
    }

    function onNetpayPaymentError(r) {
        doGtagEvent('hfy_payment_error', {});
        jQuery.ajax({
            type: 'POST',
            data: { action: 'netpay_payment_fail', data: {
                reservation_id: reservationId,
                transaction_id: transactionId
            } },
            url: "<?= admin_url('admin-ajax.php') ?>",
            timeout: 180000, // 3 min
            success: function(response) {
                if (response.success) {
                    //window.location.href = ['<?php //= HFY_PAGE_CHARGE_URL . '?reservation_id=' ?>//', transactionId].join('');
                } else {
                    if (response.message)
                        console.log(response.message);
                }
            }
            //,error: function(xhr, status, error) {}
        });
    }
</script>
