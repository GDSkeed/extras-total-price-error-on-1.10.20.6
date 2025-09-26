<?php if (!defined('WPINC')) die; ?>

<?php
// Only show reviews if there are reviews with ratings
$hasReviewsWithRatings = false;
foreach ($listingReviews as $review) {
    if (($review->rating ?? 0) > 0) {
        $hasReviewsWithRatings = true;
        break;
    }
}

if ($hasReviewsWithRatings):
?>

<div class="hfy-reviews-comments" data-total-reviews="<?= count($listingReviews); ?>" data-initial-count="10">

    <div class="reviews-comments-list <?= $horizontal ? 'horiz' : '' ?>" id="reviews-comments-list">
        <?php 
        $shown = 0;
        foreach ($listingReviews as $review) :
            if ($shown >= 10) break;
            
            // Handle guest picture - use fallback if no image
            $guestPicture = $review->guest_picture ?? '';
            $guestName = $review->name ?? '';
            $firstLetter = !empty($guestName) ? strtoupper(substr($guestName, 0, 1)) : '?';
            
            // Check if we have a valid picture URL
            $hasValidPicture = !empty($guestPicture) && 
                              $guestPicture !== '/assets/global/img/no-avatar.png' && 
                              filter_var($guestPicture, FILTER_VALIDATE_URL);
            
            // Handle rating and stars for individual review
            $reviewRating = $review->rating ?? 0;
            
            // Determine max stars based on review source
            $displayRating = $reviewRating;
            if (isset($review->source) && $review->source === 'bcom') {
                $displayRating = $reviewRating / 2; // Convert 10-star to 5-star
            }
            // VRBO and internal reviews are already in 5-star scale, no conversion needed
            
            // Generate stars for this review
            $reviewStars = ListingHelper::generateStarRating($displayRating, $settings->custom_color ?? '#FF5A5F', '#E4E5E6', 120, 20);
            
            // Check if review has content (stars or comments)
            $hasStars = $reviewRating > 0;
            $hasComments = !empty(trim($review->comments ?? ''));
            
            // Skip this review if it has no stars and no comments
            if (!$hasStars && !$hasComments) {
                continue;
            }
        ?>
            <div class="reviews-comments-item">
                <?php if ($hasValidPicture): ?>
                    <img class="comment-author" src="<?= $guestPicture ?>" alt="<?= $guestName; ?>" />
                <?php else: ?>
                    <div class="comment-author-fallback" title="<?= $guestName; ?>">
                        <?= $firstLetter ?>
                    </div>
                <?php endif; ?>
                <div class="comment-body">
                    <h5>
                        <span class="review-header-left">
                            <?= $guestName; ?>, <span><?= date('F Y', strtotime($review->created)); ?></span>
                        </span>
                        <span class="review-header-right">
                            <?php if ($reviewRating > 0): ?>
                                <span class="review-rating-inline">
                                    <span class="review-stars">
                                        <?= $reviewStars['background'] ?>
                                        <span style="height:20px;width:<?= $reviewStars['width'] ?>px">
                                            <?= $reviewStars['foreground'] ?>
                                        </span>
                                    </span>
                                    <span class="review-rating-number"><?= number_format($displayRating, 1) ?></span>
                                </span>
                            <?php endif; ?>
                            <?php if (isset($review->source) && $show_review_source && !($use_old_api ?? false)): ?>
                                <span class="review-source">
                                    <?php if ($review->source === 'airbnb'): ?>
                                        <span style="color: #FF5A5F; font-weight: bold;">• Airbnb</span>
                                    <?php elseif ($review->source === 'bcom'): ?>
                                        <span style="color: #003580; font-weight: bold;">• Booking.com</span>
                                    <?php elseif ($review->source === 'vrbo'): ?>
                                        <span style="color: #00A699; font-weight: bold;">• VRBO</span>
                                    <?php elseif ($review->source === 'internal'): ?>
                                        <span style="color: #767676; font-weight: bold;">• Hostify</span>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </h5>
                    
                    <p class="comment-content"><?= $review->comments; ?></p>
                </div>
            </div>
        <?php 
            $shown++;
        endforeach;
        ?>
    </div>

    <div id="more-reviews-container"></div>

    <?php if (count($listingReviews) > 10): ?>
        <button id="view-more-reviews" class="view-more-reviews-btn" data-current-count="10">
            View More Reviews
        </button>
    <?php endif; ?>

</div>

<script>
window.listingReviewsData = <?php echo json_encode($listingReviews); ?>;
window.hostifyNoAvatarUrl = '<?= HOSTIFYBOOKING_URL . 'public/res/images/1.png' ?>';
window.hfyShowReviewSource = <?php echo $show_review_source ? 'true' : 'false'; ?>;
window.hfyCustomColor = '<?= $settings->custom_color ?? '#FF5A5F' ?>';
window.hfyUseOldApi = <?php echo ($use_old_api ?? false) ? 'true' : 'false'; ?>;


</script>

<?php endif; ?>
