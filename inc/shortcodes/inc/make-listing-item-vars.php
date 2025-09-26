<?php
if ( ! defined( 'WPINC' ) ) die;

$listingDescription = $listing->description ?? null;
$listingTitle = (isset($listingDescription) && !empty($listingDescription->name)) ? $listingDescription->name : $listing->name;
// Use description name as translation source if available, otherwise use listing name
$listing->name = (isset($listingDescription) && !empty($listingDescription->name)) ? __($listingDescription->name, 'hostifybooking') : __($listing->name, 'hostifybooking');

// Handle rating with fallback to old API v2 structure
$channel_reviews = $listing->channel_reviews ?? null;
$review_system = HFY_REVIEW_SYSTEM; // 0=Airbnb, 1=Booking.com, 2=VRBO, 3=Internal
$use_old_api = false; // Flag to track if we should use old API v2 structure

if ($channel_reviews) {
    // Use API v3 structure
    switch ($review_system) {
        case 1: // Booking.com only
            $originalRating = $listing->rating->bcom->rating ?? 0;
            if ($originalRating > 5) {
                // Rating is in 10-star scale, convert to 5-star
                $rating = $originalRating / 2;
            } else {
                // Rating is already in 5-star scale
                $rating = $originalRating;
            }
            break;
        case 2: // VRBO only
            $rating = $listing->rating->vrbo->rating ?? 0;
            break;
        case 3: // Internal only
            $rating = $listing->rating->internal->rating ?? 0;
            break;
        default: // 0 or any other value - use Airbnb reviews only
            $rating = isset($listing->rating->airbnb->rating) ? $listing->rating->airbnb->rating : 0;
            break;
    }
    
    // Check if the selected channel has any rating, if not, fall back to old API
    if ($rating <= 0) {
        $use_old_api = true;
    }
} else {
    // No channel_reviews available, use old API v2 structure
    $use_old_api = true;
}

// If we need to use old API v2 structure
if ($use_old_api) {
    $rating = isset($listing->rating->rating) ? ListingHelper::getReviewRating($listing->rating->rating) : 0;
}

$reviewsRating = ListingHelper::getReviewStarRating($rating);

$issetDates = !empty($startDate) && !empty($endDate);

$listingUrl = [
    'id' => $listing->id,
    'guests' => $guests ?? 1,
    'adults' => $adults ?? 1,
    'children' => $children ?? 0,
    'infants' => $infants ?? 0,
    'pets' => $pets ?? 0,
];

if ($issetDates && isset($guests)) {
    $listingUrl = array_merge($listingUrl, [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
}

// if (isset($listing->min_nights)) {
//     if ($listing->min_nights >= 28) {
//         $longTermMode = 1;
//     }
// }

$pricePrefix = $issetDates
    ? __('Total', 'hostifybooking')
    : __('From', 'hostifybooking');

$priceSuffix = $issetDates
    ? __('stay', 'hostifybooking')
    : (
        ($longTermMode ?? HFY_LONG_TERM_DEFAULT) == 1 ? __('month', 'hostifybooking') : __('night', 'hostifybooking')
    );

$nights = isset($listing->nights)
    ? $listing->nights
    : (isset($listing->min_nights) ? $listing->min_nights : 1);

if ($issetDates && isset($listing->calculated_price->nights)) {
    $nights = $listing->calculated_price->nights;
}

if ($nights < 1) $nights = 1;

$listing->price = empty($listing->price) ? ($listing->default_daily_price ?? $listing->price) : $listing->price;

// $priceNight = ($listing->price ?? 0) / $nights;
$priceNight = floatval(str_replace(',', '', ''.$listing->price) ?? 0) / $nights;

$priceMarkup = empty($settings->price_markup) ? $listing->price_markup : $settings->price_markup;

// if (isset($listing->calculated_price)) {
//     $price = $listing->calculated_price->priceWithMarkup;
// } else {
//     $price = ListingHelper::calcPriceMarkup(($listing->price ?? 0), $priceMarkup);
// }

// if (isset($listing->extra_person_price) && $listing->extra_person_price) {
//     $price += $listing->extra_person_price;
// }

//// see before
// $listingUrl = [
//     'id' => (int) $listing->id,
//     'guests' => (int) ($guests ?? 1),
//     'long_term_mode' => intval($longTermMode ?? HFY_LONG_TERM_DEFAULT)
// ];
$listingUrl['long_term_mode'] = intval($longTermMode ?? HFY_LONG_TERM_DEFAULT);

$_d1 = hfy_get_('start_date', '');
$_d2 = hfy_get_('end_date', '');
if (!empty($_d1) && !empty($_d2)) {
    $listingUrl = array_merge($listingUrl, [
        'start_date' => $_d1,
        'end_date' => $_d2,
        'guests' => (int) hfy_get_('guests', 1)
    ]);
}
else if ($listingUrl['long_term_mode'] == 1) {
    // $price = ListingHelper::calcPriceMarkup((30 * $listing->default_daily_price), $priceMarkup);
    // if (!empty($listing->monthly_price_factor) && $listing->monthly_price_factor > 0) {
    //     $price = round($price * ((100 - $listing->monthly_price_factor) / 100));
    // }
}

$showReviews = isset($settings->reviews) ? $settings->reviews : false;

$priceFallbackMonth = $priceNight * 30;

// if ($price <= 0)
$price = ($longTermMode ?? HFY_LONG_TERM_DEFAULT) == 1
    ? (
        $listing->price_monthly > 0 ? $listing->price_monthly : (
            $listing->calculated_price->total
            ?? $listing->calculated_price->price
            ?? $listing->min_price_monthly
            ?? $priceFallbackMonth
        )
    )
    : (isset($listing->calculated_price) ? (
        $listing->calculated_price->total
        ?? $listing->calculated_price->price
        ?? $listing->price
    ) : $listing->price);


$priceOnRequest = $listing->is_upon_request ?? $listing->price_on_request ?? 0 == 1;

$priceIsEmpty = is_object($price) ? ($price->price ?? 0) : $price;
$priceIsEmpty = $priceIsEmpty <= 0;

if ($priceIsEmpty) {
    $priceOnRequest = true;
}

$min_notice_hours_not_met = $listing->min_notice_hours_not_met ?? false;
