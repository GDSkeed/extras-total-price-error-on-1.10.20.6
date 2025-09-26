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
            <div><?= __('Price for', 'hostifybooking') ?> <?=$reserveInfo->listingInfo->nights;?> <?= __('nights', 'hostifybooking') ?></div>
        </div>
    <?php endif; ?>

<?php else: ?>

    <?php if (isset($reserveInfo->prices->v3->advanced_fees)) : ?>
        <?php // V3 ACCOMMODATION from advanced_fees ?>
        <?php foreach ($reserveInfo->prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'accommodation'): ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                    <?= $fee->name ?>
                    <br/><small><?= ListingHelper::withSymbol($fee->amount, $reserveInfo->prices, $sym) ?> <?= $fee->fee_charge_type ?? '' ?></small>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php // V3 FEES (non-accommodation, non-tax) from advanced_fees ?>
        <?php foreach ($reserveInfo->prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'fee' && $fee->total != 0): ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                    <?= $fee->name ?> <?= $fee->fee_charge_type ?? '' ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php // V3 SUBTOTAL (base subtotal only, no extras) ?>
        <div class="booking-title sub" style="border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 10px;">
            <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->v3->subtotal ?? 0, $reserveInfo->prices, $sym) ?></div>
            <?= __('Subtotal', 'hostifybooking') ?>
        </div>
        
        <?php // V3 EXTRAS from selected optional extras (after subtotal) ?>
        <?php if (!empty($reserveInfo->extrasOptional) && !empty($extrasOptionalSelected)): ?>
            <?php foreach ($reserveInfo->extrasOptional as $extra): ?>
                <?php if ($extrasOptionalSelected[$extra->fee_id] ?? false): ?>
                    <div class="booking-title sub">
                        <div style="float:right"><?= ListingHelper::withSymbol($extra->total, $reserveInfo->prices, $sym) ?></div>
                        <?= $extra->fee_name ?? $extra->name ?? __('Extra', 'hostifybooking') ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php // V3 TAXES from advanced_fees ?>
        <?php foreach ($reserveInfo->prices->v3->advanced_fees as $fee) : ?>
            <?php if ($fee->type == 'tax' && $fee->total != 0): ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                    <?= $fee->name ?> <?= $fee->fee_charge_type ?? '' ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>


// Track all displayed fees to prevent duplicates
$displayedFees = [];


// Display regular extras if any are selected
if (isset($reserveInfo->selectedExtras) && !empty($reserveInfo->selectedExtras)) {
    foreach ($reserveInfo->selectedExtras as $extra) {
        if (isset($extra->price) && $extra->price != 0) {
            $extraName = $extra->name ?? $extra->title ?? 'Extra';
            $extraAmount = $extra->price;
            $isExtraDisplayed = false;
            
            // Check if this extra is already displayed
            foreach ($displayedFees as $displayed) {
                $displayedName = strtolower(trim($displayed['name']));
                $displayedAmount = floatval($displayed['amount']);
                if (strtolower(trim($extraName)) === $displayedName && floatval($extraAmount) === $displayedAmount) {
                    $isExtraDisplayed = true;
                    break;
                }
            }
            
            if (!$isExtraDisplayed) { ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($extra->price, $reserveInfo->prices, $sym) ?></div>
                    <?= $extraName ?>
                </div>
            <?php 
                $displayedFees[] = ['name' => $extraName, 'amount' => $extraAmount];
            }
        }
    }
} ?>

