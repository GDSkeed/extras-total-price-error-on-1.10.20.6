<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$data = !empty($_POST['data']) ? $_POST['data'] : [];

if (
    empty($data['reservation_id'])
    || empty($data['transaction_id'])
) {
    $out = ['success' => false];
} else {

    try {
        $requestData = [];
        $requestData['reservation_id'] = $data['reservation_id'];
        $requestData['transaction_id'] = $data['transaction_id'];

        $api = new HfyApi();
        $status = HfyApi::RESERVATION_STATUS_NEW;

        $result = $api->netpayPaymentFail($requestData);
        $out = [
            'success' => !empty($result->success ?? null),
            'message' => $result->message ?? ''
        ];

        if (isset($result->error) && !empty(isset($result->error))) {
            $out['error'] = $result->error;
        }

    } catch (\Exception $e) {
        $out['error'] = "{$e->getMessage()}. Failed at reservation init.";
    }

}
