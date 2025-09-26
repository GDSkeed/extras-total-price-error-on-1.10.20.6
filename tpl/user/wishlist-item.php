<?php
if (!defined('WPINC')) die;

$title = $listing->name;

?>

<div class="list-card <?= isset($type) && $type === 'top' ? 'col-md-3' : '' ?>">

    <a href="<?= UrlHelper::get_listing_human_url($listing->id) ?>" class="nostylish-hover">

        <div class="img">
            <?php if (isset($listing->thumbnail_file)): ?>
                <img src="<?= esc_attr($listing->thumbnail_file) ?>" alt="<?= esc_attr($title . ' ' . get_bloginfo('name')) ?>" />
            <?php endif; ?>
            <?php if (is_user_logged_in()) : ?>
                <span class="added-to-wish" data-id="<?= $listing->id ?>" title="<?= esc_attr(__('Remove from wish list', 'hostifybooking')) ?>"></span>
            <?php endif; ?>
        </div>

        <div class="description">

            <div class="title" data-no-translation data-no-dynamic-translation>
                <?= $title ?>
            </div>

            <div class="details">

                <div class='address' data-no-translation data-no-dynamic-translation>
                    <?= __($listing->country, 'hostifybooking') ?>,
                    <?= __($listing->city, 'hostifybooking') ?><?= trim($listing->neighbourhood ?? '') !== '' ? ', ' . __($listing->neighbourhood, 'hostifybooking') : '' ?>
                </div>

                <?= __('Persons:', 'hostifybooking') ?> <?= $listing->person_capacity ?? '&nbsp;' ?></span>
                <?php if ($listing->bedrooms ?? 0 > 0): ?>
                    | <?= __('Bedrooms:', 'hostifybooking') ?> <?= $listing->bedrooms ?>
                <?php endif; ?>
                | <?= __('Baths:', 'hostifybooking') ?> <?= $listing->bathrooms ?? '&nbsp;' ?>
                <?php if ($listing->area): ?>
                    | <?= __('Area:', 'hostifybooking') ?> <?= $listing->area ?>&nbsp;m<sup>2</sup>
                <?php endif; ?>
            </div>

            <div class="price">
                <span><?= __('Price', 'hostifybooking') ?></span>
                <?php if (($listing->price_on_request ?? 0) || (($price ?? $listing->price) <= 0)): ?>
                    <?= __('on request', 'hostifybooking') ?>
                <?php else: ?>
                    <?= __('from', 'hostifybooking'); ?>
                    <?= ListingHelper::formatPrice($price, $listing, true, 0, ' ') ?>
                    <?php if (!empty($priceSuffix)): ?>
                        <span class="suffix"> / <?= $priceSuffix ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>

    </a>
</div>
