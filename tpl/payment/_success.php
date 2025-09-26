<?php
if (!defined('WPINC')) die;

$cardBrand = !empty($paymentData->source) && !empty($paymentData->source->brand) ? $paymentData->source->brand : null;
$lastFour = !empty($paymentData->source) && !empty($paymentData->source->last4) ? $paymentData->source->last4 : null;
$amount = !empty($paymentData->amount) ? ($paymentData->amount / 100) : null;
$currency = !empty($paymentData->currency) ? strtoupper($paymentData->currency) : null;
$note = !empty($paymentData->note) ? nl2br($paymentData->note) : null;
?>
<div class="widget">
    <div class="header">
        <div class="text-center">
            <svg id="successAnimation" class="animated" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 70 70"><path id="successAnimationResult" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"></path><circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke-width="2" stroke-linecap="round" fill="transparent"></circle><polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"></polyline></svg>
        </div>
        <div class=" text-center text-uppercase h4">
            <?= __('Payment successful', 'hostifybooking') ?>
        </div>
        <div class="text-center text-muted">
            <?= $message; ?>
        </div>
    </div>
    <div class="body">
        <?php if (!empty($paymentData->id)) { ?>
            <div class="row mt-1 mb-2">
                <div class="col-5 font-weight-bold">
                    <?= __('Transaction No', 'hostifybooking') ?>
                </div>
                <div class="col-7">
                    <?= $paymentData->id ?>
                </div>
            </div>
        <?php }
        if (!empty($paymentData->created)) { ?>
            <div class="row mt-1 mb-2">
                <div class="col-5 font-weight-bold">
                    <?= __('Transaction date', 'hostifybooking') ?>
                </div>
                <div class="col-7">
                    <?= date('d/m/Y H:i:s', $paymentData->created) ?>
                </div>
            </div>
        <?php }
        if (!empty($cardBrand) && !empty($lastFour)) {
            $brand = in_array(strtolower($cardBrand), ['visa', 'mastercard'])
                ? '<img src="' . HOSTIFYBOOKING_URL . 'public/res/images/payment-' . strtolower($cardBrand) . '.svg">'
                : ucwords($cardBrand);
            $label = sprintf('%s **** **** **** %s', $brand, $lastFour);
            ?>
            <div class="row mt-1 mb-2">
                <div class="col-5 font-weight-bold">
                    <?= __('Payment method', 'hostifybooking') ?>
                </div>
                <div class="col-7">
                    <div>
                        <?= $label ?>
                    </div>
                </div>
            </div>
        <?php }
        if (!empty($amount)) { ?>
            <div class="row mt-1 mb-2">
                <div class="col-4 font-weight-bold">
                    <?= __('Payment amount', 'hostifybooking') ?>
                </div>
                <div class="col-8">
                    <?= $amount . ' ' . (!empty($currency) ? ' ' . $currency : '') ?>
                </div>
            </div>
        <?php }
        if (!empty($note)) { ?>
            <div class="row mt-3 mb-2">
                <div class="col-12">
                    <div class="small font-weight-bold text-uppercase mb-2">
                        <?= __('Note', 'hostifybooking') ?>
                    </div>
                    <div class="small text-muted">
                        <?= $note ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row mt-4">
            <a href="/" class='col'>
                <div class="btn btn-success btn-block btn-lg round-1">
                    <?= __('Go back', 'hostifybooking') ?>
                </div>
            </a>
        </div>
    </div>
</div>