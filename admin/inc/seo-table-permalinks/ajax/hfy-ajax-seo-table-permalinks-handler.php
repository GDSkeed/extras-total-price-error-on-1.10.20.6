<?php
/**
 * AJAX handler for SEO Table Permalinks
 *
 * @package Hostify_Booking
 * @subpackage Admin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle AJAX requests for SEO permalinks table
 * 
 * Note: This is a helper file for any additional AJAX functionality.
 * The main AJAX handlers are already included in the main class-seo-table-permalinks.php file.
 */
class HFY_SEO_Table_Permalinks_AJAX {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Additional AJAX handlers can be added here if needed
    }

    /**
     * Helper method to get listing details from the API
     * 
     * @param int $listing_id The listing ID
     * @return array|WP_Error Listing details or error
     */
    public static function get_listing_details($listing_id) {
        // TODO: Implement actual API call to fetch listing details
        // This should call the Hostify API to get detailed information for the specified listing
        // Return format should be an array with at least 'id' and 'name' keys
        
        // Return empty array for development
        return [];
    }

    /**
     * Helper method to sanitize permalink
     * 
     * @param string $permalink The permalink to sanitize
     * @return string Sanitized permalink
     */
    public static function sanitize_permalink($permalink) {
        // Convert to lowercase
        $permalink = strtolower($permalink);
        
        // Remove special characters and replace spaces with hyphens
        $permalink = sanitize_title($permalink);
        
        // Remove any characters that might cause issues in URLs
        $permalink = preg_replace('/[^a-z0-9\-]/', '', $permalink);
        
        // Remove multiple hyphens
        $permalink = preg_replace('/-+/', '-', $permalink);
        
        // Trim hyphens from beginning and end
        $permalink = trim($permalink, '-');
        
        return $permalink;
    }
}

// Initialize AJAX handler if needed
// new HFY_SEO_Table_Permalinks_AJAX(); 