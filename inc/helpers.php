<?php

if (!defined('WPINC')) die;

if (!function_exists('getopt_')) {
	function getopt_($opts, $name)
	{
		return isset($opts[$name]) ? $opts[$name] : '';
	}
}

if (!function_exists('getopt_c_')) {
	function getopt_c_($opts, $name, $valname = 'color')
	{
		return isset($opts[$name]) ? ($opts[$name]['use'] == 'yes'
			? (isset($opts[$name][$valname]) ? $opts[$name][$valname] : false)
			: false
		) : false;
	}
}

if (!function_exists('hfy_get_')) {
	function hfy_get_($name, $def = '')
	{
		$x = isset($_GET[$name]) ? (is_array($_GET[$name]) ? array_map('sanitize_text_field', $_GET[$name]) : sanitize_text_field($_GET[$name])
		) : $def;
		if (gettype($def) !== gettype($x)) settype($x, gettype($def));
		return $x;
	}
}

if (!function_exists('hfy_get_sess_')) {
	function hfy_get_sess_($name, $def = '')
	{
		return isset($_SESSION[$name]) ? $_SESSION[$name] : $def;
	}
}

if (!function_exists('hfy_get_vars_')) {
	function hfy_get_vars_($names = [], $int = false, $object = true)
	{
		$out = [];
		foreach ($names as $n) {
			$s = get_query_var($n);
			
			// For certain parameters like keyword, also check $_GET if get_query_var returns empty
			if (empty($s) && in_array($n, ['keyword', 'custom_search', 'neighbourhood'])) {
				$s = $_GET[$n] ?? '';
			}
			
			if ($n == 'guests' || $n == 'adults') {
				$out[$n] = $s < 1 || !is_numeric($s) ? 1 : (int) $s;
			}
			else if ($n == 'children' || $n == 'infants' || $n == 'pets') {
				$out[$n] = $s < 1 || !is_numeric($s) ? 0 : (int) $s;
			}
			else if ($n == 'long_term_mode') {
				$s = (int) $s;
				$out[$n] = $s < 1 ? HFY_LONG_TERM_DEFAULT : $s;
			}
			else {
				$out[$n] = is_array($s) ? array_map('sanitize_text_field', $s) : sanitize_text_field($s);
			}
			if ($int) {
				$out[$n] = (int) $out[$n];
			}
		}
		return $object ? (object) $out : $out;
	}
}

if (!function_exists('hfy_get_vars_def')) {
	function hfy_get_vars_def()
	{
		$a = hfy_get_vars_([
			'start_date',
			'end_date',
		], false, false);
		$ai = hfy_get_vars_([
			'id',
			'guests',
			'adults',
			'children',
			'infants',
			'pets',
			'bedrooms',
			'sort',
			'discount_code',
		], true, false);
		return (object) array_merge($a, $ai);
	}
}

if (!function_exists('hfy_post_vars_')) {
	function hfy_post_vars_($names = [])
	{
		$out = [];
		foreach ($names as $n) {
			$out[$n] = isset($_POST[$n]) ? (is_array($_POST[$n]) ? array_map('sanitize_text_field', $_POST[$n]) : sanitize_text_field($_POST[$n])
			) : '';
		}
		return (object) $out;
	}
}

if (!function_exists('hfy_tpl')) {
	function hfy_tpl($name = '')
	{
		// skip 'listing/listings-item-map'
		if ($name == 'listing/listings-item-map') return ''; // since v.1.8.1

		/* $name like 'listing/listing-amenities' */
		if (empty($name)) return '';
		$hfy_options = get_option('hostifybooking-plugin');
		$tpl_path = trim($hfy_options['hfy_tpl_path'] ?? ''); // from options
		if (empty($tpl_path)) {
			// from themes dir
			if (!empty(locate_template(["hostify-booking-templates/$name.php"]))) {
				$tpl_path = locate_template(["hostify-booking-templates/"]);
			}
		}
		if (empty($tpl_path) || (HFY_DISABLE_TEMPLATES_PAYMENT_OVERRIDE && preg_match('/payment\//i', $name))) {
			$tpl_path = HOSTIFYBOOKING_DIR . 'tpl/'; // default - from plugin's tpl dir
		}
		$file = $tpl_path . $name . '.php';
		$file_filtered = apply_filters('hfy_tpl_path', [
			'file' => $file, 'path' => $tpl_path, 'name' => $name
		]);
		return (!is_array($file_filtered) && file_exists($file_filtered)) ? $file_filtered : $file;
	}
}

