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
$ids = empty($ids) ? '' : $ids;
$detailed = isset($detailed) ? !!$detailed : false;
$selected = isset($selected) ? !!$selected : false;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';

// todo $listing->extras

$api = new HfyApi();
$result = $api->getExtras($id, $ids);

$error = isset($result->error) ? $result->error : null;

if ($error) {
	include hfy_tpl('element/error');
} else {

	if ($result && $result->success) {
		$extras = $result->extras->items ?? null;
		$total = $result->extras->total ?? 0;
	} else {
		$extras = null;
		$total = 0;
	}

	// echo '<pre>';	print_r($result);	echo '</pre>';

	include hfy_tpl('payment/extras-set');
}
