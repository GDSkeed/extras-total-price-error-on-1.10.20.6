<?php
/**
 * Class for SEO Table Permalinks functionality
 *
 * @package Hostify_Booking
 * @subpackage Admin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle SEO table permalinks functionality
 */
class HFY_SEO_Table_Permalinks {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Create database table if it doesn't exist
        $this->create_table();
        
        // Initialize the table
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_hfy_get_permalinks_data', array($this, 'get_permalinks_data'));
        add_action('wp_ajax_hfy_update_permalink', array($this, 'update_permalink'));
        add_action('wp_ajax_hfy_delete_permalink', array($this, 'delete_permalink'));
        add_action('wp_ajax_hfy_regenerate_permalinks', array($this, 'regenerate_permalinks'));
    }

    /**
     * Create the permalinks database table if it doesn't exist
     */
    private function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hfy_listing_permalink';
        
        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table doesn't exist, create it
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                listing_id mediumint(9) NOT NULL,
                listing_name varchar(255) NOT NULL,
                thumb varchar(255) DEFAULT '',
                permalink varchar(255) NOT NULL,
                date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY listing_id (listing_id),
                UNIQUE KEY permalink (permalink)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Enqueue necessary assets for the table
     */
    public function enqueue_assets($hook) {
        // Only load on plugin options page
        if (strpos($hook, 'hostifybooking-plugin') === false) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'hfy-seo-table-permalinks-css',
            HOSTIFYBOOKING_URL . 'admin/inc/seo-table-permalinks/css/seo-table-permalinks.css',
            array(),
            HOSTIFYBOOKING_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'hfy-seo-table-permalinks-js',
            HOSTIFYBOOKING_URL . 'admin/inc/seo-table-permalinks/js/seo-table-permalinks.js',
            array('jquery'),
            HOSTIFYBOOKING_VERSION,
            true
        );

        // Get the site URL for building full permalinks
        $site_url = trailingslashit(get_site_url());

        // Pass data to JS
        wp_localize_script(
            'hfy-seo-table-permalinks-js',
            'hfyPermalinksData',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('hfy_permalinks_nonce'),
                'site_url' => $site_url,
                'use_nickname' => defined('HFY_SEO_LISTING_SLUG') && HFY_SEO_LISTING_SLUG == 1,
                'strings' => array(
                    'loading' => __('Loading permalinks data...', 'hostifybooking'),
                    'no_data' => __('No permalinks found.', 'hostifybooking'),
                    'confirm_delete' => __('Are you sure you want to delete this permalink?', 'hostifybooking'),
                    'delete' => __('Delete', 'hostifybooking'),
                    'edit' => __('Edit', 'hostifybooking'),
                    'save' => __('Save', 'hostifybooking'),
                    'cancel' => __('Cancel', 'hostifybooking'),
                    'error' => __('An error occurred.', 'hostifybooking'),
                    'success' => __('Permalink updated successfully.', 'hostifybooking'),
                    'regenerating' => __('Regenerating...', 'hostifybooking'),
                    'regenerate_success' => __('Permalinks regenerated successfully!', 'hostifybooking'),
                    'regenerate_error' => __('Error regenerating permalinks.', 'hostifybooking'),
                    'regenerate_button' => __('Regenerate Permalinks', 'hostifybooking'),
                    'search' => __('Search', 'hostifybooking'),
                    'search_placeholder' => __('Search by ID or name...', 'hostifybooking'),
                    'clear' => __('Clear', 'hostifybooking'),
                    'no_results' => __('No matching permalinks found.', 'hostifybooking'),
                    'found_count' => __('Found %d out of %d total', 'hostifybooking')
                )
            )
        );
    }

    /**
     * Get permalinks data for the table
     */
    public function get_permalinks_data() {
        // Check nonce
        check_ajax_referer('hfy_permalinks_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'hostifybooking'));
        }
        
        // Track server processing time
        $start_time = microtime(true);

        global $wpdb;
        $table_name = $wpdb->prefix . 'hfy_listing_permalink';

        // Check if we have a search query
        $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
        
        // Get total count for all records
        $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        
        // Add search condition if search query is provided
        $where_clause = '';
        $found_count = $total_count;
        
        if (!empty($search_query)) {
            $where_clause = $wpdb->prepare(
                "WHERE listing_id = %d OR listing_name LIKE %s OR permalink LIKE %s",
                is_numeric($search_query) ? intval($search_query) : 0,
                '%' . $wpdb->esc_like($search_query) . '%',
                '%' . $wpdb->esc_like($search_query) . '%'
            );
            
            // Get count of filtered records
            $found_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} {$where_clause}");
        }

        // Get data from the database with search filter
        $permalinks = $wpdb->get_results("SELECT * FROM {$table_name} {$where_clause} ORDER BY listing_id ASC", ARRAY_A);

        // Get listing names for each permalink
        foreach ($permalinks as &$permalink) {
            // Generate a name based on the permalink
            $name = str_replace('-', ' ', $permalink['permalink']);
            $name = ucwords($name); // Capitalize each word
            $permalink['listing_name'] = $name;
            
            // Ensure the permalink is properly formatted
            // If you need to add a specific path structure (like /listings/), do it here
            $permalink['permalink_url'] = trailingslashit(get_site_url()) . $permalink['permalink'];
        }
        
        // Calculate the server processing time
        $server_time = round(microtime(true) - $start_time, 3);

        // Send the response
        wp_send_json_success([
            'permalinks' => $permalinks,
            'total_count' => intval($total_count),
            'found_count' => intval($found_count),
            'search_query' => $search_query,
            'server_time' => $server_time
        ]);
    }

    /**
     * Update permalink
     */
    // public function update_permalink() {
    //     // Check nonce
    //     check_ajax_referer('hfy_permalinks_nonce', 'nonce');

    //     // Check permissions
    //     if (!current_user_can('manage_options')) {
    //         wp_send_json_error(__('You do not have permission to perform this action.', 'hostifybooking'));
    //     }

    //     // Get the data
    //     $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
    //     $permalink = isset($_POST['permalink']) ? sanitize_title($_POST['permalink']) : '';

    //     if (empty($listing_id) || empty($permalink)) {
    //         wp_send_json_error(__('Invalid data.', 'hostifybooking'));
    //     }

    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'hfy_listing_permalink';

    //     // Check if this permalink already exists
    //     $exists = $wpdb->get_var($wpdb->prepare(
    //         "SELECT COUNT(*) FROM {$table_name} WHERE permalink = %s AND listing_id != %d",
    //         $permalink,
    //         $listing_id
    //     ));

    //     if ($exists) {
    //         wp_send_json_error(__('This permalink is already in use. Please choose another one.', 'hostifybooking'));
    //     }

    //     // Check if this listing already has a permalink
    //     $has_permalink = $wpdb->get_var($wpdb->prepare(
    //         "SELECT COUNT(*) FROM {$table_name} WHERE listing_id = %d",
    //         $listing_id
    //     ));

    //     if ($has_permalink) {
    //         // Update existing permalink
    //         $result = $wpdb->update(
    //             $table_name,
    //             array('permalink' => $permalink),
    //             array('listing_id' => $listing_id)
    //         );
    //     } else {
    //         // Insert new permalink
    //         $result = $wpdb->insert(
    //             $table_name,
    //             array(
    //                 'listing_id' => $listing_id,
    //                 'permalink' => $permalink
    //             )
    //         );
    //     }

    //     if ($result === false) {
    //         wp_send_json_error(__('Failed to update permalink.', 'hostifybooking'));
    //     }

    //     wp_send_json_success(array(
    //         'message' => __('Permalink updated successfully.', 'hostifybooking'),
    //         'permalink' => $permalink
    //     ));
    // }

    /**
     * Delete permalink
     */
    // public function delete_permalink() {
    //     // Check nonce
    //     check_ajax_referer('hfy_permalinks_nonce', 'nonce');

    //     // Check permissions
    //     if (!current_user_can('manage_options')) {
    //         wp_send_json_error(__('You do not have permission to perform this action.', 'hostifybooking'));
    //     }

    //     // Get the data
    //     $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;

    //     if (empty($listing_id)) {
    //         wp_send_json_error(__('Invalid data.', 'hostifybooking'));
    //     }

    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'hfy_listing_permalink';

    //     // Delete the permalink
    //     $result = $wpdb->delete(
    //         $table_name,
    //         array('listing_id' => $listing_id)
    //     );

    //     if ($result === false) {
    //         wp_send_json_error(__('Failed to delete permalink.', 'hostifybooking'));
    //     }

    //     wp_send_json_success(array(
    //         'message' => __('Permalink deleted successfully.', 'hostifybooking')
    //     ));
    // }

    /**
     * Regenerate permalinks for all listings
     */
    public function regenerate_permalinks() {
        // Check nonce
        check_ajax_referer('hfy_permalinks_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to perform this action.', 'hostifybooking'));
        }

        try {
            // Get initial count for comparison
            global $wpdb;
            $table_name = $wpdb->prefix . 'hfy_listing_permalink';
            $initial_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
            
            // Track the start time
            $start_time = microtime(true);
            
            // Call the main function that handles listing permalinks with pagination
            hfy_update_listings_permalinks();
            
            // Calculate processing time
            $processing_time = round(microtime(true) - $start_time, 2);
            
            // Count the number of permalinks after regeneration
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
            $new_items = $count - ($initial_count ?? 0);
            
            wp_send_json_success(array(
                'message' => __('Permalinks regenerated successfully.', 'hostifybooking'),
                'count' => intval($count),
                'new_items' => max(0, $new_items),
                'processing_time' => $processing_time
            ));
        } catch (Exception $e) {
            wp_send_json_error(__('An error occurred while regenerating permalinks: ', 'hostifybooking') . $e->getMessage());
        }
    }

    /**
     * Get listings from the Hostify API or database
     * This method should be implemented to fetch actual listing data
     * 
     * @return array Array of listings with 'id' and 'name' keys
     */
    private function get_listings_from_api() {
        // This method is no longer used as we're getting listings from the database directly
        return array();
    }
}

// Initialize the class
$hfy_seo_table_permalinks = new HFY_SEO_Table_Permalinks(); 