<?php
if (!defined('WPINC')) die;

$_target = HFY_USE_LISTINGS_GALLERY_CLICK ? "_blank" : "_self";

?>

<div class="list-card <?= isset($type) && $type === 'top' ? 'col-md-3' : '' ?>" >

    <?php if (HFY_USE_LISTINGS_GALLERY): ?>
        <div class="img">
            <?php if (count($listingSlides ?? []) > 1): ?>
                <div class='slider'>
                    <div class="blaze-container">
                        <div class="blaze-track-container">
                            <div class="blaze-track">
                                <?php foreach(($listingSlides ?? []) as $idx => $listingThumb): ?>
                                    <a class="img-wrap" href="<?= UrlHelper::listing($listingUrl); ?>" target="<?= $_target ?>">
                                        <img src="<?= $listingThumb ?>" alt="<?= esc_attr($listing->name . ' ' . $idx . ' ' . get_bloginfo('name')) ?>" loading="lazy" />
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="blaze-prev"></div>
                        <div class="blaze-next"></div>
                    </div>
                </div>
            <?php else: ?>
                <a class="img-wrap" href="<?= UrlHelper::listing($listingUrl); ?>" target="<?= $_target ?>">
                    <img src="<?= $listing->thumbnail_file ?>" alt="<?= esc_attr($listing->name . ' ' . get_bloginfo('name')) ?>" loading="lazy" />
                </a>
            <?php endif; ?>
            <?php include hfy_tpl('user/wishlist-item-control'); ?>
        </div>
    <?php endif; ?>

    <a href="<?= UrlHelper::listing($listingUrl); ?>" class="nostylish-hover" target="<?= $_target ?>">

        <?php if (!HFY_USE_LISTINGS_GALLERY): ?>
            <div class="img">
                <img src="<?= $listing->thumbnail_file; ?>" alt="<?= esc_attr($listing->name . ' ' . get_bloginfo('name')) ?>" loading="lazy" />
                <?php include hfy_tpl('user/wishlist-item-control'); ?>
            </div>
        <?php endif; ?>

        <div class="info">

            <div class="description">
                <?php if (isset($type) && $type === 'top') : ?>
                    <p class="l-description"><?= $listing->name; ?></p>
                <?php else : ?>
                    <?= $listing->name; ?>
                <?php endif; ?>
            </div>

            <div class="title">
                <?php if (!empty($pricePrefix)): ?>
                    <span class="prefix"><?= $pricePrefix ?></span>
                <?php endif; ?>

                <?= ListingHelper::formatPrice($price, $listing, true) ?>

                <?php if (!empty($priceSuffix)): ?>
                    <span class="suffix"> / <?= $priceSuffix ?></span>
                <?php endif; ?>
            </div>

            <?php if (isset($min_notice_hours_not_met) && $min_notice_hours_not_met === true): ?>
                <div class="min-notice-hint">
                    <?php _e('Minimum advance notice required', 'hostify-booking'); ?>
                </div>
            <?php endif; ?>

            <?php if ($showReviews && intval($reviewsRating) > 0) : ?>
                <?php include hfy_tpl('listing/listing-reviews-stars'); ?>
            <?php endif; ?>

        </div>
    </a>
</div>
