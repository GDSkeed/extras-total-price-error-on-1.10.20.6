<?php if (!defined('WPINC')) die; ?>

<div class="hfy-wrap payment-extras-set <?= $selected ? 'selected' : '' ?>" data-ids="<?= $ids ?>">

	<?php if (!empty($content)) echo $content; ?>

	<?php if ($detailed): ?>
		<?php foreach ($extras as $extra): ?>
			<div class="row payment-extras-set-item" data-id="<?= $extra->fee_id ?>">
				<div class="col-md-8">
					<?= __($extra->name, 'hostifybooking') ?>
					<?php if (!empty($extra->description)): ?>
						<div class="extra-description hfy-wrap small">
							<?= nl2br($extra->description) ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-md-4 text-right price">
					<?php // todo  $extra->percent ?>
					<?= ListingHelper::withSymbol($extra->amount, $listing->currency_data) ?>
					<small><?= hfy_fee_charge_type($extra->fee_charge_type_id ?? 0) ?></small>
				</div>
			</div>
		<?php endforeach; ?>
		<hr/>
	<?php endif; ?>

	<?php if ($total): ?>
		<div class="col text-center price">
			<?= ListingHelper::withSymbol($total, $listing->currency_data) ?>
		</div>
	<?php endif; ?>

</div>
