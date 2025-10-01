<?php

// update payment preview (block with prices)

if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

$prm = (object) (!empty($_POST['data']) ? $_POST['data'] : []);

$prm->listing_id = $prm->listing_id ?? 0;
$prm->start_date = $prm->start_date ?? null;
$prm->end_date = $prm->end_date ?? null;
$prm->guests = $prm->guests ?? 1;
$prm->adults = $prm->adults ?? 1;
$prm->children = $prm->children ?? 0;
$prm->infants = $prm->infants ?? 0;
$prm->pets = $prm->pets ?? 0;
$prm->discount_code = $prm->discount_code ?? null;
$prm->extrasOptional = $prm->extrasOptional ?? null;

$listing_id = intval(empty($id) ? $prm->listing_id : $id);

if (empty($listing_id)) {
	throw new Exception(__('No listing ID', 'hostifybooking'));
}

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;
$pets = $prm->pets;

$startDate = isset($start_date) ? $start_date : ($prm->start_date ? $prm->start_date : null);
$endDate = isset($end_date) ? $end_date : ($prm->end_date ? $prm->end_date : null);

$id = $listing_id;
$payment_flag = true;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-payment-settings.php';

$listingData = $listing->listing;
$listingDescription = $listing->description;

if (isset($settings->direct_inquiry_email) && $settings->direct_inquiry_email) {
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php';
}

$listingPrice = null;

$extrasSet = (string) trim($prm->extrasSet ?? '');
$extrasSetArray = empty($extrasSet) ? [] : explode(',', $extrasSet);

$extrasAll = [];

if (isset($listing->extras)) {
	$extrasAll = $listing->extras;
} else {
	$getExtrasResult = $api->getExtras($id);
	$getExtrasError = isset($getExtrasResult->error) ? $getExtrasResult->error : null;
	if (!$getExtrasError) {
		if ($getExtrasResult && $getExtrasResult->success) {
			$extrasAll = $getExtrasResult->extras->items ?? null;
		}
	}
}

$extrasOptional = (string) trim($prm->extrasOptional ?? ''); // "id:0|1,..."
$extrasOptionalSelected = [];
$extrasOptionalSelectedIds = [];

if (HFY_SHOW_PAYMENT_EXTRAS) {
	foreach ($extrasAll as $item) {
		$extrasOptionalSelected[$item->fee_id] = false;
	}
}

$extrasOptionalArray_ = explode(',', $extrasOptional);
foreach ($extrasOptionalArray_ as $eItem) {
	$x = explode(':', $eItem);
	$item_id = (int) ($x[0] ?? 0);
	if ($item_id > 0) {
		$item_is_selected = ($x[1] ?? 0) == 1;
		$extrasOptionalSelected[$item_id] = $item_is_selected;
		if ($item_is_selected) {
			$extrasOptionalSelectedIds[] = $item_id;
		}
	}
}

$feesToSend = array_unique(array_merge($extrasSetArray, $extrasOptionalSelectedIds));

$api = new HfyApi();

// For V3, also extract fee IDs from advanced_fees (cleaning, taxes, etc.)
// This is needed because V3 includes these in advanced_fees, not in extras
error_log("DEBUG V3 FEES: HFY_USE_API_V3=" . (HFY_USE_API_V3 ? 'true' : 'false') . ", feesToSend before=" . json_encode($feesToSend));
if (HFY_USE_API_V3) {
    // Get initial price to check for v3 structure
    $presInitial = $api->getListingPrice($listing_id, $startDate, $endDate, $guests, false, $prm->discount_code, $adults, $children, $infants, $pets, []);
    error_log("DEBUG V3 FEES: presInitial->success=" . ($presInitial->success ?? 'not set') . ", has v3=" . (isset($presInitial->price->v3) ? 'yes' : 'no'));
    if ($presInitial && $presInitial->success && !empty($presInitial->price->v3->advanced_fees)) {
        error_log("DEBUG V3 FEES: advanced_fees count=" . count($presInitial->price->v3->advanced_fees));
        foreach ($presInitial->price->v3->advanced_fees as $fee) {
            // Add fee IDs for fees and taxes (not accommodation)
            if (($fee->type ?? '') !== 'accommodation' && !empty($fee->fee_id)) {
                $feesToSend[] = $fee->fee_id;
                error_log("DEBUG V3 FEES: Added fee_id=" . $fee->fee_id . ", name=" . ($fee->name ?? 'unnamed') . ", type=" . ($fee->type ?? 'no type'));
            }
        }
        $feesToSend = array_unique($feesToSend);
        error_log("DEBUG V3 FEES: feesToSend after=" . json_encode($feesToSend));
    }
}

