<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

$prm = hfy_get_vars_([
	'id',
	'rid',
], true);

$rid = (int) (empty($rid) ? $prm->rid : $rid);
$id = (int) (empty($id) ? $prm->id : $id);
$except = empty($except) ? '' : $except;
$checked = isset($checked) ? !!$checked : true;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';

$extras = null;
$except_ids = array_map('trim', explode(',', $except));

if (!empty($listing->extras)) {
	foreach ($listing->extras as $item) {
		if (!empty($except_ids) && !in_array($item->fee_id, $except_ids)) {
			$extras[] = $item;
		}
	}

	include hfy_tpl('payment/extras-optional');
}
