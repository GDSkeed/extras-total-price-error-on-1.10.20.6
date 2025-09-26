<?php

$api = new HfyApi();
$pres = $api->getListingPrice(
	$listing_id,
	$startDate,
	$endDate,
	$guests,
	false,
	isset($discount_code) ? $discount_code : $prm->discount_code,
	$adults,
	$children,
	$infants,
	$pets
);
if (!$pres || !$pres->success) {
	throw new Exception(isset($pres->error) ? $pres->error : __('Listing price is not available', 'hostifybooking'));
}
$listingPrice = $pres->price;
