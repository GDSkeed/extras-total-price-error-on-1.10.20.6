<?php if (!defined('WPINC')) die; ?>

<div id="lb-<?= $listing->id ?>" class="listings-selected-item effect-hover-1 <?= $idx > 3 ? 'no-grow' : '' ?>">

    <a href="<?= UrlHelper::listing($listingUrl); ?>">
        <div class="img">
            <img src="<?= $listing->thumbnail_file; ?>" alt="<?= esc_attr($listing->name . ' ' . get_bloginfo('name')) ?>" />

            <?php include hfy_tpl('user/wishlist-item-control'); ?>
        </div>

        <div class="info" style='display:none'>
            <div class="description">
                <div class="title"><?= $listing->name; ?></div>
                <div class="address"><?= $listing->street; ?></div>

                <div class="details">
                    <div class="guests">
                        <?= $item->details->person_capacity; ?>
                        <?= __( 'guests', 'hostifybooking' ) ?>
                    </div>
                    <div class="beds">
                        <?= $item->details->beds; ?>
                        <?= __( 'beds', 'hostifybooking' ) ?>
                    </div>
                    <div class="rooms">
                        <?= $item->details->bedrooms; ?>
                        <?= __( 'bedrooms', 'hostifybooking' ) ?>
                    </div>
                </div>

                <?php //if ($showReviews) { ?>
                <?php if ($reviewsRating) {
                    $star = '<polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon>';
                    ?>
                    <div class="reviews-rating">
                        <span style="width:100px;height:18px;display:inline-table;position:relative;top:3px">
                            <svg width="100" height="18" viewBox="0 0 100 16"><g stroke="none" fill="#E4E5E6" stroke-width="0"><g><?= $star ?></g><g transform="translate(20)"><?= $star ?></g><g transform="translate(40)"><?= $star ?></g><g transform="translate(60)"><?= $star ?></g><g transform="translate(80)"><?= $star ?></g></g></svg>
                            <span style="height:18px;float:left;position:absolute;left:0;overflow:hidden;width:<?= $reviewsRating  ?>px">
                                <svg width="100" height="18" viewBox="0 0 100 16"><g stroke="none" fill="" stroke-width="0"><g><?= $star ?></g><g transform="translate(20)"><?= $star ?></g><g transform="translate(40)"><?= $star ?></g><g transform="translate(60)"><?= $star ?></g><g transform="translate(80)"><?= $star ?></g></g></svg>
                            </span>
                        </span>
                        <div class="reviews-rating-num">
                            <?= round($rating, 1); ?> <small>/ 5</small>
                        </div>
                    </div>
                <?php } ?>

                <div class="price">
                    <?php if ($listing->price_on_request ?? 0): ?>
                        <?= __( 'on request', 'hostifybooking' ) ?>
                    <?php else: ?>
                        <?= __( 'from', 'hostifybooking' ) ?>
                        <?= ListingHelper::formatPrice($price, $item) ?>
                    <?php endif; ?>
                </div>

                <div class="more">
                    <button class="more-button">
                        <?= __( 'See more', 'hostifybooking' ) ?>
                    </button>
                </div>
            </div>
        </div>
    </a>

    <?php // include hfy_tpl('listing/listings-item-map'); // DEPRECATED ?>

</div>
