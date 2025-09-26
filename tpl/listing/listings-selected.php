<?php if (!defined('WPINC')) die; ?>

<div class="hfy-widget-wrap">
	<div class="listings-selected">

        <?php if (!empty($listings)) {
            $idx = 0;
            foreach ($listings as $item) {
                if (!isset($item->listing)) continue;

                $listing = $item->listing;

                // Handle rating with fallback to old API v2 structure
                $channel_reviews = $item->channel_reviews ?? null;
                $review_system = HFY_REVIEW_SYSTEM; // 0=Airbnb, 1=Booking.com, 2=VRBO, 3=Internal
                $use_old_api = false; // Flag to track if we should use old API v2 structure

                if ($channel_reviews) {
                    // Use API v3 structure
                    switch ($review_system) {
                        case 1: // Booking.com only
                            $originalRating = $item->rating->bcom->rating ?? 0;
                            if ($originalRating > 5) {
                                // Rating is in 10-star scale, convert to 5-star
                                $rating = $originalRating / 2;
                            } else {
                                // Rating is already in 5-star scale
                                $rating = $originalRating;
                            }
                            break;
                        case 2: // VRBO only
                            $rating = $item->rating->vrbo->rating ?? 0;
                            break;
                        case 3: // Internal only
                            $rating = $item->rating->internal->rating ?? 0;
                            break;
                        default: // 0 or any other value - use Airbnb reviews only
                            $rating = isset($item->rating->airbnb->rating) ? $item->rating->airbnb->rating : 0;
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
                    $rating = isset($item->rating->rating) ? ListingHelper::getReviewRating($item->rating->rating) : 0;
                }

                $reviewsRating = ListingHelper::getReviewStarRating($rating);

                $listingUrl = ['id' => $listing->id];
                if (isset($startDate) && isset($endDate) && isset($guests)) {
                    $listingUrl = array_merge($listingUrl);
                }

                $priceMarkup = !empty($settings->price_markup) ? $settings->price_markup : $listing->price_markup;
                if (isset($item->price)) {
                    $price = ListingHelper::calcPriceMarkup($item->price, $priceMarkup);
                } else {
                    $price = ListingHelper::calcPriceMarkup($listing->default_daily_price, $priceMarkup);
                }

                if (isset($listing->extra_person_price) && $listing->extra_person_price) {
                    $price += $listing->extra_person_price;
                }

                $listingUrl = ['id' => $listing->id];
                if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
                    $listingUrl = array_merge($listingUrl);
                }

                $showReviews = isset($settings->reviews) ? $settings->reviews : false;

                //
                $custom_color = '';//$this->params['custom_color'];
                $type = 'default';
                include hfy_tpl('listing/listings-selected-item');

                $idx++;
            } ?>
        <?php } else { ?>
            <div class="alert alert-primary" role="alert">
                <?= $msgnodata ?>
            </div>
        <?php } ?>

    </div>
</div>
