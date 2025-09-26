<?php
if (!defined('WPINC')) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

$data = !empty($_POST['data']) ? $_POST['data'] : [];

if (
    empty($data['listing_id']) || empty($data['start_date']) || empty($data['end_date']) || empty($data['total']) ||
    empty($data['name']) || !isset($data['email']) || !isset($data['phone'])
) {
    $out = ['success' => false];
} else {

	try {
		$name = $data['name'];
		$email = $data['email'];
		$phone = $data['phone'];
		$note = $data['note'] ?? '';

		$listing_id = $data['listing_id'];
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		$guests = ($data['guests'] < 1 || !is_numeric($data['guests'])) ? 1 : $data['guests'];
		$adults = ($data['adults'] < 1 || !is_numeric($data['adults'])) ? 1 : $data['adults'];
		$children = ($data['children'] < 1 || !is_numeric($data['children'])) ? 0 : $data['children'];
		$infants = ($data['infants'] < 1 || !is_numeric($data['infants'])) ? 0 : $data['infants'];
		$pets = ($data['pets'] < 1 || !is_numeric($data['pets'])) ? 0 : $data['pets'];
		$total = $data['total'];
		$discount_code = $data['discount_code'];
		$dcid = $data['dcid'];
		$fees = $data['fees'];

		$api = new HfyApi();
		$status = HfyApi::RESERVATION_STATUS_NEW;

		// $settings = $api->getSettings();
		// $websiteId = $settings->data->_id ?? null;

		$result = $api->postBookListing($listing_id, $start_date, $end_date, $guests, $total, $name, $email, $phone, $note, $status, $discount_code, $dcid, $adults, $children, $infants, $pets, $fees);

		// if (!empty($result->error)) {
		// 	$this->sendSupportError("{$result->error}. Failed at reservation init.", $data);
		// }

		$out = [
			'success' => !empty($result->success ?? null),
			'reservation' => !empty($result->reservation->id) ? $result->reservation->id : null,
		];
		if (isset($result->error) && !empty(isset($result->error))) {
			$out['error'] = $result->error;
		}

	} catch (\Exception $e) {
		$out['error'] = "{$e->getMessage()}. Failed at reservation init.";
	}

}
