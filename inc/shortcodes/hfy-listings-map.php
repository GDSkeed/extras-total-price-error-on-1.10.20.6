<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';



// wp_enqueue_script( 'hfygmaps' );
// wp_enqueue_script( 'hfygmap3' );
hfyIncludeMaps($settings);

require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_([
	'neighbourhood',
	'city_id',
	'start_date',
	'end_date',
	'guests',
	'adults',
	'children',
	'infants',
	'bedrooms',
	'bathrooms',
	'long_term_mode',
	'pmin',
	'pmax',
	'tag',
	'pg',
	'pets',
	'custom_search',
	'keyword',
]);

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;
$pets = $prm->pets;

$page = $prm->pg < 1 ? 1 : $prm->pg;

$prm->prop = hfy_get_('prop', []);
$prm->am = hfy_get_('am', []);
$prm->hr = hfy_get_('hr', []);

// Fix tags parameter processing - prioritize shortcode attributes over URL parameters
// First check if shortcode attribute 'tags' is provided
if (!empty($tags)) {
	// Use shortcode attribute directly
	$prm->tag = $tags;
} else {
	// Fall back to URL parameters
	$prm->tag = hfy_get_('tag', '');
	if (empty($prm->tag)) {
		$prm->tag = hfy_get_('tags', '');
	}
	$tags = $prm->tag;
}

$ids = isset($ids) ? $ids : '';
$max = isset($max) ? $max : 9999; // todo

$_cities = empty($city) ? (empty($cities) ? false : $cities) : $city;
$city_id = $prm->city_id ? $prm->city_id : $_cities;
$city_list = $city_id !== false;

if (!empty($neighbourhood)) {
	$prm->neighbourhood = $neighbourhood;
}

$longTermMode = hfy_ltm_fix_(!empty($monthly) ? $monthly : ($prm->long_term_mode ?? HFY_LONG_TERM_DEFAULT));

$api = new HfyApi();

// Enhanced map search parameters for better coverage
$map_search_params = [
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
	'keyword' => $prm->keyword,
	'hr' => $prm->hr,
	'pmin' => floatval($prm->pmin),
	'pmax' => floatval($prm->pmax),
	'tags' => $tags,
	'page' => 1,
	'per_page' => 9999, // Get all listings for map
	'show_prices' => 1,// HFY_MAP_PRICE_LABEL,
	'pets' => $pets
];

// Apply neighbourhood correction logic for map search
if (!empty($prm->neighbourhood) && !empty($prm->custom_search)) {
    // Parse the neighbourhood to get components
    $nh = hfyParseNeighbourhood($prm->neighbourhood);
    
    // For API v3, use city_id approach since q parameter doesn't work with city_id
    if (HFY_USE_API_V3) {
        // Set city_id from neighbourhood for API v3 (this approach works)
        if ($nh->city_id > 0) {
            $map_search_params['city_id'] = $nh->city_id;
        }
    }
}

// Get map data (listings for map markers)
$result = $api->getAvailableListings($map_search_params, true);
$listings = isset($result->listings) ? (array) $result->listings : null;

// If no listings found, try alternative search approaches
if (empty($listings) && !empty($prm->custom_search)) {
    // Try search with just custom_search (keyword search)
    $alt_params = $map_search_params;
    unset($alt_params['neighbourhood']);
    unset($alt_params['city_id']);
    $alt_result = $api->getAvailableListings($alt_params, true);
    if (!empty($alt_result->listings)) {
        $listings = (array) $alt_result->listings;
    }
    
    // If still no results, try with just city_id
    if (empty($listings) && !empty($prm->neighbourhood)) {
        $nh = hfyParseNeighbourhood($prm->neighbourhood);
        if ($nh && $nh->city_id > 0) {
            $city_params = $map_search_params;
            unset($city_params['custom_search']);
            unset($city_params['neighbourhood']);
            $city_params['city_id'] = $nh->city_id;
            $city_result = $api->getAvailableListings($city_params, true);
            if (!empty($city_result->listings)) {
                $listings = (array) $city_result->listings;
            }
        }
    }
}

