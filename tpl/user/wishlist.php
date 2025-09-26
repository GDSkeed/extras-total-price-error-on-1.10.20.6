<?php
if (!defined('WPINC')) die;

$sort = intval($_GET['sort'] ?? 3);
if ($sort <= 0) $sort = 3; // def
if ($sort < 2) $sort = 2;

$out = [];
foreach ($listings as $listing) {
    $l = $listing->listing;
    $l->price = $listing->price;
    $l->photos = array_map(function($c) { return $c->thumbnail_file; }, $listing->photos);
    $out[] = $l;
}
$listings = $out;
uasort($listings, $sort == 3 ? 'hfySortListings_fn_desc' : 'hfySortListings_fn');
?>

<?php if (!empty($listings)) : ?>

    <div class='sort-controls-wrap'>
        <?php if (count($listings) > 0): ?>
            <div>
                <span style='color:#777'><?= __('Sort by:', 'hostifybooking') ?></span>
                <select name='sort' class='custom-search-ctrl'>
                    <option value='2' <?= $sort == 2 ? 'selected' : '' ?>><?= __('Price ascending', 'hostifybooking') ?> </option>
                    <option value='3' <?= $sort == 3 ? 'selected' : '' ?>><?= __('Price descending', 'hostifybooking') ?></option>
                </select>
            </div>
        <?php endif; ?>
    </div>

    <div class="hfy-widget-wrap">
        <div class="listings">
            <div class="section">
                <div class="row listing-block">
                    <?php
                    foreach ($listings as $listing) :
                        $listingSlides = [$listing->thumbnail_file];
                        if (isset($listing->photos)) {
                            if (count($listing->photos) > 1) {
                                $listingSlides = $listing->photos;
                            }
                        }
                        ?>
                        <div id="lb-<?= $listing->id ?>" class="listing-item">
                            <?php
                            $custom_color = '';
                            $type = 'default';

                            $price = $listing->price ?? $listing->price_monthly ?? $listing->calculated_price->price ?? $listing->default_daily_price;

                            include hfy_tpl('user/wishlist-item');
                            ?>
                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
            </div>

        </div>
    </div>

<?php else: ?>

    <?php include hfy_tpl('user/error-wishlist-empty'); ?>

<?php endif; ?>