<?php // Process feesAll.fees array first (this contains ALL fees including taxes) - V2 ONLY
if (empty($reserveInfo->prices->v3) && isset($reserveInfo->prices->feesAll) && isset($reserveInfo->prices->feesAll->fees)) : ?>
    <?php foreach ($reserveInfo->prices->feesAll->fees as $fee) : ?>
        <?php
        // Skip accommodation fees as they're handled separately
        if (($fee->fee_type ?? '') === 'accommodation') {
            continue;
        }
        
        // Handle discount fees (show as negative amount)
        $isDiscount = strpos(strtolower($fee->fee_name ?? ''), 'discount') !== false;
        if ($isDiscount) {
            $feeAmount = -floatval($fee->total); // Make discount negative
        } else {
            $feeAmount = $fee->total;
        }

        $feeName = $fee->fee_name . ($fee->charge_type_label ? ' ' . $fee->charge_type_label : '');
        $isFeeDisplayed = false;

        // Check if this fee is already displayed
        foreach ($displayedFees as $displayed) {
            $displayedName = strtolower(trim($displayed['name']));
            $displayedAmount = floatval($displayed['amount']);
            $currentFeeName = strtolower(trim($feeName));
            $currentFeeAmount = floatval($feeAmount);

            // Check for exact match
            if ($currentFeeName === $displayedName && $currentFeeAmount === $displayedAmount) {
                $isFeeDisplayed = true;
                break;
            }
        }

        if (!$isFeeDisplayed && $feeAmount != 0) { ?>
            <div class="booking-title sub">
                <div style="float:right"><?= ListingHelper::withSymbol($feeAmount, $reserveInfo->prices, $sym) ?></div>
                <?= ($fee->fee_name ?? $fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->charge_type_label ?? '' ?>
            </div>
        <?php
            $displayedFees[] = ['name' => $feeName, 'amount' => $feeAmount];
        } ?>
    <?php endforeach; ?>
<?php else : ?>
    <?php // Fallback: Display fees from regular fees array if feesAll is not available - V2 ONLY
    if (empty($reserveInfo->prices->v3) && isset($reserveInfo->prices->fees) && !empty($reserveInfo->prices->fees)) : ?>
        <?php foreach ($reserveInfo->prices->fees as $fee) : ?>
            <?php
            // Skip accommodation fees as they're handled separately
            if (($fee->fee_type ?? '') === 'accommodation') {
                continue;
            }

            $feeName = $fee->fee_name . ($fee->charge_type_label ? ' ' . $fee->charge_type_label : '');
            $feeAmount = $fee->total;
            $isFeeDisplayed = false;

            // Check if this fee is already displayed
            foreach ($displayedFees as $displayed) {
                $displayedName = strtolower(trim($displayed['name']));
                $displayedAmount = floatval($displayed['amount']);
                $currentFeeName = strtolower(trim($feeName));
                $currentFeeAmount = floatval($feeAmount);

                // Check for exact match
                if ($currentFeeName === $displayedName && $currentFeeAmount === $displayedAmount) {
                    $isFeeDisplayed = true;
                    break;
                }
            }

            if (!$isFeeDisplayed && $fee->total != 0) { ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reserveInfo->prices, $sym) ?></div>
                    <?= ($fee->fee_name ?? $fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->charge_type_label ?? '' ?>
                </div>
            <?php
                $displayedFees[] = ['name' => $feeName, 'amount' => $feeAmount];
            } ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php // V3 SPECIFIC FEE PROCESSING - Only process if advanced_fees are not available ?>
<?php if (!empty($reserveInfo->prices->v3) && empty($reserveInfo->prices->v3->advanced_fees) && !empty($reserveInfo->prices->feesAll) && !empty($reserveInfo->prices->feesAll->fees)): ?>
    <?php foreach ($reserveInfo->prices->feesAll->fees as $fee) : ?>
        <?php
        // V3 uses 'type' field instead of 'fee_type'
        // Skip accommodation fees as they're handled separately
        if (($fee->type ?? '') === 'accommodation') {
            continue;
        }
        
        // Handle discount fees (show as negative amount)
        $isDiscount = strpos(strtolower($fee->name ?? ''), 'discount') !== false;
        if ($isDiscount) {
            $feeAmount = -floatval($fee->total); // Make discount negative
        } else {
            $feeAmount = $fee->total;
        }

        $feeName = ($fee->name ?? __('Fee', 'hostifybooking')) . ($fee->fee_charge_type ? ' ' . $fee->fee_charge_type : '');
        $isFeeDisplayed = false;

        // Check if this fee is already displayed
        foreach ($displayedFees as $displayed) {
            $displayedName = strtolower(trim($displayed['name']));
            $displayedAmount = floatval($displayed['amount']);
            $currentFeeName = strtolower(trim($feeName));
            $currentFeeAmount = floatval($feeAmount);

            // Check for exact match
            if ($currentFeeName === $displayedName && $currentFeeAmount === $displayedAmount) {
                $isFeeDisplayed = true;
                break;
            }
        }

        if (!$isFeeDisplayed && $feeAmount != 0) { ?>
            <div class="booking-title sub">
                <div style="float:right"><?= ListingHelper::withSymbol($feeAmount, $reserveInfo->prices, $sym) ?></div>
                <?= ($fee->name ?? __('Fee', 'hostifybooking')) ?> <?= $fee->fee_charge_type ?? '' ?>
            </div>
        <?php
            $displayedFees[] = ['name' => $feeName, 'amount' => $feeAmount];
        } ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php // Display cleaning fee from property only if not already shown in fees array - V2 ONLY
if (empty($reserveInfo->prices->v3) && isset($reserveInfo->prices->cleaning_fee) && $reserveInfo->prices->cleaning_fee > 0) {
    $cleaningFeeName = 'Cleaning fee';
    $cleaningFeeAmount = $reserveInfo->prices->cleaning_fee;
    $isCleaningFeeDisplayed = false;
    
    // Check if cleaning fee is already displayed from fees array
    foreach ($displayedFees as $displayed) {
        $displayedName = strtolower(trim($displayed['name']));
        if (strpos($displayedName, 'cleaning') !== false) {
            $isCleaningFeeDisplayed = true;
            break;
        }
    }
    
    // Only display if no cleaning fee was shown from fees array
    if (!$isCleaningFeeDisplayed) { ?>
        <div class="booking-title sub">
            <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->cleaning_fee, $reserveInfo->prices, $sym) ?></div>
            <?= __('Cleaning fee', 'hostifybooking') ?>
        </div>
    <?php 
        $displayedFees[] = ['name' => $cleaningFeeName, 'amount' => $cleaningFeeAmount];
    }
} ?>