// Get booking engine data (cities for coordinate lookup) - use same params but without forMap
$bookingEngineResult = $api->getAvailableListings($map_search_params, false);
$cities = !empty($bookingEngineResult->booking_engine->cities) ? $bookingEngineResult->booking_engine->cities : [];

// Calculate map center coordinates based on search parameters
$mapCenterLat = null;
$mapCenterLng = null;

// If we have listings, calculate center from the first few listings
if (!empty($listings)) {
    $latSum = 0;
    $lngSum = 0;
    $count = 0;
    $maxListings = min(5, count($listings)); // Use first 5 listings for center calculation
    
    for ($i = 0; $i < $maxListings; $i++) {
        if (isset($listings[$i]->lat) && isset($listings[$i]->lng)) {
            $latSum += floatval($listings[$i]->lat);
            $lngSum += floatval($listings[$i]->lng);
            $count++;
        }
    }
    
    if ($count > 0) {
        $mapCenterLat = $latSum / $count;
        $mapCenterLng = $lngSum / $count;
    }
}

// Fallback to location-based coordinates if no listings found
if ($mapCenterLat === null || $mapCenterLng === null) {
    // Try to get coordinates from the search parameters using API data
    if (!empty($prm->custom_search)) {
        // Use custom search location coordinates from API
        $locationCoords = hfyGetLocationCoordinatesFromAPI($prm->custom_search, $cities);
        if ($locationCoords) {
            $mapCenterLat = $locationCoords['lat'];
            $mapCenterLng = $locationCoords['lng'];
        }
    } elseif (!empty($prm->neighbourhood)) {
        // Use neighbourhood coordinates from API
        $nh = hfyParseNeighbourhood($prm->neighbourhood);
        if ($nh && $nh->city_id > 0) {
            // Try to find city by city_id in the API data
            foreach ($cities as $city) {
                if (isset($city->city_id) && $city->city_id == $nh->city_id) {
                    if (isset($city->lat) && isset($city->lng)) {
                        $mapCenterLat = floatval($city->lat);
                        $mapCenterLng = floatval($city->lng);
                        break;
                    }
                }
            }
        }
    }
    
    // Final fallback to default coordinates
    if ($mapCenterLat === null || $mapCenterLng === null) {
        $mapCenterLat = 40.7128; // Default to New York coordinates
        $mapCenterLng = -74.0060;
    }
}

$mapMarkers = [];
if ($listings) {
    foreach ($listings as $list) {
        if (empty($list->lat) || empty($list->lng)) {
            continue; // Skip listings without coordinates
        }
        
        $p = $list->price ?? 0;
        if (HFY_MAP_PRICE_LABEL && $p > 0) {
            $priceText = ListingHelper::formatPrice($p, (object) [
                'symbol' => $list->cur_symbol ?? $list->symbol,
                'position' => $list->cur_position ?? $list->position,
            ], true, 2, ',', ' ');
            // Escape any special characters that could break JavaScript
            $priceText = htmlspecialchars($priceText, ENT_QUOTES, 'UTF-8');
            $mapMarkers[] = [$list->id, $list->lat, $list->lng, $priceText];
        } else {
            $mapMarkers[] = [$list->id, $list->lat, $list->lng];
        }
    }
} else {
    // No listings to process for map markers
}

?>
<script>
var
mapPrices = <?= HFY_MAP_PRICE_LABEL ? 'true' : 'false' ?>,
mgreyImg = '<?= HOSTIFYBOOKING_URL . 'public/res/images/mgrey.png' ?>',
mredImg = '<?= HOSTIFYBOOKING_URL . 'public/res/images/mred.png' ?>',
meImg = '<?= HOSTIFYBOOKING_URL . 'public/res/images/mc.png' ?>',
hfyPerPage = <?= HFY_LISTINGS_PER_PAGE ?>,
hfyMapMarkers = <?= json_encode($mapMarkers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
hfyMapMarkersClusters = <?= HFY_MAP_CLUSTERS ? 'true' : 'false' ?>,
lat = <?= floatval($mapCenterLat) ?>,
lng = <?= floatval($mapCenterLng) ?>;

</script>
<?php

include hfy_tpl('listing/listings-map');
?>

<div class="info-window-content-wrap" style='display:none'>
	<?php include hfy_tpl('listing/listings-map-marker-info'); ?>
</div>
