<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

$ids = $_POST['ids'];
if (!is_array($ids)) $ids = [];

$page = intval($_POST['page'] ?? 1);
if ($page < 1) $page = 1;

$prms = $_POST['prms'] ?? null;

$longTermMode = hfy_ltm_fix_(isset($prms['long_term_mode']) ? $prms['long_term_mode'] : HFY_LONG_TERM_DEFAULT);

$sort = intval($prms['sort'] ?? 0);
$tags = empty($prms['tag']) ? ($tags ?? '') : $prms['tag'];

$startDate = $prms['start_date'] ?? '';
$endDate = $prms['end_date'] ?? '';
$guests = intval($prms['guests'] ?? 1);

$out = [
	'success' => true,
	'data' => '',
	// 'ids' => $ids,
	// 'idsCount' => count($ids),
];

if (empty($ids)) {
	return;
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';

$_cities = empty($city) ? (empty($cities) ? false : $cities) : $city;
$city_id = $prms['city_id'] ?? $_cities;
$city_list = $city_id !== false;

$api = new HfyApi();
$result = $api->getAvailableListings([
	'ids' => $ids,
	'page' => $page,
	'sort' => $sort,
	'show_prices' => HFY_MAP_PRICE_LABEL ? 1 : 0,

	'city_list' => $city_list,
	'city_id' => $city_id,
	'start_date' => $prms['start_date'] ?? null,
	'end_date' => $prms['end_date'] ?? null,
	'guests' => (int) ($prms['guests'] ?? null),
	'bedrooms' => $prms['bedrooms'] ?? null,
	'bathrooms' => $prms['bathrooms'] ?? null,
	'longTermMode' => $longTermMode,
	'prop' => $prms['prop'] ?? null,
	'am' => $prms['am'] ?? null,
	'neighbourhood' => $prms['neighbourhood'] ?? null,
	'custom_search' => $prms['custom_search'] ?? null,
	'keyword' => $prms['keyword'] ?? null,
	'hr' => $prms['hr'] ?? null,
	'pmin' => floatval($prms['pmin'] ?? 0),
	'pmax' => floatval($prms['pmax'] ?? 0),
	'tags' => $tags,
]);

if (isset($result->error)) {
	$error = $result->error;
}

$listings = isset($result->listings) ? (array) $result->listings : null;

$cities = !empty($result->booking_engine->cities) ? $result->booking_engine->cities : [];

$bookingEngine = !empty($result->booking_engine) ? $result->booking_engine : null;

$wishlist = HfyHelper::getWishlist();

?>
<script>
window.listingsNoResult = <?= count($listings ?? []) <= 0 ? 'true' : 'false' ?>;
</script>
<?php

if ($result->success ?? false) {

	$pages = null;
	$html = '';

	ob_start();
	$noLazyLoader = 1;
	include hfy_tpl('listing/listings');
	$html .= ob_get_contents();
	ob_end_clean();

	ob_start();
	if ($result->total > 0) {
		if ($result->total > 1) {
			$total_pages = ceil(($result->total) / HFY_LISTINGS_PER_PAGE);
			$pages = (object) [
				'total' => $total_pages,
				'current' => $page,
				'prev' => $page > 1 ? $page - 1 : null,
				'next' => ($page + 1 <= $total_pages) ? ($page + 1) : null,
				'link' => '#',
			];
			include hfy_tpl('element/pagination-block');
			$html .= ob_get_contents();
		}
	} else {
		include hfy_tpl('element/no-listings-available');
		$html .= ob_get_contents();
	}
	ob_end_clean();

	$out['data'] = $html;
	$out['fetched'] = count($listings);
	$out['total'] = $result->total;
	$out['totalPages'] = ceil($result->total / 20);

} else {
	$out['success'] = false;
	$out['error'] = $result->error ?? 'error';
}
