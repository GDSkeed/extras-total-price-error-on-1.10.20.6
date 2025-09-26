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

if ($result->success ?? false) {
	$success = true;
	$prices = $result->price;
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

		if (!empty($result->price->feesAccommodation)) {
			foreach ($result->price->feesAccommodation as $fee) {
				if (strtolower($fee->fee_charge_type) == 'per month') {
					$detailedAccomodation = true;
				}
			}
		}
	}

	ob_start();
	if (($prices->price_on_request ?? 0) == 1) {
		include hfy_tpl('element/price-block-on-request');
	} else {
		include hfy_tpl('element/price-block');
	}
	$html = ob_get_contents();
	ob_end_clean();

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