// IMPORTANT: The API has a bug - when optional extras are passed in fees parameter,
// it returns ONLY the extra's price, not the full booking price + extras.
// Solution: Always get base price WITHOUT optional extras, then add optional extras manually.

// Get base price WITHOUT optional extras (only regular extras/fees + v3 fees)
// For V3, $feesToSend already includes the v3 fees extracted above
// We need to exclude optional extras but keep v3 fees
$baseFeesToSend = array_diff($feesToSend, $extrasOptionalSelectedIds);
error_log("DEBUG FEES: baseFeesToSend (for price API) = " . json_encode($baseFeesToSend));
$pres = $api->getListingPrice($listing_id, $startDate, $endDate, $guests, false, $prm->discount_code, $adults, $children, $infants, $pets, $baseFeesToSend);

if ($pres && $pres->success) {
	$listingPrice = $pres->price;
	
	// Store the correct base values before API potentially overwrites them
	$baseTotalAfterTax = $listingPrice->totalAfterTax ?? $listingPrice->total ?? 0;
	$baseSubtotal = $listingPrice->subtotal ?? 0;
	$originalTax = $listingPrice->totalTaxesCalc ?? $listingPrice->tax_amount ?? 0;
} else {
	throw new Exception(isset($pres->error) ? $pres->error : __('Listing price is not available', 'hostifybooking'));
}

if (!(
	$listing
	&& $listing->success
	&& is_object($listingPrice)
	&& $paymentSettings
	&& $paymentSettings->success
)) {
	throw new Exception(isset($listing->error) ? $listing->error : __('Incorrect parameters', 'hostifybooking'));
	// return $this->redirect(['listing', 'id' => $listing_id]);
}

$accountingActive = ($listingPrice->accounting_module ?? 0) == 1;


$selectedPaymentService = $paymentSettings->services->service ?? '';

if ($selectedPaymentService != 'netpay') {
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-payment-token.php';
}

$price = $listingPrice->priceWithoutDiscount;
$listingPricePerNight = number_format($price / ($listingPrice->nights <> 0 ? $listingPrice->nights : 1), 2, '.', '');
// Only apply fixFees if there are no extras to avoid resetting fees
$api = new HfyApi();
if (empty($feesToSend)) {
    $listingPrice = $api->fixFees($listingPrice, $startDate, $endDate, $guests, $adults, $children, $infants, $pets, $feesToSend);
}

$tax = $originalTax;
$total = $listingPrice->totalAfterTax ?? $listingPrice->totalPrice ?? $listingPrice->total;
// Add tax to total if not already included
if ($originalTax > 0 && $total < ($listingPrice->total + $originalTax)) {
    $total = $listingPrice->total + $originalTax;
}

$monthlyDiscount = $listingPrice->monthlyPriceDiscount;
$monthlyDiscountPercent = $listingPrice->monthlyPriceDiscountPercent;
$weeklyDiscount = $listingPrice->weeklyPriceDiscount;
$weeklyDiscountPercent = $listingPrice->weeklyPriceDiscountPercent;

$totalPartial = !empty($pres->price->totalPartial) ? $pres->price->totalPartial : 0;
// $totalPartial = 0;
// if (!empty($settings->data->payment_percent)) {
// 	if ($settings->data->payment_percent > 0 && $settings->data->payment_percent < 100) {
// 		$totalPartial = $total * $settings->data->payment_percent / 100;
// 	}
// } else {
// 	if (!empty($listingPrice->offline)) {
// 		$totalPartial = $total - ($listingPrice->totalOfflineCalc ?? 0);
// 	}
// }

$_extrasSet = [];
$_extrasOptional = [];

foreach ($extrasAll as $e) {

	$e->total = $e->amount ?? 0;
	if ($e->fee_charge_type_id == 2) { // per night
		$e->total = $listingPrice->nights * $e->total;
	} else if ($e->fee_charge_type_id == 4) { // per guest
		$e->total = $guests * $e->total;
	}

	$e->fee_name = $e->name ?? $e->fee_name ?? '';
	if (in_array($e->fee_id, $extrasSetArray)) {
		$_extrasSet[] = $e;
	}
	if (isset($extrasOptionalSelected[$e->fee_id]) && $extrasOptionalSelected[$e->fee_id]) {
		$_extrasOptional[] = $e;
	}
}

$isExtrasOptional = !empty($_extrasOptional);
$sliderStepsCount = $isExtrasOptional ? 3 : 2;

// Calculate total for selected optional extras
$selectedExtrasTotal = 0;
foreach ($_extrasOptional as $extra) {
    // Check if this is a discount extra
    $isDiscount = strpos(strtolower($extra->fee_name ?? ''), 'discount') !== false || 
                  strpos(strtolower($extra->name ?? ''), 'discount') !== false;
    
    if ($isDiscount) {
        // Subtract discount extras
        $selectedExtrasTotal -= $extra->total;
        $extra->total = -$extra->total; // Make it negative for display
    } else {
        // Add regular extras
        $selectedExtrasTotal += $extra->total;
    }
}


