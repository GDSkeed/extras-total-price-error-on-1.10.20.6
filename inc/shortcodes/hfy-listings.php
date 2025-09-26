<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

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
	'keyword',
	'pmin',
	'pmax',
	'sort',
	'tag',
	'pg',
	'custom_search',
	'max',
]);

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;
$pets = $prm->pets;
$keyword = trim($prm->keyword ?? '');

$page = $prm->pg < 1 ? 1 : $prm->pg;

$max = (int) ($max ?? 0);
$max_ = (int) $prm->max;
if ($max_ > 0) {
	$max = $max < HFY_LISTINGS_PER_PAGE ? HFY_LISTINGS_PER_PAGE : $max;
}

$prm->prop = hfy_get_('prop', []);
$prm->am = hfy_get_('am', []);
$prm->hr = hfy_get_('hr', []);

$prm->tag = hfy_get_('tag', '');
if (empty($prm->tag)) {
	$prm->tag = hfy_get_('tags', '');
}

// Prioritize shortcode attribute over URL parameters
$tags = !empty($tags) ? $tags : $prm->tag;

$ids = isset($ids) ? $ids : '';

$_cities = empty($city) ? (empty($cities) ? false : $cities) : $city;
$city_id = $prm->city_id ? $prm->city_id : $_cities;
$city_list = $city_id !== false;

if (!empty($neighbourhood)) {
	$prm->neighbourhood = $neighbourhood;
}

if (!empty(trim($prm->custom_search))) {
	$prm->neighbourhood = null;
	$prm->custom_search = trim($prm->custom_search);
}


include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-properties.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-amenities.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-data.php';


// for sort
// include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-top-listings.php';

$longTermMode = hfy_ltm_fix_(!empty($monthly) ? $monthly : ($prm->long_term_mode ?? HFY_LONG_TERM_DEFAULT));

// sort by price, default - no sort
$sort_get = strlen($prm->sort) > 0 ? intval($prm->sort) : HFY_LISTINGS_SORT;
$sort = (isset($sort) && strlen($sort) > 0) ? (int) $sort : $sort_get;

$api = new HfyApi();

$result = $api->getAvailableListings([
	'ids' => $ids,
	'city_list' => $city_list,
	'city_id' => $city_id,
	'start_date' => $prm->start_date,
	'end_date' => $prm->end_date,
	'guests' => (int) $prm->guests,
	'bedrooms' => $prm->bedrooms,
	'bathrooms' => $prm->bathrooms,
	'longTermMode' => $longTermMode,
	'prop' => $prm->prop,
	'am' => $prm->am,
	'neighbourhood' => $prm->neighbourhood,
	'custom_search' => $prm->custom_search,
	'keyword' => $keyword,
	'hr' => $prm->hr,
	'sort' => $sort,
	'pmin' => floatval($prm->pmin),
	'pmax' => floatval($prm->pmax),
	'tags' => $tags,
	'page' => $page,
	'with_amenities' => false,
	'pets' => $pets,
	'per_page' => $max,
]);

$error = false;
if (isset($result->error)) {
	$error = $result->error;
}

$listings = isset($result->listings) ? (array) $result->listings : null;

$cities = !empty($result->booking_engine->cities) ? $result->booking_engine->cities : [];

$currentCity = false;
if ($prm->city_id) {
	foreach ($cities as $city) {
		if ($city->city_id == $prm->city_id) {
			$currentCity = (object)[
				'id' => $city->id,
				'city_id' => $city->city_id,
				'name' => $city->name,
				'image' => $city->image
			];
		}
	}
}

$bookingEngine = !empty($result->booking_engine) ? $result->booking_engine : null;

$startDate = $prm->start_date;
$endDate = $prm->end_date;

$neighbourhood = $prm->neighbourhood ?? null;
// $totalPages = ceil((isset($result->total) ? $result->total : 0) / $api::LISTINGS_PER_PAGE);

$wishlist = HfyHelper::getWishlist();

?>
<script>
window.listingsNoResult = <?= count($listings ?? []) <= 0 ? 'true' : 'false' ?>;
</script>
<?php

$total_items = $result->total ?? 0;

// pagination
$page = intval($page ?? 1);
if ($page < 1) $page = 1;
$total_pages = ceil($total_items / $api->listings_per_page);

global $wp;
$args_ = $_GET ?? [];
if (isset($args_['pg'])) unset($args_['pg']);
$args_['pg'] = '{page}';

array_walk($args_, function(&$item, $key) {
    if (is_string($item) && $key != 'pg') $item = urlencode($item);
});

$pages = (object) [
	'total' => $total_pages,
	'current' => $page,
	'prev' => $page > 1 ? $page - 1 : null,
	'next' => ($page + 1 <= $total_pages) ? ($page + 1) : null,
	'link' => home_url(add_query_arg($args_, $wp->request)),
];

?>
<script>var hfyltm=<?= $longTermMode ?>;</script>
<div class="hfy-widget-wrap-listings">
	<div>
		<?php include hfy_tpl('listing/listings'); ?>
	</div>
	<span class="hfy-wwl-none" style="display:none"><?php include hfy_tpl('element/error-no-listings-found'); ?></span>
	<span class="hfy-wwl-updating" style="display:none"></span>
</div>
<?php
