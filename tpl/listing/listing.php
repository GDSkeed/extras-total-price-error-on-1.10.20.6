<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing">
    <div class="listing-page-section-md">

        <div class="listing-page-content">
            <div class="listing-page-header">
                <h2><?= $listingData->name; ?></h2>
            </div>
            <div class="listing-page-description">

                <?php
                $pt = ListingHelper::getPropertyType($listingData->property_type_id ?? 0);
                if (!empty($pt)) {
                    ?><h4><?= $pt ?></h4><?php
                }
                ?>

                <?php include hfy_tpl('listing/listing-facilities'); ?>

                <?php include hfy_tpl('listing/listing-info'); ?>

                <h4><?= __( 'Amenities', 'hostifybooking' ) ?></h4>
                <?php include hfy_tpl('listing/listing-amenities'); ?>

                <?php
                /*
                include hfy_tpl('listing/listing-virtual-tour');
                */
                ?>

                <h4><?= __( 'Location', 'hostifybooking' ) ?></h4>
                <?php include hfy_tpl('listing/listing-location'); ?>

                <?php
                if (!empty($listingData->revyoos_widget_id)):
                    ?><script type="application/javascript" src="https://www.revyoos.com/js/widgetBuilder.js" data-revyoos-widget="<?= esc_attr($listingData->revyoos_widget_id) ?>"></script><?php
                endif;
                ?>

            </div>
        </div>

    </div>
</div>
