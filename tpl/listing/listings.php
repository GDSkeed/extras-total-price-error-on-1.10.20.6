<?php if (!defined('WPINC')) die; ?>

<div class="hfy-widget-wrap">
	<div class="listings">

        <?php
            if (!empty($bookingEngine)) {
                // todo
                // echo $this->render('../element/booking-search', ['bookingEngine' => $bookingEngine, 'custom_color' => $this->params['custom_color'], 'currentCity' => $currentCity]);
            }
        ?>

        <div class="section">
            <div class="container">
                <?php if (!empty($listings)) { ?>
                    <div class="row listing-block">
                        <?php foreach ($listings as $listing) { ?>

                            <?php
                            $listingSlides = [
                                $listing->thumbnail_file
                            ];
                            if (HFY_USE_LISTINGS_GALLERY) {
                                if (isset($listing->photos)) {

                                    $listing->photos = explode(',', $listing->photos ?? '');
                                    // todo max slides limitation

                                    if (count($listing->photos) > 1) {
                                        $listingSlides = $listing->photos;
                                    }
                                }
                            }
                            ?>

                            <div id="lb-<?= $listing->id ?>" class="col-xs-12 col-sm-6 col-md-6">
                                <?php
                                $custom_color = '';//$this->params['custom_color'];
                                $type = 'default';
                                include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-item-vars.php';
                                include hfy_tpl('listing/listings-item');
                                // include hfy_tpl('listing/listings-item-map'); // DEPRECATED
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <?php
                        if ($pages && $pages->total > 1) include hfy_tpl('element/pagination-block');

                        if (isset($error) && !empty($error)) {
                            include hfy_tpl('element/error');
                        } else {
                            include hfy_tpl('element/error-no-listings-found');
                        }
                    ?>
                <?php } ?>
            </div>
        </div>

        <?php if ($pages && $pages->total > 1) include hfy_tpl('element/pagination-block'); ?>

    </div>
</div>