if (!function_exists('arrayMoveSelectedToTop')) {
	function arrayMoveSelectedToTop(&$arr, $sel)
	{
		if (!empty($arr) && !empty($sel)) {
			$a = [];
			foreach ($sel as $id) {
				if (isset($arr[$id])) {
					$a[$id] = $arr[$id];
					unset($arr[$id]);
				}
			}
			foreach ($arr as $k => $m) {
				$a[$k] = $m;
			}
			$arr = $a;
		}
	}
}

if (!function_exists('hfyGetCurrentLang')) {
	function hfyGetCurrentLang()
	{
		return strtolower(substr(get_bloginfo('language'), 0, 2));
	}
}

if (!function_exists('hfyUseGA')) {
	function hfyUseGA()
	{
		$options = get_option('hostifybooking-plugin');
		$x = $options['seo_events'] ?? 'yes';
		return $x == 'yes' || $x == 1;
	}
}

if (!function_exists('hfyMapLoc')) {
	function hfyMapLoc()
	{
		$options = get_option('hostifybooking-plugin');
		return ($options['map_loc_circle'] ?? 'yes') == 'yes';
	}
}

if (!function_exists('hfyConvertPHPToMomentFormat')) {
	function hfyConvertPHPToMomentFormat($format)
	{
		$out = preg_replace(
			['/[\\\](.{1})/'],
			['[$1]'],
			$format, -1
		);
		$repl = [
			't' => '',       // days in the month => moment().daysInMonth();
			'B' => '',       // Swatch internet time (.beats), no equivalent
			'Z' => '',       // time zone offset in minutes => moment().zone();
			'I' => '',       // Daylight Saving Time? => moment().isDST();
			'L' => '',       // Leap year? => moment().isLeapYear();
			'Y' => 'YYYY',
			'A' => 'A',      // for the sake of escaping below
			'a' => 'a',      // for the sake of escaping below
			'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
			'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
			'M' => 'MMM',
			'F' => 'MMMM',
			'm' => 'MM',
			'H' => 'HH',
			'G' => 'H',
			'g' => 'h',
			'h' => 'hh',
			'i' => 'mm',
			'l' => 'dddd',
			'N' => 'E',
			'n' => 'M',
			'O' => 'ZZ',
			// 'o' => 'YYYY',
			'P' => 'Z',
			'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
			'S' => 'o',
			's' => 'ss',
			'T' => 'z',      // deprecated since version 1.6.0 of moment.js
			'U' => 'X',
			'u' => 'SSSSSS', // microseconds
			'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
			'W' => 'W',      // for the sake of escaping below
			'w' => 'e',
			// 'D' => 'ddd',
			'd' => 'DD',
			'z' => 'DDD',
			'j' => 'D',
			// 'y' => 'YY',
		];
		$fin = [];
		$rep = [];
		foreach ($repl as $from => $to) {
			$fin[] = '/([^\[]{1})'.$from.'/';
			$rep[] = '$1'.$to;
			$fin[] = '/^'.$from.'/';
			$rep[] = $to;
		}
		$out = preg_replace($fin, $rep, $out);
		return $out;
	}
}

if (!function_exists('hfyGetDateFormatJS')) {
	function hfyGetDateFormatJS()
	{
		return hfyConvertPHPToMomentFormat(get_option('date_format'));
	}
}

if (!function_exists('hfyParseNeighbourhood')) {
	function hfyParseNeighbourhood($s = '')
	{
		$s = sanitize_text_field($s);
		$x = explode(':', $s);
		if (count($x)) {
			[$country, $state, $city_id, $neighbourhood] = array_pad($x, 4, null);
			return (object) [
				'sanitized' => $s,
				'country' => $country ?? '',
				'state' => $state ?? '',
				'city_id' => (int) ($city_id ?? 0),
				'neighbourhood' => $neighbourhood ?? '',
			];
		}
		return (object) [
			'sanitized' => $s,
			'country' => '',
			'state' => '',
			'city_id' => 0,
			'neighbourhood' => $s,
		];
	}
}

