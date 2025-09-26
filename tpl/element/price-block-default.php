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
            <?= ListingHelper::withSymbol($prices->priceWithMarkup ?? $totalNights, $prices, $currencySymbol) ?>
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
    // Always show weekly and monthly discounts when they exist
    if (!empty($prices->v3->weekly_discount_percent) && $prices->v3->weekly_discount_percent > 0):?>
        <div class='price-block-item'>
            <div class="_label"><?= $prices->v3->weekly_discount_percent ?>% <?= __('weekly discount', 'hostifybooking') ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->v3->weekly_discount_amount, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif;?>
    
    <?php if (!empty($prices->v3->monthly_discount_percent) && $prices->v3->monthly_discount_percent > 0):?>
        <div class='price-block-item'>
            <div class="_label"><?= $prices->v3->monthly_discount_percent ?>% <?= __('monthly discount', 'hostifybooking') ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->v3->monthly_discount_amount, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif;?>
    
    <?php
    // Show general discount only if it's not a length of stay discount (weekly/monthly) and not from a coupon
    $has_v3_coupon = !empty($prices->v3) && !empty($prices->v3->coupon);
    if (!empty($prices->v3->discount_percent) && $prices->v3->discount_percent > 0 && 
        !$has_v3_coupon && 
        $prices->v3->discount_percent != $prices->v3->weekly_discount_percent && 
        $prices->v3->discount_percent != $prices->v3->monthly_discount_percent):?>
        <div class='price-block-item'>
            <div class="_label"><?= $prices->v3->discount_percent ?>% <?= __('discount', 'hostifybooking') ?></div>
            <div class="_value">&minus;&nbsp;<?= ListingHelper::withSymbol($prices->v3->discount_percent * $prices->v3->base_price_original / 100, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif;?>

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

<?php // TOTAL ?>

<div class='price-block-item price-block-total'>
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
