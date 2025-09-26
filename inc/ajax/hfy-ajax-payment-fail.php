<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$data = !empty($_POST['data']) ? $_POST['data'] : [];

if (
    empty($data['reservationId'])
) {
    $out = ['success' => false];
} else {
    $api = new HfyApi();
    $out = $api->reservationFailedPayment($data['reservationId'] ?? '');
}
