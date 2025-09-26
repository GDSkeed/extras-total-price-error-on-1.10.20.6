<?php

if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception(__('No listing ID', 'hostifybooking'));
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';

// wp_enqueue_script( 'hfygmaps' );
// wp_enqueue_script( 'hfygmap3' );
hfyIncludeMaps($settings);

require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;

$startDate = $prm->start_date;
$endDate = $prm->end_date;

if ($startDate == $endDate || empty($startDate) || empty($endDate)) {
	$startDate = null;
	$endDate = null;
	$prm->start_date = null;
	$prm->end_date = null;
}

$pages = null;

$longTermMode = hfy_ltm_fix_($_GET['long_term_mode'] ?? HFY_LONG_TERM_DEFAULT);

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

if (isset($settings->direct_inquiry_email) && $settings->direct_inquiry_email) {
	include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php';
}

include hfy_tpl('listing/listing');
