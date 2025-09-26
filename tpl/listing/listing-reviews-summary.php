<?php if (!defined('WPINC')) die; ?>

<?php
//if ($showReviews) :

// Determine rating system based on review system setting
$review_system = HFY_REVIEW_SYSTEM; // 0=Airbnb, 1=Booking.com, 2=VRBO, 3=Internal
$maxStars = ($review_system == 1) ? 10 : 5; // Booking.com uses 10-star, others use 5-star

// Generate main rating stars
$mainStars = ListingHelper::generateStarRating($rating, $settings->custom_color ?? '#FF5A5F', '#E4E5E6', 120, 20);

// Only show reviews if rating is greater than zero
if ($rating > 0):
?>
<div class="hfy-reviews-summary-modern">
    <div class="reviews-summary-header">
        <div class="main-rating">
            <div class="rating-number"><?= number_format($rating, 1) ?></div>
            <div class="review-rating-inline">
                <span class="review-stars">
                    <?= $mainStars['background'] ?>
                    <span style="height:20px;width:<?= $mainStars['width'] ?>px">
                        <?= $mainStars['foreground'] ?>
                    </span>
                </span>
            </div>
            <div class="rating-text"><?= sprintf(__('%d reviews', 'hostifybooking'), $reviewsCount) ?></div>
        </div>
    </div>
    
    <div class="reviews-summary-details">
        <div class="rating-categories">
            <?php
            // Check if we're using old API v2 structure (fallback)
            $use_old_api = false;
            if (isset($accuracyRating) || isset($cleanRating) || isset($communicationRating) || 
                isset($locationRating) || isset($valueRating) || isset($checkinRating)) {
                $use_old_api = true;
            }
            
            if ($use_old_api) {
                // Use old API v2 structure - display individual ratings from variables
                $old_ratings = [
                    'accuracy' => $accuracyRating ?? 0,
                    'cleanliness' => $cleanRating ?? 0,
                    'communication' => $communicationRating ?? 0,
                    'location' => $locationRating ?? 0,
                    'value' => $valueRating ?? 0,
                    'check_in' => $checkinRating ?? 0
                ];
                
                foreach ($old_ratings as $key => $value) {
                    if ($value > 0) {
                        // Convert from star rating percentage back to 5-star scale for display
                        $displayValue = ($value / 20); // 20px per star, so divide by 20 to get stars
                        
                        // Generate category stars
                        $categoryStars = ListingHelper::generateStarRating($displayValue, $settings->custom_color ?? '#FF5A5F', '#E4E5E6', 120, 20);
                        
                        // Convert key to readable label
                        $label = ucwords(str_replace('_', ' ', $key));
                        if ($key === 'check_in') $label = 'Check In';
                        ?>
                        <div class="rating-category">
                            <div class="category-label"><?= $label ?></div>
                            <div class="review-rating-inline">
                                <span class="review-stars">
                                    <?= $categoryStars['background'] ?>
                                    <span style="height:20px;width:<?= $categoryStars['width'] ?>px">
                                        <?= $categoryStars['foreground'] ?>
                                    </span>
                                </span>
                                <span class="review-rating-number"><?= number_format($displayValue, 1) ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
            } else {
                // Use new API v3 structure
                $rating_obj = null;
                if ($review_system == 1) {
                    $rating_obj = $listing->rating->bcom ?? null;
                } elseif ($review_system == 2) {
                    $rating_obj = $listing->rating->vrbo ?? null;
                } elseif ($review_system == 3) {
                    $rating_obj = $listing->rating->internal ?? null;
                } else {
                    $rating_obj = $listing->rating->airbnb ?? null;
                }
                
                if ($rating_obj) {
                    // Loop through all properties except 'reviews' and 'rating'
                    foreach ($rating_obj as $key => $value) {
                        if ($key !== 'reviews' && $key !== 'rating' && $value > 0) {
                            // Convert to 5-star scale for display and star calculation (same as comments template)
                            $displayValue = $value;
                            if ($review_system == 1) {
                                $displayValue = $value / 2; // Convert 10-star to 5-star
                            }
                            
                            // Generate category stars using 5-star scale (same as comments template)
                            $categoryStars = ListingHelper::generateStarRating($displayValue, $settings->custom_color ?? '#FF5A5F', '#E4E5E6', 120, 20);
                            
                            // Convert key to readable label - completely dynamic
                            $label = str_replace('_', ' ', $key);
                            $label = str_replace(' rating', '', $label);
                            $label = ucwords($label);
                            ?>
                            <div class="rating-category">
                                <div class="category-label"><?= $label ?></div>
                                <div class="review-rating-inline">
                                    <span class="review-stars">
                                        <?= $categoryStars['background'] ?>
                                        <span style="height:20px;width:<?= $categoryStars['width'] ?>px">
                                            <?= $categoryStars['foreground'] ?>
                                        </span>
                                    </span>
                                    <span class="review-rating-number"><?= number_format($displayValue, 1) ?></span>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php //endif; ?>