// Store original values before updating - preserve ALL fees and extras
$originalCleaningFee = $basePrice->cleaning_fee ?? 0;
$originalExtraPersonPrice = $basePrice->extra_person_price ?? 0;
$originalTax = $tax;
$originalTotal = $listingPrice->total ?? $listingPrice->totalAfterTax ?? $listingPrice->totalPrice ?? 0;

// Simple approach like old plugin: use API values directly
// The API (after fixFees) now correctly calculates:
// - subtotal = accommodation + fees (NO taxes)
// - totalAfterTax = subtotal + taxes + optional extras
$total = $listingPrice->totalAfterTax ?? $listingPrice->totalPrice ?? $listingPrice->total;
$totalPrice = number_format($totalPartial > 0 ? $totalPartial : $total, 2, '.', '');

error_log("DEBUG PAYMENT AJAX: listingPrice->totalAfterTax=" . ($listingPrice->totalAfterTax ?? 'not set') . 
    ", listingPrice->subtotal=" . ($listingPrice->subtotal ?? 'not set') . 
    ", total=" . $total . 
    ", feesToSend count=" . count($feesToSend));

// Build fees details array for audit trail (what was actually charged to customer)
$feesDetails = [];

// Add all fees from feesAll
if (!empty($listingPrice->feesAll) && !empty($listingPrice->feesAll->fees)) {
    foreach ($listingPrice->feesAll->fees as $fee) {
        if (($fee->fee_type ?? '') !== 'accommodation' && floatval($fee->total ?? 0) != 0) {
            $feesDetails[] = [
                'fee_id' => $fee->fee_id ?? $fee->property_fee_id ?? null,
                'name' => $fee->fee_name ?? $fee->name ?? 'Fee',
                'amount' => floatval($fee->total ?? 0),
                'type' => $fee->fee_type ?? $fee->type ?? 'fee',
            ];
        }
    }
}

// Add selected optional extras
foreach ($_extrasOptional as $extra) {
    $feesDetails[] = [
        'fee_id' => $extra->fee_id ?? null,
        'name' => $extra->fee_name ?? $extra->name ?? 'Extra',
        'amount' => floatval($extra->total ?? 0),
        'type' => 'optional_extra',
    ];
}

$nights = $listingPrice->nights;

$listingDescription = $listing->description;
$listingTitle = empty($listingDescription->name) ? $listing->listing->name : $listingDescription->name;
$listing->listing->name = $listingTitle;

// Store all regular extras that should remain visible
$originalExtrasSet = $_extrasSet;


// Override with listing data as primary source (more reliable than API response)
if (isset($listing->listing->cleaning_fee) && $listing->listing->cleaning_fee > 0) {
    $originalCleaningFee = $listing->listing->cleaning_fee;
}

if (isset($listing->listing->extra_person_price) && $listing->listing->extra_person_price > 0) {
    $originalExtraPersonPrice = $listing->listing->extra_person_price;
}

// Also ensure the listingPrice object has all fees for consistency
if ($originalCleaningFee > 0) {
    $listingPrice->cleaning_fee = $originalCleaningFee;
}
if ($originalExtraPersonPrice > 0) {
    $listingPrice->extra_person_price = $originalExtraPersonPrice;
}

$listingInfo = (object) [
	'id' => $listing->listing->id,
	'thumbnail_file' => $listing->listing->thumbnail_file,
	'name' => $listing->listing->name,
	'city' => $listing->listing->city,
	'country' => $listing->listing->country,
	'currency_symbol' => $listing->currency_data->symbol,
	'nights' => $listingPrice->nights,
	'cleaning_fee' => $originalCleaningFee,
	'extra_person_price' => $originalExtraPersonPrice,
	'tax' => $originalTax,
	'security_deposit' => $listingPrice->security_deposit,
];

// Calculate total: base total (from API without optional extras) + optional extras amount
// This is needed because the API has a bug when optional extras are in the fees parameter
$calculatedTotal = $baseTotalAfterTax + $selectedExtrasTotal;

// Update listingPrice with correct values
$listingPrice->totalAfterTax = $calculatedTotal;
$listingPrice->total = $calculatedTotal;
$listingPrice->subtotal = $baseSubtotal; // Subtotal doesn't change with optional extras

// Update $total variable to match (used in templates)
$total = $calculatedTotal;

// Ensure ALL fees and extras are preserved for display
$listingPrice->cleaning_fee = $originalCleaningFee;
$listingPrice->extra_person_price = $originalExtraPersonPrice;