<?php // Taxes are now displayed via the feesAll.fees array above ?>

<?php if (empty($reserveInfo->prices->v3) && $reserveInfo->prices->subtotal != $reserveInfo->prices->total): ?>
    <hr class="mob-hide mb-2" />
    <div class='booking-title mb-4'>
        <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->subtotal, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
        <?= __( 'Subtotal', 'hostifybooking' ) ?>
    </div>
<?php endif; ?>

<?php if (empty($reserveInfo->prices->v3)): ?>

    <?php if (isset($reserveInfo->monthlyDiscount) && $reserveInfo->monthlyDiscount <> 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->monthlyDiscount, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
            <?= $reserveInfo->monthlyDiscountPercent ?>% <?= __('monthly discount', 'hostifybooking') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($reserveInfo->weeklyDiscount) && $reserveInfo->weeklyDiscount <> 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->weeklyDiscount, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
            <?= $reserveInfo->weeklyDiscountPercent ?>% <?= __('weekly discount', 'hostifybooking') ?>
        </div>
    <?php endif; ?>

<?php else: ?>

    <?php 
    // Only show the general discount if it's not from a coupon
    $has_v3_coupon = !empty($reserveInfo->prices->v3) && !empty($reserveInfo->prices->v3->coupon);
    if (!empty($reserveInfo->prices->v3->discount_percent) && $reserveInfo->prices->v3->discount_percent > 0 && !$has_v3_coupon):?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->v3->discount_percent * $reserveInfo->prices->v3->base_price_original / 100, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
            <?= $reserveInfo->prices->v3->discount_percent ?>% <?= __('discount', 'hostifybooking') ?>
        </div>
    <?php endif;?>

<?php endif;?>

<?php if (HFY_SHOW_DISCOUNT): ?>
    <?php 
    $has_v3_coupon = !empty($reserveInfo->prices->v3) && !empty($reserveInfo->prices->v3->coupon);
    $has_legacy_discount = isset($reserveInfo->prices->discount) && isset($reserveInfo->prices->discount->success) && $reserveInfo->prices->discount->success;
    
    if ($has_v3_coupon && $reserveInfo->prices->v3->coupon->discount_absolute != 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->v3->coupon->discount_absolute, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
            <?= __('Coupon discount', 'hostifybooking') ?>
        </div>
    <?php elseif ($has_legacy_discount && $reserveInfo->prices->discount->abs != 0): ?>
        <div class="booking-title sub">
            <div style="float:right">&minus;&nbsp;<?= ListingHelper::withSymbol($reserveInfo->prices->discount->abs, $reserveInfo->prices, $reserveInfo->listingInfo->currency_symbol) ?></div>
            <?= __('Coupon discount', 'hostifybooking') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
