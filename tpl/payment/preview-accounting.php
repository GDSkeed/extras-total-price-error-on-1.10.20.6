<?php
if (!defined('WPINC')) die;
?>

<?php if (empty($reserveInfo->prices->v3)): ?>

    <?php if ($detailedAccomodation): ?>
        <?php // detailed breakdown ?>
        <?php if (!empty($reserveInfo->prices->feesAccommodation)) foreach ($reserveInfo->prices->feesAccommodation as $fee) : ?>
            <div class="booking-title sub">
                <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                <div><?= __('Price for', 'hostifybooking') ?> <?= $fee->charge_type_label ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php // just a price per night ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->priceWithMarkup, $reserveInfo->prices, $sym) ?></div>
            <div><?= __('Price for', 'hostifybooking') ?> <?=$listingInfo->nights;?> <?= __('nights', 'hostifybooking') ?></div>
        </div>
    <?php endif; ?>

<?php else: ?>

    <?php if (isset($reserveInfo->prices->v3->advanced_fees)) : ?>
        <?php foreach ($reserveInfo->prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'accommodation'): ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                    <?= $fee->name ?>
                    <br/><small><?= ListingHelper::withSymbol($fee->amount, $reserveInfo->prices, $sym) ?> <?= $fee->fee_charge_type ?? '' ?></small>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>

<?php if (isset($reserveInfo->prices->fees)) : ?>
    <?php foreach ($reserveInfo->prices->fees as $fee) : ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
            <?= $fee->fee_name ?> <?= $fee->charge_type_label ?? '' ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($reserveInfo->prices->taxes)): ?>
    <?php if (HFY_USE_API_V3 && empty($reserveInfo->prices->v3) && $reserveInfo->prices->v3->subtotal != $reserveInfo->prices->v3->total): ?>
        <hr class="mob-hide mb-2" />
        <div class='booking-title mb-4'>
            <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->subtotal, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
            <?= __( 'Subtotal', 'hostifybooking' ) ?>
        </div>
    <?php endif; ?>
    <?php foreach ($reserveInfo->prices->taxes as $t): ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($t->total, $reserveInfo->prices, $sym) ?></div>
            <?= $t->fee_name ?> <?= $t->charge_type_label ?? '' ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

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
