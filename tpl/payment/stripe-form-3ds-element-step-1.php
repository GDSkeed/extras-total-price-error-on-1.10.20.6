<?php
if (!defined('WPINC')) die;

$stepActive = 1;
include hfy_tpl('payment/stripe-form-3ds-step-slider');

?>

<div class="booking-tab-title"><?= __('Personal details', 'hostifybooking') ?></div>

<div class="row">
	<div class="col">
		<div class="field">
			<label for="hfy-payment-name"><?= __('Name', 'hostifybooking') ?></label>
			<input id="hfy-payment-name" name="pname" class="input <?= $fill_name ? '' : 'empty' ?>" type="text" placeholder="<?= esc_attr__('Name', 'hostifybooking') ?>" required="" value="<?= esc_attr($fill_name) ?>">
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="field">
			<label for="hfy-payment-email"><?= __('Email', 'hostifybooking') ?></label>
			<input id="hfy-payment-email" name="pemail" class="input <?= $fill_email ? '' : 'empty' ?>" type="email" placeholder="<?= esc_attr__('Email', 'hostifybooking') ?>" required="" value="<?= esc_attr($fill_email) ?>" data-value="<?= esc_attr($fill_email) ?>">
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="field">
			<label for="hfy-payment-phone"><?= __('Phone', 'hostifybooking') ?></label>
			<input id="hfy-payment-phone" name="pphone" class="input <?= $fill_phone ? '' : 'empty' ?>" type="tel" placeholder="<?= esc_attr__('Phone', 'hostifybooking') ?>" required="" value="<?= esc_attr($fill_phone) ?>" pattern=".+" title="<?= esc_attr__('Phone with country code is mandatory. For example: +1234567890', 'hostifybooking') ?>">
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="field textarea-field">
			<label for="hfy-payment-note"><?= __('Note', 'hostifybooking') ?></label>
			<textarea id="hfy-payment-note" name="note" class="input input-note <?= $reserveInfo->note ? '' : 'empty' ?>" placeholder="<?= esc_attr__('Feel free to leave any additional questions...', 'hostifybooking') ?>"><?= $reserveInfo->note ? $reserveInfo->note : ''; ?></textarea>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-7 field hfy-payment-address-country-wrap">
		<label for="hfy-payment-address-country"><?= __('Country', 'hostifybooking') ?></label>
		<select class="input empty" id="hfy-payment-address-country" required>
			<option value="" disabled selected><?= __('Country', 'hostifybooking'); ?></option>
			<?php foreach (HFY_COUNTRY_CODES_ALPHA2 as $c_code => $c_name) : ?>
				<option value="<?= $c_code; ?>"><?= __($c_name, 'hostifybooking'); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-5 field">
		<label for="hfy-payment-zip"><?= __('Postal code', 'hostifybooking') ?></label>
		<input id="hfy-payment-zip" placeholder="<?= esc_attr__('ZIP', 'hostifybooking') ?>" name="zip" class="input <?= $reserveInfo->zip ? '' : 'empty' ?>" type="text" <?= $reserveInfo->zip ? 'value="' . esc_attr($reserveInfo->zip) . '"' : ''; ?> required />
	</div>
</div>

<div class="row mt-5">
	<div class="col text-right">
		<button class='next-btn' type="button"><?= __('Next', 'hostifybooking') ?> &nbsp; &rsaquo;</button>
	</div>
</div>
