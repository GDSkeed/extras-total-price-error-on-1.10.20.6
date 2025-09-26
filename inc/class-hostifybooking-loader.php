<?php
/**
 * Register all actions and filters for the plugin
 */
class Hostifybooking_Loader
{

	protected $actions;
	protected $filters;
	protected $shortcodes;

	public function __construct()
	{
		$this->actions    = [];
		$this->filters    = [];
		$this->shortcodes = [
			[ 'hfy_top_listings', 'sc_hfy_top_listings' ],
			[ 'hfy_recent_listings', 'sc_hfy_recent_listings' ],

			[ 'hfy_listings', 'sc_hfy_listings' ],
			[ 'hfy_listings_info', 'sc_hfy_listings_info' ],
			[ 'hfy_listings_map', 'sc_hfy_listings_map' ],
			[ 'hfy_listings_map_toggle', 'sc_hfy_listings_map_toggle' ],
			[ 'hfy_listings_selected', 'sc_hfy_listings_selected' ],
			[ 'hfy_listings_sort', 'sc_hfy_listings_sort' ],

			[ 'hfy_listing', 'sc_hfy_listing' ],

			[ 'hfy_listing_info', 'sc_hfy_listing_info' ],

			[ 'hfy_listing_info_summary', 'sc_hfy_listing_info_summary' ],
			[ 'hfy_listing_info_space', 'sc_hfy_listing_info_space' ],
			[ 'hfy_listing_info_guest_access', 'sc_hfy_listing_info_guest_access' ],
			[ 'hfy_listing_info_interaction', 'sc_hfy_listing_info_interaction' ],
			[ 'hfy_listing_info_notes', 'sc_hfy_listing_info_notes' ],
			[ 'hfy_listing_info_transit', 'sc_hfy_listing_info_transit' ],
			[ 'hfy_listing_info_neighbourhood', 'sc_hfy_listing_info_neighbourhood' ],
			[ 'hfy_listing_info_house_rules', 'sc_hfy_listing_info_house_rules' ],
			[ 'hfy_listing_info_address', 'sc_hfy_listing_info_address' ],
			[ 'hfy_listing_info_prices', 'sc_hfy_listing_info_prices' ],
			[ 'hfy_listing_info_permit', 'sc_hfy_listing_info_permit' ],

			[ 'hfy_listing_field', 'sc_hfy_listing_field' ],
			[ 'hfy_listing_details_field', 'sc_hfy_listing_details_field' ],

			[ 'hfy_listing_title', 'sc_hfy_listing_title' ],
			[ 'hfy_listing_room_type', 'sc_hfy_listing_room_type' ],
			[ 'hfy_listing_facilities', 'sc_hfy_listing_facilities' ],
			[ 'hfy_listing_cancellation_policy', 'sc_hfy_listing_cancellation_policy' ],

			[ 'hfy_listing_gallery', 'sc_hfy_listing_gallery' ],
			[ 'hfy_listing_image', 'sc_hfy_listing_image' ],
			[ 'hfy_listing_slider', 'sc_hfy_listing_slider' ],

			[ 'hfy_listing_amenities', 'sc_hfy_listing_amenities' ],
			[ 'hfy_listing_booking_form', 'sc_hfy_listing_booking_form' ],
			[ 'hfy_listing_location', 'sc_hfy_listing_location' ],
			[ 'hfy_listing_map', 'sc_hfy_listing_location' ], // synonym
			[ 'hfy_listing_availability', 'sc_hfy_listing_availability' ],

			[ 'hfy_payment', 'sc_hfy_payment' ],
			[ 'hfy_payment_charge', 'sc_hfy_payment_charge' ],

			[ 'hfy_booking_search', 'sc_hfy_booking_search' ],
			[ 'hfy_booking_search_popup', 'sc_hfy_booking_search_popup' ],

			[ 'hfy_listing_reviews_summary', 'sc_hfy_listing_reviews_summary' ],
			[ 'hfy_listing_reviews_count', 'sc_hfy_listing_reviews_count' ],
			[ 'hfy_listing_reviews_comments', 'sc_hfy_listing_reviews_comments' ],
			[ 'hfy_listing_reviews_stars', 'sc_hfy_listing_reviews_stars' ],

			[ 'hfy_listing_virtual_tour', 'sc_hfy_listing_virtual_tour' ],
			[ 'hfy_listing_rules', 'sc_hfy_listing_rules' ],

			[ 'hfy_user_bookings_list', 'sc_hfy_user_bookings_list' ],
			[ 'hfy_user_booking_manage', 'sc_hfy_user_booking_manage' ],
			[ 'hfy_user_wishlist', 'sc_hfy_user_wishlist' ],
			[ 'hfy_user_wishlist_link', 'sc_hfy_user_wishlist_link' ],

			[ 'hfy_payment_extras_set', 'sc_hfy_payment_extras_set' ],
			[ 'hfy_payment_extras_optional', 'sc_hfy_payment_extras_optional' ],

			[ 'hfy_recommended_listings', 'sc_hfy_recommended_listings' ],
		];
	}

