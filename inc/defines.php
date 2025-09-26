<?php

if (!defined('WPINC')) die;

function wporg_prfex_get_theme_option($option_name, $defaults = [])
{
	$options = get_option($option_name);
	$out = $defaults;
	if (!empty($options)) {
		foreach ($options as $name => $value) {
			$out[$name] = $value;
		}
	}
	return $out;
}

function hfy_define_consts()
{
	$defaults = [
		'apiUrl' => '',
		'apiWpkey' => '',
		'page_listing' => 0,
		'page_listings' => 0,
		'page_payment' => 0,
		'page_charge' => 0,
		'page_booking_manage' => 0,
		'show_discount' => 'yes',
		'show_captcha' => 'no',
		'map_tracking' => 'no',
		'map_price_label' => 'yes',
		'map_loc_circle' => 'yes',
		'map_max_zoom' => 12,
		'selected_amenities' => '',
		'amenities_images' => 'no',
		'amenities_images_only' => 'no',
		'adv_search_am' => 'no',
		'adv_search_am_list' => '',
		'disable_cache' => 'no',
		'locations_selector' => 0,
		'show_location_filter_v3' => 'no',
		'rich_select_loc' => 'no',
		'adv_search_am_short' => 'yes',
		'adv_search_am_groups' => 'yes',
		'adv_search_am_groups_hide_other' => 'yes',
		'adv_search_pets' => 'no',
		// 'show_img_loader' => 'yes',
		'listings_per_page' => 20,
		'use_listing_booking_form_v2' => 'yes',
		'show_on_bar' => 'yes',
		'long_short_selector' => 'no',
		'long_term_default' => 0,
		'seo_listings' => 'no',
		'seo_noindex' => '',
		'seo_events' => 'no',
		'seo_listing_slug' => 0,
		'seo_listing_slug_find' => '',
		'seo_listing_slug_replace' => '',
		'guests_max' => 10,
		'show_pets' => 'yes',
		'show_pets_max' => 10,
		'show_infants' => 'yes',
		'show_infants_max' => 10,
		'listing_not_found_url' => '',
		'listings_sort' => 0,
		'text_select_location' => __('Select location', 'hostifybooking'),
		'use_api_v3' => 'no',
		'tags_menu' => '',
		'redirect_on_payment' => 0,
		'redirect_on_payment_url' => '',
		'show_payment_extras' => 'no',
		'use_new_calendar' => 'no',
		'show_guests_hints' => 'yes',
		'google_maps_api_key' => '',
		'google_recaptcha_site_key' => '',
		'google_recaptcha_secret_key' => '',
		'google_recaptcha_version' => 'v2',
	'use_listings_gallery' => '',
		'use_listings_gallery_click' => '',
		'redirect_no_listing' => 0,
		'redirect_no_listing_url' => '',
		'disable_templates_payment_override' => 'yes',
		'payment_phone_code_default' => 0,
		'payment_phone_code' => '',
		'map_custom_style' => '',
		'map_type' => 'roadmap',
		'dynamic_google_map' => 'no',
		'use_new_inquiry_form' => 'no', // Added new option default
		'use_v3_calendar' => 'no',
		'enable_keyword_search' => 'no',
		'keyword_search_placeholder' => __('Search by keyword...', 'hostifybooking'),
		'review_system' => 0,
		'show_review_source' => 'yes',
	];
	// $options = get_option('hostifybooking-plugin', $defaults);
	$options = wporg_prfex_get_theme_option('hostifybooking-plugin', $defaults);

	if (!defined('HFY_API_URL'))
		define('HFY_API_URL', rtrim($options['apiUrl'], '/') . '/');

	if (!defined('HFY_API_WPKEY'))
		define('HFY_API_WPKEY', trim($options['apiWpkey'] ?? ''));

	if (!defined('HFY_PAYMENT_PHONE_CODE'))
		define('HFY_PAYMENT_PHONE_CODE', strtolower(trim($options['payment_phone_code'] ?? '')));

	if (!defined('HFY_PAYMENT_PHONE_CODE_DEFAULT'))
		define('HFY_PAYMENT_PHONE_CODE_DEFAULT', intval($options['payment_phone_code_default'] ?? 0));

	if (!defined('HFY_GOOGLE_MAPS_API_KEY'))
		define('HFY_GOOGLE_MAPS_API_KEY', trim($options['google_maps_api_key'] ?? ''));

	if (!defined('HFY_GOOGLE_RECAPTCHA_SITE_KEY'))
		define('HFY_GOOGLE_RECAPTCHA_SITE_KEY', trim($options['google_recaptcha_site_key'] ?? ''));

	if (!defined('HFY_GOOGLE_RECAPTCHA_SECRET_KEY'))
		define('HFY_GOOGLE_RECAPTCHA_SECRET_KEY', trim($options['google_recaptcha_secret_key'] ?? ''));

	if (!defined('HFY_GOOGLE_RECAPTCHA_VERSION'))
		define('HFY_GOOGLE_RECAPTCHA_VERSION', trim($options['google_recaptcha_version'] ?? 'v2'));

	if (!defined('HFY_PAGE_LISTING'))
		define('HFY_PAGE_LISTING', intval($options['page_listing'] ?? 0));

	if (!defined('HFY_PAGE_LISTING_URL'))
		define('HFY_PAGE_LISTING_URL', empty(HFY_PAGE_LISTING) ? '' : get_page_link(HFY_PAGE_LISTING));

	if (!defined('HFY_PAGE_LISTINGS'))
		define('HFY_PAGE_LISTINGS', intval($options['page_listings'] ?? 0));

	if (!defined('HFY_PAGE_LISTINGS_URL'))
		define('HFY_PAGE_LISTINGS_URL', empty(HFY_PAGE_LISTINGS) ? '' : get_page_link(HFY_PAGE_LISTINGS));

	if (!defined('HFY_PAGE_PAYMENT'))
		define('HFY_PAGE_PAYMENT', intval($options['page_payment'] ?? 0));

	if (!defined('HFY_PAGE_PAYMENT_URL'))
		define('HFY_PAGE_PAYMENT_URL', empty(HFY_PAGE_PAYMENT) ? '' : get_page_link(HFY_PAGE_PAYMENT));

	if (!defined('HFY_PAGE_CHARGE'))
		define('HFY_PAGE_CHARGE', intval($options['page_charge'] ?? 0));

	if (!defined('HFY_PAGE_CHARGE_URL'))
		define('HFY_PAGE_CHARGE_URL', empty(HFY_PAGE_CHARGE) ? '' : get_page_link(HFY_PAGE_CHARGE));

	if (!defined('HFY_PAGE_BOOKINGS_LIST'))
		define('HFY_PAGE_BOOKINGS_LIST', intval($options['page_bookings_list'] ?? 0));

	if (!defined('HFY_PAGE_BOOKINGS_LIST_URL'))
		define('HFY_PAGE_BOOKINGS_LIST_URL', empty(HFY_PAGE_BOOKINGS_LIST) ? '' : get_page_link(HFY_PAGE_BOOKINGS_LIST));

	if (!defined('HFY_PAGE_BOOKING_MANAGE'))
		define('HFY_PAGE_BOOKING_MANAGE', intval($options['page_booking_manage'] ?? 0));

	if (!defined('HFY_PAGE_BOOKING_MANAGE_URL'))
		define('HFY_PAGE_BOOKING_MANAGE_URL', empty(HFY_PAGE_BOOKING_MANAGE) ? '' : get_page_link(HFY_PAGE_BOOKING_MANAGE));

	if (!defined('HFY_PAGE_WISHLIST'))
		define('HFY_PAGE_WISHLIST', intval($options['page_wishlist'] ?? 0));

	if (!defined('HFY_PAGE_WISHLIST_URL'))
		define('HFY_PAGE_WISHLIST_URL', empty(HFY_PAGE_WISHLIST) ? '' : get_page_link(HFY_PAGE_WISHLIST));

	if (!defined('HFY_USE_LISTINGS_GALLERY'))
		define('HFY_USE_LISTINGS_GALLERY', ($options['use_listings_gallery'] ?? 'yes') == 'yes');

	if (!defined('HFY_USE_LISTINGS_GALLERY_CLICK'))
		define('HFY_USE_LISTINGS_GALLERY_CLICK', ($options['use_listings_gallery_click'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_ON_BAR'))
		define('HFY_SHOW_ON_BAR', ($options['show_on_bar'] ?? 'yes') == 'yes');

	if (!defined('HFY_USE_API_V3'))
		define('HFY_USE_API_V3', ($options['use_api_v3'] ?? 'no') == 'yes');

	if (!defined('HFY_USE_STRIPE_ELEMENT'))
		define('HFY_USE_STRIPE_ELEMENT', ($options['use_stripe_element'] ?? 'no') == 'yes');

	if (!defined('HFY_USE_NEW_CALENDAR'))
		define('HFY_USE_NEW_CALENDAR', ($options['use_new_calendar'] ?? 'no') == 'yes');

	if (!defined('HFY_USE_V3_CALENDAR'))
		define('HFY_USE_V3_CALENDAR', ($options['use_v3_calendar'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_DISCOUNT'))
		define('HFY_SHOW_DISCOUNT', ($options['show_discount'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_CAPTCHA'))
		define('HFY_SHOW_CAPTCHA', ($options['show_captcha'] ?? 'yes') == 'yes');

	if (!defined('HFY_MAP_TRACKING'))
		define('HFY_MAP_TRACKING', ($options['map_tracking'] ?? 'no') == 'yes');

	if (!defined('HFY_MAP_PRICE_LABEL'))
		define('HFY_MAP_PRICE_LABEL', ($options['map_price_label'] ?? 'yes') == 'yes');

	if (!defined('HFY_MAP_LOC_CIRCLE'))
		define('HFY_MAP_LOC_CIRCLE', ($options['map_loc_circle'] ?? 'yes') == 'yes');

	if (!defined('HFY_DISABLE_TEMPLATES_PAYMENT_OVERRIDE'))
		define('HFY_DISABLE_TEMPLATES_PAYMENT_OVERRIDE', ($options['disable_templates_payment_override'] ?? 'yes') == 'yes');

	if (!defined('HFY_MAP_MAX_ZOOM'))
		$hmz = intval($options['map_max_zoom'] ?? 12);
	if ($hmz < 2) $hmz = 2;
	define('HFY_MAP_MAX_ZOOM', $hmz);

	if (!defined('HFY_MAP_CLUSTERS'))
		define('HFY_MAP_CLUSTERS', ($options['map_clusters'] ?? 'yes') == 'yes');

	$selected_am_filtered = array();
	if (isset($options['selected_amenities'])) {
		$selected_am_filtered = array_filter(
			explode(',', trim($options['selected_amenities'])),
			function ($x) {
				return intval($x) > 0;
			}
		);
	}

	if (!defined('HFY_AMENITIES_IDS'))
		define('HFY_AMENITIES_IDS', serialize($selected_am_filtered));

	if (!defined('HFY_AMENITIES_IMAGES_FIRST'))
		define('HFY_AMENITIES_IMAGES_FIRST', ($options['amenities_images'] ?? 'no') == 'yes');

	if (!defined('HFY_AMENITIES_IMAGES_ONLY'))
		define('HFY_AMENITIES_IMAGES_ONLY', ($options['amenities_images_only'] ?? 'no') == 'yes');


	if (!defined('HFY_ADV_SEARCH_AM'))
		define('HFY_ADV_SEARCH_AM', ($options['adv_search_am'] ?? 'yes') == 'yes');

	if (!defined('HFY_ADV_SEARCH_AM_LIST'))
		define('HFY_ADV_SEARCH_AM_LIST', $options['adv_search_am_list']);

	if (!defined('HFY_SEARCH_EXACT_BEDROOMS'))
		define('HFY_SEARCH_EXACT_BEDROOMS', ($options['exact_bedrooms'] ?? 'no') == 'yes');

	if (!defined('HFY_SHOW_STUDIO_OPTION'))
		define('HFY_SHOW_STUDIO_OPTION', ($options['show_studio_option'] ?? 'no') == 'yes');

	if (!defined('HFY_LONG_TERM_DEFAULT')) {
		$ltd = (int) ($options['long_term_default'] ?? 0);
		define('HFY_LONG_TERM_DEFAULT', $ltd == 1 ? 1 : 2);
	}

	if (!defined('HFY_SEO_LISTINGS'))
		define('HFY_SEO_LISTINGS', ($options['seo_listings'] ?? 'yes') == 'yes');

	if (!defined('HFY_SEO_LISTING_SLUG'))
		define('HFY_SEO_LISTING_SLUG', intval($options['seo_listing_slug'] ?? 0) == 1 ? 1 : 0);

	if (!defined('HFY_SEO_LISTING_SLUG_FIND'))
		define('HFY_SEO_LISTING_SLUG_FIND', $options['seo_listing_slug_find'] ?? '');

	if (!defined('HFY_SEO_LISTING_SLUG_REPLACE'))
		define('HFY_SEO_LISTING_SLUG_REPLACE', $options['seo_listing_slug_replace'] ?? '');

	if (!defined('HFY_SEO_NOINDEX'))
		define('HFY_SEO_NOINDEX', preg_split("/[\s,\.]+/", $options['seo_noindex'] ?? ''));

	if (!defined('HFY_DISABLE_CACHE'))
		define('HFY_DISABLE_CACHE', ($options['disable_cache'] ?? 'no') == 'yes');

	if (!defined('HFY_LOCATIONS_SELECTOR'))
		define('HFY_LOCATIONS_SELECTOR', (int) ($options['locations_selector'] ?? 0));

	if (!defined('HFY_LONG_SHORT_SELECTOR'))
		define('HFY_LONG_SHORT_SELECTOR', ($options['long_short_selector'] ?? 'no') == 'yes');

	if (!defined('HFY_RICH_SELECT_LOC'))
		define('HFY_RICH_SELECT_LOC', ($options['rich_select_loc'] ?? 'no') == 'yes');

	if (!defined('HFY_ADV_SEARCH_AM_SHORT'))
		define('HFY_ADV_SEARCH_AM_SHORT', ($options['adv_search_am_short'] ?? 'yes') == 'yes');

	if (!defined('HFY_ADV_SEARCH_AM_GROUPS'))
		define('HFY_ADV_SEARCH_AM_GROUPS', ($options['adv_search_am_groups'] ?? 'yes') == 'yes');

	if (!defined('HFY_ADV_SEARCH_AM_GROUPS_HIDE_OTHER'))
		define('HFY_ADV_SEARCH_AM_GROUPS_HIDE_OTHER', ($options['adv_search_am_groups_hide_other'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_IMAGE_LOADER'))
		// define('HFY_SHOW_IMAGE_LOADER', ($options['show_img_loader'] ?? 'yes') == 'yes');
		define('HFY_SHOW_IMAGE_LOADER', false);

	if (!defined('HFY_SHOW_PAYMENT_EXTRAS'))
		define('HFY_SHOW_PAYMENT_EXTRAS', ($options['show_payment_extras'] ?? 'yes') == 'yes');

	if (!defined('HFY_USE_BOOKING_FORM_V2'))
		define('HFY_USE_BOOKING_FORM_V2', ($options['use_listing_booking_form_v2'] ?? 'yes') == 'yes');

	if (!defined('HFY_LISTINGS_PER_PAGE'))
		define('HFY_LISTINGS_PER_PAGE', intval($options['listings_per_page'] ?? 20));

	if (!defined('HFY_LISTINGS_SORT'))
		define('HFY_LISTINGS_SORT', intval($options['listings_sort'] ?? 0));

	if (!defined('HFY_LISTING_NOT_FOUND_URL'))
		define('HFY_LISTING_NOT_FOUND_URL', trim($options['listing_not_found_url'] ?? ''));

	if (!defined('HFY_SHOW_PETS'))
		define('HFY_SHOW_PETS', ($options['show_pets'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_PETS_MAX'))
		define('HFY_SHOW_PETS_MAX', intval($options['show_pets_max'] ?? 10));

	if (!defined('HFY_ADV_SEARCH_PETS'))
		define('HFY_ADV_SEARCH_PETS', !HFY_USE_BOOKING_FORM_V2 && ($options['adv_search_pets'] ?? 'no') == 'yes');

	if (!defined('HFY_SHOW_INFANTS'))
		define('HFY_SHOW_INFANTS', ($options['show_infants'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_GUESTS_HINTS'))
		define('HFY_SHOW_GUESTS_HINTS', ($options['show_guests_hints'] ?? 'yes') == 'yes');

	if (!defined('HFY_SHOW_INFANTS_MAX'))
		define('HFY_SHOW_INFANTS_MAX', intval($options['show_infants_max'] ?? 10));

	if (!defined('HFY_GUESTS_MAX'))
		define('HFY_GUESTS_MAX', intval($options['guests_max'] ?? 10));

	if (!defined('HFY_REDIRECT_ON_PAYMENT'))
		define('HFY_REDIRECT_ON_PAYMENT', intval($options['redirect_on_payment'] ?? 0));

	if (!defined('HFY_REDIRECT_ON_PAYMENT_URL'))
		define('HFY_REDIRECT_ON_PAYMENT_URL', trim($options['redirect_on_payment_url'] ?? ''));

	if (!defined('HFY_REDIRECT_NO_LISTING'))
		define('HFY_REDIRECT_NO_LISTING', intval($options['redirect_no_listing'] ?? 0));

	if (!defined('HFY_REDIRECT_NO_LISTING_URL'))
		define('HFY_REDIRECT_NO_LISTING_URL', trim($options['redirect_no_listing_url'] ?? ''));

	if (!defined('HFY_TEXT_SELECT_LOCATION')) {
		$xx = trim($options['text_select_location'] ?? '');
		define('HFY_TEXT_SELECT_LOCATION', empty($xx) ? __('Select location', 'hostifybooking') : $xx);
	}

	if (!defined('HFY_MAP_TYPE'))
		define('HFY_MAP_TYPE', $options['map_type'] ?? 'roadmap');

	if (!defined('HFY_MAP_CUSTOM_STYLE')) {
		$xx = trim($options['map_custom_style'] ?? '');
		define('HFY_MAP_CUSTOM_STYLE', empty($xx) ? json_encode(HFY_MAP_CUSTOM_STYLE_DEFAULT) : $xx);
	}

	if (!defined('HFY_TAGS_MENU')) {
		$m = [];
		$tags_menu = trim($options['tags_menu'] ?? '');
		if (strlen($tags_menu) > 0) {
			$menu = wp_get_nav_menu_object($tags_menu);
			if ($menu) {
				// Get menu items for the specified menu
				// $menuitems = wp_get_nav_menu_items($menu->term_id, array('order' => 'DESC'));
				$menuitems = wp_get_nav_menu_items($menu->term_id);
				foreach ($menuitems as $i) {
					if ($i->menu_item_parent <= 0) $m[$i->ID]['title'] = $i->title;
				}
				foreach ($menuitems as $i) {
					if ($i->menu_item_parent > 0) {
						if (isset($m[$i->menu_item_parent])) $m[$i->menu_item_parent]['items'][$i->ID]['title'] = $i->title;
					}
				}
			}
		}
		define('HFY_TAGS_MENU', $m);
	}

	if (!defined('HFY_DYNAMIC_GOOGLE_MAP'))
        define('HFY_DYNAMIC_GOOGLE_MAP', ($options['dynamic_google_map'] ?? 'no') == 'yes');

	// Define constant for the new inquiry form option
	if (!defined('HFY_USE_NEW_INQUIRY_FORM'))
		define('HFY_USE_NEW_INQUIRY_FORM', ($options['use_new_inquiry_form'] ?? 'no') == 'yes');

	// Define constants for keyword search
	if (!defined('HFY_ENABLE_KEYWORD_SEARCH'))
		define('HFY_ENABLE_KEYWORD_SEARCH', ($options['enable_keyword_search'] ?? 'no') == 'yes');

	if (!defined('HFY_KEYWORD_SEARCH_PLACEHOLDER')) {
		$xx = trim($options['keyword_search_placeholder'] ?? '');
		define('HFY_KEYWORD_SEARCH_PLACEHOLDER', empty($xx) ? __('Search by keyword...', 'hostifybooking') : $xx);
	}

	// Define constants for review system
	if (!defined('HFY_REVIEW_SYSTEM'))
		define('HFY_REVIEW_SYSTEM', intval($options['review_system'] ?? 0));

	if (!defined('HFY_SHOW_REVIEW_SOURCE'))
		define('HFY_SHOW_REVIEW_SOURCE', ($options['show_review_source'] ?? 'yes') == 'yes');
}

add_action('init', 'hfy_define_consts');

const HFY_MEXICO_STATE_CODES_ALPHA3 = [

	'AGU' => 'Aguascalientes',
	'BCN' => 'Baja California',
	'BCS' => 'Baja California Sur',
	'CAM' => 'Campeche',
	'CHP' => 'Chiapas',
	'CHH' => 'Chihuahua',
	'CMX' => 'Ciudad de México',
	'COA' => 'Coahuila',
	'COL' => 'Colima',
	'DUR' => 'Durango',
	'GUA' => 'Guanajuato',
	'GRO' => 'Guerrero',
	'HID' => 'Hidalgo',
	'JAL' => 'Jalisco',
	'MEX' => 'México',
	'MIC' => 'Michoacán',
	'MOR' => 'Morelos',
	'NAY' => 'Nayarit',
	'NLE' => 'Nuevo León',
	'OAX' => 'Oaxaca',
	'PUE' => 'Puebla',
	'QUE' => 'Querétaro',
	'ROO' => 'Quintana Roo',
	'SLP' => 'San Luis Potosí',
	'SIN' => 'Sinaloa',
	'SON' => 'Sonora',
	'TAB' => 'Tabasco',
	'TAM' => 'Tamaulipas',
	'TLA' => 'Tlaxcala',
	'VER' => 'Veracruz',
	'YUC' => 'Yucatán',
	'ZAC' => 'Zacatecas'
];

const HFY_USA_STATE_CODES_ALPHA2 = [
	'AL' => 'Alabama',
	'AK' => 'Alaska',
	'AZ' => 'Arizona',
	'AR' => 'Arkansas',
	'CA' => 'California',
	'CO' => 'Colorado',
	'CT' => 'Connecticut',
	'DE' => 'Delaware',
	'FL' => 'Florida',
	'GA' => 'Georgia',
	'HI' => 'Hawaii',
	'ID' => 'Idaho',
	'IL' => 'Illinois',
	'IN' => 'Indiana',
	'IA' => 'Iowa',
	'KS' => 'Kansas',
	'KY' => 'Kentucky',
	'LA' => 'Louisiana',
	'ME' => 'Maine',
	'MD' => 'Maryland',
	'MA' => 'Massachusetts',
	'MI' => 'Michigan',
	'MN' => 'Minnesota',
	'MS' => 'Mississippi',
	'MO' => 'Missouri',
	'MT' => 'Montana',
	'NE' => 'Nebraska',
	'NV' => 'Nevada',
	'NH' => 'New Hampshire',
	'NJ' => 'New Jersey',
	'NM' => 'New Mexico',
	'NY' => 'New York',
	'NC' => 'North Carolina',
	'ND' => 'North Dakota',
	'OH' => 'Ohio',
	'OK' => 'Oklahoma',
	'OR' => 'Oregon',
	'PA' => 'Pennsylvania',
	'RI' => 'Rhode Island',
	'SC' => 'South Carolina',
	'SD' => 'South Dakota',
	'TN' => 'Tennessee',
	'TX' => 'Texas',
	'UT' => 'Utah',
	'VT' => 'Vermont',
	'VA' => 'Virginia',
	'WA' => 'Washington',
	'WV' => 'West Virginia',
	'WI' => 'Wisconsin',
	'WY' => 'Wyoming'
];

const HFY_COUNTRY_CODES_ALPHA2 = [
	"AF" => "Afghanistan",
	"AX" => "Åland Islands",
	"AL" => "Albania",
	"DZ" => "Algeria",
	"AS" => "American Samoa",
	"AD" => "Andorra",
	"AO" => "Angola",
	"AI" => "Anguilla",
	"AQ" => "Antarctica",
	"AG" => "Antigua and Barbuda",
	"AR" => "Argentina",
	"AM" => "Armenia",
	"AW" => "Aruba",
	"AU" => "Australia",
	"AT" => "Austria",
	"AZ" => "Azerbaijan",
	"BS" => "Bahamas",
	"BH" => "Bahrain",
	"BD" => "Bangladesh",
	"BB" => "Barbados",
	"BY" => "Belarus",
	"BE" => "Belgium",
	"BZ" => "Belize",
	"BJ" => "Benin",
	"BM" => "Bermuda",
	"BT" => "Bhutan",
	"BO" => "Bolivia, Plurinational State of",
	"BQ" => "Bonaire, Sint Eustatius and Saba",
	"BA" => "Bosnia and Herzegovina",
	"BW" => "Botswana",
	"BV" => "Bouvet Island",
	"BR" => "Brazil",
	"IO" => "British Indian Ocean Territory",
	"BN" => "Brunei Darussalam",
	"BG" => "Bulgaria",
	"BF" => "Burkina Faso",
	"BI" => "Burundi",
	"KH" => "Cambodia",
	"CM" => "Cameroon",
	"CA" => "Canada",
	"CV" => "Cape Verde",
	"KY" => "Cayman Islands",
	"CF" => "Central African Republic",
	"TD" => "Chad",
	"CL" => "Chile",
	"CN" => "China",
	"CX" => "Christmas Island",
	"CC" => "Cocos (Keeling) Islands",
	"CO" => "Colombia",
	"KM" => "Comoros",
	"CG" => "Congo",
	"CD" => "Congo, the Democratic Republic of the",
	"CK" => "Cook Islands",
	"CR" => "Costa Rica",
	"CI" => "Côte d'Ivoire",
	"HR" => "Croatia",
	"CU" => "Cuba",
	"CW" => "Curaçao",
	"CY" => "Cyprus",
	"CZ" => "Czech Republic",
	"DK" => "Denmark",
	"DJ" => "Djibouti",
	"DM" => "Dominica",
	"DO" => "Dominican Republic",
	"EC" => "Ecuador",
	"EG" => "Egypt",
	"SV" => "El Salvador",
	"GQ" => "Equatorial Guinea",
	"ER" => "Eritrea",
	"EE" => "Estonia",
	"ET" => "Ethiopia",
	"FK" => "Falkland Islands (Malvinas)",
	"FO" => "Faroe Islands",
	"FJ" => "Fiji",
	"FI" => "Finland",
	"FR" => "France",
	"GF" => "French Guiana",
	"PF" => "French Polynesia",
	"TF" => "French Southern Territories",
	"GA" => "Gabon",
	"GM" => "Gambia",
	"GE" => "Georgia",
	"DE" => "Germany",
	"GH" => "Ghana",
	"GI" => "Gibraltar",
	"GR" => "Greece",
	"GL" => "Greenland",
	"GD" => "Grenada",
	"GP" => "Guadeloupe",
	"GU" => "Guam",
	"GT" => "Guatemala",
	"GG" => "Guernsey",
	"GN" => "Guinea",
	"GW" => "Guinea-Bissau",
	"GY" => "Guyana",
	"HT" => "Haiti",
	"HM" => "Heard Island and McDonald Islands",
	"VA" => "Holy See (Vatican City State)",
	"HN" => "Honduras",
	"HK" => "Hong Kong",
	"HU" => "Hungary",
	"IS" => "Iceland",
	"IN" => "India",
	"ID" => "Indonesia",
	"IR" => "Iran, Islamic Republic of",
	"IQ" => "Iraq",
	"IE" => "Ireland",
	"IM" => "Isle of Man",
	"IL" => "Israel",
	"IT" => "Italy",
	"JM" => "Jamaica",
	"JP" => "Japan",
	"JE" => "Jersey",
	"JO" => "Jordan",
	"KZ" => "Kazakhstan",
	"KE" => "Kenya",
	"KI" => "Kiribati",
	"KP" => "Korea, Democratic People's Republic of",
	"KR" => "Korea, Republic of",
	"KW" => "Kuwait",
	"KG" => "Kyrgyzstan",
	"LA" => "Lao People's Democratic Republic",
	"LV" => "Latvia",
	"LB" => "Lebanon",
	"LS" => "Lesotho",
	"LR" => "Liberia",
	"LY" => "Libya",
	"LI" => "Liechtenstein",
	"LT" => "Lithuania",
	"LU" => "Luxembourg",
	"MO" => "Macao",
	"MK" => "Macedonia, the former Yugoslav Republic of",
	"MG" => "Madagascar",
	"MW" => "Malawi",
	"MY" => "Malaysia",
	"MV" => "Maldives",
	"ML" => "Mali",
	"MT" => "Malta",
	"MH" => "Marshall Islands",
	"MQ" => "Martinique",
	"MR" => "Mauritania",
	"MU" => "Mauritius",
	"YT" => "Mayotte",
	"MX" => "Mexico",
	"FM" => "Micronesia, Federated States of",
	"MD" => "Moldova, Republic of",
	"MC" => "Monaco",
	"MN" => "Mongolia",
	"ME" => "Montenegro",
	"MS" => "Montserrat",
	"MA" => "Morocco",
	"MZ" => "Mozambique",
	"MM" => "Myanmar",
	"NA" => "Namibia",
	"NR" => "Nauru",
	"NP" => "Nepal",
	"NL" => "Netherlands",
	"NC" => "New Caledonia",
	"NZ" => "New Zealand",
	"NI" => "Nicaragua",
	"NE" => "Niger",
	"NG" => "Nigeria",
	"NU" => "Niue",
	"NF" => "Norfolk Island",
	"MP" => "Northern Mariana Islands",
	"NO" => "Norway",
	"OM" => "Oman",
	"PK" => "Pakistan",
	"PW" => "Palau",
	"PS" => "Palestinian Territory, Occupied",
	"PA" => "Panama",
	"PG" => "Papua New Guinea",
	"PY" => "Paraguay",
	"PE" => "Peru",
	"PH" => "Philippines",
	"PN" => "Pitcairn",
	"PL" => "Poland",
	"PT" => "Portugal",
	"PR" => "Puerto Rico",
	"QA" => "Qatar",
	"RE" => "Réunion",
	"RO" => "Romania",
	"RU" => "Russian Federation",
	"RW" => "Rwanda",
	"BL" => "Saint Barthélemy",
	"SH" => "Saint Helena, Ascension and Tristan da Cunha",
	"KN" => "Saint Kitts and Nevis",
	"LC" => "Saint Lucia",
	"MF" => "Saint Martin (French part)",
	"PM" => "Saint Pierre and Miquelon",
	"VC" => "Saint Vincent and the Grenadines",
	"WS" => "Samoa",
	"SM" => "San Marino",
	"ST" => "Sao Tome and Principe",
	"SA" => "Saudi Arabia",
	"SN" => "Senegal",
	"RS" => "Serbia",
	"SC" => "Seychelles",
	"SL" => "Sierra Leone",
	"SG" => "Singapore",
	"SX" => "Sint Maarten (Dutch part)",
	"SK" => "Slovakia",
	"SI" => "Slovenia",
	"SB" => "Solomon Islands",
	"SO" => "Somalia",
	"ZA" => "South Africa",
	"GS" => "South Georgia and the South Sandwich Islands",
	"SS" => "South Sudan",
	"ES" => "Spain",
	"LK" => "Sri Lanka",
	"SD" => "Sudan",
	"SR" => "Suriname",
	"SJ" => "Svalbard and Jan Mayen",
	"SZ" => "Swaziland",
	"SE" => "Sweden",
	"CH" => "Switzerland",
	"SY" => "Syrian Arab Republic",
	"TW" => "Taiwan",
	"TJ" => "Tajikistan",
	"TZ" => "Tanzania, United Republic of",
	"TH" => "Thailand",
	"TL" => "Timor-Leste",
	"TG" => "Togo",
	"TK" => "Tokelau",
	"TO" => "Tonga",
	"TT" => "Trinidad and Tobago",
	"TN" => "Tunisia",
	"TR" => "Turkey",
	"TM" => "Turkmenistan",
	"TC" => "Turks and Caicos Islands",
	"TV" => "Tuvalu",
	"UG" => "Uganda",
	"UA" => "Ukraine",
	"AE" => "United Arab Emirates",
	"GB" => "United Kingdom",
	"US" => "United States",
	"UM" => "United States Minor Outlying Islands",
	"UY" => "Uruguay",
	"UZ" => "Uzbekistan",
	"VU" => "Vanuatu",
	"VE" => "Venezuela, Bolivarian Republic of",
	"VN" => "Viet Nam",
	"VG" => "Virgin Islands, British",
	"VI" => "Virgin Islands, U.S.",
	"WF" => "Wallis and Futuna",
	"EH" => "Western Sahara",
	"YE" => "Yemen",
	"ZM" => "Zambia",
	"ZW" => "Zimbabwe",
];

const HFY_MAP_CUSTOM_STYLE_DEFAULT = [
	[
		"featureType" => "landscape.man_made",
		"elementType" => "geometry",
		"stylers" => [
			[
				"color" => "#f7f1df"
			]
		]
	],
	[
		"featureType" => "landscape.natural",
		"elementType" => "geometry",
		"stylers" => [
			[
				"color" => "#d0e3b4"
			]
		]
	],
	[
		"featureType" => "landscape.natural.terrain",
		"elementType" => "geometry",
		"stylers" => [
			[
				"visibility" => "off"
			]
		]
	],
	[
		"featureType" => "poi",
		"elementType" => "labels",
		"stylers" => [
			[
				"visibility" => "off"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "geometry",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "geometry.fill",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "geometry.stroke",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "labels",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "labels.text",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.attraction",
		"elementType" => "labels.icon",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.business",
		"elementType" => "all",
		"stylers" => [
			[
				"visibility" => "off"
			]
		]
	],
	[
		"featureType" => "poi.medical",
		"elementType" => "geometry",
		"stylers" => [
			[
				"color" => "#fbd3da"
			]
		]
	],
	[
		"featureType" => "poi.park",
		"elementType" => "all",
		"stylers" => [
			[
				"visibility" => "on"
			]
		]
	],
	[
		"featureType" => "poi.park",
		"elementType" => "geometry",
		"stylers" => [
			[
				"color" => "#bde6ab"
			]
		]
	],
	[
		"featureType" => "road",
		"elementType" => "geometry.stroke",
		"stylers" => [
			[
				"visibility" => "off"
			]
		]
	],
	[
		"featureType" => "road",
		"elementType" => "labels",
		"stylers" => [
			[
				"visibility" => "off"
			]
		]
	],
	[
		"featureType" => "road.highway",
		"elementType" => "geometry.fill",
		"stylers" => [
			[
				"color" => "#ffe15f"
			]
		]
	],
	[
		"featureType" => "road.highway",
		"elementType" => "geometry.stroke",
		"stylers" => [
			[
				"color" => "#efd151"
			]
		]
	],
	[
		"featureType" => "road.arterial",
		"elementType" => "geometry.fill",
		"stylers" => [
			[
				"color" => "#ffffff"
			]
		]
	],
	[
		"featureType" => "road.local",
		"elementType" => "geometry.fill",
		"stylers" => [
			[
				"color" => "black"
			]
		]
	],
	[
		"featureType" => "road.local",
		"elementType" => "labels",
		"stylers" => [
			[
				"weight" => "6.18"
			],
			[
				"saturation" => "-51"
			],
			[
				"visibility" => "simplified"
			]
		],
	],
	[
		"featureType" => "transit",
		"elementType" => "all",
		"stylers" => [
			[
				"visibility" => "on"
			]
		],
	],
	[
		"featureType" => "transit.station.airport",
		"elementType" => "geometry.fill",
		"stylers" => [
			[
				"color" => "#cfb2db"
			]
		]
	],
	[
		"featureType" => "water",
		"elementType" => "geometry",
		"stylers" => [
			[
				"color" => "#a2daf2"
			]
		]
	]
];
