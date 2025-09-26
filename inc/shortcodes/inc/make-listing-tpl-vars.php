<?php

if ( ! defined( 'WPINC' ) ) die;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-amenities.php';

$listingData = $listing->listing;
$listingData->is_listed = true; // deprecated, fallback

$listingDescription = $listing->description;

$listingTitle = empty($listingDescription->name) ? $listingData->name : $listingDescription->name;
$listingData->name = $listingTitle;

$listingDetails = $listing->details;
$listingAmenities = $listing->amenities;
$listingCurrencyData = $listing->currency_data;

$listingCurrencySymbol = isset($listingCurrencyData->symbol) ? $listingCurrencyData->symbol : $listingData->currency;
$listingPrice = $listing->price;
$priceTitle = '';

// Calculate price per night for display
$listingPricePerNight = null;
$total = null;
$totalNights = null;
$tax = null;
$discount_code = null;

if (!empty($listingPrice) && is_object($listingPrice)) {
    // Calculate price per night for both v2 and v3 pricing structures
    $listingPricePerNight = ListingHelper::calcPricePerNight($listingPrice);
    $listingPricePerNight = number_format($listingPricePerNight, 2, '.', '');
    
    // Set other required variables for price block
    $total = $listingPrice->totalAfterTax ?? 0;
    $totalNights = $listingPrice->priceWithMarkup ?? 0;
    $tax = $listingPrice->tax_amount ?? 0;
    $discount_code = $prm->discount_code ?? '';
}

$listingPriceOnRequest = ($listingData->price_on_request ?? 0) == 1;

$minPrices = $listing->min_prices ?? null;
// $minPrice = $minPrices->min_price ?? null;
$minPrice = null;
if (is_object($listingPrice)) {
    $minPrice = $listingPrice->price && $listingPrice->nights
        ? intval($listingPrice->price / $listingPrice->nights)
        : $minPrices->min_price ?? null;
}

$minPriceMonthly = $minPrices->min_price_monthly ?? null;

if (($listingData->min_nights ?? 1) >= 28 && $minPriceMonthly > 0) {
    $showPrice = $minPriceMonthly;
    $showPricePer = __('month', 'hostifybooking');
} else {
    $showPrice = $minPrice;
    $showPricePer = __('night', 'hostifybooking');
}

if ($listingPriceOnRequest) {
    $priceTitle = __('Price on request', 'hostifybooking');
} else {
    if (!empty($listingPrice) && is_object($listingPrice)) {
        $price = isset($listingPrice->priceWithMarkup) ? $listingPrice->priceWithMarkup : $listingPrice->totalAfterTax;
        $priceFormatted = ListingHelper::formatPrice($price, $listingPrice);
        $priceTitle = sprintf('price <span class="h3">%s</span> for %s %s',
            $priceFormatted,
            $listingPrice->nights,
            ($listingPrice->nights > 1 ? 'nights' : 'night')
        );
    }
}

$calendarDisabledDates = [];

foreach (($listing->calendar ?? []) as $val) {
    $calendarDisabledDates[] = [
        'start' => $val->start,
        'end' => $val->end ?? $val->date_end,
    ];
}

$calendarCustomMinStay = $listingMinStay ?? [];
$calendarCustomMinStay = array_filter($calendarCustomMinStay, function($x) { return !is_null($x); }); // filter nulls

$calendarCustomStay = $listing->custom_stay ?? [];

// New Review System Logic - Handle channel_reviews based on setting
$channel_reviews = $listing->channel_reviews ?? null;
$review_system = HFY_REVIEW_SYSTEM; // 0=Airbnb, 1=Booking.com 2=VRBO, 3=Internal
$show_review_source = HFY_SHOW_REVIEW_SOURCE;

// Determine which reviews to use based on review system setting
$use_old_api = false; // Flag to track if we should use old API v2 structure

if ($channel_reviews) {
    // Use channel_reviews data (API v3 structure)
    $airbnb_reviews = $channel_reviews->airbnb ?? [];
    $bcom_reviews = $channel_reviews->bcom ?? [];
    $vrbo_reviews = $channel_reviews->vrbo ?? [];
    $internal_reviews = $channel_reviews->internal ?? [];
    
    // Add source information to each review
    foreach ($airbnb_reviews as $review) {
        $review->source = 'airbnb';
    }
    foreach ($bcom_reviews as $review) {
        $review->source = 'bcom';
    }
    foreach ($vrbo_reviews as $review) {
        $review->source = 'vrbo';
    }
    foreach ($internal_reviews as $review) {
        $review->source = 'internal';
    }
    
    switch ($review_system) {
        case 1: // Booking.com only
            $listingReviews = $bcom_reviews;
            // Convert Booking.com rating - check if it's already in 5-star scale
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
            $listingReviews = $vrbo_reviews;
            $rating = $listing->rating->vrbo->rating ?? 0;
            break;
        case 3: // Internal only
            $listingReviews = $internal_reviews;
            $rating = $listing->rating->internal->rating ?? 0;
            break;
        default: // 0 or any other value - use Airbnb reviews only
            $listingReviews = $airbnb_reviews;
            $rating = isset($listing->rating->airbnb->rating) ? $listing->rating->airbnb->rating : 0;
            break;
    }
    
    // Check if the selected channel has any reviews, if not, fall back to old API
    if (empty($listingReviews) || count($listingReviews) == 0) {
        $use_old_api = true;
    }
} else {
    // No channel_reviews available, use old API v2 structure
    $use_old_api = true;
}

