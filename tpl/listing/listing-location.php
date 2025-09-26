<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-location">
    <?php if (HFY_DYNAMIC_GOOGLE_MAP && !empty(HFY_GOOGLE_MAPS_API_KEY)) { ?>
        <div class="location-wrapper"></div>
    <?php } else { ?>
        <div class="static-location-wrapper"></div>
    <?php } ?>
</div>
