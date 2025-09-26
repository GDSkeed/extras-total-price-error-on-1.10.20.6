<?php if (!defined('WPINC')) die; ?>

<div class="hfy-wrap payment-extras-optional">
	<?php foreach ($extras as $extra): ?>
		<label class="row payment-extras-optional-item <?= $checked ? "selected" : '' ?>" data-id="<?= $extra->fee_id ?>">
			<input type="checkbox" class="d-none" id="extra_opt_<?= $extra->fee_id ?>" <?= $checked ? 'checked' : '' ?> />
			<div class="col-md-1">
				<span class="checkbox-control"></span>
			</div>
			<div class="col-md-6">
				<?= __($extra->name, 'hostifybooking') ?>
				<?php if (!empty($extra->description)): ?>
					<div class="extra-description hfy-wrap small">
						<?= nl2br($extra->description) ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="col-md-5 text-right price">
				<?php // todo  $extra->percent ?>
				<?= ListingHelper::withSymbol($extra->amount, $listing->currency_data) ?>
				<small><?= hfy_fee_charge_type($extra->fee_charge_type_id ?? 0) ?></small>
			</div>
		</label>
	<?php endforeach; ?>
</div>
