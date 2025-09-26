<?php
if (!defined('WPINC')) die;

$data = !empty($_POST['data']) ? $_POST['data'] : [];

if (
    empty($data['id'])
) {
    $out = ['success' => false];
} else {

    // $api = new HfyApi();

	// stripe

	// get the process state

	$out = ['success' => true, 'state' => 'processing'];

	$pkey = 'hfy_stripe_payment_'.$data['id'];
	$state = get_transient($pkey);

	if (false === $state) {
		$out = ['success' => false, 'error' => 'Missed process'];
	} else {
		if (!empty($state) && is_object($state)) {
			$out['state'] = 'done';
			$out['data'] = $state;

			delete_transient($pkey);
		}
	}

}
