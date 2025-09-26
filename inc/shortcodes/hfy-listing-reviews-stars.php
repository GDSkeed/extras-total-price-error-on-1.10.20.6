<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

$prm = hfy_get_vars_([
	'id',
], true);

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception('');
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

include hfy_tpl('listing/listing-reviews-stars');
