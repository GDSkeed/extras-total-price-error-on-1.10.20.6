<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception('');
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';

$listingData = $listing->listing;
$listingData->is_listed = true; // deprecated, fallback

$listingDetails = $listing->details;
$listingDescription = $listing->description;
$listingPhotos = $listing->photos;
$listingAmenities = $listing->amenities;
$listingCurrencyData = $listing->currency_data;
$listingPrice = $listing->price;
$priceTitle = '';

$photos = [];
foreach ($listingPhotos as $key => $photo) {
    $photos[] = [
        'thumb' => $photo->thumbnail_file,
        'src' => $photo->original_file
    ];
}

if (isset($listingPhotos[0]->original_file)) {
    $mainPhoto = $listingPhotos[0]->original_file;
} else {
    $mainPhoto = $listingData->thumbnail_file;
}

include hfy_tpl('listing/listing-slider');
