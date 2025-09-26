<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$res1 = null;

$current_user = wp_get_current_user();

if (0 !== $current_user->ID) {
	$api = new HfyApi();
	$res1 = $api->getReservationsByEmail($current_user->user_email);
} else {
	throw new Exception(__('You are not logged in', 'hostifybooking'));
}

if (empty($res1)) {

	throw new Exception(__('You currently do not have any bookings yet', 'hostifybooking'));

} else {

	$current = [];
	$future = [];
	$past = [];
	$cancelled = [];

	$data = $res1->data;

	foreach ($data as $item) {
		$d1 = new DateTime($item->checkIn);
		$d2 = new DateTime($item->checkOut);
		$now = new DateTime();

		if ($item->status == 'cancelled') {
			$cancelled[] = $item;
		} elseif ($now->getTimestamp() > $d2->getTimestamp()) {
			$past[] = $item;
		} elseif (
			$now->getTimestamp() >= $d1->getTimestamp()
			&& $now->getTimestamp() <= $d2->getTimestamp()
		) {
			$current[] = $item;
		} else {
			$future[] = $item;
		}
	}

	$type = isset($type) ? strtolower(trim($type)) : '';

	if (empty($type) || $type == 'current') {
		$bookings_type = 'current';
		$listings = $current;
		include hfy_tpl('user/bookings-list');
	}

	if (empty($type) || $type == 'future') {
		$bookings_type = 'future';
		$listings = $future;
		include hfy_tpl('user/bookings-list');
	}

	if (empty($type) || $type == 'past') {
		$bookings_type = 'past';
		$listings = $past;
		include hfy_tpl('user/bookings-list');
	}

	if (empty($type) || $type == 'cancelled') {
		$bookings_type = 'cancelled';
		$listings = $cancelled;
		include hfy_tpl('user/bookings-list');
	}

}
