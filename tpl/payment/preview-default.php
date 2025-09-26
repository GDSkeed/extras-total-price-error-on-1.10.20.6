<?php
if (!defined('WPINC')) die;

$price_original = $reserveInfo->prices->v3->base_price_original ?? $reserveInfo->prices->price_original ?? 0;
$p = $reserveInfo->prices->v3->base_price ?? $reserveInfo->prices->base_price ?? 0;
if ($price_original > 0 && $p > 0 && $p < $price_original) {
    //
} else {
    $price_original = 0;
}

?>

<div class="booking-title sub">
	<div style="float:right">
		<?php if ($price_original > 0): ?>
            <s style="color:#aaa"><?= ListingHelper::withSymbol($price_original, $reserveInfo->prices, $sym) ?></s><br/>
        <?php endif; ?>
		<?= ListingHelper::withSymbol($reserveInfo->prices->priceWithMarkup, $reserveInfo->prices, $sym) ?>
	</div>
	<div>
		<?php // ListingHelper::withSymbol($listingPricePerNight, $reserveInfo->prices, $sym) ?>
		<?= __('Price for', 'hostifybooking') ?> <?=$reserveInfo->listingInfo->nights;?> <?= __('nights', 'hostifybooking') ?>
	</div>
</div>

<?php 
?>
<?php if (isset($reserveInfo->prices->cleaning_fee) && $reserveInfo->prices->cleaning_fee > 0) { ?>
	<div class="booking-title sub">
		<div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->cleaning_fee, $reserveInfo->prices, $sym) ?></div>
		<?= __('Cleaning fee', 'hostifybooking') ?>
	</div>
<?php } else { ?>
	<!-- Debug: cleaning_fee not displayed - value: <?= $reserveInfo->prices->cleaning_fee ?? 'not set' ?>, isset: <?= isset($reserveInfo->prices->cleaning_fee) ? 'yes' : 'no' ?>, >0: <?= ($reserveInfo->prices->cleaning_fee ?? 0) > 0 ? 'yes' : 'no' ?> -->
<?php } ?>

<?php if (isset($reserveInfo->listingInfo->extra_person_price) && $reserveInfo->listingInfo->extra_person_price > 0) { ?>
	<div class="booking-title sub">
		<div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->listingInfo->extra_person_price, $reserveInfo->prices, $sym) ?></div>
		<?= __('Extra person', 'hostifybooking') ?>
	</div>
<?php } ?>

<?php // Display tax from prices object (more reliable)
$taxAmount = $reserveInfo->prices->tax_amount ?? 0;
if ($taxAmount > 0) { ?>
	<div class="booking-title sub">
		<div style="float:right"><?= ListingHelper::withSymbol($taxAmount, $reserveInfo->prices, $sym) ?></div>
		<?= __('Tax', 'hostifybooking') ?>
	</div>
<?php } else if (isset($reserveInfo->listingInfo->tax) && $reserveInfo->listingInfo->tax > 0) { ?>
	<div class="booking-title sub">
		<div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->listingInfo->tax, $reserveInfo->prices, $sym) ?></div>
		<?= __('Tax', 'hostifybooking') ?>
	</div>
<?php } ?>

<?php // Display regular extras if any are selected
if (isset($reserveInfo->selectedExtras) && !empty($reserveInfo->selectedExtras)) {
    foreach ($reserveInfo->selectedExtras as $extra) {
        if (isset($extra->price) && $extra->price > 0) { ?>
            <div class="booking-title sub">
                <div style="float:right"><?= ListingHelper::withSymbol($extra->price, $reserveInfo->prices, $sym) ?></div>
                <?= $extra->name ?? $extra->title ?? 'Extra' ?>
            </div>
        <?php }
    }
} ?>

<?php if (empty($reserveInfo->prices->v3)): ?>

	<?php if (isset($reserveInfo->monthlyDiscount) && $reserveInfo->monthlyDiscount <> 0): ?>
		<div class="booking-title sub">
			<div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->monthlyDiscount, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= $reserveInfo->monthlyDiscountPercent ?>% <?= __('monthly discount', 'hostifybooking') ?>
		</div>
	<?php endif; ?>

	<?php if (isset($reserveInfo->weeklyDiscount) && $reserveInfo->weeklyDiscount <> 0): ?>
		<div class="booking-title sub">
			<div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->weeklyDiscount, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= $reserveInfo->weeklyDiscountPercent ?>% <?= __('weekly discount', 'hostifybooking') ?>
		</div>
	<?php endif; ?>

<?php else: ?>

	<?php 
	// Only show the general discount if it's not from a coupon
	$has_v3_coupon = !empty($reserveInfo->prices->v3) && !empty($reserveInfo->prices->v3->coupon);
	if (!empty($reserveInfo->prices->v3->discount_percent) && $reserveInfo->prices->v3->discount_percent > 0 && !$has_v3_coupon):?>
		<div class="booking-title sub">
			<div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->v3->discount_percent * $reserveInfo->prices->v3->base_price_original / 100, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= $reserveInfo->prices->v3->discount_percent ?>% <?= __('discount', 'hostifybooking') ?>
		</div>
	<?php endif;?>

<?php endif;?>

<?php if (HFY_SHOW_DISCOUNT): ?>
	<?php 
	$has_v3_coupon = !empty($reserveInfo->prices->v3) && !empty($reserveInfo->prices->v3->coupon);
	$has_legacy_discount = isset($reserveInfo->prices->discount) && isset($reserveInfo->prices->discount->success) && $reserveInfo->prices->discount->success;
	
	if ($has_v3_coupon): ?>
		<div class="booking-title sub">
			<div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->v3->coupon->discount_absolute, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= __('Coupon discount', 'hostifybooking') ?>
		</div>
	<?php elseif ($has_legacy_discount): ?>
		<div class="booking-title sub">
			<div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->discount->abs, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
			<?= __('Coupon discount', 'hostifybooking') ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
