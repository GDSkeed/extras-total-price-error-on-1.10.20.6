<?php if (!defined('WPINC')) die; ?>

<div class="hfy-widget-wrap">
    <div class="row overflow-hidden">
        <div class="col-12">
            <div class="row">
                <?php
                if (!empty($listings)) {
                    include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
                    foreach ($listings as $item) {
                        if (!isset($item->listing)) {
                            $listing = $item;
                        } else {
                            $listing = $item->listing;
                        }
                        $custom_color = '';
                        $type = 'top';
                        include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-item-vars.php';
                        include hfy_tpl('listing/listings-item');
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