// Preserve tax amount
if ($originalTax > 0) {
    $listingPrice->tax_amount = $originalTax;
    $listingPrice->totalTaxesCalc = $originalTax;
    
    // Add tax to taxes array for accounting template display (V2 ONLY - V3 has taxes in advanced_fees)
    if (empty($listingPrice->v3)) {
        if (!isset($listingPrice->taxes)) {
            $listingPrice->taxes = [];
        }
        
        // Check if tax already exists in taxes array
        $taxExists = false;
        foreach ($listingPrice->taxes as $existingTax) {
            if (($existingTax->fee_type ?? '') == 'tax' || ($existingTax->type ?? '') == 'tax') {
                $taxExists = true;
                break;
            }
        }
        
        if (!$taxExists) {
            $listingPrice->taxes[] = (object) [
                'fee_name' => 'Tax Per Stay',
                'charge_type_label' => '',
                'total' => $originalTax
            ];
        }
    }
}

// Preserve regular extras in the prices object
if (!empty($originalExtrasSet)) {
    $listingPrice->extrasSet = $originalExtrasSet;
}

// Preserve all fees from feesAll.fees array for template display
if (isset($presBase->price->feesAll) && isset($presBase->price->feesAll->fees)) {
    $listingPrice->feesAll = $presBase->price->feesAll;
} elseif (isset($pres->price->feesAll) && isset($pres->price->feesAll->fees)) {
    $listingPrice->feesAll = $pres->price->feesAll;
}


$reserveInfo = (object) [
	'monthlyDiscount' => $monthlyDiscount,
	'monthlyDiscountPercent' => $monthlyDiscountPercent,
	'weeklyDiscount' => $weeklyDiscount,
	'weeklyDiscountPercent' => $weeklyDiscountPercent,
	'start_date' => $startDate,
	'end_date' => $endDate,
	'guests' => $guests,
	'adults' => $prm->adults,
	'children' => $prm->children,
	'infants' => $prm->infants,
	'pets' => $prm->pets,
	'listing_id' => $listing_id,
	'name' => $prm->pname ?? '',
	'email' => $prm->pemail ?? '',
	'phone' => $prm->pphone ?? '',
	'note' => $prm->note ?? '',
	'zip' => $prm->zip ?? '',
	'discount_code' => $prm->discount_code,
	'dcid' => $listingPrice->discount->id ?? null,
	'prices' => $listingPrice,
	'listingInfo' => $listingInfo,
	'extrasSet' => $originalExtrasSet, // Use original regular extras
	
	// Debug logging for payment preview
	'extrasOptional' => $_extrasOptional,
	'extrasOptionalSelected' => $extrasOptionalSelected,
	'fees_ids' => implode(',', $feesToSend), // Fee IDs for PMS API
	'fees_details' => $feesDetails, // Fee details for audit trail (what was charged)
];

$currency =
	$reserveInfo->prices->price->iso_code
	?? $reserveInfo->prices->iso_code
	?? $listing->listing->currency
	?? 'USD';

$startDateFormatted = hfyDateFormatOpt($startDate);
$endDateFormatted = hfyDateFormatOpt($endDate);
$detailedAccomodation = false;
if (is_object($reserveInfo->prices)) {
	if (!empty($reserveInfo->prices->feesAccommodation)) {
		foreach ($reserveInfo->prices->feesAccommodation as $fee) {
			if (strtolower($fee->fee_charge_type) == 'per month') {
				$detailedAccomodation = true;
			}
		}
	}
}

$redirectOnSuccess = null;


ob_start();


// Make variables available to the template
$reserveInfo->listingInfo = $listingInfo; // Ensure listingInfo is available in the object
$accountingActive = $accountingActive;


include hfy_tpl('payment/preview');

// Add script to update hfystripedata with new total
// $calculatedTotal is already set above (lines 285-303)
// Handle partial payments: if totalPartial exists, we need to add optional extras to it
// because the API's totalPartial doesn't include optional extras
$amountToPay = $calculatedTotal;
if ($totalPartial > 0) {
    // Partial payment exists: add optional extras to the partial amount
    $amountToPay = $totalPartial + $selectedExtrasTotal;
}

$newTotal = number_format($amountToPay, 2, '.', '');
echo '<script>
    hfystripedata.total = "' . $newTotal . '";
    hfystripedata.amount = ' . intval($amountToPay * 100) . ';
    
    // Also update hidden form fields to ensure payment submission uses correct values
    var feesInput = document.getElementById("fees");
    if (feesInput) feesInput.value = "' . esc_js($reserveInfo->fees_ids) . '";
    
    var feesDetailsInput = document.getElementById("fees-details");
    if (feesDetailsInput) feesDetailsInput.value = \'' . esc_js(json_encode($reserveInfo->fees_details ?? [])) . '\';
</script>';

$out = ob_get_contents();
ob_end_clean();
