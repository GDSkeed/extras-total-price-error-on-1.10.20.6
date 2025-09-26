<?php

if (!defined('WPINC')) die;

// ini_set('display_errors', 0);
// ini_set('log_errors', 1);

require_once HOSTIFYBOOKING_DIR . 'inc/helpers.php';
require_once HOSTIFYBOOKING_DIR . 'inc/api.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
// require_once HOSTIFYBOOKING_DIR . 'inc/vendors/action-scheduler/action-scheduler.php';

if (!function_exists('hfy_clear_cache'))
{
	function hfy_clear_cache($name = '')
	{
		global $wpdb;
		$wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name LIKE '%_hfybook_%' ");

		wp_cache_flush();
		unset($_SESSION['x-api-key']);
        unset($_SESSION['integration-id']);
		// hfy_update_listings_permalinks(); // update local listings ids/names table from api

		add_option('hfy_cache_cleared', '1');
	}
}

if (!function_exists('hfy_stripe_send_payment'))
{
	function hfy_stripe_send_payment($prm)
	{
		$api = new HfyApi();
        $result = $api->postPayment3ds($prm['data']);
// error_log(print_r($result,1));
		$pkey = 'hfy_stripe_payment_'.$prm['pguid'];
		$state = get_transient($pkey);
		if (false !== $state) {
			if (!empty($state) && $state == 1 && $result != false) {
				set_transient($pkey, $result, 600);
			} else {
				delete_transient($pkey);
			}
		}

	}
}

if (!function_exists('hfy_fee_charge_type'))
{
	function hfy_fee_charge_type($id)
	{
		$a = [
			1 => 'stay',
			2 => 'night',
			// 3 => PERCENT
			4 => 'guest',
			// 5 => 'guest night',
			6 => 'month',
			// 7 => ADULT_PER_NIGHT
		];
		return $a[(int) $id] ?? '';
	}
}

if (!function_exists('hfy_fee_charge_type_text'))
{
	function hfy_fee_charge_type_text($id, $nights = 1, $guests = 1)
	{
		$nights = (int) $nights;
		$guests = (int) $guests;
		$a = [
			1 => 'stay',
			2 => ''.$nights.' night(s)',
			4 => ''.$guests.' guest(s)',
		];
		return $a[(int) $id] ?? '';
	}
}
