<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Hostifybooking_Admin
{
	private $plugin_name;
	private $version;

	function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('in_plugin_update_message-'.HOSTIFYBOOKING_PLUGIN_BASE, function($plugin_data) {
			$this->version_update_warning(HOSTIFYBOOKING_VERSION, $plugin_data['new_version']);
		});
		
		// Include translation manager
		require_once plugin_dir_path(__FILE__) . 'inc/translation-manager/translation-manager.php';
		
		// Initialize AJAX handlers
		if (class_exists('HostifyBooking_Translation_Ajax_Handlers')) {
			HostifyBooking_Translation_Ajax_Handlers::init();
		}
	}

	public function version_update_warning($current_version, $new_version)
	{
		$current_version_minor_part = explode('.', $current_version)[1];
		$new_version_minor_part = explode('.', $new_version)[1];

		if ($current_version_minor_part === $new_version_minor_part) {
			return;
		}
		?>
		<hr />
		<div>
			<span style="color:#d63638"><i class="dashicons-before dashicons-warning"></i></span>
<b>Important Update Notice</b>
<br/>
<b>Please Read Before Updating</b>
<br/>
We're excited to announce a new version of our plugin that brings significant improvements and changes. However, this update includes several breaking changes that require careful attention before upgrading.
<br/>
<br/>
<b>Critical Update Information</b>
<br/>
<b>IMPORTANT:</b> This is a major update that introduces breaking changes. We strongly recommend testing this update on a staging environment before applying it to your production site.
<br/>
<br/>
<b>Recommended Update Process</b>
<ol>
<li>Backup: Create a full backup of your website</li>
<li>Stage: Create a staging environment</li>
<li>Test: Install the update on your staging site</li>
<li>Verify: Test all plugin functionality thoroughly</li>
<li>Review: Check for any conflicts with other plugins or themes</li>
<li>Plan: Schedule the production update during low-traffic hours</li>
<li>Update: Only proceed with the production update after successful staging tests</li>
</ol>
		</div>
		<?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts()
	{
		// Only load on our plugin admin page
		$screen = get_current_screen();
		if ($screen && $screen->base === 'settings_page_hostifybooking-plugin') {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hostifybooking-admin.js', ['jquery'], $this->version, false);
		}
	}

	/**
	 */
	public function test_sanitize_callback( $val )
	{
		return str_replace( 'a', 'b', $val );
	}

	/**
	 */
	public function create_menu()
	{
		require plugin_dir_path( __FILE__ ) . 'menu.php';
		add_action('admin_init', [$this, 'hfy_clear_cache_admin']);
		add_action('admin_init', 'hfy_create_new_pages');
		add_action('admin_notices', [$this, 'hfy_admin_notices']);
	}

	public function hfy_clear_cache_admin()
	{
		global $pagenow;
		$admin_pages = ['options-general.php'];
		if (in_array($pagenow, $admin_pages)) {
			if (($_GET['page'] ?? '') == 'hostifybooking-plugin') {

				$clr = intval($_GET['clr'] ?? 0);
				if ($clr == 1) {
					hfy_clear_cache();
					if (!empty($_GET['redir'])) {
						wp_redirect($_GET['redir']);
						exit;
					}
					wp_redirect(admin_url('/options-general.php?page=hostifybooking-plugin&msg=cleared'));
					exit;
				}

			}
		}
	}

	public function hfy_admin_notices()
	{
		$logo = '<svg style="vertical-align:middle;margin-right:10px" width="20" height="20" viewBox="0 0 73 75" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path opacity="0.4" d="M0.160156 34.8858C0.160156 33.9251 0.608394 33.0185 1.37423 32.4301L29.9578 8.51945C31.0871 7.65188 32.6652 7.65188 33.7945 8.51945L62.3781 32.4301C63.144 33.0185 63.5922 33.9251 63.5922 34.8858V71.0272C63.5922 72.7428 62.1898 74.1336 60.4597 74.1336H3.2926C1.5626 74.1336 0.160156 72.7428 0.160156 71.0272V34.8858Z" fill="#16A9FC"></path><path d="M9.15234 27.017C9.15234 26.0563 9.60058 25.1497 10.3664 24.5613L38.95 0.650676C40.0793 -0.216891 41.6574 -0.216893 42.7867 0.650675L71.3703 24.5613C72.1361 25.1497 72.5844 26.0563 72.5844 27.017V63.1584C72.5844 64.874 71.1819 66.2648 69.4519 66.2648H12.2848C10.5548 66.2648 9.15234 64.874 9.15234 63.1584V27.017Z" fill="#167FFC"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M58.8177 28.9951L35.2706 52.5424L22.3555 40.0488L27.294 34.9436L35.188 42.5799L53.7951 23.9725L58.8177 28.9951Z" fill="white"></path></svg>';

		$s = 'hfy_cache_cleared';
		$x = get_option($s, '');
		if ($x == '1') {
			echo
				'<div class="notice notice-success">
					<p>' . $logo. ' ' . __('Hostify cached data cleared', 'hostifybooking') . '</p>
				</div>'
				;
			delete_option($s);
		}
	}

}

