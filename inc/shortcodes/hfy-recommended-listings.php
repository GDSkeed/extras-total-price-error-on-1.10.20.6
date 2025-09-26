<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_def();

if ($prm->id > 0) {
	$api = new HfyApi();

	$listings = $api->getRecommendedListings(
		(int) $prm->id,
		isset($tags) ? $tags : '',
		isset($max) ? intval($max) : 4
	);

	include hfy_tpl('listing/listings-recommended');
}
