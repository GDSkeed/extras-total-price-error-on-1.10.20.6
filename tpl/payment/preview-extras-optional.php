<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($reserveInfo->extrasOptional)) : ?>

	<div class="booking-block">
		<?php foreach ($reserveInfo->extrasOptional as $extra): ?>
			<?php if ($extrasOptionalSelected[$extra->fee_id] ?? false): ?>
				<div class="booking-title sub extras-optional">
					<div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
					<label>
						&#10003;
						<?= __($extra->fee_name, 'hostifybooking') ?>
					</label>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

<?php endif; ?>
