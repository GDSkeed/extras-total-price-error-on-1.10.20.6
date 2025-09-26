<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_([
	'neighbourhood',
	'start_date',
	'end_date',
	'guests',
	'adults',
	'children',
	'infants',
	'pets',
	'bedrooms',
	'bathrooms',
	'city_id',
	'long_term_mode',
	'keyword',
	// advanced
	'pmin',
	'pmax',
	'tag',
	'custom_search',
]);

$cityneighbourhood = isset($cityneighbourhood) && ($cityneighbourhood == 1);

$prm->prop = hfy_get_('prop', []);
$prm->am = hfy_get_('am', []);
$prm->tag = hfy_get_('tag', '');

$longTermMode = hfy_ltm_fix_(!empty($monthly) ? $monthly : ($prm->long_term_mode ?? HFY_LONG_TERM_DEFAULT));

if (!empty($neighbourhood)) {
	$prm->neighbourhood = $neighbourhood;
}

$city_id = $prm->city_id;

$startDate = $prm->start_date;
$start_date = $prm->start_date;
$endDate = $prm->end_date;
$end_date = $prm->end_date;

// fix dates
$d1 = DateTime::createFromFormat('d-m-Y', $start_date);
$d2 = DateTime::createFromFormat('d-m-Y', $end_date);
if ($d1 > $d2) {
	$start_date = $prm->end_date;
	$end_date = $prm->start_date;
	list($d1, $d2) = array($d2, $d1);
}

$guests = $prm->guests < 1 || !is_numeric($prm->guests) ? 1 : $prm->guests;
$bedrooms = (int) ($_GET['bedrooms'] ?? 0);
$bathrooms = (int) ($_GET['bathrooms'] ?? false);

$adults = (int) $prm->adults;
$children = (int) $prm->children;
$infants = (int) $prm->infants;
$pets = (int) $prm->pets;
$keyword = trim($prm->keyword ?? '');

$advanced = $advanced == 1 || strtolower($advanced) === 'true';

$api = new HfyApi();

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-data.php';

// if ($advanced) {
	$propTypes = $api->getAvailablePropertyTypes();
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-amenities.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-properties.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
// }

// $neighbourhoods = $api->getNeighbourhoods();

$showIcons = false;
$noBedroomFilter = false;
$noLocationFilter = false;

if (!empty($bookingEngine)) {
	$noBedroomFilter = empty($bookingEngine->bedroom_filter ?? null);
	
	// Check if PMS has location filter enabled
	if (HFY_USE_API_V3) {
		// For v3, use WordPress plugin setting to control location filter visibility
		$options = get_option('hostifybooking-plugin');
		$showLocationFilterV3 = ($options['show_location_filter_v3'] ?? 'no') == 'yes';
		$noLocationFilter = !$showLocationFilterV3 || empty($bookingEngine->cities ?? null);
	} else {
		// For v2, use the location_filter property from PMS
		if (property_exists($bookingEngine, 'location_filter')) {
			$noLocationFilter = ($bookingEngine->location_filter != 1);
		} else {
			// Safety fallback for v2
			$noLocationFilter = true;
		}
	}
}

$tagsmenu = empty($tagsmenu) ? [] : explode(',', $tagsmenu);

$prop = $prm->prop;
$amenitiesSelected = $prm->am;
$priceMin = floatval($_GET['price_min'] ?? $_GET['pmin'] ?? false);
$priceMax = floatval($_GET['price_max'] ?? $_GET['pmax'] ?? false);
$cat = $_GET['cat'] ?? [];
$hr = $_GET['hr'] ?? [];
$hidemap = (int) ($_GET['hidemap'] ?? 0);

if (empty(trim($prm->custom_search))) {
	// country:state:city_id:neighbourhood
	$nh = hfyParseNeighbourhood($prm->neighbourhood);
	$neighbourhood = $nh->neighbourhood;
	$city_id = $nh->city_id ? $nh->city_id : $city_id;
	$city_neigh = $nh->sanitized;
	if (empty($city_neigh)) $city_neigh = $city_id;
	$custom_search = null;
} else {
	$neighbourhood = null;
	$city_id = null;
	$city_neigh = null;
	$custom_search = trim($prm->custom_search);
}

if ($longTermMode == 1) {
    $price_min = $settings->price_min_month ?? 0;
    $price_max = $settings->price_max_month ?? 100000;
} else {
    $price_min = $settings->price_min_day ?? 0;
    $price_max = $settings->price_max_day ?? 10000;
}

$showSearchIndicator =
    // ($bookingEngine->bedroom_filter && !empty($bedrooms))
    !empty($bedrooms)
    || !empty($bathrooms)
    || !empty($prop)
    || !empty($amenitiesSelected)
    || (!empty($priceMin) && $priceMin > $price_min)
    || (!empty($priceMax) && $priceMax < $price_max)
    || !empty($cat)
    || !empty($hr)
;

if (HFY_RICH_SELECT_LOC):
	?>
	<script>
	var entloctxt = '<?= str_replace("'", '', HFY_TEXT_SELECT_LOCATION) ?>';
	</script>
	<?php
endif;

// we need both dates
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
	// for calentim value
    $dates_value = $start_date_formatted . ' - ' . $end_date_formatted;
}

echo '<script>var hfyltm=' . intval($longTermMode) . ';</script>';

include hfy_tpl('element/booking-search');
