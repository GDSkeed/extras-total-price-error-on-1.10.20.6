<?php

$api = new HfyApi();

global $wp;

$id = (int) ($id ?? $listingId ?? $_GET['id'] ?? $wp->query_vars['id'] ?? 0);

$listing = $api->getWebsiteListing($id, true, $prm->startDate ?? '', $prm->endDate ?? '', $prm->guests ?? '', $prm->adults ?? '', $prm->children ?? '', $prm->infants ?? '', $prm->pets ?? '', true);


//echo '<div style="background: #f5f5f5; padding: 20px; margin: 20px; border: 1px solid #ddd;">';
//echo '<h3>Debug Info:</h3>';
//echo '<pre>';
//var_dump($listing);
//echo '</pre>';
//echo '</div>';

if (!$listing) {
	// throw new HttpException(503, __('Please try again later', 'hostifybooking'));
	hfy_no_listing();
}

if (!isset($listing->listing)) {
	// throw new Exception(__('No listing', 'hostifybooking'));
	hfy_no_listing();
}

$prm->adults = intval($prm->adults ?? 1);
$prm->children = intval($prm->children ?? 0);
$prm->infants = intval($prm->infants ?? 0);
$prm->pets = intval($prm->pets ?? 0);

if (HFY_USE_BOOKING_FORM_V2) {
	$pers = intval($listing->details->person_capacity ?? 1);
	if ($prm->adults > $pers) $prm->adults = $pers;
	if ($prm->children > $pers) $prm->children = $pers;
	if (intval($prm->adults) + intval($prm->children) > $pers) $prm->children = $pers - intval($prm->adults);
	if ($prm->children < 0) $prm->children = 0;
	$prm->guests = intval($prm->adults) + intval($prm->children);
	if ($prm->guests < 1) $prm->guests = 1;
}

$res = $api->getWebsiteListingMinstay($id);
$listingMinStay = [];
if ($res->success ?? false) {
	$now = new DateTime();
	$minn = intval($listing->listing->min_nights ?? 1);
	foreach (($res->calendar ?? []) as $item) {
		$d = new DateTime($item->date);
		if ($d >= $now && $item->min_stay <> $minn) {
			$listingMinStay[$item->date] = $item->min_stay;
		}
	}
}
