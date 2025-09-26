<?php

$api = new HfyApi();

$listing = $api->getWebsiteListing($id, false);

// if (!$listing) {
// 	throw new HttpException(503, __('Please try again later', 'hostifybooking'));
// }

// if (!isset($listing->listing)) {
// 	throw new Exception(__('No listing', 'hostifybooking'));
// }

$listing = $listing->listing ?? null;
