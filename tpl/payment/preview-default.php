<?php
if (!defined('WPINC')) die;

$price_original = $reserveInfo->prices->v3->base_price_original ?? $reserveInfo->prices->price_original ?? 0;
$p = $reserveInfo->prices->v3->base_price ?? $reserveInfo->prices->base_price ?? 0;
if ($price_original > 0 && $p > 0 && $p < $price_original) {
    //
} else {
    $price_original = 0;
}

// Define variables for subtotal logic (V2 ONLY)
// Check for regular extras and taxes
$hasExtras = !empty($reserveInfo->prices->extras) || !empty($reserveInfo->prices->extrasSet) || !empty($reserveInfo->prices->extrasOptional);
$hasTaxes = !empty($reserveInfo->prices->taxes);

// Check for selected optional extras (from step 2)
$hasSelectedOptionalExtras = false;
if (!empty($reserveInfo->extrasOptional) && isset($reserveInfo->extrasOptionalSelected)) {
    foreach ($reserveInfo->extrasOptional as $extra) {
        if ($reserveInfo->extrasOptionalSelected[$extra->fee_id] ?? false) {
            $hasSelectedOptionalExtras = true;
            break;
        }
    }
}

// Use the same subtotal logic as the booking form
$shouldShowSubtotal = empty($reserveInfo->prices->v3) && isset($reserveInfo->prices->subtotal) && ($hasExtras || $hasTaxes || $hasSelectedOptionalExtras);

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
<?php // FEES ?>
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

<?php // DISCOUNTS - V2 ONLY ?>
<?php if (empty($reserveInfo->prices->v3)): ?>

    <?php if (isset($reserveInfo->monthlyDiscount) && $reserveInfo->monthlyDiscount <> 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->monthlyDiscount, $reserveInfo->prices, $sym) ?></div>
            <?= $reserveInfo->monthlyDiscountPercent ?>% <?= __('monthly discount', 'hostifybooking') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($reserveInfo->weeklyDiscount) && $reserveInfo->weeklyDiscount <> 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->weeklyDiscount, $reserveInfo->prices, $sym) ?></div>
            <?= $reserveInfo->weeklyDiscountPercent ?>% <?= __('weekly discount', 'hostifybooking') ?>
        </div>
    <?php endif; ?>

    <?php // Coupon discount ?>
    <?php if (HFY_SHOW_DISCOUNT): ?>
        <?php
        $has_legacy_discount = isset($reserveInfo->prices->discount) && isset($reserveInfo->prices->discount->success) && $reserveInfo->prices->discount->success;
        if ($has_legacy_discount) { ?>
            <div class="booking-title sub">
                <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->discount->abs, $reserveInfo->prices, $sym) ?></div>
                <?= $reserveInfo->prices->discount->type == '%' ? $reserveInfo->prices->discount->message . ' coupon' : 'Coupon' ?> discount
            </div>
        <?php } ?>
    <?php endif; ?>

<?php endif; ?>

<?php // V2 SUBTOTAL (only show for V2 when there are extras or taxes) ?>
<?php if ($shouldShowSubtotal) : ?>
    <div class="booking-title sub" style="border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 10px;">
        <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->subtotal ?? 0, $reserveInfo->prices, $sym) ?></div>
        <?= __( 'Subtotal', 'hostifybooking' ) ?>
    </div>
<?php endif; ?>
<!-- DEBUG PAYMENT: shouldShowSubtotal=<?= $shouldShowSubtotal ? 'true' : 'false' ?>, hasExtras=<?= $hasExtras ? 'true' : 'false' ?>, hasTaxes=<?= $hasTaxes ? 'true' : 'false' ?>, hasSelectedOptionalExtras=<?= $hasSelectedOptionalExtras ? 'true' : 'false' ?>, v3=<?= !empty($reserveInfo->prices->v3) ? 'true' : 'false' ?> -->

<?php // EXTRAS ?>
<?php if (!empty($reserveInfo->prices->extras)) foreach ($reserveInfo->prices->extras as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $sym) ?></div>
            <?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($reserveInfo->prices->extrasSet)) foreach ($reserveInfo->prices->extrasSet as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $sym) ?></div>
            <?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($reserveInfo->prices->extrasOptional)) foreach ($reserveInfo->prices->extrasOptional as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $sym) ?></div>
            <?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php // SELECTED OPTIONAL EXTRAS are handled by preview-extras-optional.php template ?>

<?php // Display regular extras if any are selected (fallback)
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

<?php // TAXES ?>
<?php if (!empty($reserveInfo->prices->taxes)) foreach ($reserveInfo->prices->taxes as $t) : ?>
    <?php if ($t->total != 0): ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($t->total, $reserveInfo->prices, $sym) ?></div>
            <?= $t->fee_name ?: $t->name ?: __('Tax', 'hostifybooking') ?> <?= $t->charge_type_label ?? '' ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php // Display tax from prices object (more reliable) - fallback
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
	// V3: Monthly/weekly discounts are already applied to base_price
	// DON'T show them as separate line items (would be double-counting)
	// Only show coupon discounts which are applied on top of the already-discounted price
	?>

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
