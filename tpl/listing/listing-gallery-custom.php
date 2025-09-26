<?php if (!defined('WPINC')) die; ?>

<?php
    $smallContainersWidth = '50%';
    $counter = 1;
    if (count($photos) < 5) {
        $smallContainersWidth = '100%';
        $counter = 3;
    }
?>

<section class="hfy-listing-gallery hfy-lg">
    <div class="list">
        <?php
        foreach ($photos as $key => $photo):
            // if ($mainPhoto == $photo['src'] || $counter === 5) continue;
            ?>
            <span class="img-wrap" data-src='<?=$photo['src']?>' data-idx='<?=$key?>'>
                <img src="<?=$photo['thumb']?>" alt="<?= esc_attr($listingData->name . ' ' . $key . ' ' . get_bloginfo('name')) ?>" />
			</span>
            <?php
            $counter++;
        endforeach;
        ?>
    </div>
</section>
