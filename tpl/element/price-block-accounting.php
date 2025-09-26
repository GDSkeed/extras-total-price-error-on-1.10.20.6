<?php
if (!defined('WPINC')) die;
?>

<?php // ACCOMODATION ?>

<?php if (empty($prices->v3)): ?>

    <?php if ($detailedAccomodation): ?>
        <?php // detailed breakdown ?>
        <?php if (!empty($prices->feesAccommodation)) foreach ($prices->feesAccommodation as $fee) : ?>
            <div class='price-block-item'>
                <div class="_label"><?= __('Price for', 'hostifybooking') ?> <?= $fee->charge_type_label ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($fee->total, $prices, $currencySymbol) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php // just a price per night ?>
        <div class='price-block-item'>
            <div class="_label">
                <?= __( 'Price for', 'hostifybooking' ) ?> <?= $prices->nights ?> <?= __( 'nights', 'hostifybooking' ) ?>
            </div>
            <div class="_value"><?= ListingHelper::withSymbol($totalNights, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>

<?php else: ?>

    <?php if (!empty($prices->v3->advanced_fees)): ?>
        <?php // V3 ACCOMMODATION from advanced_fees ?>
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
        
        <?php // V3 FEES (non-accommodation, non-tax) from advanced_fees ?>
        <?php foreach ($prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'fee' && $fee->total != 0): ?>
                <div class='price-block-item'>
                    <div class="_label"><?= $fee->name ?> <?= $fee->fee_charge_type ?? '' ?></div>
                    <div class="_value"><?= ListingHelper::withSymbol($fee->total, $prices, $currencySymbol) ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php // V3 SUBTOTAL ?>
        <div class='price-block-item' style="border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 10px;">
            <div class="_label"><?= __('Subtotal', 'hostifybooking') ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->v3->subtotal ?? 0, $prices, $currencySymbol) ?></div>
        </div>
        
        <?php // V3 TAXES from advanced_fees ?>
        <?php foreach ($prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'tax' && $fee->total != 0): ?>
                <div class='price-block-item'>
                    <div class="_label"><?= $fee->name ?> <?= $fee->fee_charge_type ?? '' ?></div>
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

<?php endif; ?>

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

