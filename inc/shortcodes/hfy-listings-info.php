<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
// require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
// require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
// require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-data.php';

$prm = hfy_get_vars_([
	'neighbourhood',
	'city_id',
	'start_date',
	'end_date',
	'guests',
	'adults',
	'children',
	'infants',
	'pets',
	'bedrooms',
	'bathrooms',
	'long_term_mode',
	'pmin',
	'pmax',
	'sort',
	'tag',
	'pg',
	'custom_search',
	'max',
]);

// todo
// $tags = empty($prm->tag) ? ($tags ?? '') : $prm->tag;
// $ids = isset($ids) ? $ids : '';
// $guests = $prm->guests;
// $adults = $prm->adults;
// $children = $prm->children;
// $infants = $prm->infants;
// $pets = $prm->pets;
// $startDate = $prm->start_date;
// $endDate = $prm->end_date;

if (!empty(trim($prm->custom_search))) {
	$prm->neighbourhood = null;
	$prm->custom_search = trim($prm->custom_search);
}

$location_raw = (object) [
	'city_id' => $prm->city_id,
	'city' => $prm->city_id ? ($bookingCities[$prm->city_id] ?? '') : '',
	'neighbourhood' => '',
	'country' => '',
	'state' => '',
];

if ($prm->neighbourhood) {
	$x = hfyParseNeighbourhood($prm->neighbourhood);
	if (!empty($x->neighbourhood)) {
		$location_raw->neighbourhood = $x->neighbourhood;
	}
	if (empty($location->city_id) && $x->city_id > 0) {
		$location_raw->city_id = $x->city_id;
	}
	if (!empty($x->country)) $location_raw->country = $x->country;
	if (!empty($x->state)) $location_raw->state = $x->state;
}

if (empty($location_raw->city)) {
	$location_raw->city = $location_raw->city_id ? ($bookingCities[$location_raw->city_id] ?? '') : '';
}

$location = [];

if (!empty($location_raw->country)) $location[] = $location_raw->country;
if (!empty($location_raw->state)) $location[] = $location_raw->state;
if (!empty($location_raw->city)) $location[] = $location_raw->city;
if (!empty($location_raw->neighbourhood)) $location[] = $location_raw->neighbourhood;

$location = $location;

include hfy_tpl('listing/listings-info');
