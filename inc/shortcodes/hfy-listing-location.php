<?php

if ( ! defined( 'WPINC' ) ) die;

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-settings.php';

// wp_enqueue_script( 'hfygmaps' );
// wp_enqueue_script( 'hfygmap3' );
hfyIncludeMaps($settings);

require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

$prm = hfy_get_vars_def();

$id = $prm->id && empty($id) ? $prm->id : $id;

if (empty($id)) {
	throw new Exception(__('No listing ID', 'hostifybooking'));
}

$guests = $prm->guests;
$adults = $prm->adults;
$children = $prm->children;
$infants = $prm->infants;

$startDate = $prm->start_date;
$endDate = $prm->end_date;

include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-listing.php';
include HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/make-listing-tpl-vars.php';

// Add necessary JavaScript variables for map initialization
if (isset($listing->lat) && isset($listing->lng)) {
    // Pass coordinates and map type to JS
    wp_add_inline_script('hfyfn', 'const lat = ' . floatval($listing->lat) . ';', 'before');
    wp_add_inline_script('hfyfn', 'const lng = ' . floatval($listing->lng) . ';', 'before');
    wp_add_inline_script('hfyfn', 'const hfyMapType = "' . (defined('HFY_MAP_TYPE') ? esc_js(HFY_MAP_TYPE) : 'roadmap') . '";', 'before');
    
    // Always pass the predefined API key to JavaScript for static maps
    // This ensures it's available even if a user hasn't provided their own key
    if (!empty($settings->api_key_maps)) {
        wp_add_inline_script('hfyfn', 'const predefinedMapApiKey = "' . esc_js($settings->api_key_maps) . '";', 'before');
    }
    
    // Override the HFY_DYNAMIC_GOOGLE_MAP constant if user has not provided their own API key
    // This ensures we only use dynamic maps when user explicitly enabled it AND provided their own key
    if (HFY_DYNAMIC_GOOGLE_MAP && empty(HFY_GOOGLE_MAPS_API_KEY)) {
        // Log message in console to help with debugging
        wp_add_inline_script('hfyfn', 'console.log("Dynamic map requested but no user API key provided. Using static map with predefined key instead.");', 'before');
    }
}

include hfy_tpl('listing/listing-location');
