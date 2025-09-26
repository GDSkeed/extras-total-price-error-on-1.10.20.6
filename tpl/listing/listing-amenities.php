<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-amenities">
    <?php
    $more = isset($more) ? !!$more : false;
    $moretext = isset($moretext) ? $moretext : __( 'show more &rarr;', 'hostifybooking' );
    $moreaction = isset($moreaction) ? !!$moreaction : true;
    $nn = 1;
    $max = isset($max) ? $max : 0;
    $rest = [];
    $am_ids = unserialize(HFY_AMENITIES_IDS);

    foreach ($listing->amenities as $amenity) :
        if (!empty($am_ids) && !in_array($amenity->id, $am_ids)) {
            continue;
        }
        $imgfile = "public/res/images/am/{$amenity->id}.svg";
        $imgurl = '';
        if (file_exists(HOSTIFYBOOKING_DIR . $imgfile)) {
            $imgurl = HOSTIFYBOOKING_URL . $imgfile;
        }
        if (HFY_AMENITIES_IMAGES_FIRST && empty($imgurl)) {
            if (!HFY_AMENITIES_IMAGES_ONLY) {
                $rest[] = $amenity;
            }
            continue;
        }
        $hidden = $max > 0 && $nn > $max;
        include hfy_tpl('listing/listing-amenities-item');
        if ($max > 0) $nn++;
    endforeach;

    if (!empty($rest)) {
        $imgurl = '';
        foreach ($rest as $amenity) {
            $hidden = $max > 0 && $nn > $max;
            include hfy_tpl('listing/listing-amenities-item');
            if ($max > 0) $nn++;
        }
    }

    if ($more) {
        ?><span class='hfy-am--more <?= $moreaction ? 'do-action' : '' ?>'><?= $moretext ?></span><?php
    }
    ?>
</div>
