<?php

$t_ = [
	'price_markup' => false,
	'book_airbnb' => false,
	'payment_service' => false,
];

$for_listing_id = (int) ($listingId ?? $_POST['id'] ?? $_GET['id'] ?? $wp->query_vars['id'] ?? 0);

$api = new HfyApi();
$settings_ = $api->getSettings($for_listing_id);

if ($settings_ && $settings_->success) {
	if (($settings_->data->is_active ?? 0) == 0) {
		throw new Exception(__('Is not active', 'hostifybooking'));
	}

	$t_['wsid'] = isset($settings_->data->_id) ? intval($settings_->data->_id) : 0;
	$t_['custom_color'] = isset($settings_->data->custom_color) ? $settings_->data->custom_color : 'orange';

	$t_['book_airbnb'] = (($settings_->data->book_airbnb ?? 0) == 1) ? true : false;
	$t_['direct_inquiry_email'] = isset($settings_->data->direct_inquiry_email) ? $settings_->data->direct_inquiry_email : false;
	$t_['reviews'] = isset($settings_->data->reviews) ? $settings_->data->reviews : false;
	$t_['fs_integration_id'] = isset($settings_->data->fs_integration_id) ? intval($settings_->data->fs_integration_id) : false;

	$t_['price_markup'] = isset($settings_->data->price_markup) && $settings_->data->price_markup
		? $settings_->data->price_markup
		: (($settings_->data->price_markup == 0 && $settings_->data->price_markup !== null) ? 0 : false);

	$t_['payment_service'] = ($settings_->data->payment_service == 'none' || !$settings_->data->payment_service_integration_id)
		? false : $settings_->data->payment_service;

	// $t_['long_term_mode'] = isset($settings_->data->long_term_mode) && $settings_->data->long_term_mode ? true : false;
	$t_['long_term_mode'] = HFY_LONG_TERM_DEFAULT;

	$t_['customer_id'] = isset($settings_->data->fs_customer_id) ? $settings_->data->fs_customer_id : false;
	$t_['security_deposit'] = ($settings_->data->security_deposit ?? 0) ? true : false;

	$t_['price_min_day'] = $settings_->price_min_day ?? null;
    $t_['price_max_day'] = $settings_->price_max_day ?? null;
    $t_['price_min_month'] = $settings_->price_min_month ?? null;
    $t_['price_max_month'] = $settings_->price_max_month ?? null;

	$t_['data'] = $settings_->data ?? null;

	$lfcs = ($bookingEngine->location_filter_cs ?? 0) == 1;

	$t_['locations'] = $settings_->locations ?? null;
	$la = [];
	foreach ($t_['locations'] as $x_country => $x_states) {
		if ($lfcs) $la[$x_country] = $x_country;
		foreach ($x_states as $x_state => $x_cities) {
			if ($lfcs) $la[$x_country . ':' . $x_state] = $x_country . ', ' . $x_state;
			foreach ($x_cities as $x_city_name => $x_item) {
				$x = implode(':', [$x_country, $x_state, $x_item->id ?? 0]);
				$xn = implode(', ', $lfcs
					? [$x_country, $x_state, $x_city_name]
					: [$x_city_name]
				);
				$la[$x] = $xn;
				if (isset($x_item->neighs)) foreach ($x_item->neighs as $x_neigh) {
					$la[$x . ':' . $x_neigh] = $xn . ', ' . $x_neigh;
				}
			}
		}
	}
	sort($la);
	$t_['locations_array'] = $la;

	$t_['api_key_maps'] = empty(HFY_GOOGLE_MAPS_API_KEY) ? ($settings_->api_key_maps ?? null) : HFY_GOOGLE_MAPS_API_KEY;
	$t_['api_key_captcha'] = empty(HFY_GOOGLE_RECAPTCHA_SITE_KEY) ? ($settings_->api_key_captcha ?? null) : HFY_GOOGLE_RECAPTCHA_SITE_KEY;
}

$settings = (object) $t_;

$showReviews = isset($settings->reviews) ? $settings->reviews : false;

// todo inject long_term_mode to js
// add_action('wp_enqueue_scripts', function() use($settings) {
// 	$script = 'var1 = 1; ';
// 	$script .= 'var2 = 2; ';
// 	wp_add_inline_script('shape_script', $script, 'before');
// });
