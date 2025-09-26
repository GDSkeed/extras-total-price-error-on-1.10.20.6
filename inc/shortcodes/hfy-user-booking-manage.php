<?php

if ( ! defined( 'WPINC' ) ) die;

// wp_enqueue_script( 'hfysc-stripe' );

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_([
	// 'id',
	'rid',
], true);

$rid = (int) (empty($rid) ? $prm->rid : $rid);
// $id = (int) (empty($id) ? $prm->id : $id);

if (empty($rid)) {
	throw new Exception('No ID');
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php';

$current_user = wp_get_current_user();
$email = (0 !== $current_user->ID) ? $current_user->user_email : '';

$api = new HfyApi();
$reservation = $api->getReservation($rid, $email, false);

if ($reservation && $reservation->success) {
	$reservation = $reservation->data;
} else {
	throw new Exception(isset($reservation->error) ? $reservation->error : 'Reservation details is not available');
}

if (!isset($reservation->id)) {
	throw new Exception(isset($reservation->error) ? $reservation->error : 'Reservation details is not available');
}

if ($reservation->id <= 0) {
	throw new Exception(isset($reservation->error) ? $reservation->error : 'Reservation details is not available');
}

$reasons = [];

// $reasons_ = $api->getCancelReasons($rid);
// if ($reasons_->success ?? false) {
// 	foreach ($reasons_->reasons->reasons ?? [] as $resid => $resval) {
// 		$reasons[$resid] = $resval->name;
// 	}
// }

$price = $reservation->base_price;
$listingPricePerNight = $reservation->price_per_night;
$total = $reservation->total ?? $reservation->total_price ?? $reservation->paid_sum;
$listing_id = $reservation->fs_listing_id;
$accounting_module = $reservation->accounting_module ?? 0;

if (!$listing_id) {
	throw new Exception(
		$listing->error ?? __('Not found', 'hostifybooking')
	);
}

$listing = $api->getWebsiteListing($listing_id);

if (!$listing->success) {
	throw new Exception(
		$listing->error ?? __('Not found', 'hostifybooking')
	);
}

// exclude dates of current reservation
$dco = date('Y-m-d', strtotime('-1 day', strtotime($reservation->checkOut)));
$listing->calendar = array_filter($listing->calendar, function($item) use ($reservation, $dco) {
	return !(
		$item->start == $reservation->checkIn &&
		$item->end == $dco
	);
});

$listingData = $listing->listing ?? ((object) []);
$listingData->is_listed = true; // deprecated, fallback

$listingInfo = (object) [
	'id' => $listing_id,
	'thumbnail_file' => $reservation->thumbnail_file,
	'name' => $reservation->name,
	'city' => $reservation->city,
	'country' => $reservation->country,
	'address' => $reservation->address,
	'currency_symbol' => $reservation->symbol,
	'nights' => $reservation->nights,
	'cleaning_fee' => $reservation->cleaning_fee,
	'extra_person_price' => $reservation->extra_person_price ?? 0,
	'tax' => $reservation->tax_amopunt,
];

$reserveInfo = (object) [
	'id' => $reservation->id,
	'start_date' => $reservation->checkIn,
	'end_date' => $reservation->checkOut,
	'guests' => $reservation->guests,
	'adults' => $reservation->adults,
	'children' => $reservation->children,
	'infants' => $reservation->infants,
	'pets' => $reservation->pets,
	'listing_id' => $listing_id,
	'prices' => $accounting_module ? ($reservation->prices ?? null) : null,
];

/* todo
$d1 = new DateTime($reservation->checkIn);
$now = new DateTime();
$showCancel = $now->getTimestamp() < $d1->getTimestamp();
*/
$showCancel = false;

$listingMinNights = $listingData->min_nights ?? 1;
$listingMaxNights = $listingData->max_nights ?? 365;

$res = $api->getWebsiteListingMinstay($listing_id);
$listingCustomMinStay = [];
if ($res->success ?? false) {
	foreach (($res->calendar ?? []) as $item) {
		if ($listingMinNights <> $item->min_stay) {
			$listingCustomMinStay[$item->date] = $item->min_stay;
		}
	}
}

include hfy_tpl('user/booking-manage');
