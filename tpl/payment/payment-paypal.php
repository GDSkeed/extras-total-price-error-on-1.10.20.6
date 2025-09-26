<?php if (!defined('WPINC')) die; ?>

<div class="hfy-wrap data-block">
    <div class="row mtop-5 payment-columns">
        <div class="col-md-4 payment-info">
            <?php include hfy_tpl('payment/preview'); ?>
        </div>
        <div class="col-md-8 payment-content">
            <div id="payment-form-main-container">
                <div style="position:relative">
                    <div>
                        <div>
                            <?php include hfy_tpl('payment/paypal-form'); ?>
                        </div>
                    </div>
                    <?php include hfy_tpl('payment/time-left'); ?>
                </div>
            </div>
            <?php // include hfy_tpl('payment/_success'); ?>
            <?php // include hfy_tpl('payment/_error'); ?>
        </div>
    </div>
</div>
