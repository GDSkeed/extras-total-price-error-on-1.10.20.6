<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';

parse_str($_POST['data'], $pdata);

$start_date = $pdata['start_date'] ?? '';
$end_date = $pdata['end_date'] ?? '';
$guests = intval(is_numeric($pdata['guests'] ?? null) ? $pdata['guests'] : 1);
$adults = intval(is_numeric($pdata['adults'] ?? null) ? $pdata['adults'] : 1);
$children = intval(is_numeric($pdata['children'] ?? null) ? $pdata['children'] : 0);
$infants = intval(is_numeric($pdata['infants'] ?? null) ? $pdata['infants'] : 0);
$pets = intval(is_numeric($pdata['pets'] ?? null) ? $pdata['pets'] : 0);
$discount_code = trim($pdata['discount_code'] ?? '');
$listing_id = intval($pdata['listing_id'] ?? 0);

$pricePerNightTotal = 0;

$api = new HfyApi();
$result = $api->getListingPrice($listing_id, $start_date, $end_date, $guests, false, $discount_code, $adults, $children, $infants, $pets);

// DEBUG: Log the raw API response
$debugInfo = "<!-- DEBUG AJAX RAW API: success=" . ($result->success ?? 'not set') . 
    ", priceWithMarkup=" . ($result->price->priceWithMarkup ?? 'not set') . 
    ", price=" . ($result->price->price ?? 'not set') . 
    ", totalAfterTax=" . ($result->price->totalAfterTax ?? 'not set') . 
    ", nights=" . ($result->price->nights ?? 'not set') . 
    ", feesAll=" . (isset($result->price->feesAll) ? 'exists' : 'not set') . 
    ", taxes_count=" . (isset($result->price->taxes) ? count($result->price->taxes) : 'not set') . 
    ", taxes=" . (isset($result->price->taxes) ? json_encode($result->price->taxes) : 'not set') . 
    ", HFY_USE_API_V3=" . (defined('HFY_USE_API_V3') ? (HFY_USE_API_V3 ? 'true' : 'false') : 'not defined') . " -->\n";


if ($result->success ?? false) {
	$success = true;
	$prices = $result->price;
	
	// Apply fixFees to process the pricing data correctly for V2
	// V3 handles fixFees in the API layer, so we only need it for V2
	if (!HFY_USE_API_V3) {
		$prices = $api->fixFees($prices, $start_date, $end_date, $guests, $adults, $children, $infants, $pets);
		
		// DEBUG: After fixFees processing
		$debugInfo .= "<!-- DEBUG AJAX AFTER FIXFEES: priceWithMarkup=" . ($prices->priceWithMarkup ?? 'not set') . 
			", price=" . ($prices->price ?? 'not set') . 
			", totalAfterTax=" . ($prices->totalAfterTax ?? 'not set') . 
			", feesAll_total=" . ($prices->feesAll->total ?? 'not set') . 
			", fees_count=" . (isset($prices->fees) ? count($prices->fees) : 0) . 
			", taxes_count=" . (isset($prices->taxes) ? count($prices->taxes) : 0) . " -->\n";
	} else {
		$debugInfo .= "<!-- DEBUG AJAX: V3 detected, skipping fixFees -->\n";
	}
	
	$channelListingId = false;
	$detailedAccomodation = false;
	$currencySymbol = $result->price->symbol ?? '';

	$accountingActive = ($prices->accounting_module ?? 0) == 1;

	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-payment-settings.php';

	$listingPricePerNight = ListingHelper::calcPricePerNight($result->price);
	if (is_object($result->price)) {
		$price = $result->price->priceWithoutDiscount;
		$total = $result->price->totalAfterTax;
		$totalNights = $result->price->priceWithMarkup;
		$listingPricePerNight = number_format($listingPricePerNight, 2, '.', '');
		$tax = isset($result->price->tax_amount) ? $result->price->tax_amount : 0;
		
		
		// Ensure nights property is set for template display
		if (!isset($prices->nights) || empty($prices->nights)) {
			$prices->nights = $result->price->nights ?? 0;
		}
		

		$totalPartial = empty($result->price->totalPartial) ? 0 : $result->price->totalPartial;
		// $totalPartial = 0;
		// if (!empty($settings->data->payment_percent)) {
		// 	if ($settings->data->payment_percent > 0 && $settings->data->payment_percent < 100) {
		// 		$totalPartial = $total * $settings->data->payment_percent / 100;
		// 	}
		// } else {
		// 	if (!empty($prices->offline)) {
		// 		$totalPartial = $total - ($prices->totalOfflineCalc ?? 0);
		// 	}
		// }

		$totalPrice = number_format($totalPartial > 0 ? $totalPartial : $total, 2, '.', '');

		// For V2, let fixFees handle everything - we just use the values it sets
		// No need to recalculate here as fixFees already does it correctly
		
		// DEBUG: Log what fixFees calculated
		$debugInfo .= "<!-- DEBUG AFTER FIXFEES: fees_count=" . (isset($prices->fees) ? count($prices->fees) : 'not set') . 
			", totalFees=" . ($prices->totalFees ?? 'not set') . 
			", taxes_count=" . (isset($prices->taxes) ? count($prices->taxes) : 'not set') . 
			", totalTaxesCalc=" . ($prices->totalTaxesCalc ?? 'not set') . 
			", totalAfterTax=" . ($prices->totalAfterTax ?? 'not set') . 
			", feesAll_total=" . ($prices->feesAll->total ?? 'not set') . " -->\n";

		if (!empty($result->price->feesAccommodation)) {
			foreach ($result->price->feesAccommodation as $fee) {
				if (strtolower($fee->fee_charge_type) == 'per month') {
					$detailedAccomodation = true;
				}
			}
		}
	}


	// Set tax variable for template
	$tax = $prices->tax_amount ?? $prices->totalTaxesCalc ?? 0;
	
	// DEBUG: Log taxes array (now populated by fixFees in api.php)
	$debugInfo .= "<!-- DEBUG TAXES: taxes_count=" . (isset($prices->taxes) ? count($prices->taxes) : 'not set') . ", totalTaxesCalc=" . ($prices->totalTaxesCalc ?? 'not set') . " -->\n";
	
	ob_start();
	if (($prices->price_on_request ?? 0) == 1) {
		include hfy_tpl('element/price-block-on-request');
	} else {
		include hfy_tpl('element/price-block');
	}
	$html = ob_get_contents();
	ob_end_clean();
	
	// Add debug info to HTML
	$html = $debugInfo . $html;
	

} else {
	$success = false;
	$html = '<div class="calendar-error">' . (isset($result->error) ? $result->error : __('Unavailable', 'hostifybooking')) . '</div>';
}

$out = [
	// -- dbg
	// 'result' => $result, // dbg
	// 'prices' => $prices,

	'success' => $success,
	'data' => $html,
	'price-per-night' => $listingPricePerNight ?? ''
];