// If we need to use old API v2 structure (either no channel_reviews or no reviews in selected channel)
if ($use_old_api) {
    $listingReviews = $listing->reviews ?? [];
    if (is_array($listingReviews) && !empty($listingReviews[0]) && is_object($listingReviews[0])) {
        usort($listingReviews, function($a, $b){
            return $a->created < $b->created;
        });
        
        // Ensure old API v2 reviews have the same structure as new API v3 reviews
        foreach ($listingReviews as $review) {
            // Add source property for old API v2 reviews (default to 'airbnb' for compatibility)
            if (!isset($review->source)) {
                $review->source = 'airbnb';
            }
            
            // Ensure all required properties exist
            if (!isset($review->rating)) {
                $review->rating = 0;
            }
            if (!isset($review->comments)) {
                $review->comments = '';
            }
            if (!isset($review->name)) {
                $review->name = '';
            }
            if (!isset($review->guest_picture)) {
                $review->guest_picture = '';
            }
            if (!isset($review->created)) {
                $review->created = '';
            }
        }
    }
    $rating = isset($listing->rating->rating) ? ListingHelper::getReviewRating($listing->rating->rating) : 0;
}

$reviewsCount = count($listingReviews);

// Calculate star rating for 5-star display system (always use 5-star for visual consistency)
$reviewsRating = round($rating * 20, 1); // 5 stars = 100px, so 1 star = 20px
// Ensure star width doesn't exceed 100px (5 stars maximum)
$reviewsRating = min($reviewsRating, 100);

// Get individual ratings based on review system setting
if ($use_old_api) {
    // Use old API v2 structure for individual ratings
    $accuracyRating = isset($listing->rating->accuracy_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->accuracy_rating)) : 0;
    $checkinRating = isset($listing->rating->checkin_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->checkin_rating)) : 0;
    $cleanRating = isset($listing->rating->clean_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->clean_rating)) : 0;
    $communicationRating = isset($listing->rating->communication_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->communication_rating)) : 0;
    $locationRating = isset($listing->rating->location_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->location_rating)) : 0;
    $valueRating = isset($listing->rating->value_rating) ? ListingHelper::getReviewStarRating(ListingHelper::getReviewRating($listing->rating->value_rating)) : 0;
}
// For new API v3, don't set individual rating variables - let the template handle it dynamically

$cancellationPolicy = $listing->cancel_policy_v2->name ?? null;
$paymentSchedule = $listing->payment_schedule ?? null;
$calendarv2 = $listing->calendar_v2 ?? null;

$overIn = [];
$overOut = [];
$overMinStay = [];

if (!empty($calendarv2)) foreach ($calendarv2 as $d => $item) {
    if ($item->cta == 1) $overIn[] = $d;
    if ($item->ctd == 1) $overOut[] = $d;
    if ($item->min > 1 && !isset($calendarCustomMinStay[$d])) $overMinStay[$d] = $item->min;
}

echo '
<script>var
calendarDisabledDates = '.json_encode($calendarDisabledDates).',
calendarCustomStay = '.json_encode($calendarCustomStay).',
calendarCustomMinStay = '.json_encode($calendarCustomMinStay).',
calendarInDays = '.intval($listingData->no_checkin_days ?? 0).',
calendarOutDays = '.intval($listingData->no_checkout_days ?? 0).',
calendarOverInDays = '.json_encode($overIn ?? []).',
calendarOverOutDays = '.json_encode($overOut ?? []).',
calendarOverMinStay = '.json_encode($overMinStay ?? []).',
minNights = '.intval($listingData->min_nights ?? 1).',
maxNights = '.intval($listingData->max_nights ?? 0).',
lat = "'.$listingData->lat.'",
lng = "'. $listingData->lng.'",
hfyltm = '.intval($longTermMode ?? HFY_LONG_TERM_DEFAULT).',
hfyminstay = '.( ($longTermMode ?? HFY_LONG_TERM_DEFAULT) == 1 ? 28 : intval($listingData->min_nights ?? 1) ).'
;
</script>';