if (!function_exists('hfyFilterLocations'))
{
    function hfyFilterLocations($settings, $filter = '', $selector_type = 0)
    {
        $filter = trim($filter);
        if (empty($settings)) return $settings->locations_array;
        [$filter_country, $filter_state, $filter_city, $filter_neigh] = array_pad(explode(':', $filter, 4), 4, null);
        $out = [];
        foreach ($settings->locations as $country => $states) {
            if ($filter_country && $filter_country !== $country) continue;
            if ($selector_type == 1 || $selector_type == 3) $out[$country] = $country;
            foreach ($states as $state => $cities) {
                if ($filter_state && $filter_state !== $state) continue;
				if ($selector_type == 1 || $selector_type == 2) $out[$country . ':' . $state] = $country . ', ' . $state;
                foreach ($cities as $city_name => $city) {
                    if ($filter_city && $filter_city !== $city_name) continue;
                    $x = implode(':', [$country, $state, isset($city->id) ? $city->id : ($city[0] ?? 0)]);
                    // by HFY_LOCATIONS_SELECTOR
					$a = [$city_name];
					if ($selector_type == 1) {
						$a = [$country, $state, $city_name];
					} else if ($selector_type == 2) {
						$a = [$state, $city_name];
					} else if ($selector_type == 3) {
						$a = [$country, $city_name];
					}
                    $xn = implode(', ', $a);
                    $out[$x] = $xn;
					if ($selector_type > 0) { // add neigh
						if (isset($city->neighs)) foreach ($city->neighs as $neigh) {
							if ($filter_neigh && $filter_neigh !== $neigh) continue;
							$out[$x . ':' . $neigh] = $selector_type == 5
								? $neigh . ', ' . $xn
								: $xn . ', ' . $neigh;
						}
					}
                }
            }
        }
		asort($out);
        return $out;
    }
}

/**
 * sort listings:
 *   1 - Shows the top/selected properties first, price desc (default)
 *   2 - price asc
 *   3 - price desc
 */

if (!function_exists('hfySortListings_fn'))
{
	function hfySortListings_fn($a, $b)
	{
		if ($a->price == $b->price) return 0;
		return ($a->price < $b->price) ? -1 : 1;
	}
}

if (!function_exists('hfySortListings_fn_desc'))
{
	function hfySortListings_fn_desc($a, $b)
	{
		if ($a->price == $b->price) return 0;
		return ($a->price > $b->price) ? -1 : 1;
	}
}

if (!function_exists('fix_check_time'))
{
	function fix_check_time($txt = '')
	{
		$txt = trim($txt);
		if (strpos($txt, ':') !== false) {
			$txt = substr($txt, 0, 5);
			if ($txt == '00:00') $txt = __('Any time', 'hostifybooking');
		}
		return $txt;
	}
}

if (!function_exists('hfyDateFormatOpt'))
{
	function hfyDateFormatOpt($str = '')
	{
		$d = DateTime::createFromFormat('d-m-Y', $str);
		if ($d) return wp_date(get_option('date_format'), $d->getTimestamp());
		return $str;
	}
}

