<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception(__('No listing ID', 'hostifybooking'));
}

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;

$startDate = $prm->start_date;
$endDate = $prm->end_date;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

include hfy_tpl('listing/listing-amenities');
