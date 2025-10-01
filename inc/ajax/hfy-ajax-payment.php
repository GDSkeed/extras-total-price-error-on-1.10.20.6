<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$data = !empty($_POST['data']) ? $_POST['data'] : [];

if (
    empty($data['listing_id'])
    || empty($data['start_date'])
    || empty($data['end_date'])
    || empty($data['total'])
    || !isset($data['guests'])
    || !isset($data['discount_code'])
    || (empty($data['stripeObject']) && empty($data['payer_id']))
) {
    $out = ['success' => false];
} else {
    // Get listing price information to access partial payment amount if available
    $api = new HfyApi();
    $adults = intval($data['adults'] ?? 1);
    $children = intval($data['children'] ?? 0);
    $infants = intval($data['infants'] ?? 0);
    $pets = intval($data['pets'] ?? 0);
    
    // Convert comma-separated fee IDs to array for API
    $feesToSend = !empty($data['fees']) ? explode(',', $data['fees']) : [];
    
    error_log("DEBUG PAYMENT: Received total from frontend: " . ($data['total'] ?? 'not set'));
    error_log("DEBUG PAYMENT: Fees to send from frontend: " . json_encode($feesToSend));
    
    // For V3, extract fee IDs from advanced_fees (cleaning, taxes, etc.)
    if (HFY_USE_API_V3) {
        error_log("DEBUG PAYMENT V3: Extracting fees from v3 advanced_fees");
        $presInitial = $api->getListingPrice(
            $data['listing_id'],
            $data['start_date'],
            $data['end_date'],
            $data['guests'],
            false,
            $data['discount_code'] ?? '',
            $adults,
            $children,
            $infants,
            $pets,
            []  // Empty fees array for initial call
        );
        
        if ($presInitial && $presInitial->success && !empty($presInitial->price->v3->advanced_fees)) {
            error_log("DEBUG PAYMENT V3: Found " . count($presInitial->price->v3->advanced_fees) . " advanced_fees");
            foreach ($presInitial->price->v3->advanced_fees as $fee) {
                // Add fee IDs for fees and taxes (not accommodation)
                if (($fee->type ?? '') !== 'accommodation' && !empty($fee->fee_id)) {
                    $feesToSend[] = $fee->fee_id;
                    error_log("DEBUG PAYMENT V3: Added fee_id=" . $fee->fee_id . ", name=" . ($fee->name ?? 'unnamed') . ", type=" . ($fee->type ?? 'no type'));
                }
            }
            $feesToSend = array_unique($feesToSend);
            error_log("DEBUG PAYMENT V3: Final fees to send: " . json_encode($feesToSend));
        } else {
            error_log("DEBUG PAYMENT V3: No advanced_fees found or API failed");
        }
    }
    
    $pres = $api->getListingPrice(
        $data['listing_id'], 
        $data['start_date'], 
        $data['end_date'], 
        $data['guests'], 
        false, 
        $data['discount_code'], 
        $adults, 
        $children, 
        $infants, 
        $pets, 
        $feesToSend
    );
    
    // Apply fixFees to preserve tax and other fees
    if (isset($pres->price)) {
        $pres->price = $api->fixFees($pres->price, $data['start_date'], $data['end_date'], $data['guests'], $adults, $children, $infants, $pets, $feesToSend);
    }
    
    error_log("DEBUG PAYMENT: API returned total: " . ($pres->price->total ?? $pres->price->totalAfterTax ?? 'not set'));
    error_log("DEBUG PAYMENT: API returned totalPartial: " . ($pres->price->totalPartial ?? 'not set'));
    
    // DON'T overwrite $data['total'] here - it already includes optional extras from frontend
    // The API response doesn't include optional extras (they're sent separately in fees array)
    // The frontend (hfy-ajax-payment-preview.php) already calculated the correct total including optional extras
    
    // Note: If partial payments are needed in the future, they should be calculated in the 
    // payment preview (hfy-ajax-payment-preview.php) which has access to both base price and optional extras
    
    error_log("DEBUG PAYMENT: Preserving frontend total: " . ($data['total'] ?? 'not set'));
    
    if (!empty($data['payer_id'])) {
        // paypal

        ob_start();

        $api = new HfyApi();
        $result = $api->paypalPayment($data);

        if ($result && $result->success == true) {
            $paymentSuccess = true;
            $reservationId = $result->reservation->id;
            $message = "Reservation created successfully!\nYour confirmation code is \"{$result->reservation->confirmation_code}\"";
        } else {
            $paymentSuccess = false;
            $reservationId = null;
            $message = '';
        }

        // save transaction
        $currency = $result->reservation->currency;
        $paymentSettings = $api->getPaymentSettings($data['listing_id'], $data['start_date'], $data['end_date']);
        $paymentIntegrationId = $paymentSettings->services->id ?? '';

        // todo processor ?
        $api->postTransaction($reservationId, $data['total'], $currency, $processor, $reservationId, $paymentIntegrationId, $reservationId, 1);


        $response = (object) [
            'success' => $paymentSuccess,
            'message' => $message,
            'paymentSuccess' => $paymentSuccess,
            'paymentData' => $data,
            'reserveInfo' => $data,
            'reservation' => $result->reservation ?? null,
        ];

        include hfy_tpl('payment/response');

        $out = ob_get_contents();
        ob_end_clean();

    } else {
        // stripe

        $api = new HfyApi();
        
        // Use detailed_fees from frontend if provided (already includes all fees + optional extras)
        // Otherwise, extract from API response as fallback
        if (!empty($data['detailed_fees'])) {
            // Frontend sent detailed_fees - it's already parsed as array by JavaScript
            error_log("DEBUG PAYMENT: Using detailed_fees from frontend: " . json_encode($data['detailed_fees']));
        } elseif (isset($pres->price)) {
            // Fallback: Extract detailed fee information from processed price data
            $detailedFees = [];
            
            // Add cleaning fees
            if (isset($pres->price->fees)) {
                foreach ($pres->price->fees as $fee) {
                    if (strpos(strtolower($fee->fee_name ?? ''), 'cleaning') !== false) {
                        $detailedFees[] = [
                            'name' => $fee->fee_name ?? 'Cleaning Fee',
                            'amount' => floatval($fee->total),
                            'type' => 'cleaning'
                        ];
                    }
                }
            }
            
            // Add taxes
            if (isset($pres->price->taxes)) {
                foreach ($pres->price->taxes as $tax) {
                    $detailedFees[] = [
                        'name' => $tax->fee_name ?? 'Tax',
                        'amount' => floatval($tax->total),
                        'type' => 'tax'
                    ];
                }
            }
            
            // Add other fees
            if (isset($pres->price->fees)) {
                foreach ($pres->price->fees as $fee) {
                    if (strpos(strtolower($fee->fee_name ?? ''), 'cleaning') === false && 
                        strpos(strtolower($fee->fee_name ?? ''), 'tax') === false) {
                        $detailedFees[] = [
                            'name' => $fee->fee_name ?? 'Fee',
                            'amount' => floatval($fee->total),
                            'type' => 'other'
                        ];
                    }
                }
            }
            
            $data['detailed_fees'] = $detailedFees;
            error_log("DEBUG PAYMENT: Built detailed_fees from API: " . json_encode($data['detailed_fees']));
        }
        
        // Ensure fees parameter is properly formatted as array for payment API
        // The payment API expects an array of fee ID strings: ["182", "600000037"]
        $data['fees'] = $feesToSend;
        
        error_log("DEBUG PAYMENT: Sending to payment API - total: " . ($data['total'] ?? 'not set') . ", fees: " . json_encode($data['fees']));
        
        $data = $api->postPayment3ds($data);

        $out = [
            'success' => true,
            'state' => 'done',
            'data' => $data,
        ];


        // put the process in background

        // $pguid = time().'_'.sha1(wp_get_session_token() . json_encode($data));
        // $pkey = 'hfy_stripe_payment_'.$pguid;
        // set_transient($pkey, 1, 600);

        // as_enqueue_async_action('hfy_one_time_action_asap', [[
        //     'func' => 'hfy_stripe_send_payment',
        //     'data' => [
        //         'pguid' => $pguid,
        //         'data' => $data,
        //     ],
        // ]]);

        // // spawn_cron();

        // $out = [
        //     'success' => true,
        //     'id' => $pguid,
        //     'state' => 'processing',
        // ];
    }
}