if (!function_exists('hfyIncludeMaps'))
{
function hfyIncludeMaps($settings)
{
	global $HostifyBookingPlugin;
	if (isset($HostifyBookingPlugin)) {
		include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';
		if (HFY_MAP_CLUSTERS) {
			// $HostifyBookingPlugin->loader->add_script('hfygmapsc', 'https://unpkg.com/@googlemaps/markerclusterer@2.3.1/dist/index.min.js');
			$HostifyBookingPlugin->loader->add_script('hfygmapsc', 'res/lib/markerclusterer.js');
		}
		
		// Get the shortcode being used - Check if we're in the BACKTRACE for a specific shortcode
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
		$in_listings_map = false;
		foreach ($backtrace as $trace) {
			if (isset($trace['file']) && strpos($trace['file'], 'hfy-listings-map.php') !== false) {
				$in_listings_map = true;
				break;
			}
		}
		
		// Always use initializeMap for the listings map
		if ($in_listings_map) {
			$callback = 'initializeMap';
			// Use the user-provided key if available, otherwise use predefined key
			$api_key = !empty(HFY_GOOGLE_MAPS_API_KEY) ? HFY_GOOGLE_MAPS_API_KEY : 
				(!empty($settings->api_key_maps) ? $settings->api_key_maps : '');
		}
		// For single listing location map
		else if (HFY_DYNAMIC_GOOGLE_MAP && !empty(HFY_GOOGLE_MAPS_API_KEY)) {
			// For dynamic maps with user-provided key, use initializeMap callback
			$callback = 'initializeMap';
			$api_key = HFY_GOOGLE_MAPS_API_KEY;
		} else {
			// For static maps or if no user key is provided, use initGmap3 callback
			$callback = 'initGmap3';
			// Use the predefined key from settings if available, otherwise use user key if it exists
			$api_key = !empty($settings->api_key_maps) ? $settings->api_key_maps : 
				(defined('HFY_GOOGLE_MAPS_API_KEY') ? HFY_GOOGLE_MAPS_API_KEY : '');
		}
		
		// Ensure we have a valid API key to use
		if (!empty($api_key)) {
			$HostifyBookingPlugin->loader->add_script('hfygmaps', 'https://maps.googleapis.com/maps/api/js?key='.$api_key.'&v=3.exp&callback='.$callback.'&loading=async', ['jquery', 'hfyfn'], true);
			
			// Make API key available to JavaScript
			wp_add_inline_script('hfyfn', 'window.predefinedMapApiKey = "' . esc_js($api_key) . '";', 'before');
		}
	}
}
}

if (!function_exists('hfyGetLinkWithPage'))
{
	function hfyGetLinkWithPage($pages, $page = 1)
	{
		if (isset($pages->link)) {
			return str_replace('{page}', $page, $pages->link);
		}
		return '';
	}
}

if (!function_exists('hfyPaypalSupportedCurrencies'))
{
	function hfyPaypalSupportedCurrencies($isoCode = 'USD')
	{
		return in_array(strtoupper($isoCode), [
			'AUD',
			'BRL',
			'CAD',
			'CZK',
			'DKK',
			'EUR',
			'HKD',
			'HUF',
			'ILS',
			'JPY',
			'MXN',
			'TWD',
			'NZD',
			'NOK',
			'PHP',
			'PLN',
			'GBP',
			'SGD',
			'SEK',
			'CHF',
			'THB',
			'USD',
		]);
	}
}

if (!function_exists('hfy_ltm_fix_')) {
	function hfy_ltm_fix_($ltm = null)
	{
		$ltm = (int) $ltm;
		if ($ltm == 0) $ltm = HFY_LONG_TERM_DEFAULT;
		else if ($ltm < 1) $ltm = 1;
		else if ($ltm > 2) $ltm = 2;
		return $ltm;
	}
}

if (!function_exists('hfy_no_listing')) {
	function hfy_no_listing()
	{
		if (HFY_REDIRECT_NO_LISTING == 1) {
			wp_redirect(HFY_PAGE_LISTINGS_URL);
			exit();
		} else if (HFY_REDIRECT_NO_LISTING == 2) {
			wp_redirect(home_url());
			exit();
		} else if (HFY_REDIRECT_NO_LISTING == 3 && !empty(HFY_REDIRECT_NO_LISTING_URL)) {
			wp_redirect(HFY_REDIRECT_NO_LISTING_URL);
			exit();
		}
		throw new Exception(__('No listing ID', 'hostifybooking'));
	}
}

if (!function_exists('hfyGetLocationCoordinatesFromAPI')) {
	function hfyGetLocationCoordinatesFromAPI($location, $cities = [])
	{
		if (!empty($cities)) {
			foreach ($cities as $city) {
				if (isset($city->name) && strcasecmp(trim($city->name), trim($location)) === 0) {
					if (isset($city->lat) && isset($city->lng)) {
						return ['lat' => floatval($city->lat), 'lng' => floatval($city->lng)];
					}
				}
				
				if (isset($city->name) && stripos($city->name, $location) !== false) {
					if (isset($city->lat) && isset($city->lng)) {
						return ['lat' => floatval($city->lat), 'lng' => floatval($city->lng)];
					}
				}
			}
		}
		
		return null;
	}
}