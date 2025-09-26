<?php

$api = new HfyApi();
$topListings_ = $api->getTopListings($max);
if ($topListings_) {
	if (!$topListings_->success) {
		throw new HttpException(503, __('Please try again later', 'hostifybooking'));
	} else {
		$topListings = $topListings_->data;
		
		if (isset($topListings->listings) && is_array($topListings->listings)) {
			// Get IDs of top listings
			$topListingIds = array_map(function($listing) {
				return $listing->id;
			}, $topListings->listings);
			
			// Get available listings data with prices
			$result = $api->getAvailableListings([
				'ids' => implode(',', $topListingIds),
				'start_date' => $prm->start_date,
				'end_date' => $prm->end_date,
				'guests' => (int) $prm->guests,
				'longTermMode' => $longTermMode,
				'show_prices' => 1,
				'with_amenities' => true,
				'pets' => $prm->pets,
				'include_related_objects' => 1
			]);
			
			if ($result && isset($result->listings)) {
				// Merge the data from both endpoints instead of replacing
				foreach ($result->listings as $idx => $listing) {
					// Find matching top listing
					foreach ($topListings->listings as $topListing) {
						if ($topListing->id === $listing->id) {
							// Ensure description object exists
							if (!isset($listing->description)) {
								$listing->description = new stdClass();
							}
							
							// If top listing has a description name, use it
							if (isset($topListing->description) && isset($topListing->description->name)) {
								$listing->description->name = $topListing->description->name;
								$listing->name = $topListing->description->name;
							} else {
								// Otherwise use the top listing name
								$listing->description->name = '';
								$listing->name = $topListing->name;
							}
							// Update the listing in the array
							$topListings->listings[$idx] = $listing;
							break;
						}
					}
				}
			}
		}
	}
}
