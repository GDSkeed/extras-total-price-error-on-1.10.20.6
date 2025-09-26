<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

$id = (int) $_POST['id'];

$out = [
	'success' => false,
	'info' => null,
];

if ($id <= 0) return;

$prm = hfy_get_vars_def();

// Get search parameters from POST data (sent by map markers)
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);
$adults = (int)($_POST['adults'] ?? 1);
$children = (int)($_POST['children'] ?? 0);
$infants = (int)($_POST['infants'] ?? 0);
$pets = (int)($_POST['pets'] ?? 0);

// Set the parameters in $prm for consistency
$prm->start_date = $startDate;
$prm->end_date = $endDate;
$prm->guests = $guests;
$prm->adults = $adults;
$prm->children = $children;
$prm->infants = $infants;
$prm->pets = $pets;

try {
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';
	
	// Get calculated price for the specific dates if dates are provided
	$calculatedPrice = null;
	if (!empty($startDate) && !empty($endDate)) {
		$api = new HfyApi();
		$priceResult = $api->getListingPrice($id, $startDate, $endDate, $guests, false, '', $adults, $children, $infants, $pets);
		if ($priceResult && $priceResult->success && isset($priceResult->price)) {
			$calculatedPrice = $priceResult->price;
		}
	}
} catch (\Exception $e) {
	return;
}

$listingUrl = ['id' => $id];

// Only preserve search parameters for API v3 (as requested)
if (HFY_USE_API_V3) {
    // Include search parameters in the listing URL if they exist
    if (!empty($startDate) && !empty($endDate)) {
        $listingUrl = array_merge($listingUrl, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'guests' => $guests
        ]);
    }
    
    // Also include other search parameters if they exist
    if ($adults > 1) $listingUrl['adults'] = $adults;
    if ($children > 0) $listingUrl['children'] = $children;
    if ($infants > 0) $listingUrl['infants'] = $infants;
    if ($pets > 0) $listingUrl['pets'] = $pets;
}

$out['success'] = true;
$out['info'] = [
    'title' => $listingTitle,
    'text' => '',
	'url' => UrlHelper::listing($listingUrl),
    'img' => $listingData->thumbnail_file,
	'price' => $calculatedPrice ? ListingHelper::formatPrice($calculatedPrice, $listing) : ListingHelper::formatPrice($listing->price ?? 0, $listing),
];