<?php // FEES - Process feesAll.fees first (includes all fees and discounts) - V2 ONLY ?>
<?php if (empty($prices->v3) && !empty($prices->feesAll) && !empty($prices->feesAll->fees)): ?>
    <?php foreach ($prices->feesAll->fees as $fee) : ?>
        <?php
        // Skip accommodation fees as they're handled separately
        if (($fee->fee_type ?? '') === 'accommodation') {
            continue;
        }
        
        // Skip taxes as they're handled separately in the taxes section
        if (($fee->fee_type ?? '') === 'tax') {
            continue;
        }
        
        // Handle discount fees (show as negative amount)
        $isDiscount = strpos(strtolower($fee->fee_name ?? ''), 'discount') !== false;
        if ($isDiscount) {
            $feeAmount = -floatval($fee->total); // Make discount negative
        } else {
            $feeAmount = $fee->total;
        }
        ?>
        <?php if ($feeAmount != 0): ?>
            <div class='price-block-item'>
                <div class="_label"><?= ($fee->fee_name ?? $fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->charge_type_label ?? '' ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($feeAmount, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <?php // Fallback: Display fees from regular fees array - V2 ONLY ?>
    <?php if (empty($prices->v3) && !empty($prices->fees)) foreach ($prices->fees as $fee) : ?>
        <?php if ($fee->total != 0): ?>
            <div class='price-block-item'>
                <div class="_label"><?= ($fee->fee_name ?? $fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->charge_type_label ?? '' ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($fee->total, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php // V3 SPECIFIC FEE PROCESSING - Only process if advanced_fees are not available ?>
<?php if (!empty($prices->v3) && empty($prices->v3->advanced_fees) && !empty($prices->feesAll) && !empty($prices->feesAll->fees)): ?>
    <?php foreach ($prices->feesAll->fees as $fee) : ?>
        <?php
        // V3 uses 'type' field instead of 'fee_type'
        // Skip accommodation fees as they're handled separately
        if (($fee->type ?? '') === 'accommodation') {
            continue;
        }
        
        // Skip taxes as they're handled separately in the taxes section
        if (($fee->type ?? '') === 'tax') {
            continue;
        }
        
        // Handle discount fees (show as negative amount)
        $isDiscount = strpos(strtolower($fee->name ?? ''), 'discount') !== false;
        if ($isDiscount) {
            $feeAmount = -floatval($fee->total); // Make discount negative
        } else {
            $feeAmount = $fee->total;
        }
        ?>
        <?php if ($feeAmount != 0): ?>
            <div class='price-block-item'>
                <div class="_label"><?= ($fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->fee_charge_type ?? '' ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($feeAmount, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php // OFFLINE OPTIONS ?>

<?php if (!empty($prices->offline)) : ?>
    <div class='price-block'>
        <div class='price-block-item prices-offline_'>
            <span class='upon-arrival'><?= __('Due upon arrival:', 'hostifybooking') ?></span>
            <?php foreach ($prices->offline as $o) : ?>
                <?php if ($o->total != 0): ?>
                    <div class='price-block-item'>
                        <div class="_label"><?= $o->fee_name ?: $o->name ?: __('Fee', 'hostifybooking') ?></div>
                        <div class="_value"><?= ListingHelper::withSymbol($o->total, $prices, $currencySymbol) ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php // TOTAL ?>

<div class='price-block-total'>
    <?php // V2 SUBTOTAL (only show for V2, V3 has its own subtotal above) ?>
    <?php if (empty($prices->v3) && isset($prices->subtotal) && $prices->subtotal != $prices->total) : ?>
        <div class='price-block-item'>
            <div class="_label"><?= __( 'Subtotal', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->subtotal, $prices, $currencySymbol) ?></div>
        </div>
    <?php endif; ?>

    <?php // TAXES ?>
    <?php if (!empty($prices->taxes)) foreach ($prices->taxes as $t) : ?>
        <?php if ($t->total != 0): ?>
            <div class='price-block-item'>
                <div class="_label"><?= $t->fee_name ?: $t->name ?: __('Tax', 'hostifybooking') ?> <?= $t->charge_type_label ?? '' ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($t->total, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class='price-block-item' style="border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 10px;">
        <div class="_label"><?= __( 'Total', 'hostifybooking' ) ?></div>
        <?php 
        // Calculate the correct total by ensuring discount is properly subtracted
        $calculatedTotal = 0;
        if (!empty($prices->feesAll) && !empty($prices->feesAll->total)) {
            $calculatedTotal = $prices->feesAll->total;
        } else {
            $calculatedTotal = $prices->totalAfterTax ?? $prices->totalPrice ?? $prices->total ?? $prices->price;
        }
        
        ?>
        <div class="_value"><?= ListingHelper::withSymbol($calculatedTotal, $prices, $currencySymbol) ?></div>
    </div>
    
    <?php if (!empty($prices->v3) && !empty($prices->v3->partial) && $prices->v3->partial > 0): ?>
        <div class='price-block-item payment-schedule'>
            <div class="_label"><?= __( 'Due now', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->v3->partial, $prices, $currencySymbol) ?></div>
        </div>
        <?php if (!empty($prices->totalAfterTax) && $prices->totalAfterTax - $prices->v3->partial > 0): ?>
            <div class='price-block-item payment-schedule'>
                <div class="_label"><?= __( 'Due later', 'hostifybooking' ) ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($prices->totalAfterTax - $prices->v3->partial, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php elseif (!empty($prices->totalPartial) && $prices->totalPartial > 0): ?>
        <div class='price-block-item payment-schedule'>
            <div class="_label"><?= __( 'Due now', 'hostifybooking' ) ?></div>
            <div class="_value"><?= ListingHelper::withSymbol($prices->totalPartial, $prices, $currencySymbol) ?></div>
        </div>
        <?php if (!empty($prices->totalAfterTax) && $prices->totalAfterTax - $prices->totalPartial > 0): ?>
            <div class='price-block-item payment-schedule'>
                <div class="_label"><?= __( 'Due later', 'hostifybooking' ) ?></div>
                <div class="_value"><?= ListingHelper::withSymbol($prices->totalAfterTax - $prices->totalPartial, $prices, $currencySymbol) ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
