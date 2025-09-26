<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php'; // todo

$direct_inquiry_email = $settings->direct_inquiry_email;

// Log received raw data
error_log('[Hostify Inquiry v2] Received POST data: ' . print_r($_POST['data'] ?? 'Not set', true));

// --- reCAPTCHA server-side verification ---
$recaptcha_secret = defined('HFY_GOOGLE_RECAPTCHA_SECRET_KEY') ? HFY_GOOGLE_RECAPTCHA_SECRET_KEY : '';
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
$recaptcha_version = defined('HFY_GOOGLE_RECAPTCHA_VERSION') ? HFY_GOOGLE_RECAPTCHA_VERSION : 'v2';

// Only verify reCAPTCHA if secret key is configured
if (!empty($recaptcha_secret)) {
    if (empty($recaptcha_response)) {
        $out = [
            'success' => false,
            'msg' => 'reCAPTCHA verification failed: No token provided.'
        ];
        error_log('[Hostify Inquiry v2] reCAPTCHA failed: No token provided.');
        wp_send_json($out);
        wp_die();
    }

    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $verify_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];

    $verify_response = wp_remote_post($verify_url, [
        'body' => $verify_data,
        'timeout' => 10
    ]);

    if (is_wp_error($verify_response)) {
        $out = [
            'success' => false,
            'msg' => 'reCAPTCHA verification failed: Network error.'
        ];
        error_log('[Hostify Inquiry v2] reCAPTCHA network error: ' . $verify_response->get_error_message());
        wp_send_json($out);
        wp_die();
    }

    $verify_result = json_decode(wp_remote_retrieve_body($verify_response), true);

    if (!$verify_result['success']) {
        $out = [
            'success' => false,
            'msg' => 'reCAPTCHA verification failed: Invalid token.'
        ];
        error_log('[Hostify Inquiry v2] reCAPTCHA failed: Invalid token.');
        wp_send_json($out);
        wp_die();
    }

    // For v3, also check the score
    if ($recaptcha_version === 'v3' && isset($verify_result['score'])) {
        $score = floatval($verify_result['score']);
        if ($score < 0.5) { // Typical threshold for v3
            $out = [
                'success' => false,
                'msg' => 'reCAPTCHA verification failed: Score too low (' . $score . ').'
            ];
            error_log('[Hostify Inquiry v2] reCAPTCHA v3 failed: Score ' . $score . ' below threshold.');
            wp_send_json($out);
            wp_die();
        }
        error_log('[Hostify Inquiry v2] reCAPTCHA v3 passed with score: ' . $score);
    } else {
        error_log('[Hostify Inquiry v2] reCAPTCHA v2 verification passed.');
    }
} else {
    error_log('[Hostify Inquiry v2] Skipping reCAPTCHA verification - secret key not configured.');
}
// --- END reCAPTCHA server-side verification ---

parse_str($_POST['data'] ?? '', $pdata);

// Log parsed data
error_log('[Hostify Inquiry v2] Parsed data ($pdata): ' . print_r($pdata, true));


$listingId = intval($pdata['listingId'] ?? $pdata['listing_id'] ?? 0); // Accept listing_id as well
$adults = intval(is_numeric($pdata['adults']) ? $pdata['adults'] : 1);
$children = intval(is_numeric($pdata['children']) ? $pdata['children'] : 0);
$infants = intval(is_numeric($pdata['infants']) ? $pdata['infants'] : 0);
$pets = intval(is_numeric($pdata['pets']) ? $pdata['pets'] : 0);

$guests = $adults + $children;

$check_in = trim($pdata['check_in'] ?? '');
$check_out = trim($pdata['check_out'] ?? '');
$discount_code = trim($pdata['discount_code'] ?? '');
$listingNickname = trim($pdata['listingNickname'] ?? '');
$listingName = trim($pdata['listingName'] ?? '');

$first_name = trim($pdata['first_name'] ?? '');
$last_name = trim($pdata['last_name'] ?? '');
$email = trim($pdata['email'] ?? '');
$phone = trim($pdata['phone'] ?? '');
$message = trim($pdata['message'] ?? '');

$out = [
	'success' => false,
	'msg' => 'Incorrect data'
];

// Detailed initial validation
if (empty($direct_inquiry_email)) {
    $out['msg'] = 'Inquiry recipient email is not configured.';
    error_log('[Hostify Inquiry v2] Validation failed: Inquiry recipient email missing.');
} elseif (empty($listingId)) {
    $out['msg'] = 'Missing listing ID.';
    error_log('[Hostify Inquiry v2] Validation failed: Listing ID missing or invalid.');
} elseif (empty($check_in)) {
    $out['msg'] = 'Check-in date is required.';
    error_log('[Hostify Inquiry v2] Validation failed: Check-in date missing.');
} elseif (empty($check_out)) {
    $out['msg'] = 'Check-out date is required.';
    error_log('[Hostify Inquiry v2] Validation failed: Check-out date missing.');
} else {
    // Validation passed, proceed with API calls
    error_log('[Hostify Inquiry v2] Initial validation passed. Proceeding with API calls for Listing ID: ' . $listingId);

	$api = new HfyApi();

    error_log('[Hostify Inquiry v2] Calling getListingPrice...'); // Log before API call
	$result = $api->getListingPrice($listingId, $check_in, $check_out, $adults, false, $discount_code, $adults, $children, $infants, $pets);
    error_log('[Hostify Inquiry v2] getListingPrice result: ' . print_r($result, true)); // Log result
	if (!$result || isset($result->error)) {
		$out = ['success' => false, 'msg' => $result->error];
	} else {

		if ($result->price->totalAfterTax) {
			$totalPrice = $result->price->totalAfterTax;
		} else {
			$priceMarkup = !empty($settings->price_markup) ? $settings->price_markup : 0;
			$result->price->price = ListingHelper::calcPriceMarkup($result->price->price, $priceMarkup);
			$totalPrice = $result->price->price + $result->price->cleaning_fee + $result->price->extra_person_price;
		}

		if ($discount_code) {
			$message .= "\n\nDISCOUNT CODE: $discount_code";
		}

		      error_log('[Hostify Inquiry v2] Calling postBookListing (as pending inquiry)...'); // Log before API call
		$result = $api->postBookListing(
			$listingId,
			$check_in,
			$check_out,
			$guests,
			$totalPrice,
			$first_name . ' ' . $last_name,
			$email,
			$phone,
			$message,
			HfyApi::RESERVATION_STATUS_PENDING,
			$discount_code,
			isset($result->price->discount->id) ? $result->price->discount->id : 0,
			$adults,
			$children,
			$infants,
			$pets
		);

		      error_log('[Hostify Inquiry v2] postBookListing result: ' . print_r($result, true)); // Log result
		if (!$result || isset($result->error)) { // Check for error in result object too
		          $apiError = isset($result->error) ? $result->error : 'Unable to submit inquiry (postBookListing failed)';
			$out = ['success' => false, 'msg' => $apiError];
		          error_log('[Hostify Inquiry v2] postBookListing failed: ' . $apiError);
		} else {
			ob_start();
			include hfy_tpl('element/direct-inquiry-mail');
			$msg = ob_get_contents();
			ob_end_clean();
			$out = ['success' => true];
		}
	}
}
