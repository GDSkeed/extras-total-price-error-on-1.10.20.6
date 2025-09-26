<?php if (!defined('WPINC')) die; ?>

<div class="container data-block large-margin">
    <div class="payment-content payment-response-container">
        <div class="booking-title"></div>
        <div>
            <?php
            if ($paymentSuccess) {
                include hfy_tpl('payment/_success');
            } else {
                include hfy_tpl('payment/_error');
            }
            ?>
        </div>
    </div>
</div>

<div class="payment-content">
    <?php // include hfy_tpl('payment/preview'); ?>
</div>
