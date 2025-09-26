<?php
if (!defined('WPINC')) die;

$stepActive = 2;
include hfy_tpl('payment/stripe-form-3ds-step-slider');

?>

<div class="booking-tab-title"><?= __('Extras', 'hostifybooking') ?></div>

<?php if (!empty($reserveInfo->extrasOptional)) : ?>
	<div class="extras-optional-wrap">

		<?php foreach ($reserveInfo->extrasOptional as $extra) :
			$checked = $extrasOptionalSelected[$extra->fee_id] ?? false;
			?>
			<div class="booking-title sub extras-optional">
				<div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
				<label>
					<input type="checkbox" value="<?= $extra->fee_id ?>" <?= $checked ? 'checked' : '' ?> />
					<?= __($extra->fee_name, 'hostifybooking') ?>
					<?php $t = hfy_fee_charge_type_text($extra->fee_charge_type_id ?? 0, $nights, $guests); ?>
					<?= empty($t) ? '' : "<small>/ $t</small>" ?>
				</label>
			</div>
		<?php endforeach; ?>

	</div>
<?php endif; ?>

<div class="row mt-5">
	<div class="col">
		<button class='prev-btn' type="button">&lsaquo; &nbsp; <?= __('Back', 'hostifybooking') ?></button>
	</div>
	<div class="col text-right">
		<button class='next-btn' type="button"><?= __('Next', 'hostifybooking') ?> &nbsp; &rsaquo;</button>
	</div>
</div>

