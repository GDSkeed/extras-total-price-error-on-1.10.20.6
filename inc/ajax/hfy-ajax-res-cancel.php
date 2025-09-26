<?php

if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$reservation_id = (int) ($_POST['data']['rid'] ?? 0);
$message_guest = trim($_POST['data']['msg'] ?? '');

if (empty($reservation_id) || empty($message_guest)) {
    echo 1;
    die;
}

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';

$api = new HfyApi();
$result = $api->postCancelReservation($reservation_id, $message_guest);

if (!($result->success ?? false)) {
    echo $result->error ?? $result ?? 'Error';
    die;
}

echo 'ok';

die;
