<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/HfyHelper.php';

$prm = hfy_get_vars_def();

// $id = $prm->id && empty($id) ? $prm->id : $id;

// if (empty($id)) {
// 	throw new Exception(__('No listing ID', 'hostifybooking'));
// }

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;
$pets = $prm->pets;

$startDate = $prm->start_date;
$endDate = $prm->end_date;
$max = intval($max ?? 0);



$longTermMode = hfy_ltm_fix_(!empty($monthly) ? $monthly : ($prm->long_term_mode ?? HFY_LONG_TERM_DEFAULT));

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-properties.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-amenities.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-top-listings.php';

$wishlist = HfyHelper::getWishlist();

?>
<script>
window.listingsNoResult = <?= count($topListings->listings ?? []) <= 0 ? 'true' : 'false' ?>;
</script>
<script>var hfyltm=<?= $longTermMode ?>;</script>
<div class="hfy-widget-wrap-listings">
    <div>
        <?php include hfy_tpl('listing/top-listings'); ?>
    </div>
    <span class="hfy-wwl-none" style="display:none"><?php include hfy_tpl('element/error-no-listings-found'); ?></span>
    <span class="hfy-wwl-updating" style="display:none"></span>
</div>
<?php
