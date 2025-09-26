<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$reservation_id = $_GET['reservation_id'] ?? $_POST['reservation_id'] ?? '';
$paymentSuccess = $_GET['success'] ?? $_POST['success'] ?? false;

if (empty($reservation_id)) {
	throw new Exception(__('No reservation ID', 'hostifybooking'));
}

$api = new HfyApi();
$result = $api->getReservation($reservation_id);
if (empty($result)) {
    throw new Exception(__('No reservation found', 'hostifybooking'));
}
$reservation = $result->data;

$payment_intent = $_GET['payment_intent'] ?? $_POST['payment_intent'] ?? false;

if ($payment_intent) { // stripe 3ds

	$guests = $reservation->guests ?? 1;
	$adults = $reservation->adults ?? 1;
	$children = $reservation->children ?? 0;
	$infants = $reservation->infants ?? 0;
	$pets = $reservation->pets ?? 0;

	$startDate = $reservation->checkIn;
	$endDate =  $reservation->checkOut;

	$prm = (object) [
		'guests' => $guests,
		'adults' => $adults,
		'children' => $children,
		'infants' => $infants,
		'pets' => $pets,
		'startDate' => $startDate,
		'endDate' => $endDate,
	];

	$requestData = [
		"name" => $reservation->guest_name,
		"email" => $reservation->email,
		"phone" => $reservation->guest_phone,
		"note" => $reservation->notes,
		"start_date" => $startDate,
		"end_date" => $endDate,
		"guests" => $guests,
		"adults" => $adults,
		"children" => $children,
		"infants" => $infants,
		"pets" => $pets,
		"listing_id" => $reservation->fs_listing_id,
		"total" => $reservation->payout_price,
		"dcid" => $reservation->dcid ?? 0,
		"discount_code" => $reservation->discount_code,
		"reservationId" => $reservation->id,

		"redirect_url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		"stripeObject" => [
		  "id" => $payment_intent,
		  "object" => "payment_intent",
		],
	];

	$result = $api->postPayment3ds($requestData);

	$paymentData = $result->paymentData ?? false;

	// listing & reservation preview

	// $pres = $api->getListingPrice(
	// 	$reservation->fs_listing_id,
	// 	$reservation->checkIn,
	// 	$reservation->checkOut,
	// 	$reservation->guests,
	// 	false,
	// 	$reservation->discount_code,
	// 	$reservation->adults,
	// 	$reservation->children,
	// 	$reservation->infants,
	// 	$reservation->pets
	// 	//, $feesToSend
	// );

	// if ($pres && $pres->success) {
	// 	$listingPrice = $pres->price;
	// } else {
	// 	throw new Exception(isset($pres->error) ? $pres->error : __('Listing price is not available', 'hostifybooking'));
	// }

	$listing_id = $reservation->fs_listing_id;
	$id = $listing_id;
	$payment_flag = true;

	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-payment-settings.php';

	$reservation->prices = (object) [
		'currency' => $reservation->currency,
	];

	$listingData = $listing->listing;
	$listingDescription = $listing->description;

	$selectedPaymentService = $paymentSettings->services->service ?? '';

	// $totalPartial = !empty($pres->price->totalPartial) ? $pres->price->totalPartial : 0;

	$listingInfo = (object) [
		'id' => $listing->listing->id,
		'thumbnail_file' => $listing->listing->thumbnail_file,
		'name' => $listing->listing->name,
		'city' => $listing->listing->city,
		'country' => $listing->listing->country,
		'currency_symbol' => $listing->currency_data->symbol,
		'nights' => $reservation->nights,
		'cleaning_fee' => $reservation->cleaning_fee,
		'extra_person_price' => $reservation->extra_person,
		// 'tax' => $tax,
		'security_deposit' => $reservation->security_deposit,
	];

	$reserveInfo = (object) [
		// 'monthlyDiscount' => $monthlyDiscount,
		// 'monthlyDiscountPercent' => $monthlyDiscountPercent,
		// 'weeklyDiscount' => $weeklyDiscount,
		// 'weeklyDiscountPercent' => $weeklyDiscountPercent,
		'start_date' => $startDate,
		'end_date' => $endDate,
		'guests' => $guests,
		'adults' => $prm->adults,
		'children' => $prm->children,
		'infants' => $prm->infants,
		'pets' => $prm->pets,
		'listing_id' => $listing_id,
		// 'name' => $prm->pname,
		// 'email' => $prm->pemail,
		// 'phone' => $prm->pphone,
		// 'note' => $prm->note,
		// 'zip' => $prm->zip,
		'discount_code' => $reservation->discount_code,
		// 'dcid' => $listingPrice->discount->id ?? null,
		// 'prices' => $listingPrice,
		// 'extrasSet' => $_extrasSet,
		// 'extrasOptional' => $_extrasOptional,
		// 'fees_ids' => implode(',', $feesToSend),
	];

	if (empty($paymentData)) {

		$api->reservationFailedPayment($reservation_id);

		$message = $result->error ?? '';
		$url_back = HFY_PAGE_PAYMENT_URL . '?' . implode('&', [
			'start_date=' . $reservation->checkIn,
			'end_date=' . $reservation->checkOut,
			'guests=' . $reservation->guests,
			'adults=' . $reservation->adults,
			'children=' . $reservation->children,
			'infants=' . $reservation->infants,
			'pets=' . $reservation->pets,
			'discount_code=' . $reservation->discount_code,
			'id=' . $reservation->fs_listing_id,
			'listing_id=' . $reservation->fs_listing_id,

		]);
		include hfy_tpl('payment/charge-error');

	} else {
		include hfy_tpl('payment/charge-success');
	}

} else {

	$paymentData = (object) [
		'listing_id' => $reservation->fs_listing_id,
		'note' => $reservation->notes,
		'amount' => $reservation->paid_sum * 100,
		'currency' => $reservation->currency,
		'confirmation_code' => $reservation->confirmation_code
	];

	$message = '';

	include hfy_tpl('payment/_success');
	//include hfy_tpl('payment/response');

}
