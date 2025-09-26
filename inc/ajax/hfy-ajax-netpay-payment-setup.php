<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$data = !empty($_POST['data']) ? $_POST['data'] : [];
if (
    empty($data['listing_id'])
    || empty($data['start_date'])
    || empty($data['end_date'])
    || empty($data['total'])
    || empty($data['name'])
    || empty($data['email'])
    || empty($data['phone'])
) {
    $out = ['success' => false];
} else {

    try {

        $api = new HfyApi();
        $status = HfyApi::RESERVATION_STATUS_NEW;

        $result = $api->postBookListing(
            $data['listing_id'],
            $data['start_date'],
            $data['end_date'],
            ($data['guests'] < 1 || !is_numeric($data['guests'])) ? 1 : $data['guests'],
            $data['total'],
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['note'] ?? '',
            $status,
            $data['discount_code'],
            $data['dcid'],
            ($data['adults'] < 1 || !is_numeric($data['adults'])) ? 1 : $data['adults'],
            ($data['children'] < 1 || !is_numeric($data['children'])) ? 1 : $data['children'],
            ($data['infants'] < 1 || !is_numeric($data['infants'])) ? 1 : $data['infants'],
            ($data['pets'] < 1 || !is_numeric($data['pets'])) ? 1 : $data['pets']
        );

        if ($result && $result->success) {
            $reservationId = $result->reservation->id;
            $requestData = [
                'integration_id' => $data['integration_id'],
                'reservation_id' => $reservationId,
                'total' => $data['total'],
            ];

            $result = $api->netpayPaymentSetup($requestData);

            $out = [
                'success' => !empty($result->success ?? null),
                'reservation_id' => $reservationId ?? '',
                'transaction_id' => $result->transaction_id ?? '',
                'tokenized_amount_data' => $result->tokenizedAmountData ?? '',
            ];
        } else {
            $out = [
                'success' => !empty($result->success ?? null),
                'message' => $result->message ?? ''
            ];
        }

        if (isset($result->error) && !empty(isset($result->error))) {
            $out['error'] = $result->error;
        }

    } catch (\Exception $e) {
        $out['error'] = "{$e->getMessage()}. Failed at reservation init.";
    }

}
