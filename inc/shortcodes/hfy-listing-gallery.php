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
// include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

$listingData = $listing->listing;
$listingData->is_listed = true; // deprecated, fallback

$listingDetails = $listing->details;
$listingDescription = $listing->description;
$listingPhotos = $listing->photos;
$listingAmenities = $listing->amenities;
$listingCurrencyData = $listing->currency_data;
$listingPrice = $listing->price;
$priceTitle = '';

if (!empty($listingPrice) && is_object($listingPrice)) {
    $price = isset($listingPrice->priceWithMarkup) ? $listingPrice->priceWithMarkup : $listingPrice->totalAfterTax;
    $priceFormatted = ListingHelper::formatPrice($price, $listingPrice);
    $priceTitle = sprintf(__('price <span class="h3">%s</span> for %s %s', 'hostifybooking'),
        $priceFormatted,
        $listingPrice->nights,
        ($listingPrice->nights > 1 ? __('nights', 'hostifybooking') : __('night', 'hostifybooking'))
    );
}

$calendarDisabledDates = $listing->calendar;
$calendarCustomStay = $listing->custom_stay;
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

if (isset($view) && in_array(strtolower($view), ['ab', 'abnb', 'airbnb'])) {
    include hfy_tpl('listing/listing-gallery-abnb');
} else {
    include hfy_tpl('listing/listing-gallery');
}
