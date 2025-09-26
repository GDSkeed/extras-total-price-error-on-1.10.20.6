<?php

// Payment

if (!defined('WPINC')) die;

$stepActive = $sliderStepsCount < 3 ? 2 : 3;
include hfy_tpl('payment/stripe-form-3ds-step-slider');

?>

<div class="booking-tab-title"><?= __('Payment', 'hostifybooking') ?></div>

<div class="row">
	<div class="col">
		<div class="field">
			<label for="hfy-payment-card-number"><?= __('Card number', 'hostifybooking') ?></label>
			<div id="hfy-payment-card-number" class="input empty"></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-6 field">
		<label for="hfy-payment-card-expiry"><?= __('Expiration', 'hostifybooking') ?></label>
		<div id="hfy-payment-card-expiry" class="input empty"></div>
	</div>
	<div class="col-6 field">
		<label for="hfy-payment-card-cvc"><?= __('CVC', 'hostifybooking') ?></label>
		<div id="hfy-payment-card-cvc" class="input empty"></div>
	</div>
</div>

<input id="discount-code" name="discount_code" type="hidden" value="<?= esc_attr($reserveInfo->discount_code) ?>" />
<input id="dcid" name="dcid" type="hidden" value="<?= esc_attr($reserveInfo->dcid) ?>" />
<input id="fees" name="fees" type="hidden" value="<?= esc_attr($reserveInfo->fees_ids) ?>" />

<div class="row">
	<div class="col">
		<div class="error" role="alert" style='display:none'>
			<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"><path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path><path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path></svg>
			<span id="card-errors" class="message"></span>
		</div>
	</div>
</div>

<?php if (!empty($terms)): ?>
	<div class="row terms-row mt-5">
		<div class="col">
			<div class="terms-checkbox">
				<input type="checkbox" name="terms" />
				<span></span>
			</div>
			<?= $terms ?>
		</div>
	</div>
<?php endif; ?>

<div class="row mt-5">
	<div class="col">
		<button class='prev-btn' type="button">&lsaquo; &nbsp; <?= __('Back', 'hostifybooking') ?></button>
	</div>
	<div class="col text-right pay-btn-wrap">
		<button class='pay-btn' type="submit">
			<?= __('Pay', 'hostifybooking') ?>
			<!-- <?= ListingHelper::withSymbol($totalPrice, $reserveInfo->prices, $listingInfo->currency_symbol) ?> -->
		</button>
	</div>
</div>
