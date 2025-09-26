<?php
if (!defined('WPINC')) die;
?>

<?php if (!empty($reserveInfo->extrasSet)) : ?>

	<h5><?= __('Selected plan:', 'hostifybooking') ?></h5>

	<?php foreach ($reserveInfo->extrasSet as $extra) : ?>
		<div class="booking-title sub">
			<div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= __($extra->fee_name, 'hostifybooking') ?>
			<small>/ <?= hfy_fee_charge_type_text($extra->fee_charge_type_id ?? 0, $nights, $guests) ?></small>
		</div>
	<?php endforeach; ?>

<?php endif; ?>
