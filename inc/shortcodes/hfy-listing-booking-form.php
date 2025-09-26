<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception(__('No listing ID', 'hostifybooking'));
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';

$guests = (int) $prm->guests;
$adults = (int) $prm->adults;
$children = (int) $prm->children;
$infants = (int) $prm->infants;
$pets = (int) $prm->pets;

$startDate = $prm->start_date;
$start_date = $prm->start_date;
$endDate = $prm->end_date;
$end_date = $prm->end_date;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-payment-settings.php';

if (isset($settings->direct_inquiry_email) && $settings->direct_inquiry_email) {
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php';
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

if (empty($start_date) || empty($end_date) || $start_date == $end_date) {
    $start_date = '';
    $end_date = '';
	$start_date_formatted = '';
	$end_date_formatted = '';
    $dates_value = '';
} else {
	// localized
	$start_date_formatted = hfyDateFormatOpt($start_date);
	$end_date_formatted = hfyDateFormatOpt($end_date);
	// for calentim value - use default format for internal processing, display format will be set by JavaScript
	// Only use default format for NEW_CALENDAR (main4.js), keep display format for other calendars
	if (HFY_USE_NEW_CALENDAR && !HFY_USE_V3_CALENDAR) {
		$dates_value = $start_date . ' - ' . $end_date;
	} else {
		$dates_value = $start_date_formatted . ' - ' . $end_date_formatted;
	}
}

if ($listingPriceOnRequest) {
	include hfy_tpl('listing/listing-booking-form-on-request');
} else {
	include HFY_USE_BOOKING_FORM_V2
		? hfy_tpl('listing/listing-booking-form-v2')
		: hfy_tpl('listing/listing-booking-form');
}
