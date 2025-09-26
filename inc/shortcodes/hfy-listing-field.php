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

if (!empty($name) && isset($listing->listing->$name)) {
	$value = $listing->listing->$name;
	if ($trim > 0) {
		$value = substr($value, 0, $trim);
	}
	include hfy_tpl('listing/listing-field');
}
