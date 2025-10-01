<?php
if (!defined('WPINC')) die;

$price_original = $prices->v3->base_price_original ?? $prices->price_original ?? 0;
$p = $prices->v3->base_price ?? $prices->base_price ?? 0;
if ($price_original > 0 && $p > 0 && $p < $price_original) {
    //
} else {
    $price_original = 0;
}

?>

<?php // ACCOMODATION ?>

<?php if (empty($prices->v3)): ?>

    <div class='price-block-item'>
        <div class="_label">
            <?php // ListingHelper::withSymbol($listingPricePerNight, $prices, $currencySymbol) ?>
            <?= __( 'Price for', 'hostifybooking' ) ?> <?= $prices->nights ?> <?= __( 'nights', 'hostifybooking' ) ?>
        </div>
        <div class="_value">
            <?php if ($price_original > 0): ?>
                <s style="color:#aaa"><?= ListingHelper::withSymbol($price_original, $prices, $currencySymbol) ?></s><br/>
            <?php endif; ?>
            <?= ListingHelper::withSymbol($totalNights ?? $prices->priceWithMarkup, $prices, $currencySymbol) ?>
        </div>
    </div>

<?php else: ?>

    <?php if (!empty($prices->v3->advanced_fees)): ?>
        <?php foreach ($prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'accommodation'): ?>
                <div class='price-block-item'>
                    <div class="_label">
                        <?= $fee->name ?>
                        <br/><small><?= ListingHelper::withSymbol($fee->amount, $prices, $currencySymbol) ?> <?= $fee->fee_charge_type ?></small>
                    </div>
                    <div class="_value"><?= ListingHelper::withSymbol($fee->total, $prices, $currencySymbol) ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Fallback for v3 pricing without advanced_fees -->
        <div class='price-block-item'>
            <div class="_label">
                <?= __('Accommodation', 'hostifybooking') ?>
                <br/><small><?= ListingHelper::withSymbol($listingPricePerNight ?? 0, $prices, $currencySymbol) ?> <?= __('Per Night', 'hostifybooking') ?></small>
            </div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->v3->base_price_total ?? $totalNights, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>

<?php endif;?>

<?php // DISCOUNTS ?>

<?php if (empty($prices->v3)): ?>

    <?php if (!empty($prices->monthlyPriceDiscountPercent) && $prices->monthlyPriceDiscountPercent > 0):?>
        <div class='price-block-item'>
            <div class="_label"><?=$prices->monthlyPriceDiscountPercent?>% <?= __( 'monthly discount', 'hostifybooking' ) ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->monthlyPriceDiscount, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif;?>

    <?php if (!empty($prices->weeklyPriceDiscountPercent) && $prices->weeklyPriceDiscountPercent > 0):?>
        <div class='price-block-item'>
            <div class="_label"><?=$prices->weeklyPriceDiscountPercent?>% <?= __( 'weekly discount', 'hostifybooking' ) ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->weeklyPriceDiscount, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif;?>

<?php else: ?>

    <?php
    // V3: Weekly/monthly discounts are already applied to base_price in advanced_fees
    // DON'T show them as separate line items (would be double-counting)
    // Only show coupon discounts which are applied on top of the already-discounted price
    ?>

<?php endif;?>

<?php if (HFY_SHOW_DISCOUNT): ?>
    <?php
    $has_v3_coupon = !empty($prices->v3) && !empty($prices->v3->coupon);
    $has_legacy_discount = isset($prices->discount) && isset($prices->discount->success) && $prices->discount->success;
    
    if ($has_v3_coupon) { 
        // No need to differentiate between absolute and percentage coupon types
        // Always show "Coupon discount" with the absolute amount
        ?>
        <div class='price-block-item'>
            <div class="_label"><?= __('Coupon discount', 'hostifybooking') ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->v3->coupon->discount_absolute, $prices, $currencySymbol) ?></div>
        </div>
        <input name="dcid" type="hidden" value="<?= $prices->v3->coupon->coupon_id ?>" />
    <?php } else if ($has_legacy_discount) { ?>
        <div class='price-block-item'>
            <div class="_label"><?= $prices->discount->type == '%' ? $prices->discount->message . ' coupon' : 'Coupon' ?> discount</div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->discount->abs, $prices, $currencySymbol) ?></div>
        </div>
        <input name="dcid" type="hidden" value="<?= isset($prices->discount->id) ? $prices->discount->id : 0 ?>" />
    <?php } else if (isset($discount_code) && trim($discount_code) !== '' && empty($prices->v3->coupon)) { ?>
        <div class='price-block-item'>
            <div class="_label"><?= __('Coupon Discount', 'hostifybooking') ?></div>
            <div class="_value color-red"><?= __('Wrong or inactive code', 'hostifybooking') ?></div>
        </div>
    <?php } ?>
<?php endif;?>

<?php // FEES, TAXES ?>

<?php if ($prices->cleaning_fee) { ?>
    <div class='price-block-item'>
        <div class="_label"><?= __( 'Cleaning fee', 'hostifybooking' ) ?></div>
        <div class="_value"><?= ListingHelper::withSymbol($prices->cleaning_fee, $prices, $currencySymbol) ?></div>
    </div>
<?php } ?>

