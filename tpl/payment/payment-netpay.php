<?php if (!defined('WPINC')) die; ?>

<div class="hfy-wrap data-block">
    <div class="row mtop-5">
        <div class="col-md-5 payment-info">
            <?php include hfy_tpl('payment/preview'); ?>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 payment-content">
            <div id="payment-form-main-container">
                <div>
                    <div class="booking-title"><?= __('Payment', 'hostifybooking') ?></div>
                </div>
                <div style="position:relative">
                    <div>
                        <div>
                            <?php include hfy_tpl('payment/netpay-form'); ?>
                        </div>
                    </div>
                    <?php include hfy_tpl('payment/time-left'); ?>
                </div>
            </div>
            <?php include hfy_tpl('payment/stripe-form-3ds_error');?>
        </div>
    </div>
</div>