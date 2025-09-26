<?php

$api = new HfyApi();
$homePageData = $api->getHomepageData();
if (!$homePageData || !isset($homePageData->success)) {
	// throw new HttpException(503, __('Please try again later', 'hostifybooking'));
} else {
	# TODO fix for V3
	/*
	# if any changes on PMS side, clear the caches
	$is_changed = ($homePageData->data->is_changed ?? 0) == 1;
	if ($is_changed) {
		hfy_clear_cache();
		$_SESSION['cache_was_cleared'] = 1;
	}
	*/
}

$bookingEngine = $homePageData->data->booking_engine ?? null;

if (isset($bookingEngine->cities)) {
	uasort($bookingEngine->cities, function($a, $b) {
		return strcmp($a->name, $b->name);
	});
}

$bookingCities = [];
foreach ((isset($bookingEngine->cities) ? $bookingEngine->cities : []) as $ix) {
	$bookingCities[$ix->city_id] = $ix->name;
}
// asort($bookingCities);

// todo for admin
// $extrasData = $api->getExtrasAll();
// $bookingExtras = [];
// foreach ((isset($extrasData->extras) ? $extrasData->extras : []) as $ix) {
// 	$bookingExtras[$ix->id] = $ix->name;
// }