<?php if ($prices->extra_person_price) { ?>
    <div class='price-block-item'>
        <div class="_label"><?= __( 'Extra person', 'hostifybooking' ) ?></div>
        <div class="_value"><?= ListingHelper::withSymbol($prices->extra_person_price, $prices, $currencySymbol) ?></div>
    </div>
<?php } ?>

<?php 
?>

<?php if ($tax): ?>
    <div class='price-block-item'>
        <div class="_label"><?= __( 'Tax', 'hostifybooking' ) ?></div>
        <div class="_value"><?= ListingHelper::withSymbol($tax, $prices, $currencySymbol) ?></div>
    </div>
<?php else: ?>
    <!-- Debug: Tax not displayed - tax value: <?= $tax ?? 'not set' ?>, isset: <?= isset($tax) ? 'yes' : 'no' ?>, >0: <?= ($tax ?? 0) > 0 ? 'yes' : 'no' ?> -->
<?php endif; ?>

<?php // V2 SUBTOTAL (only show for V2 when there are extras or taxes) ?>
<?php 
$hasExtras = !empty($prices->extras) || !empty($prices->extrasSet) || !empty($prices->extrasOptional);
$hasTaxes = !empty($prices->taxes);
$shouldShowSubtotal = empty($prices->v3) && isset($prices->subtotal) && ($hasExtras || $hasTaxes);
?>
    <?php if ($shouldShowSubtotal) : ?>
        <div class='price-block-item' style="border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 10px;">
            <div class="_label"><?= __( 'Subtotal', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->subtotal ?? 0, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>

<?php // EXTRAS ?>
<?php if (!empty($prices->extras)) foreach ($prices->extras as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class='price-block-item'>
            <div class="_label"><?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($extra->total, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($prices->extrasSet)) foreach ($prices->extrasSet as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class='price-block-item'>
            <div class="_label"><?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($extra->total, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($prices->extrasOptional)) foreach ($prices->extrasOptional as $extra) : ?>
    <?php if ($extra->total != 0): ?>
        <div class='price-block-item'>
            <div class="_label"><?= $extra->fee_name ?: $extra->name ?: __('Extra', 'hostifybooking') ?> <?= $extra->charge_type_label ?? '' ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($extra->total, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php // TAXES - V2 ONLY (V3 taxes are in advanced_fees) ?>
<?php if (empty($prices->v3) && !empty($prices->taxes)) foreach ($prices->taxes as $t) : ?>
    <?php if ($t->total != 0): ?>
        <div class='price-block-item'>
            <div class="_label"><?= $t->fee_name ?: $t->name ?: __('Tax', 'hostifybooking') ?> <?= $t->charge_type_label ?? '' ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($t->total, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php // TOTAL ?>

<div class='price-block-item price-block-total<?= $shouldShowSubtotal ? ' no-border-top' : '' ?>'>
    <div class="_label"><?= __( 'Total', 'hostifybooking' ) ?></div>
    <?php 
    // Calculate the total value for debugging
    $calculatedTotal = (HFY_USE_API_V3 && isset($prices->v3)) || isset($prices->v3)
        ? $prices->v3->total 
        : ($prices->totalAfterTax ?? $prices->totalPrice ?? $prices->total ?? 0);
    
    ?>
    <div class="_value"><?= ListingHelper::withSymbol($calculatedTotal, $prices, $currencySymbol) ?></div>
    <!-- DEBUG: totalAfterTax=<?= $prices->totalAfterTax ?? 'not set' ?>, totalPrice=<?= $prices->totalPrice ?? 'not set' ?>, total=<?= $prices->total ?? 'not set' ?>, v3_total=<?= isset($prices->v3) ? $prices->v3->total : 'not set' ?>, calculatedTotal=<?= $calculatedTotal ?> -->
</div>

<?php if (!empty($prices->v3) && !empty($prices->v3->partial) && $prices->v3->partial > 0): ?>
    <div class='price-block-item payment-schedule'>
        <div class="_label"><?= __( 'Due now', 'hostifybooking' ) ?></div>
        <div class="_value"><?= ListingHelper::withSymbol($prices->v3->partial, $prices, $currencySymbol) ?></div>
    </div>
    <?php if (!empty($total) && $total - $prices->v3->partial > 0): ?>
        <div class='price-block-item payment-schedule'>
            <div class="_label"><?= __( 'Due later', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($total - $prices->v3->partial, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php elseif (!empty($prices->totalPartial) && $prices->totalPartial > 0): ?>
    <div class='price-block-item payment-schedule'>
        <div class="_label"><?= __( 'Due now', 'hostifybooking' ) ?></div>
        <div class="_value"><?= ListingHelper::withSymbol($prices->totalPartial, $prices, $currencySymbol) ?></div>
    </div>
    <?php if (!empty($total) && $total - $prices->totalPartial > 0): ?>
        <div class='price-block-item payment-schedule'>
            <div class="_label"><?= __( 'Due later', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($total - $prices->totalPartial, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>
<?php endif; ?>