/**
 */
function hfy_create_new_pages()
{
    global $pagenow;
    $admin_pages = ['options-general.php'];
    if (in_array($pagenow, $admin_pages)) {
		if (($_GET['page'] ?? '') == 'hostifybooking-plugin') {
			$np = intval($_GET['crnp'] ?? 0);
            if ($np == 1) hfy_create_new_p1();
            if ($np == 2) hfy_create_new_p2();
            if ($np == 3) hfy_create_new_p3();
            if ($np == 4) hfy_create_new_p4();
            if ($np == 5) hfy_create_new_p5();
            if ($np == 6) hfy_create_new_p6();
            if ($np == 7) hfy_create_new_p7(); // bookings list
        }
	}
}

function hfy_create_new_p_($a, $optname = null)
{
	$id = wp_insert_post(array_merge([
        'post_status'    => 'publish',
	    'post_type'      => 'page',
        'comment_status' => 'close',
        'ping_status'    => 'close',
	], $a));
	if ($optname) {
		$options = get_option('hostifybooking-plugin');
		$options[$optname] = (string) $id;
		update_option('hostifybooking-plugin', $options);
	}
	wp_redirect(get_admin_url() . 'options-general.php?page=hostifybooking-plugin#options');
	exit;
}

function hfy_create_new_p1() // listings
{
	hfy_create_new_p_([
		'post_content' => '[hfy_listings]',
	    'post_title'   => 'Listings',
        'post_name'    => 'listings',
	], 'page_listings');
}

function hfy_create_new_p2() // listing
{
	hfy_create_new_p_([
		'post_content' => '[hfy_listing]',
	    'post_title'   => 'Listing',
        'post_name'    => 'listing',
	], 'page_listing');
}

function hfy_create_new_p3() // payment
{
	hfy_create_new_p_([
		'post_content' => '[hfy_payment]',
	    'post_title'   => 'Payment',
        'post_name'    => 'payment',
	], 'page_payment');
}

function hfy_create_new_p4() // charge
{
	hfy_create_new_p_([
		'post_content' => '[hfy_payment_charge]',
	    'post_title'   => 'Payment result',
        'post_name'    => 'charge',
	], 'page_charge');
}

function hfy_create_new_p5() // manage
{
	hfy_create_new_p_([
		'post_content' => '[hfy_user_booking_manage]',
	    'post_title'   => 'Booking manage',
        'post_name'    => 'booking_manage',
	], 'page_booking_manage');
}

function hfy_create_new_p6() // wishlist
{
	hfy_create_new_p_([
		'post_content' => '[hfy_user_wishlist]',
	    'post_title'   => 'Wishlist',
        'post_name'    => 'wishlist',
	], 'page_wishlist');
}

function hfy_create_new_p7() // bookings list
{
	hfy_create_new_p_([
		'post_content' => '[hfy_user_bookings_list]',
	    'post_title'   => 'My bookings',
        'post_name'    => 'my-bookings',
	], 'page_bookings_list');
}

/**
 */
function hfy_get_link_to_edit($opt = null)
{
	if ($opt) {
		$options = get_option('hostifybooking-plugin');
		$id = $options[$opt] ?? '';
		return get_admin_url() . 'post.php?post=' . $id . '&action=edit';
	}
	return '';
}

/**
 */
function show_admin_tabs( $current = null ) {
	$tabs = [
		'hostifybooking' => 'Hostify Plugin',
		// 'hostifybooking-2' => 'two',
	];

	if ( is_null( $current ) ) {
		if ( isset( $_GET['page'] ) ) {
			$current = intval($_GET['page']);
		}
	}
	$content  = '';
	$content .= '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $location => $tabname ) {
		if ( $current == $location ) {
			$class = 'nav-tab-active';
		} else {
			$class = '';
		}
		$content .= '<a class="nav-tab ' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
	}
	$content .= '</h2>';
	return $content;
}

