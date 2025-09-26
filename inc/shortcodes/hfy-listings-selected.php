<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_def();

$id_except = 0;

if (strtolower($paramcity) == 'true' || $paramcity == 1) {
	if (intval($prm->city_id) > 0) {
		$cities = '' . intval($prm->city_id);
	}
}

$api = new HfyApi();

if (strtolower($currentlistingcity) == 'true' || $currentlistingcity == 1) {
	if (intval($prm->id) > 0) {
		$x = $api->getListingWithParams((int) $prm->id);
		if ($x && isset($x->listing)) {
			$cities = '' . $x->listing->city_id;
			$id_except = $x->listing->id;
		}
	}
}

$listings = $api->getListings(
	isset($ids) ? $ids : '',
	isset($cities) ? $cities : '',
	$max,
	$id_except
);

if (isset($template)) {
	include hfy_tpl($template);
} else {
	include hfy_tpl('listing/listings-selected');
}
