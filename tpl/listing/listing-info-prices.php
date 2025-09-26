<?php if (!defined('WPINC')) die; ?>

<ul class="hfy-listing-info-prices">

	<li>
		<?= __('Nightly:', 'hostifybooking'); ?>
		<span><?= __('from', 'hostifybooking'); ?> <?= ListingHelper::formatPrice($listing->min_prices->min_price ?? $listing->price, $listing, true); ?></span>
	</li>

	<?php if (!empty($listingData->weekend_price) && $listingData->weekend_price > 0) : ?>
		<li>
			<?= __('Weekends (Sat & Sun):', 'hostifybooking'); ?>
			<span><?= __('from', 'hostifybooking'); ?> <?= ListingHelper::formatPrice($listingData->weekend_price, $listing, true); ?></span>
		</li>
	<?php endif; ?>

	<?php if (!empty($listingData->monthly_price_factor) && $listingData->monthly_price_factor > 0) : ?>
		<li>
			<?= __('Monthly (30d+):', 'hostifybooking'); ?>
			<span><?= __('from', 'hostifybooking'); ?> <?= ListingHelper::formatPrice($listing->min_prices->min_price_monthly ?? ($listingPrice->price * $listingData->monthly_price_factor * 30), $listing, true, 4); ?></span>
		</li>
	<?php endif; ?>

	<?php if (!empty($listingData->security_deposit) && $listingData->security_deposit > 0) : ?>
		<li>
			<?= __('Security Deposit:', 'hostifybooking'); ?>
			<span><?= ListingHelper::formatPrice($listingData->security_deposit, $listing, true); ?></span>
		</li>
	<?php endif; ?>

	<?php if (!empty($listingData->extra_person) ) { ?>
		<li>
			<?= __('Allow Additional Guests:', 'hostifybooking'); ?>
			<span><?= __('Yes', 'hostifybooking'); ?></span>
		</li>
		<li>
			<?= __('Additional Guests:', 'hostifybooking'); ?>
			<span><?= ListingHelper::formatPrice($listingData->extra_person, $listing, true); ?></span>
		</li>
	<?php } ?>

	<?php if (!empty($listingData->cleaning_fee) && $listingData->cleaning_fee > 0) : ?>
		<li>
			<?= __('Cleaning Fee: ', 'hostifybooking'); ?>
			<span><?= ListingHelper::formatPrice($listingData->cleaning_fee, $listing, true) ?></span>
		</li>
	<?php endif; ?>

	<?php if (!empty($listingData->city_tax_formula) && $listingData->city_tax_formula > 0) : ?>
		<li><?= __('Per Stay City Free: ', 'hostifybooking'); ?>
		<span><?= ListingHelper::formatPrice($listingData->city_tax_formulas, $listing, true) ?></span></li>
	<?php endif; ?>

	<?php if (!empty($listingData->min_nights) && $listingData->min_nights > 0) : ?>
		<li>
			<?= __('Per Stay Minimum Number Of Days: ', 'hostifybooking'); ?>
			<span><?= $listingData->min_nights; ?></span>
		</li>
	<?php endif; ?>

	<?php if (!empty($listingData->max_nights) && $listingData->max_nights > 0) : ?>
		<li>
			<?= __('Maximum Number Of Days: ', 'hostifybooking'); ?>
			<span><?= $listingData->max_nights; ?></span>
		</li>
	<?php endif; ?>

</ul>