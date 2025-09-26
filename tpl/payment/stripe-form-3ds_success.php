<?php
if (!defined('WPINC')) die;

$color = $settings->custom_color ?? '#007bff';
?>
<div id="payment-form-success" class="widget hfy-payment-success hidden">
    <div class="header">
        <div class="text-center">
            <svg id="successAnimation" class="animated" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 70 70"><path id="successAnimationResult" fill="<?= $color ?>" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"></path><circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="<?= $color ?>" stroke-width="2" stroke-linecap="round" fill="transparent"></circle><polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"></polyline></svg>
        </div>
        <div class=" text-center text-uppercase h4">
            <?= __('Payment Successful', 'hostifybooking') ?>
        </div>
        <div class="text-center text-muted" id="transaction-message"></div>
    </div>
    <div class="body">
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Property', 'hostifybooking') ?>
            </div>
            <div class="col-7" id="property-name"></div>
        </div>
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Reservation', 'hostifybooking') ?>
            </div>
            <div class="col-7" id="reservation-id"></div>
        </div>
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Transaction No', 'hostifybooking') ?>
            </div>
            <div class="col-7" id="transaction-id"></div>
        </div>
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Transaction Date', 'hostifybooking') ?>
            </div>
            <div class="col-7" id="transaction-date"></div>
        </div>
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Payment Method', 'hostifybooking') ?>
            </div>
            <div class="col-7">
                <div id="transaction-payment-method"></div>
            </div>
        </div>
        <div class="row mt-1 mb-2">
            <div class="col-5 font-weight-bold">
                <?= __('Payment Amount', 'hostifybooking') ?>
            </div>
            <div class="col-7" id="transaction-amount"></div>
        </div>
        <div class="row mt-4">
            <a href="/" class='col'>
                <div class="btn btn-success btn-block btn-lg round-1" style="background-color: <?= $color ?> !important; border-color: <?= $color ?> !important;">
                    <?= __('Go Back', 'hostifybooking') ?>
                </div>
            </a>
        </div>
    </div>
</div>