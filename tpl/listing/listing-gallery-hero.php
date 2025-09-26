<?php if (!defined('WPINC')) die; ?>

<section class="hfy-listing-gallery hfy-listing-gallery-hero">
    <div class="main">
        <img src="<?=$mainPhoto?>" />
    </div>
    <div class="list">
        <?php
        foreach ($photos as $key => $photo):
            if ($mainPhoto == $photo['src'] || $counter === 5) continue;
            ?>
            <div class="img-wrap">
                <img src="<?=$photo['src']?>" alt="<?= esc_attr($listingData->name . ' ' . $key . ' ' . get_bloginfo('name')) ?>"  />
            </div>
            <?php
            $counter++;
        endforeach;
        ?>
    </div>
</section>
