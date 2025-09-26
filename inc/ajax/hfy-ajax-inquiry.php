<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-terms.php'; // todo

$direct_inquiry_email = $settings->direct_inquiry_email;


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
        wp_send_json($out);
        wp_die();
    }

    $verify_result = json_decode(wp_remote_retrieve_body($verify_response), true);

    if (!$verify_result['success']) {
        $out = [
            'success' => false,
            'msg' => 'reCAPTCHA verification failed: Invalid token.'
        ];
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
            wp_send_json($out);
            wp_die();
        }
    }
}
// --- END reCAPTCHA server-side verification ---

parse_str($_POST['data'] ?? '', $pdata);



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
} elseif (empty($listingId)) {
    $out['msg'] = 'Missing listing ID.';
} elseif (empty($check_in)) {
    $out['msg'] = 'Check-in date is required.';
} elseif (empty($check_out)) {
    $out['msg'] = 'Check-out date is required.';
} else {
    // Validation passed, proceed with API calls

	$api = new HfyApi();

	$result = $api->getListingPrice($listingId, $check_in, $check_out, $adults, false, $discount_code, $adults, $children, $infants, $pets);
	if (!$result || isset($result->error)) {
		$out = ['success' => false, 'msg' => $result->error];
	} else {

		if ($result->price->totalAfterTax) {
			$totalPrice = $result->price->totalAfterTax;
		} else {
			// Use pre-calculated total from API
			$totalPrice = $result->price->totalAfterTax ?? $result->price->total ?? $result->price->totalPrice ?? 0;
		}

		if ($discount_code) {
			$message .= "\n\nDISCOUNT CODE: $discount_code";
		}

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

		if (!$result || isset($result->error)) { // Check for error in result object too
		          $apiError = isset($result->error) ? $result->error : 'Unable to submit inquiry (postBookListing failed)';
			$out = ['success' => false, 'msg' => $apiError];
		} else {
			ob_start();
			include hfy_tpl('element/direct-inquiry-mail');
			$msg = ob_get_contents();
			ob_end_clean();
			$out = ['success' => true];
		}
	}
}
