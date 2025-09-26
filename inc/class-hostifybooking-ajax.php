<?php

defined( 'ABSPATH' ) or die();

class Hostifybooking_AJAX {

	protected static $instance = null;
	// protected $csrf = '';

	public function __construct()
	{
		// $this->csrf = wp_create_nonce('hfy_csrf');

		add_action('wp_enqueue_scripts', [$this, 'ajax_register'], 1000);

		// // Backend AJAX calls
		// if (current_user_can('manage_options')) {
		// add_action('wp_ajax_admin_backend_call', array($this, 'ajax_backend_call'));
		// }

		// Frontend AJAX calls

		add_action('wp_ajax_listing_price', [$this, 'listing_price']);
		add_action('wp_ajax_nopriv_listing_price', [$this, 'listing_price']);

		add_action('wp_ajax_filter_listings', [$this, 'filter_listings']);
		add_action('wp_ajax_nopriv_filter_listings', [$this, 'filter_listings']);

		add_action('wp_ajax_listing_short', [$this, 'listing_short']);
		add_action('wp_ajax_nopriv_listing_short', [$this, 'listing_short']);

		add_action('wp_ajax_inquiry', [$this, 'inquiry']);
		add_action('wp_ajax_nopriv_inquiry', [$this, 'inquiry']);

        add_action('wp_ajax_payment_preview', [$this, 'payment_preview']);
        add_action('wp_ajax_nopriv_payment_preview', [$this, 'payment_preview']);

        add_action('wp_ajax_payment', [$this, 'payment']);
        add_action('wp_ajax_nopriv_payment', [$this, 'payment']);

        add_action('wp_ajax_payment_processing', [$this, 'payment_processing']);
        add_action('wp_ajax_nopriv_payment_processing', [$this, 'payment_processing']);

        add_action('wp_ajax_payment_fail', [$this, 'payment_fail']);
        add_action('wp_ajax_nopriv_payment_fail', [$this, 'payment_fail']);

        add_action('wp_ajax_init_reservation', [$this, 'init_reservation']);
        add_action('wp_ajax_nopriv_init_reservation', [$this, 'init_reservation']);

		add_action('wp_ajax_wish', [$this, 'wish']);
        add_action('wp_ajax_nopriv_wish', [$this, 'wish']);

        add_action('wp_ajax_netpay_payment_setup', [$this, 'netpay_payment_setup']);
        add_action('wp_ajax_nopriv_netpay_payment_setup', [$this, 'netpay_payment_setup']);
        add_action('wp_ajax_netpay_payment_success', [$this, 'netpay_payment_success']);
        add_action('wp_ajax_nopriv_netpay_payment_success', [$this, 'netpay_payment_success']);
        add_action('wp_ajax_netpay_payment_fail', [$this, 'netpay_payment_fail']);
        add_action('wp_ajax_nopriv_netpay_payment_fail', [$this, 'netpay_payment_fail']);

		// todo
        // add_action('wp_ajax_res_cancel', [$this, 'res_cancel']);
        // add_action('wp_ajax_nopriv_res_cancel', [$this, 'res_cancel']);

        // add_action('wp_ajax_alt_quote', [$this, 'alt_quote']);
        // add_action('wp_ajax_nopriv_alt_quote', [$this, 'alt_quote']);

        // add_action('wp_ajax_alt_request', [$this, 'alt_request']);
        // add_action('wp_ajax_nopriv_alt_request', [$this, 'alt_request']);

        // add_action('wp_ajax_alt_request_cancel', [$this, 'alt_request_cancel']);
        // add_action('wp_ajax_nopriv_alt_request_cancel', [$this, 'alt_request_cancel']);

        // add_action('wp_ajax_alt_request_get', [$this, 'alt_request_get']);
        // add_action('wp_ajax_nopriv_alt_request_get', [$this, 'alt_request_get']);


	}

