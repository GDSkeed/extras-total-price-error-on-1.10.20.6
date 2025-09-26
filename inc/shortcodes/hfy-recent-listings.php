<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_def();

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;
$pets = $prm->pets;

$recent = isset($_SESSION["recent_listings"]) ? $_SESSION["recent_listings"] : '';
if (!empty($recent)) {
    $ids = [];
    $ids_ = array_reverse(explode(',', $recent));
    foreach ($ids_ as $x) {
        $ids[] = (int) $x;
    }
    $listings = [];
    // $listings_sorted = [];
    $api = new HfyApi();

    foreach ($ids as $lid) {
        $res = $api->getWebsiteListing($lid);
        if (isset($res) && $res->success) {
            $listings[] = $res;
        }
    }

    // $listings_tmp = $api->getListingsByIds($ids);
    // if (isset($listings_tmp->listings)) {
    //     foreach ($listings_tmp->listings as $x) {
    //         $listings_sorted[$x->listing->id] = $x->listing;
    //     }
    //     foreach ($ids as $x) {
    //         if (isset($listings_sorted[$x])) {
    //             $listings[] = $listings_sorted[$x];
    //         }
    //     }
    // }

    if (!empty($listings)) {
        include hfy_tpl('listing/recent-listings');
    }

}