	function action_parse_request($query)
	{
		// save GET['source'] to session
		if (!empty($query->query_vars['source'])) {
			$source = trim($query->query_vars['source']);
			if (!empty($source)) {

				// todo
				// 1 - check the cookie for empty to not overwrite every time and save only the first one
				// 2 - check for referrer

				$s = preg_replace('/[^A-Za-z0-9\-\(\)]/', '', $source);
				$s = preg_replace('/-+/', '-', $s);
				$s = preg_replace('/_+/', '_', $s);
				// $_SESSION['hfy_source'] = $s;
				setcookie('hfy_source', $s, strtotime('+1 day'), '/');
			}
		}

		// update recent listings ids
		$this->hfy_update_recent_listings($query);

		return $query;
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             wp action name
	 * @param    object $component        ref to the instance of the object with defined action
	 * @param    string $callback         fn name on the $component.
	 * @param    int    $priority         (opt) fn priority whan it should be fired. Default is 10.
	 * @param    int    $accepted_args    (opt) number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         The priority at which the function should be fired.
	 * @param    int    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array     The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];
		return $hooks;
	}

	/**
	 * top listings
	 */
	public function sc_hfy_top_listings( $attr ) {
		return $this->_sc_render('hfy-top-listings', $attr, [
			'max' => '4',
		]);
	}

	/**
	 * recent listings
	 */
	public function sc_hfy_recent_listings( $attr ) {
		return $this->_sc_render('hfy-recent-listings', $attr, []);
	}

	/**
	 * listings
	 */
	public function sc_hfy_listings( $attr )
	{
		return $this->_sc_render('hfy-listings', $attr, [
			'ids' => '',
			'cities' => '',
			'city' => '',
			'monthly' => '', // 0 - default, 1 - long, 2 - short
			'tags' => '',
			'sort' => '',
			'neighbourhood' => null, // string
			'with_amenities' => false,
			'max' => HFY_LISTINGS_PER_PAGE ?? 6,
		]);
	}

	/**
	 * listings info
	 */
	public function sc_hfy_listings_info($attr)
	{
		return $this->_sc_render('hfy-listings-info', $attr, [
			'nowrap' => 1
		]);
	}

	/**
	 * listings map
	 */
	public function sc_hfy_listings_map( $attr ) {
		return $this->_sc_render('hfy-listings-map', $attr, [
			'ids' => '',
			'cities' => '',
			'city' => '',
			'monthly' => '',
			'closebutton' => false,
			'tags' => '',
			'neighbourhood' => null, // string
		]);
	}

	/**
	 * listings map show/hide toggle
	 */
	public function sc_hfy_listings_map_toggle( $attr ) {
		return $this->_sc_render('hfy-listings-map-toggle', $attr, [
			'mobile' => '',
		  	'tablet' => '',
		]);
	}

	/**
	 * selected listings by listings ids
	 */
	public function sc_hfy_listings_selected( $attr ) {
		return $this->_sc_render('hfy-listings-selected', $attr, [
			'ids' => '',
			'cities' => '',
			'currentlistingcity' => false,
			'paramcity' => false,
			'max' => '4',
			'msgnodata' => __('No available properties for your selection.', 'hostifybooking'),
			'template' => null,
		]);
	}

	/**/
	public function sc_hfy_listings_sort($attr) {
		return $this->_sc_render('hfy-listings-sort', $attr, [
			'by' => '',
		]);
	}

	/**
	 * single listing
	 */
	public function sc_hfy_listing( $attr ) {
		return $this->_sc_render('hfy-listing', $attr, [
			'id' => 0,
			'max' => 0,
			'more' => false,
			'moretext' => 'show more &rarr;',
			'moreaction' => true,
		]);
	}

	/**
	 * single listing info
	 */
	public function sc_hfy_listing_info( $attr ) {
		return $this->_sc_render('hfy-listing-info', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_summary( $attr ) {
		return $this->_sc_render('hfy-listing-info-summary', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_space( $attr ) {
		return $this->_sc_render('hfy-listing-info-space', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_guest_access( $attr ) {
		return $this->_sc_render('hfy-listing-info-guest-access', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_interaction( $attr ) {
		return $this->_sc_render('hfy-listing-info-interaction', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_notes( $attr ) {
		return $this->_sc_render('hfy-listing-info-notes', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_transit( $attr ) {
		return $this->_sc_render('hfy-listing-info-transit', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_neighbourhood( $attr ) {
		return $this->_sc_render('hfy-listing-info-neighbourhood', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_house_rules( $attr ) {
		return $this->_sc_render('hfy-listing-info-house-rules', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_address( $attr ) {
		return $this->_sc_render('hfy-listing-info-address', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_prices( $attr ) {
		return $this->_sc_render('hfy-listing-info-prices', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_info_permit( $attr ) {
		return $this->_sc_render('hfy-listing-info-permit', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * get field value of listing object
	 */
	public function sc_hfy_listing_field( $attr )
	{
		return $this->_sc_render_field($attr, [
			'id' => 0,
			'name' => '',
			'trim' => 0,
		]);
	}

	public function sc_hfy_listing_details_field( $attr )
	{
		return $this->_sc_render_details_field($attr, [
			'id' => 0,
			'name' => '',
		]);
	}

	public function _sc_render_field($attrs = [], $prms = [], $addclass = '' )
	{
		extract(shortcode_atts($prms, $attrs));
		ob_start();
		// echo '<div class="hfy-wrap ' . $this->get_current_hfy_theme_name() . ' ' . $addclass . '">';
		try {
			include HOSTIFYBOOKING_DIR . 'inc/shortcodes/hfy-listing-field.php';
        } catch (Exception $e) {
			echo $e->getMessage();
        }
		// echo '</div>';
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function _sc_render_details_field($attrs = [], $prms = [], $addclass = '' )
	{
		extract(shortcode_atts($prms, $attrs));
		ob_start();
		// echo '<div class="hfy-wrap ' . $this->get_current_hfy_theme_name() . ' ' . $addclass . '">';
		try {
			include HOSTIFYBOOKING_DIR . 'inc/shortcodes/hfy-listing-details-field.php';
        } catch (Exception $e) {
			echo $e->getMessage();
        }
		// echo '</div>';
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	/**
	 * single listing title
	 */
	public function sc_hfy_listing_title( $attr ) {
		return $this->_sc_render('hfy-listing-title', $attr, [
			'id' => 0,
			'tag' => 'div',
		]);
	}

	/**
	 * single listing title
	 */
	public function sc_hfy_listing_room_type( $attr ) {
		return $this->_sc_render('hfy-listing-room-type', $attr, [
			'id' => 0,
			'tag' => 'div',
			// 'showimg' => true,
		]);
	}

	/**
	 * single listing hotel facilities
	 */
	public function sc_hfy_listing_facilities( $attr ) {
		return $this->_sc_render('hfy-listing-facilities', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_cancellation_policy( $attr ) {
		return $this->_sc_render('hfy-listing-cancellation-policy', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * single listing gallery
	 */
	public function sc_hfy_listing_gallery( $attr ) {
		return $this->_sc_render('hfy-listing-gallery', $attr, [
			'id' => 0,
			'view' => false,
		], 'nopad');
	}

	/**
	 * single listing main image
	 */
	public function sc_hfy_listing_image( $attr ) {
		return $this->_sc_render('hfy-listing-image', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_slider( $attr ) {
		return $this->_sc_render('hfy-listing-slider', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * single listing amenities
	 */
	public function sc_hfy_listing_amenities( $attr ) {
		return $this->_sc_render('hfy-listing-amenities', $attr, [
			'id' => 0,
			'max' => 0,
			'more' => false,
			'moretext' => 'show more &rarr;',
			'moreaction' => true,
		]);
	}

	/**
	 * single listing booking form
	 */
	public function sc_hfy_listing_booking_form( $attr ) {
		return $this->_sc_render('hfy-listing-booking-form', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * single listing location map
	 */
	public function sc_hfy_listing_location( $attr ) {
		return $this->_sc_render('hfy-listing-location', $attr, [
			'id' => 0,
		]);
	}

	/**/
	public function sc_hfy_listing_availability($attr) {
		return $this->_sc_render('hfy-listing-availability', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * single payment
	 */
	public function sc_hfy_payment( $attr ) {
		return $this->_sc_render('hfy-payment', $attr, [
			'id' => 0,
		], 'payment-wrapper');
	}

	/**
	 * payment charge
	 */
	public function sc_hfy_payment_charge( $attr ) {
		return $this->_sc_render('hfy-payment-charge', $attr, [
			'id' => 0,
		]);
	}

	/**
	 * booking search
	 */
	public function sc_hfy_booking_search( $attr ) {
		return $this->_sc_render('hfy-booking-search', $attr, [
			'advanced' => false,
			// added for E&V
			'neighbourhood' => null, // string
			'typecode' => null, // int
			'amenitycode' => null, // int
			'monthly' => '', // 1|0
			'tagsmenu' => '',
			'locations_filter' => '', // '' | Italy[:Lombardy[:2959[:Duomo]]]
			'samepage' => '',
		]);
	}

	/**
	 * booking search, use template for popup
	 */
	public function sc_hfy_booking_search_popup( $attr ) {
		return $this->_sc_render('hfy-booking-search-popup', $attr);
	}

	/**
	 * listing reviews
	 */
	public function sc_hfy_listing_reviews_summary( $attr ) {
		return $this->_sc_render('hfy-listing-reviews-summary', $attr, [
			'id' => 0,
		]);
	}
	public function sc_hfy_listing_reviews_count( $attr ) {
		return $this->_sc_render('hfy-listing-reviews-count', $attr, [
			'id' => 0,
		]);
	}
	public function sc_hfy_listing_reviews_comments( $attr ) {
		return $this->_sc_render('hfy-listing-reviews-comments', $attr, [
			'id' => 0,
			'max' => 3,
			'layout' => '',
		]);
	}
	public function sc_hfy_listing_reviews_stars( $attr ) {
		return $this->_sc_render('hfy-listing-reviews-stars', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_virtual_tour( $attr ) {
		return $this->_sc_render('hfy-listing-virtual-tour', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_listing_rules( $attr ) {
		return $this->_sc_render('hfy-listing-rules', $attr, [
			'id' => 0,
		]);
	}

	public function sc_hfy_user_bookings_list($attr)
	{
		return $this->_sc_render('hfy-user-bookings-list', $attr, [
			'type' => ''
		]);
	}

	public function sc_hfy_user_booking_manage($attr)
	{
		return $this->_sc_render('hfy-user-booking-manage', $attr, [
			'rid' => 0,
		]);
	}

	public function sc_hfy_user_wishlist($attr)
	{
		return $this->_sc_render('hfy-user-wishlist', $attr, []);
	}

	public function sc_hfy_user_wishlist_link($attr)
	{
		return $this->_sc_render('hfy-user-wishlist-link', $attr, []);
	}

	public function sc_hfy_payment_extras_set($attr, $content = null)
	{
		return $this->_sc_render('hfy-payment-extras-set', $attr, [
			'id' => 0,
			'ids' => '',
			'detailed' => false,
			'selected' => false,
		], '', $content);
	}

	public function sc_hfy_payment_extras_optional($attr)
	{
		return $this->_sc_render('hfy-payment-extras-optional', $attr, [
			'id' => 0,
			'except' => '',
			'checked' => true,
		]);
	}

	public function sc_hfy_recommended_listings($attr)
	{
		return $this->_sc_render('hfy-recommended-listings', $attr, [
			'max' => 4,
			'tags' => '',
		]);
	}

	/**
	 * attrs    - custom attributes from shortcode
	 * defaults - default values
	 */
	public function _sc_render($tpl, $attrs = [], $defaults = [], $addclass = '', $content = null)
	{
		$nowrap = intval($attrs['nowrap'] ?? 0);
		extract(shortcode_atts($defaults, $attrs));
		ob_start();
		if (!$nowrap) echo '<div class="hfy-wrap ' . $this->get_current_hfy_theme_name() . ' ' . $addclass . '">';
		try {
			include HOSTIFYBOOKING_DIR . 'inc/shortcodes/' . $tpl . '.php';
        } catch (Exception $e) {
			echo $e->getMessage();
        }
		if (!$nowrap) echo '</div>';
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	/**
	 * register script
	 */
	public function reg_script($tag, $fname, $afterJquery = ['jquery'], $async = false)
	{
		$args = ['in_footer' => true];
		if ($async) $args['strategy'] = 'async';
		wp_register_script(
			$tag,
			preg_match('/^(\/\/|https?:\/\/)/i', $fname) ? $fname : HOSTIFYBOOKING_URL . 'public/' . $fname,
			$afterJquery,
			HOSTIFYBOOKING_VERSION,
			$args
		);
	}

	public function reg_script_raw($tag, $fname, $args = [ 'in_footer' => true ], $noversion = false)
	{
		wp_register_script(
			$tag,
			preg_match('/^(\/\/|https?:\/\/)/i', $fname) ? $fname : HOSTIFYBOOKING_URL . 'public/' . $fname,
			['jquery'],
			$noversion ? null : HOSTIFYBOOKING_VERSION,
			$args
		);
	}

	/**
	 * register and enqueue
	 */
	public function add_script($tag, $fname, $afterJquery = ['jquery'], $async = false)
	{
		$this->reg_script($tag, $fname, $afterJquery, $async);
		wp_enqueue_script($tag);
	}

	/**
	 * add our js
	 */
	public function hfy_scripts()
	{
		// $this->add_script( 'hfyld', 'res/lib/lodash.core.min.js');
		$this->add_script('hfyjqobj', 'res/lib/jquery.query-object.js');
		$this->add_script('hfymoment', 'res/lib/moment.2.30.1.min.js');
		$this->add_script('hfycalentim', 'res/lib/calendar/calentim.js');
		// $this->add_script('hfycalendar-init', 'res/js/calendar-init.js', ['jquery', 'hfycalentim']);
		$this->add_script('hfyjqhammer', 'res/lib/jquery.hammer.js');
		// $this->add_script('hfyjqlazy', 'res/lib/jquery-lazy/jquery.lazy.min.js');
		$this->add_script('hfylightglry', 'res/lib/lightgallery/dist/js/lightgallery-all.min.js');
		$this->add_script('hfytipso', 'res/lib/tipso/tipso.min.js');
		$this->add_script('hfytel', 'res/lib/intl-tel-input/js/intlTelInputWithUtils.min.js');
		// Datepicker

		$this->add_script('hfycalendar-new-fecha', 'res/lib/fecha/fecha.min.js');
		$this->add_script('hfycalendar-new-datepicker', 'res/lib/datepicker/dist/js/hotel-datepicker.min.js');
		$this->add_script( 'hfyhoteldatepicker', 'res/datepicker.js', ['jquery', 'hfycalendar-new-fecha', 'hfycalendar-new-datepicker'] ); // hotel datepicker initialization

		// End Datepicker
		if (HFY_RICH_SELECT_LOC) {
			$this->add_script('hfysumo', 'res/lib/sumo/sumoselect.js' );
		}

		$this->add_script('hfyjqhidemax', 'res/lib/jquery.hideMaxList.js' );
		$this->add_script('hfyjqmodal', 'res/lib/jquery.modal.js' );

		if (HFY_USE_LISTINGS_GALLERY) {
			$this->add_script('hfyblaze', 'res/lib/blaze.js' );
		}

		// $this->add_script( 'hfyajaxhandle', 'res/main.js' ); // in ajax class
		$this->add_script( 'hfyfn', 'res/fn.js' ); // common functions
		
		// Add translations for tooltips and datepicker
		wp_localize_script('hfyfn', 'hfyTranslations', [
			// Formatted versions (might not be needed by new tooltip, but keep for now)
			'minStaySingular' => sprintf(_n('Minimum stay %d night', 'Minimum stay %d nights', 1, 'hostifybooking'), 1), // Example for singular
			'minStayPlural' => sprintf(_n('Minimum stay %d night', 'Minimum stay %d nights', 2, 'hostifybooking'), 2), // Example for plural > 1
			// Raw versions for custom tooltip formatting
			'minStaySingularRaw' => _n('Minimum stay %d night', 'Minimum stay %d nights', 1, 'hostifybooking'),
			'minStayPluralRaw' => _n('Minimum stay %d night', 'Minimum stay %d nights', 2, 'hostifybooking'),
			'notAvailableIn' => __('This date is not available for check-in', 'hostifybooking'),
			'notAvailableOut' => __('This date is not available for check-out', 'hostifybooking'),
			// Calendar button translations
			'calendarCancel' => __('Cancel', 'hostifybooking'),
			'calendarApply' => __('Apply', 'hostifybooking'),
			'calendarReset' => __('Reset', 'hostifybooking'),
			// Datepicker month names (full)
			'monthNames' => [
				__('January', 'hostifybooking'),
				__('February', 'hostifybooking'),
				__('March', 'hostifybooking'),
				__('April', 'hostifybooking'),
				__('May', 'hostifybooking'),
				__('June', 'hostifybooking'),
				__('July', 'hostifybooking'),
				__('August', 'hostifybooking'),
				__('September', 'hostifybooking'),
				__('October', 'hostifybooking'),
				__('November', 'hostifybooking'),
				__('December', 'hostifybooking')
			],
			// Datepicker month names (short)
			'monthNamesShort' => [
				__('Jan', 'hostifybooking'),
				__('Feb', 'hostifybooking'),
				__('Mar', 'hostifybooking'),
				__('Apr', 'hostifybooking'),
				__('May', 'hostifybooking'),
				__('Jun', 'hostifybooking'),
				__('Jul', 'hostifybooking'),
				__('Aug', 'hostifybooking'),
				__('Sep', 'hostifybooking'),
				__('Oct', 'hostifybooking'),
				__('Nov', 'hostifybooking'),
				__('Dec', 'hostifybooking')
			],
			// Datepicker day names (full)
			'dayNames' => [
				__('Sunday', 'hostifybooking'),
				__('Monday', 'hostifybooking'),
				__('Tuesday', 'hostifybooking'),
				__('Wednesday', 'hostifybooking'),
				__('Thursday', 'hostifybooking'),
				__('Friday', 'hostifybooking'),
				__('Saturday', 'hostifybooking')
			],
			// Datepicker day names (short)
			'dayNamesShort' => [
				__('Sun', 'hostifybooking'),
				__('Mon', 'hostifybooking'),
				__('Tue', 'hostifybooking'),
				__('Wed', 'hostifybooking'),
				__('Thu', 'hostifybooking'),
				__('Fri', 'hostifybooking'),
				__('Sat', 'hostifybooking')
			]
		]);

		// just register, will be used for shortcodes later
		$this->reg_script('hfysc-payment-3ds', 'res/payment-3ds.js');

		///// stripe loading - moved to js
		// $this->reg_script_raw('hfysc-stripe', 'https://js.stripe.com/v3/', [ 'strategy' => 'async' ], true);
		// add_filter('script_loader_tag', function($tag, $handle, $source) {
		// 	if ('hfysc-stripe' === $handle) {
		// 		$tag = '<script src="' . $source . '" id="hfysc-stripe-js" async data-wp-strategy="async" onload="initPayment3ds"></script>';
		// 	}
		// 	return $tag;
		// }, 10, 3 );

		$this->reg_script('hfysc-payment', 'res/payment.js'); // deprecated
        $this->reg_script('hfysc-payment-3ds-element', 'res/payment-3ds-element.js');
        $this->reg_script('hfysc-netpay', 'res/netpay.js');
	}

	public function hfy_scripts_footer()
	{
		$this->add_script('hostifybooking', 'res/footer.js' );

		// $this->reg_script( 'hfygmaps', 'https://maps.googleapis.com/maps/api/js?key='.$settings->api_key_maps.'&v=3.exp&callback=initializeMap' );
		// $this->reg_script( 'hfygmap3', 'res/lib/gmap3.min.js' );
	}

	/**
	 * Add a link to the settings on the Plugins screen
	 */
	public static function add_settings_link( $links, $file )
	{
		if ( $file === 'hostify-booking/hostifybooking.php' && current_user_can( 'manage_options' ) ) {
			$url = admin_url( 'options-general.php?page=hostifybooking-plugin' );
			// Prevent warnings in PHP 7.0+ when a plugin uses this filter incorrectly.
			$links   = (array) $links;
			$links[] = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'hostifybooking' ) );
		}
		return $links;
	}

	/**/
	public function add_options_link( $links )
	{
		$links[] = '<a href="' . admin_url( 'options-general.php?page=hostifybooking-plugin' ) . '">' . __( 'Settings', 'hostifybooking' ) . '</a>';
		return $links;
	}

	/**/
	function register_hostifybooking_widgets() {
		// todo
		// register_widget( 'HostifybookingWidget1' );
		// ...
	}

	/**
	 * Filters list of class names to be added to HTML element.
	 *
	 * @param array $classes
	 * @return array
	 */
	function _additional_body_classes($classes)
	{
		$classes = array_merge(
			$classes,
			// [$this->get_current_hfy_theme_name()], // todo maybe
			intval($_GET['hidemap'] ?? 0) == 1 ? ['listings-map-hidden'] : []
		);
		return $classes;
	}

	/**/
	public function add_theme_classname_to_body() {
		add_filter('body_class', [ $this, '_additional_body_classes' ]);
	}

	/**/
	public function get_current_hfy_theme_name() {
		// todo
		return 'hfy-theme1';
	}

	/**/
	function add_hfy_rewrite() {
		global $wp_rewrite;
		// $wp_rewrite->add_rule('location/([^/]+)/code/([^/]+)','index.php?loc=$matches[1]&code=$matches[2]','top');
		$wp_rewrite->flush_rules(false);  // todo on a plugin activation
	}

	/**/
	function hfy_query_vars($a)
	{
		$a[] = 'id';
		$a[] = 'rid';
		$a[] = 'listing_id';
		$a[] = 'start_date';
		$a[] = 'end_date';
		$a[] = 'guests';
		$a[] = 'adults';
		$a[] = 'children';
		$a[] = 'infants';
		$a[] = 'pets';
		$a[] = 'bedrooms';
		$a[] = 'bathrooms';
		$a[] = 'city_id';
		$a[] = 'pmin';
		$a[] = 'pmax';
		$a[] = 'neighbourhood';
		$a[] = 'long_term_mode';
		$a[] = 'sort';
		$a[] = 'hidemap';
		$a[] = 'pg'; // page number
		$a[] = 'pname';
		$a[] = 'pemail';
		$a[] = 'pphone';
		$a[] = 'pcountry';
		$a[] = 'zip';
		$a[] = 'ids';
		$a[] = 'discount_code';
		$a[] = 'custom_search';
		$a[] = 'extrasSet';
		$a[] = 'extrasOptional';
		$a[] = 'fees';
		$a[] = 'max';
		$a[] = 'source';
		return $a;
	}

	/**/
	function add_general_nonce_and_settings()
	{
		/*
		// limit to specific pages:
		$current_page = get_current_screen()->base;
		if ( false === strpos( $current_page, 'my-page' ) ) {
			return;
		}
		*/

		$options = get_option('hostifybooking-plugin', [
			'map_tracking' => 'no',
		]);
		$tr = isset($options['map_tracking']) && $options['map_tracking'] == 'yes';

		$mapCustom = is_array(json_decode(HFY_MAP_CUSTOM_STYLE)) ? HFY_MAP_CUSTOM_STYLE : '[]';

		$nonce = wp_create_nonce( 'hfy_general_nonce' );
		echo
			"<meta name='hfy-csrf-token' content='$nonce' />"
			. '<script>'
			. 'window.hfyMapTracking=' . ($tr ? 'true' : 'false') . ';'
			. 'var '
				. 'hfyStartOfWeek=' . intval(get_option('start_of_week', 0))
				. ',hfySelectedLang="' . hfyGetCurrentLang() . '"'
				. ',hfyPhoneLang="' . (HFY_PAYMENT_PHONE_CODE_DEFAULT == 1 ? HFY_PAYMENT_PHONE_CODE : hfyGetCurrentLang()). '"'
				. ',hfyDF="' . hfyGetDateFormatJS() . '"'
				. ',hfyGA="' . (hfyUseGA() ? 1 : 0) . '"'
				. ',hfyMapLoc=' . intval(hfyMapLoc() ? 2 : 1)
				. ',hfyMapMaxZoom=' . intval(HFY_MAP_MAX_ZOOM)
				. ',hfyMapType="' . HFY_MAP_TYPE . '"'
				. ',mgreyImg="'. HOSTIFYBOOKING_URL . 'public/res/images/mgrey.png"'
				. ',mredImg="'. HOSTIFYBOOKING_URL . 'public/res/images/mred.png"'
				. ',meImg="'. HOSTIFYBOOKING_URL . 'public/res/images/1.png"'
				. ',preciseImg="'. HOSTIFYBOOKING_URL . 'public/res/images/precise.png"'
				. ',approxImg="'. HOSTIFYBOOKING_URL . 'public/res/images/approx.png"'
				. ',hfyGoogleMapsApiKey="'. HFY_GOOGLE_MAPS_API_KEY .'"'
				. ',hfyMapStylesOption='.$mapCustom
				. ';'
			. '</script>'
			;
	}

	function handle_robots_tag($attrs)
	{
		// add noindex meta
		if (
			is_page(HFY_PAGE_LISTING)
			|| (get_post_meta(get_the_ID(), 'use_as_listing', true) == 'on')
		) {
			$id = get_query_var('listing_id');
			if (!empty($id)) {
				if (in_array($id, HFY_SEO_NOINDEX)) {
					$attrs = [
						'noindex' => true,
						'nofollow' => true,
					];
				}
			}
		}

		return $attrs;
	}

	/**/
	function things_after_init()
	{
		add_filter('wp_robots', [$this, 'handle_robots_tag']);

		add_action('wp_head', [$this, 'add_general_nonce_and_settings']);
		add_action('wp_enqueue_scripts', [$this, 'hfy_scripts'], 1000);
		new Hostifybooking_AJAX();
		add_action('wp_footer', [$this, 'hfy_scripts_footer']);


		// ### action-scheduler
		// # to run custom background processes
		// add_action('hfy_one_time_action_asap', [$this, 'hfy_one_time_function_asap']);
		// # cleanup action scheduler records older than 30 min
		// add_filter('action_scheduler_retention_period', function($sec){
		// 	return 1800;
		// });
		// # cleanup failed too
		// add_filter('action_scheduler_default_cleaner_statuses', function($statuses){
		// 	$statuses[] = 'failed';
		// 	return $statuses;
		// });
	}

	// function hfy_one_time_function_asap($params)
	// {
	// 	if (isset($params['func']) && isset($params['data'])) {
	// 		call_user_func($params['func'], $params['data']);
	// 	}
	// }

	/**
	 */
	public function get_listing_info($id = null)
	{
		if ($id) {
			try {
				include_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
				include_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';
				include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing-seo.php';
				return $listing;
        	} catch (Exception $e) {
				// $e->getMessage();
			}
		}
		return null;
	}


	private static function do_sql($sql = null)
	{
		if (isset($sql)) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	private static function do_create_table($table_name = null, $ver = '1', $fields = [])
	{
		if (isset($table_name) && !empty($fields)) {
			global $wpdb;

			$installed_ver = get_option($table_name . '_table_ver');
			if ($installed_ver == $ver) {
				return;
			}

			$tname = $wpdb->prefix . $table_name;
			$wpdb->query("DROP TABLE IF EXISTS $tname;");
			self::do_sql("CREATE TABLE $tname (" . implode(',', $fields) . ') COLLATE "utf8_general_ci";');

			update_option($table_name . '_table_ver', $ver);
		}
	}

	/**
	 * Create listings permalinks table
	 */
	function create_listings_permalinks_table()
	{
		self::do_create_table(
			'hfy_listing_permalink',
			'1.0.5',
			[
				'id int(11) unsigned NOT NULL AUTO_INCREMENT',
				'listing_id int(11) unsigned DEFAULT null',
				'listing_name text DEFAULT null',
				'thumb text DEFAULT null',
				'permalink text DEFAULT null',
				'PRIMARY KEY (id)',
				'KEY listing_id (listing_id)',
			]
		);
	}

	function hfy_get_page_by_title($t)
	{
		$query = new WP_Query([
			'title'                  => $t,
			'post_type'              => 'page',
			'post_status'            => 'all',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		]);
		return empty($query->post) ? null : $query->post;
	}

	function hfy_update_recent_listings($t)
	{
		// $_SESSION['recent_listings'] = '';
		// store last viewed listing id to cookie
		$pageid = $t->query_vars['page_id'] ?? false;
		if (empty($pageid)) {
			$pagename = $t->query_vars['pagename'] ?? '';
			if (!empty($pagename)) {
				$page = $this->hfy_get_page_by_title($pagename);
				$pageid = $page->ID ?? null;
			}
		}
		$id = $t->query_vars['id'] ?? false;
		if ($pageid && $id) {
			if ($pageid == HFY_PAGE_LISTING) {
				$recent = $_SESSION["recent_listings"] ?? '';
				if (empty($recent)) {
					$re = $id;
				} else {
					$ids = explode(',', $recent);
					$ids[] = $id;
					$ids = array_reverse($ids);
					$ids2 = [];
					foreach ($ids as $val) {
						if (!in_array($val, $ids2)) $ids2[] = $val;
					}
					$re = implode(',', array_reverse($ids2));
				}
				// setcookie('recent_listings', $re, strtotime('+1 day'));
				$_SESSION['recent_listings'] = $re;
			}
		}
	}

	/**
	 * /?hfylisting=999 --> /<listing_page>
	 */

	function redirect_to_listing_page() {
		$id = (int) ($_GET['hfylisting'] ?? 0);
		if (!empty($id)) {
			require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
			$params = $_GET;
			unset($params['hfylisting']);
			$redirect_url = UrlHelper::get_listing_human_url($id, $params);
			
			// Ensure that the 'id' parameter is always present in the URL
			if (strpos($redirect_url, 'id=') === false) {
				$redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&') . 'id=' . $id;
			}

			wp_redirect($redirect_url);
			exit();
		}
	}

	/**
	 * Register the filters and actions
	 */
	public function run()
	{
		add_action( 'plugins_loaded', [$this, 'create_listings_permalinks_table'] );

		foreach ( $this->filters as $f ) {
			add_filter( $f['hook'], [ $f['component'], $f['callback'] ], $f['priority'], $f['accepted_args'] );
		}

		foreach ( $this->actions as $a ) {
			add_action( $a['hook'], [ $a['component'], $a['callback'] ], $a['priority'], $a['accepted_args'] );
		}

		add_action('parse_request', [$this, 'action_parse_request']);

		foreach ( $this->shortcodes as $shortcode ) {
			add_shortcode( $shortcode[0], [ $this, $shortcode[1] ] );
		}

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'add_options_link' );
		add_filter( 'plugin_action_links', [ __CLASS__, 'add_settings_link' ], 10, 2 );
		add_filter( 'network_admin_plugin_action_links', [ __CLASS__, 'add_settings_link' ], 10, 2 );

		# parse shortcodes in html widgets
		add_filter( 'widget_text', 'do_shortcode' );

		// add_action('init', [$this, 'add_hfy_rewrite']);
		add_filter('query_vars', [$this, 'hfy_query_vars']);

		add_action('init', [$this, 'things_after_init']);

		$this->add_theme_classname_to_body();

		// add_action( 'widgets_init', [ $this, 'register_hostifybooking_widgets' ] );

		// require_once HOSTIFYBOOKING_DIR . 'inc/hostifybooking-cron-activate.php';

		add_action('template_redirect', [$this, 'redirect_to_listing_page']);

		add_action('init', function() {
			require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
			if (defined('HFY_SEO_LISTINGS') && HFY_SEO_LISTINGS) {
				require_once HOSTIFYBOOKING_DIR . 'inc/seo.php';
				// Generate rewrite rules after constants are defined
				if (function_exists('hfy_init_generate_listings_rewrite_rules')) {
					hfy_init_generate_listings_rewrite_rules();
				}
			}
		});

		add_action('current_screen', [$this, 'current_screen']);

		add_action('template_redirect', [$this, 'redirect_if_404']);

		if (is_admin()) {
			add_filter('update_plugins_wp-update.hostify.com', [$this, 'plugin_check_for_updates'], 10, 3);
		} else {
			add_filter('elementor_pro/dynamic_tags/shortcode/should_escape', '__return_false');
		}
	}

	function plugin_check_for_updates($update, $plugin_data, $plugin_file)
	{
		static $response = false;
		if (empty($plugin_data['UpdateURI']) || !empty($update)) return $update;
		if ($response === false) $response = wp_remote_get($plugin_data['UpdateURI']);
		if (is_wp_error($response) || empty($response['body'])) return $update;
		$custom_plugins_data = json_decode($response['body'], true);

		if (!empty($custom_plugins_data[$plugin_file])) {
			$custom_plugins_data[$plugin_file]['package'] = "https://wp-update.hostify.com/update.php?k=".base64_encode(HFY_API_WPKEY.'|'.HFY_API_URL);
			return $custom_plugins_data[$plugin_file];
		}
		return $update;
	}

	function redirect_if_404()
	{
		if (empty(HFY_LISTING_NOT_FOUND_URL)) return;

		if (
			is_page(HFY_PAGE_LISTING)
            || (get_post_meta(get_the_ID(), 'use_as_listing', true) == 'on')
		) {
			$prm = hfy_get_vars_def();
			$id = intval($prm->id ?? 0);
			if ($id > 0) {
				global $wpdb;
				$tname = $wpdb->prefix . 'hfy_listing_permalink';
				$res = $wpdb->get_results("select * from {$tname} where listing_id={$id} limit 1");
				if (($res[0]->id ?? 0) <= 0) {
					wp_redirect(HFY_LISTING_NOT_FOUND_URL, 301);
					die;
				}
			}
		}
	}

	function current_screen($screen)
	{
		// if (isset($screen->post_type) && $screen->post_type === '...') {
		if ($screen->base === 'settings_page_hostifybooking-plugin') {
			add_action('in_admin_header', [$this, 'in_admin_header']);
			add_filter('admin_footer_text', [$this, 'admin_footer_text']);
		}
	}

	function admin_footer_text($text)
	{
		return preg_replace('/(<a[\S\s]+?\/a>)/', '$1 and <a href="https://hostify.com" target="_blank">Hostify</a>', $text, 1);
	}

	function in_admin_header()
	{
		?>
<div class="hfy-admin-toolbar">

	<span class='hfy-atb-title'>
		<svg style="vertical-align:text-bottom;height:24px" width="20" height="20" viewBox="0 0 73 75" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity="0.4" d="M0.160156 34.8858C0.160156 33.9251 0.608394 33.0185 1.37423 32.4301L29.9578 8.51945C31.0871 7.65188 32.6652 7.65188 33.7945 8.51945L62.3781 32.4301C63.144 33.0185 63.5922 33.9251 63.5922 34.8858V71.0272C63.5922 72.7428 62.1898 74.1336 60.4597 74.1336H3.2926C1.5626 74.1336 0.160156 72.7428 0.160156 71.0272V34.8858Z" fill="#16A9FC"/><path d="M9.15234 27.017C9.15234 26.0563 9.60058 25.1497 10.3664 24.5613L38.95 0.650676C40.0793 -0.216891 41.6574 -0.216893 42.7867 0.650675L71.3703 24.5613C72.1361 25.1497 72.5844 26.0563 72.5844 27.017V63.1584C72.5844 64.874 71.1819 66.2648 69.4519 66.2648H12.2848C10.5548 66.2648 9.15234 64.874 9.15234 63.1584V27.017Z" fill="#167FFC"/><path fill-rule="evenodd" clip-rule="evenodd" d="M58.8177 28.9951L35.2706 52.5424L22.3555 40.0488L27.294 34.9436L35.188 42.5799L53.7951 23.9725L58.8177 28.9951Z" fill="white"/></svg>
		Hostify Booking Engine
		<span class='hfy-atb-version'>
			<?= HOSTIFYBOOKING_VERSION ?>
			API v.<?= HFY_USE_API_V3 ? '3' : '2' ?>
		</span>
	</span>

	<span class='hfy-atb-right'>
		<?php if (HFY_DISABLE_CACHE): ?>
			<span style='margin-left:20px;color:red' class='button'><?= __('Cache is disabled now', 'hostifybooking'); ?></span>
		<?php else: ?>
			<a href='<?= get_admin_url() ?>options-general.php?page=hostifybooking-plugin&clr=1' style='margin-left:20px' class='button hfy-clear-cache-btn'><?= __('Clear cache', 'hostifybooking'); ?></a>
		<?php endif; ?>
		&nbsp;
		<input type="button" class="button button-primary hostifybooking-plugin-save-atb" value="<?= __('Save Settings', 'hostifybooking'); ?>" />
	</span>

</div>
		<?php

	}

}