	/**/
	function ajax_register()
	{
		wp_register_script('hfyajaxhandle', HOSTIFYBOOKING_URL . (HFY_USE_NEW_CALENDAR ? 'public/res/main4.js' : 'public/res/main3.js'), ['jquery'], HOSTIFYBOOKING_VERSION, true);
		wp_enqueue_script('hfyajaxhandle');
		wp_localize_script('hfyajaxhandle', 'hfyx', [
			'url' => admin_url('admin-ajax.php'),
			'use_v3_calendar' => defined('HFY_USE_V3_CALENDAR') ? HFY_USE_V3_CALENDAR : false,
			// 'csrf' => $this->csrf,
		]);
	}

	/**/
	function check_nonce_()
	{
		if (!check_ajax_referer('hfy_csrf', 'csrf', false)) {
			echo 'wrong csrf';
			wp_die();
		}
	}

	function check_nonce()
	{
		$nonce = isset( $_SERVER['HTTP_X_HFY_CSRF_TOKEN'] ) ? $_SERVER['HTTP_X_HFY_CSRF_TOKEN'] : '';
		if ( !wp_verify_nonce( $nonce, 'hfy_general_nonce' ) ) {
			echo 'wrong csrf';
			wp_die();
		}
	}

	/**/
	public static function get_instance()
	{
		if ( null == self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Handle AJAX: Backend
	 */
	public function ajax_backend_call_example()
	{
		// Security check
		// check_ajax_referer( 'referer_id', 'nonce' ); // die if wrong
		$response = 'OK';
		wp_send_json_success( $response );
		die();
	}

	/**/
	function listing_price()
	{
		// $this->check_nonce(); // don't check nonce on simple operations like this
		ob_start();
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-listing-price.php';
		ob_end_clean();
		wp_send_json($out);
		wp_die();
	}

	/**/
	function filter_listings()
	{
		// $this->check_nonce();
		ob_start();
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-filter-listings.php';
		ob_end_clean();
		wp_send_json($out);
		wp_die();
	}

	/**/
	function listing_short()
	{
		// $this->check_nonce();
		ob_start();
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-listing-short.php';
		ob_end_clean();
		wp_send_json($out);
		wp_die();
	}

	/**/
	function wish()
	{
		// $this->check_nonce();
		ob_start();
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-wish.php';
		ob_end_clean();
		wp_send_json($out);
		wp_die();
	}



	/**/
	function res_cancel()
	{
		// $this->check_nonce();
		ob_start();
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-res-cancel.php';
		ob_end_clean();
		wp_send_json($out);
		wp_die();
	}

	/**/
	function alt_quote()
	{
		// todo
	}

	/**/
	function alt_request()
	{
		// todo
	}

	/**/
	function alt_request_cancel()
	{
		// todo
	}

	/**/
	function alt_request_get()
	{
		// todo
	}

	/**/
	function inquiry()
	{
		include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-inquiry.php';
		if ($out['success'] && $direct_inquiry_email && $listingId) {
			wp_mail(
				$direct_inquiry_email,
				"New Direct Inquiry for ID " . $listingId,
				$msg,
				[
					'Content-Type: text/html; charset=UTF-8',
				]
			);
		}
		wp_send_json($out);
		wp_die();
	}

    /**/
    function payment_preview()
    {
        // $this->check_nonce();
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-payment-preview.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    /**/
    function payment()
    {
        // $this->check_nonce();
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-payment.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    /**/
    function payment_processing()
    {
        // $this->check_nonce();
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-payment-processing.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    /**/
    function payment_fail()
    {
        // $this->check_nonce();
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-payment-fail.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    /**/
    function init_reservation()
    {
        // $this->check_nonce();
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-init-reservation.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    function netpay_payment_setup()
    {
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-netpay-payment-setup.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    function netpay_payment_success()
    {
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-netpay-payment-success.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }

    function netpay_payment_fail()
    {
        ob_start();
        include HOSTIFYBOOKING_DIR . 'inc/ajax/hfy-ajax-netpay-payment-fail.php';
        ob_end_clean();
        wp_send_json($out);
        wp_die();
    }


}
