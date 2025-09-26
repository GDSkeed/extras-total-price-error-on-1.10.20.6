<?php
if (!defined('WPINC')) die;

$smallContainersWidth = '50%';
$counter = 1;
if (count($photos) < 5) {
    $smallContainersWidth = '100%';
    $counter = 3;
}
?>

<section class="hfy-listing-gallery hfy-lg hfy-listing-gallery-abnb">
    <div class="main img-wrap" data-id="0" data-src="<?=$mainPhoto?>">
        <img src="<?=$mainPhoto?>" alt="<?= esc_attr($listingData->name . ' ' . get_bloginfo('name')) ?>" />
    </div>
    <div class="list">
        <?php
        foreach ($photos as $key => $photo):
            if ($mainPhoto == $photo['src']) continue;
            ?>
            <div class="img-wrap" data-src='<?=$photo['src']?>' data-id='<?=$key?>'>
                <img src="<?=$photo['src']?>" alt="<?= esc_attr($listingData->name . ' ' . $key . ' ' . get_bloginfo('name')) ?>" />
            </div>
            <?php
            $counter++;
        endforeach;
        ?>
    </div>
</section>