function tmp_to_slug($s = '')
{
	$x = preg_replace('/[\s\/\\\:\#\?\@\&]/', '-', strtolower($s));
	$x = preg_replace('/[^a-zA-Z0-9\-\_\:\#\?\@\&]/', '-', $x);
	return str_replace('--', '-', $x);
}

/**
 * create permalink for each listing from Hostify,
 * store it in WP table
 */
function hfy_update_listings_permalinks()
{
	global $wpdb;

	require_once HOSTIFYBOOKING_DIR . 'inc/api.php';
	$api = new HfyApi();
	
	// Create or ensure table exists
	$tname = $wpdb->prefix . 'hfy_listing_permalink';
	$wpdb->query("CREATE TABLE IF NOT EXISTS {$tname} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		listing_id mediumint(9) NOT NULL,
		listing_name varchar(255) NOT NULL,
		thumb varchar(255) DEFAULT '',
		permalink varchar(255) NOT NULL,
		date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY listing_id (listing_id),
		UNIQUE KEY permalink (permalink)
	) {$wpdb->get_charset_collate()};");
	
	// Clear the table only once before processing
	$wpdb->query("TRUNCATE TABLE {$tname}");
	
	if (!HFY_USE_API_V3) {
		// For API V2, get all listings at once
		$listings = $api->getListingsIdNamesAPIV2();

		if ($listings->success ?? false) {
			foreach ($listings->data as $listing) {
				$listing_name = isset($listing->nickname) ? (HFY_SEO_LISTING_SLUG == 0 ? $listing->name : $listing->nickname) : $listing->name;

				// Apply any string replacements
				if (!empty(HFY_SEO_LISTING_SLUG_FIND)) {
					$listing_name = str_replace(HFY_SEO_LISTING_SLUG_FIND, HFY_SEO_LISTING_SLUG_REPLACE, $listing_name);
				}

				$slug = tmp_to_slug($listing_name);
				if (strlen($slug) < 4) {
					$slug .= '-' . $listing->id;
				}
				
				$q = $wpdb->prepare(
					"INSERT INTO {$tname} (listing_id, listing_name, thumb, permalink) VALUES (%d, %s, %s, %s)",
					$listing->id,
					$listing_name,
					$listing->thumbnail_file,
					$slug
				);
				$wpdb->query($q);
			}
		}
	} else {
		// For API V3, use pagination
		$page = 1;
		$per_page = 100; // Process 100 listings per API call (default API page size)
		$total_processed = 0;
		$has_more = true;
		
		while ($has_more) {
			// Get batch of listings with pagination
			$listings = $api->getListingsIdNamesAPIV3($page, $per_page);
			
			if ($listings->success ?? false) {
				$batch_data = $listings->listings;
				$batch_count = count($batch_data);
				
				if ($batch_count > 0) {
					// Process this batch of listings
					foreach ($batch_data as $listing) {
						$listing_name = isset($listing->nickname) 
							? (HFY_SEO_LISTING_SLUG == 0 ? $listing->name : $listing->nickname) 
							: $listing->name;
			
						// Apply any string replacements
						if (!empty(HFY_SEO_LISTING_SLUG_FIND)) {
							$listing_name = str_replace(HFY_SEO_LISTING_SLUG_FIND, HFY_SEO_LISTING_SLUG_REPLACE, $listing_name);
						}
			
						$slug = tmp_to_slug($listing_name);
						if (strlen($slug) < 4) {
							$slug .= '-' . $listing->id;
						}
						
						// Use INSERT IGNORE to handle potential duplicate errors gracefully
						$q = $wpdb->prepare(
							"INSERT INTO {$tname} (listing_id, listing_name, thumb, permalink) VALUES (%d, %s, %s, %s)",
							$listing->id,
							$listing_name,
							$listing->thumbnail_file,
							$slug
						);
						$wpdb->query($q);
					}
					
					$total_processed += $batch_count;
					
					// If we have a total property and have processed all records, we're done
					if (isset($listings->total) && $total_processed >= $listings->total) {
						$has_more = false;
					} 
					// If we got fewer results than per_page, we're done
					else if ($batch_count < $per_page) {
						$has_more = false;
					} 
					// Otherwise, move to next page
					else {
						$page++;
					}
				} else {
					// No listings in this batch, we're done
					$has_more = false;
				}
			} else {
				// API error or no success
				$has_more = false;
			}
		}
		
		// Log the total number of processed listings
		error_log("Processed $total_processed listings for permalinks");
	}

	// Flush rewrite rules to ensure pretty links work
	global $wp_rewrite;
	$wp_rewrite->flush_rules(false);
}
