<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	// throw new Exception(__('No listing ID', 'hostifybooking'));
	throw new Exception('');
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';

$listingDetails = $listing->details ?? null;

if (!empty($name) && isset($listingDetails->$name)) {
	$value = $listingDetails->$name;
	include hfy_tpl('listing/listing-details-field');
}
