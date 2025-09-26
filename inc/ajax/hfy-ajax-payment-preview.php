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

// First get the base price without extras to preserve tax information
$presBase = $api->getListingPrice($listing_id, $startDate, $endDate, $guests, false, $prm->discount_code, $adults, $children, $infants, $pets, []);

if ($presBase && $presBase->success) {
	$basePrice = $presBase->price;
	$originalTax = $basePrice->totalTaxesCalc ?? $basePrice->tax_amount ?? 0;
	$originalDiscount = $basePrice->discount ?? 0; // Store original discount before any modifications
} else {
	$originalTax = 0;
	$originalDiscount = 0;
}

// Now get the price with extras (only if there are extras to send)
if (!empty($feesToSend)) {
	$pres = $api->getListingPrice($listing_id, $startDate, $endDate, $guests, false, $prm->discount_code, $adults, $children, $infants, $pets, $feesToSend);
} else {
	$pres = $presBase; // Use the base price if no extras
}

if ($pres && $pres->success) {
	$listingPrice = $pres->price;
	
	// Preserve the tax from the base price
	if ($originalTax > 0) {
		$listingPrice->totalTaxesCalc = $originalTax;
		$listingPrice->tax_amount = $originalTax;
	}
	
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
$selectedExtras = $_extrasOptional; // Define selectedExtras for template
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


// Calculate the correct base total that includes all fees (cleaning, taxes, etc.)
// Always reconstruct the base total to ensure all fees are included
// Use the stored original discount from the first API call (without extras) to avoid double-counting discount extras
$baseTotalWithFees = $listingPrice->priceWithMarkup + $originalCleaningFee + $originalExtraPersonPrice + $originalTax - $originalDiscount;

if (HFY_USE_API_V3 && isset($listingPrice->v3)) {
    // For v3, use the base price total (without extras) plus selected optional extras
    $baseV3Total = $basePrice->v3->total ?? 0;
    $calculatedTotal = $baseV3Total + $selectedExtrasTotal;
    $totalPrice = number_format($totalPartial > 0 ? $totalPartial : $calculatedTotal, 2, '.', '');
} else {
    // For v2, use the reconstructed base total with fees plus selected optional extras
    $baseTotal = $baseTotalWithFees; // Use the reconstructed total that includes cleaning fee
    $calculatedTotal = $baseTotal + $selectedExtrasTotal;
    $totalPrice = number_format($totalPartial > 0 ? $totalPartial : $calculatedTotal, 2, '.', '');
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



// Update the listingPrice object to include the calculated total with selected extras
if (HFY_USE_API_V3 && isset($listingPrice->v3)) {
    $listingPrice->v3->total = $calculatedTotal;
    // Don't modify V3 subtotal - let the template handle extras display
} else {
    $listingPrice->total = $calculatedTotal;
    $listingPrice->totalAfterTax = $calculatedTotal;
    $listingPrice->totalPrice = $calculatedTotal;
    $listingPrice->subtotal = $baseTotalWithFees; // Set V2 subtotal to base total without extras
}

// Ensure ALL fees and extras are preserved for display
$listingPrice->cleaning_fee = $originalCleaningFee;
$listingPrice->extra_person_price = $originalExtraPersonPrice;

// Preserve tax amount
if ($originalTax > 0) {
    $listingPrice->tax_amount = $originalTax;
    $listingPrice->totalTaxesCalc = $originalTax;
    
    // Add tax to taxes array for accounting template display
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
	'selectedExtras' => $selectedExtras,
	'extrasSet' => $originalExtrasSet, // Use original regular extras
	
	// Debug logging for payment preview
	'debug_cleaning_fee' => $listingInfo->cleaning_fee ?? 'not set',
	'debug_tax_amount' => $listingPrice->tax_amount ?? 'not set',
	'extrasOptional' => $_extrasOptional,
	'fees_ids' => implode(',', $feesToSend),
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
if (HFY_USE_API_V3 && isset($listingPrice->v3)) {
    // For V3 API, use original total plus selected extras
    $baseTotal = !empty($listingPrice->v3->partial) && $listingPrice->v3->partial > 0 
                 ? $listingPrice->v3->partial 
                 : $baseTotalWithFees;
    $partialAmount = $baseTotal + $selectedExtrasTotal;
    
    $newTotal = number_format($calculatedTotal, 2, '.', '');
    echo '<script>
        hfystripedata.total = "' . $newTotal . '";
        hfystripedata.amount = ' . intval($calculatedTotal * 100) . ';
    </script>';
} else {
    // For V2 API, use original total plus selected optional extras
    $calculatedTotal = $baseTotalWithFees + $selectedExtrasTotal;
    $newTotal = number_format($totalPartial > 0 ? $totalPartial : $calculatedTotal, 2, '.', '');
    echo '<script>
        hfystripedata.total = "' . $newTotal . '";
        hfystripedata.amount = ' . intval(($totalPartial > 0 ? $totalPartial : $calculatedTotal) * 100) . ';
    </script>';
}

$out = ob_get_contents();
ob_end_clean();
