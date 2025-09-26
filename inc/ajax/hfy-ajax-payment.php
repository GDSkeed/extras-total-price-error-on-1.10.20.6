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
    $feesToSend = !empty($data['fees']) ? explode(',', $data['fees']) : [];
    
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
    
    // Calculate the full total first
    if (HFY_USE_API_V3 && isset($pres->price->v3)) {
        $fullTotal = $pres->price->v3->total;
        // Use partial payment if available
        $data['total'] = !empty($pres->price->v3->partial) && $pres->price->v3->partial > 0 
            ? $pres->price->v3->partial 
            : $fullTotal;
    } else {
        // For v2, use pre-calculated total from API
        $fullTotal = $pres->price->total ?? $pres->price->totalAfterTax ?? $pres->price->totalPrice ?? 0;
        // Use partial payment if available
        $data['total'] = !empty($pres->price->totalPartial) && $pres->price->totalPartial > 0 
            ? $pres->price->totalPartial 
            : $fullTotal;
    }
    
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
        
        // Extract detailed fee information from processed price data
        if (isset($pres->price)) {
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
            
            $data['fees'] = $detailedFees;
        }
        
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
