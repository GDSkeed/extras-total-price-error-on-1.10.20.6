<?php

// Include SEO Table Permalinks functionality
require_once HOSTIFYBOOKING_DIR . 'admin/inc/seo-table-permalinks/class-seo-table-permalinks.php';
require_once HOSTIFYBOOKING_DIR . 'admin/inc/seo-table-permalinks/ajax/hfy-ajax-seo-table-permalinks-handler.php';

$options = get_option('hostifybooking-plugin');

$config_submenu = [
	'title'           => 'Hostify Booking Engine',
	'menu_title'      => 'Hostify Booking Engine Plugin',
	'type'            => 'menu', // menu or metabox
	'submenu'         => true,
	'id'              => $this->plugin_name,
	'capability'      => 'manage_options',
	'plugin_basename' => plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ),
	'multilang'       => false,
	'tabbed'          => true,
];

require_once HOSTIFYBOOKING_DIR . 'inc/vendors/markdown/class-markdown.php';
$md = new Markdown();
$readme_md = file_get_contents(plugin_dir_path( __DIR__ ) . '/readme.txt');
$readme_md = str_replace('<PLUGIN_URL>/', HOSTIFYBOOKING_URL, $readme_md);
$readme = trim($md->transform($readme_md));

$md2 = new Markdown();
$changelog_md = file_get_contents(plugin_dir_path( __DIR__ ) . '/CHANGELOG.md');
$changelog = trim($md2->transform($changelog_md));

$linke1 = hfy_get_link_to_edit('page_listings');
$linke2 = hfy_get_link_to_edit('page_listing');
$linke3 = hfy_get_link_to_edit('page_payment');
$linke4 = hfy_get_link_to_edit('page_charge');
$linke5 = hfy_get_link_to_edit('page_booking_manage');
$linke6 = hfy_get_link_to_edit('page_wishlist');
$linke7 = hfy_get_link_to_edit('page_bookings_list');

require_once HOSTIFYBOOKING_DIR . 'inc/lib.php';
$api = new HfyApi();
require_once HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/load-data.php';
require_once HOSTIFYBOOKING_DIR . 'inc/shortcodes/inc/dict-amenities.php';

$citiesList = '';
asort($bookingCities);
foreach ($bookingCities as $key => $value) {
	$citiesList .= "<span class='ctcc'>$key</span><span class='ctcc' data-copy='".((int) $key)."'>$value</span>";
}

$amenitiesList = '<ul>';
foreach ($dictionaryAmenities_ as $key => $value) {
	$amenitiesList .= '<li>';
	$amenitiesList .= '<div><b>'.$key.'</b></div>';
	$amenitiesList .= '<div class="help-cities-list">';
	asort($value);
	foreach ($value as $k => $v) {
		$amenitiesList .= "<span class='ctcc'>$k</span><span class='ctcc' data-copy='".((int) $k)."'>$v</span>";
	}
	$amenitiesList .= '</div>';
	$amenitiesList .= '</li>';
}
$amenitiesList .= '</ul>';

if ($bookingEngine->id ?? 0 > 0) {
	$connect_msg = '<div class="hfy-notice-connected notice notice-success"><i class="fa fa-link" style="color:green"></i> Hostify: connected</div>';
} else {
	$connect_msg = '<div class="hfy-notice-connected notice notice-'.($api->getInstalledStatus() ? 'success' : 'error').'">' . $api->getInstalledMessage() . '</div>';
	$connect_msg .= '<div class="notice notice-error"><i class="fa fa-unlink" style="color:red"></i> Can not connect. Please check if API URL and API WPKEY are correct and try &nbsp; <a href="' . get_admin_url() . 'options-general.php?page=hostifybooking-plugin&clr=1" style="vertical-align:baseline" class="button hfy-clear-cache-btn">Clear cache</a> </div>';
}

$connect_msg .= " <a href='" . get_admin_url() . "options-general.php?page=hostifybooking-plugin&clr=1&redir=".urlencode(admin_url('/options-general.php?page=hostifybooking-plugin#optionsapi'))."' style='display:none' class='check-connection-hfy hfy-clear-cache-btn button hfy-clear-cache-btn'><span class='dashicons dashicons-admin-plugins' style='vertical-align:text-bottom'></span> Check connection to Hostify</a> ";

$fields[] = [
	'title'  => __( 'Read me first', 'hostifybooking' ),
	'name'   => 'readme',
	// 'icon'   => 'dashicons-editor-help',
	'icon'   => 'dashicons-arrow-right-alt',
	'fields' => [
		[
			// 'title'      => __( 'readme.txt', 'hostifybooking' ),
			'id'         => 'txt1',
			'type'       => 'notice',
			'content'    => '<style>.readme-md p{line-height: 120%; margin: 0;} .readme-md h4,.readme-md ul,.readme-md li{margin:0}</style><pre class="readme-md" style="white-space:pre-line">'.$readme.'</pre>',
		],
	],
];

$fields[] = [
	'name'   => 'optionsapi',
	'title'  => __( 'API settings', 'hostifybooking' ),
	'icon'   => 'dashicons-admin-plugins',
	'fields' => [
		[
			'title' => __( "API URL", 'hostifybooking' ),
			'id'    => 'apiUrl',
			'after' => "PMS API address provided by <a target=_blank href='//hostify.com/'>Hostify</a>",
			'type'  => 'text',
			'class' => 'text-class',
		],
		[
			'title' => __( "API WPKEY", 'hostifybooking' ),
			'id'    => 'apiWpkey',
			'after' => "PMS API WPKEY provided by <a target=_blank href='//hostify.com/'>Hostify</a> <br/><br/> " . $connect_msg,
			'type'  => 'text',
			'class' => 'text-class',
			'attributes' => [
				'autocomplete' => 'off'
			]
		],
	],
];

$fields[] = [
	'name'   => 'options',
	'title'  => __( 'Pages', 'hostifybooking' ),
	'icon'   => 'dashicons-admin-page',
	'fields' => [
		[
            'title'          => __( 'Listings page', 'hostifybooking' ),
		    'description'    => __( 'or listings search result [hfy_listings]', 'hostifybooking' ),
            'id'             => 'page_listings',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'Page to show list of Listings or search result', 'hostifybooking' )
				. ' <br/> <a href="' . get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=1">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke1) ? '' : (
					HFY_PAGE_LISTINGS > 0
						? ' &nbsp; | &nbsp; <a href="' . $linke1 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],

		[
			'title'          => __( 'Single listing page', 'hostifybooking' ),
			'description'    => __( 'single page [hfy_listing]', 'hostifybooking' ),
            'id'             => 'page_listing',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'This page will be used in links to certain Listing', 'hostifybooking' )
				. ' <br/> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=2">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke2) ? '' : (
					HFY_PAGE_LISTING > 0
						? ' &nbsp; | &nbsp; <a href="' . $linke2 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],
		[
			'title'          => __( 'Payment page', 'hostifybooking' ),
			'description'    => '[hfy_payment]',
            'id'             => 'page_payment',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'In case you use the direct payment acceptance', 'hostifybooking' )
				. ' <br/> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=3">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke3) ? '' : (
					HFY_PAGE_PAYMENT > 0
						? ' &nbsp; | &nbsp; <a href="' . $linke3 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],
		[
			'title'          => __('Payment charge page', 'hostifybooking'),
			'description'    => '[hfy_payment_charge]',
            'id'             => 'page_charge',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __('To show the payment result', 'hostifybooking') . ' ('.__('Applicable to NetPay', 'hostifybooking' ).')'
				. ' <br /> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=4">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke4) ? '' : (
					HFY_PAGE_CHARGE > 0
						? ' &nbsp; | &nbsp; <a href="' . $linke4 . '">'.__('Edit page', 'hostifybooking').'</a> '
					: ''
				)),
		],
		[
			'title'          => __( 'User bookings list page', 'hostifybooking' ),
			'description'    => '[hfy_user_bookings_list]',
            'id'             => 'page_bookings_list',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'To show list of user bookings', 'hostifybooking' )
				. ' <br /> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=7">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke7) ? '' : (
					HFY_PAGE_BOOKINGS_LIST
						? ' &nbsp; | &nbsp; <a href="' . $linke7 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],
		[
			'title'          => __( 'User booking details page', 'hostifybooking' ),
			'description'    => '[hfy_user_booking_manage]',
            'id'             => 'page_booking_manage',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'To show single booking details', 'hostifybooking' )
				. ' <br /> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=5">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke5) ? '' : (
					HFY_PAGE_BOOKING_MANAGE
						? ' &nbsp; | &nbsp; <a href="' . $linke5 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],
		[
			'title'          => __( 'User wishlist page', 'hostifybooking' ),
			'description'    => '[hfy_user_wishlist]',
            'id'             => 'page_wishlist',
            'type'           => 'select',
            'query'          => [
                'type'           => 'pages',
                'args'           => [
                    'orderby'      => 'post_date',
                    'order'        => 'DESC',
				],
			],
            'default_option' => '',
			'class' => 'chosen',
			'after' => __( 'To show a list of Listings that user was marked', 'hostifybooking' )
				. ' <br /> <a href="'.get_admin_url() . 'options-general.php?page=hostifybooking-plugin&crnp=6">'.__('Create new page', 'hostifybooking').'</a> '
				. ( empty($linke6) ? '' : (
					HFY_PAGE_WISHLIST
						? ' &nbsp; | &nbsp; <a href="' . $linke6 . '">'.__('Edit page', 'hostifybooking').'</a> '
						: ''
				)),
		],

	],
];

function get_yoast_install_link()
{
	$slug = 'wordpress-seo';
	$action = 'install-plugin';
	return wp_nonce_url(
		add_query_arg(
			[
				'action' => $action,
				'plugin' => $slug
			],
			admin_url('update.php')
		),
		$action.'_'.$slug
	);
}

function get_wpp_search_link($s = '')
{
	return get_admin_url() . 'plugin-install.php?s='.$s.'&tab=search&type=term';
}

$fields[] = [
	'name'   => 'options_add',
	'title'  => __( 'Options', 'hostifybooking' ),
	'icon'   => 'dashicons-admin-tools', 
	'fields' => [
		[
			'content' => '<div class="x-title">'.__( 'Search', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_4',
			'type'    => 'notice',
		],

		[
			'title'          => __( 'Location selector options', 'hostifybooking' ),
            'id'             => 'locations_selector',
            'type'           => 'select',
            'options'        => [
                '0' => 'City name only (default)',
				'1' => 'Country name, State name, City name, Neighbourhood',
                '2' => 'State name, City name, Neighbourhood',
				'3' => 'Country name, City name, Neighbourhood',
                '4' => 'City name, Neighbourhood',
                '5' => 'Neighbourhood, City name',
			],
		],
		[
            'title'          => __( 'Location selector placeholder', 'hostifybooking' ),
            'id'             => 'text_select_location',
            'type'           => 'text',
            'default'        => HFY_TEXT_SELECT_LOCATION ?? __('Select location', 'hostifybooking'),
			'after'          => __( 'Default text if nothing is selected', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Tags menu', 'hostifybooking' ),
            'id'             => 'tags_menu',
            'type'           => 'text',
			'after'          => __( 'Name of WP menu (<a href="' . get_admin_url() . 'nav-menus.php">see here</a>). If the "tagsmenu" parameter is defined in the [hfy_booking_search] shortcode, like [hfy_booking_search tagsmenu="tag1,tag2"], this option will be overridden.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Live search', 'hostifybooking' ),
            'id'             => 'rich_select_loc',
            'type'           => 'checkbox',
			'label'          => __( 'Filter locations while typing in selector', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Show location filter (v3)', 'hostifybooking' ),
            'id'             => 'show_location_filter_v3',
            'type'           => 'checkbox',
			'label'          => __( 'If enabled, the location dropdown will be shown in the search form for API v3.', 'hostifybooking' ),
			'after'          => __( 'This setting controls the visibility of the location filter when using API v3. For API v2, the location filter visibility is controlled by the PMS settings.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Enable keyword search', 'hostifybooking' ),
            'id'             => 'enable_keyword_search',
            'type'           => 'checkbox',
			'default_option' => (defined('HFY_ENABLE_KEYWORD_SEARCH') && HFY_ENABLE_KEYWORD_SEARCH) ? 'yes' : 'no',
			'label'          => __( 'If enabled, a keyword search field will be added to the search form.', 'hostifybooking' ),
			'after'          => __( 'This allows users to search for listings by keywords like property names, descriptions, amenities, city names, or neighbourhoods. Works with API v3 for comprehensive search across all listings.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Keyword search placeholder', 'hostifybooking' ),
            'id'             => 'keyword_search_placeholder',
            'type'           => 'text',
            'default'        => (defined('HFY_KEYWORD_SEARCH_PLACEHOLDER') ? HFY_KEYWORD_SEARCH_PLACEHOLDER : __('Search by keyword...', 'hostifybooking')),
			'after'          => __( 'Placeholder text for the keyword search field', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Search for the exact number of bedrooms', 'hostifybooking' ),
            'id'             => 'exact_bedrooms',
            'type'           => 'checkbox',
			'label'          => __( 'If enabled, only listings with the specified number of bedrooms will be shown in the search results.', 'hostifybooking' ),
			'after'          => __( 'Otherwise, by default, the result includes the specified number or more.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Show "Studio" option', 'hostifybooking' ),
            'id'             => 'show_studio_option',
            'type'           => 'checkbox',
            'default_option' => HFY_SHOW_STUDIO_OPTION ? 'yes' : 'no',
			'label'          => __( 'If enabled, "Studio" option will be added to the bedroom selection.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Default term mode', 'hostifybooking' ),
            'id'             => 'long_term_default',
            'type'           => 'radio',
			'default_option' => HFY_LONG_TERM_DEFAULT == 1 ? '1' : '0',
            'options'        => [
				'0' => 'short term',
				'1' => 'long term',
			],
		],
		[
            'title'          => __( 'Show long/short terms selector', 'hostifybooking' ),
            'id'             => 'long_short_selector',
            'type'           => 'checkbox',
			'default_option' => HFY_LONG_SHORT_SELECTOR ? 'yes' : 'no',
			'label'          => __( 'If enabled, the "short-term" and "long-term bookings" will be added the search form.', 'hostifybooking' ),
		],

		[
			'title'          => __( 'Show infants selector', 'hostifybooking' ),
			'id'             => 'show_infants',
			'type'           => 'checkbox',
			'default_option' => 'yes',
		],
		[
			'title'          => __( 'Max infants number', 'hostifybooking' ),
			'id'             => 'show_infants_max',
			'type'           => 'text',
			'default'        => '10',
		],
		[
			'title'          => __( 'Show pets selector', 'hostifybooking' ),
			'id'             => 'show_pets',
			'type'           => 'checkbox',
			'default_option' => 'yes',
		],
		[
			'title'          => __( 'Max pets number', 'hostifybooking' ),
			'id'             => 'show_pets_max',
			'type'           => 'text',
			'default'        => '10',
		],

		[
			'title'          => __('Max guests number', 'hostifybooking'),
			'id'             => 'guests_max',
			'type'           => 'text',
			'default'        => '10',
		],

		[
			'title'          => __('Show age hints', 'hostifybooking'),
			'label'          => __('Show age hints in the guests selection dialog', 'hostifybooking'),
			'id'             => 'show_guests_hints',
			'type'           => 'checkbox',
			'default_option' => HFY_SHOW_GUESTS_HINTS ? 'yes' : 'no',
		],

		[
			'content' => '<div class="x-title">'.__( 'Advanced search', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_2',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Short list of amenities', 'hostifybooking' ),
            'id'             => 'adv_search_am_short',
            'type'           => 'checkbox',
			'default_option' => HFY_ADV_SEARCH_AM_SHORT ? 'yes' : 'no',
			'label'          => __( 'Use short list of predefined amenities.', 'hostifybooking' ),
			'after'          => __( 'ATTENTION: If disabled, all available amenities will be shown, this can be a huge list, so it is recommended to use the options below in this case.', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Select amenities to show', 'hostifybooking' ),
            'id'             => 'adv_search_am',
            'type'           => 'checkbox',
			'label'          => __( 'Only selected amenities will be used in the advanced search form', 'hostifybooking' ),
		],
		[
            'title'          => '',
            'id'             => 'adv_search_am_list',
            'type'           => 'text',
			'default_option' => '',
			'before'         => __( 'List of amenities to show (ID separated by comma):', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Amenities groups', 'hostifybooking' ),
            'id'             => 'adv_search_am_groups',
            'type'           => 'checkbox',
			'default_option' => HFY_ADV_SEARCH_AM_GROUPS ? 'yes' : 'no',
			'label'          => __( 'Show amenities groups', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Hide \'Other\' group', 'hostifybooking' ),
            'id'             => 'adv_search_am_groups_hide_other',
            'type'           => 'checkbox',
			'default_option' => HFY_ADV_SEARCH_AM_GROUPS_HIDE_OTHER ? 'yes' : 'no',
			'label'          => __( 'Do not show \'Other\' group in advanced search', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Pets selector in advanced', 'hostifybooking' ),
            'id'             => 'adv_search_pets',
            'type'           => 'checkbox',
			'default_option' => HFY_ADV_SEARCH_PETS ? 'yes' : 'no',
			'label'          => __( 'Show Pets selector in advanced search form instead of Guests dialog', 'hostifybooking' ),
		],

		[
			'content' => '<div class="x-title">'.__( 'Map', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_5',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Map tracking', 'hostifybooking' ),
            'id'             => 'map_tracking',
            'type'           => 'checkbox',
            'label'          => __( 'Show/hide listings in search results while map moving', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Map price', 'hostifybooking' ),
            'id'             => 'map_price_label',
            'type'           => 'checkbox',
            'default_option' => HFY_MAP_PRICE_LABEL ? 'yes' : 'no',
            'label'          => __( 'Show price labels on the map instead of pins', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Group to clusters', 'hostifybooking' ),
            'id'             => 'map_clusters',
            'type'           => 'checkbox',
            'default_option' => HFY_MAP_CLUSTERS ? 'yes' : 'no',
            'label'          => __( 'Display many closely spaced markers as one cluster on the map', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Show area', 'hostifybooking' ),
            'id'             => 'map_loc_circle',
            'type'           => 'checkbox',
            'default_option' => HFY_MAP_LOC_CIRCLE ? 'yes' : 'no',
            'label'          => __( 'Show the approximate area instead of the exact location point', 'hostifybooking' ),
		],
		[
            'title'          => __('Max zoom', 'hostifybooking'),
            'id'             => 'map_max_zoom',
            'type'           => 'text',
			'default'        => 12,
		],
		[
            'title'          => __('Default map type', 'hostifybooking'),
            'id'             => 'map_type',
            'type'           => 'select',
            'options'        => [
                'roadmap'   => __('Roadmap (default)', 'hostifybooking'),
                'satellite' => __('Satellite', 'hostifybooking'),
                'hybrid'    => __('Hybrid', 'hostifybooking'),
                'terrain'   => __('Terrain', 'hostifybooking')
            ],
            'default'        => 'roadmap',
            'after'          => __('Select the default map type. Users can change this using the map type control.', 'hostifybooking'),
		],
		[
            'title'          => __('Custom styles', 'hostifybooking'),
            'id'             => 'map_custom_style',
            'type'           => 'textarea',
			'after'          => json_decode(HFY_MAP_CUSTOM_STYLE) ? '' : '<div class="notice notice-error">syntax error</div>'
		],

		[
			'content' => '<div class="x-title">'.__( 'Listings', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_7',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Items (listings) per page', 'hostifybooking' ),
            'id'             => 'listings_per_page',
            'type'           => 'text',
			'default'        => 20,
		],
		[
            'title'          => __( 'Default sorting', 'hostifybooking' ),
            'id'             => 'listings_sort',
			'type'           => 'radio',
			'default_option' => strval(HFY_LISTINGS_SORT),
            'options'        => [
				'0' => 'No sort',
				'1' => 'Sort by listing price, descending order (high to low)',
				'2' => 'Sort by listing price, ascending order (low to high)',
				'3' => 'Sort by listing title',
				'4' => 'Sort by listing nickname',
			],
		],
		[
            'title'          => __('Images slider', 'hostifybooking'),
            'id'             => 'use_listings_gallery',
            'type'           => 'checkbox',
            'label'          => __( 'Add images slider for each listing item', 'hostifybooking' ),
		],
		[
            'title'          => __('Open in new window', 'hostifybooking'),
            'id'             => 'use_listings_gallery_click',
            'type'           => 'checkbox',
            'label'          => __('Click on a card will open the listing in a new tab/window', 'hostifybooking'),
		],

		[
			'content' => '<div class="x-title">'.__( 'Single listing', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_6',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Select amenities to show', 'hostifybooking' ),
            'id'             => 'selected_amenities',
            'type'           => 'text',
			'after'          => __( 'Define a list of amenities to show (IDs separated with a comma). All others will be hidden.', 'hostifybooking' ),
			'class'          => 'text-class',
		],
		[
            'title'          => __( 'Amenities with images first', 'hostifybooking' ),
            'id'             => 'amenities_images',
            'type'           => 'checkbox',
            'label'          => __( 'Amenities with images will be shown first at the top of the list', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Amenities with images only', 'hostifybooking' ),
            'id'             => 'amenities_images_only',
            'type'           => 'checkbox',
            'label'          => __( 'Only amenities with images will be displayed', 'hostifybooking' ),
		],
		[
            'title'          => __( 'Use new guests template', 'hostifybooking' ),
            'id'             => 'use_listing_booking_form_v2',
            'type'           => 'checkbox',
            'default_option' => HFY_USE_BOOKING_FORM_V2 ? 'yes' : 'no',
			'label'          => __( 'Use new dialog for guests select: adults, children, infants, pets.', 'hostifybooking' ),
		],



		[
			'title'          => __('Redirect if no listing', 'hostifybooking'),
			'id'             => 'redirect_no_listing',
			'type'           => 'radio',
			'default_option' => (string) HFY_REDIRECT_NO_LISTING,
			'options'        => [
				'0' => __('No redirect', 'hostifybooking'),
				'1' => __('Redirect to Listings page', 'hostifybooking'),
				'2' => __('Redirect to Home page', 'hostifybooking'),
				'3' => __('Redirect to custom URL', 'hostifybooking'),
			],
		],
		[
            'title'          => ' ',
            'id'             => 'redirect_no_listing_url',
            'type'           => 'text',
			'before'         => __('Custom URL', 'hostifybooking' ),
			'class'          => 'text-class',
			'attributes'     => [
				'placeholder' => 'https://www.mybsite.com/'
			]
		],
		[
            'title'          => __( 'Dynamic Google Map', 'hostifybooking' ),
            'id'             => 'dynamic_google_map',
            'type'           => 'checkbox',
			'default_option' => HFY_DYNAMIC_GOOGLE_MAP ? 'yes' : 'no',
			'label'          => __( 'Show dynamic google map ', 'hostifybooking' ) . '(<span style="color: orange;">' . __(' Required Google Maps API Key In Miscellaneous Section ','hostifybooking') . '</span>)',
		],

		[
			'content' => '<div class="x-title">'.__( 'Reviews', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_9',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Review system', 'hostifybooking' ),
            'id'             => 'review_system',
            'type'           => 'radio',
			'default_option' => (string) HFY_REVIEW_SYSTEM,
            'options'        => [
                '0' => __( 'Airbnb reviews only (5-star system)', 'hostifybooking' ),
                '1' => __( 'Booking.com reviews only (10-star system)', 'hostifybooking' ),
				'2' => __( 'VRBO reviews only (5-star systems)', 'hostifybooking' ),
				'3' => __( 'Internal reviews only (5-star system)', 'hostifybooking' ),
			],
		],
		[
            'title'          => __( 'Show review source', 'hostifybooking' ),
            'id'             => 'show_review_source',
            'type'           => 'checkbox',
			'default_option' => HFY_SHOW_REVIEW_SOURCE ? 'yes' : 'no',
			'label'          => __( 'Show the source platform (Airbnb/Booking.com) next to each review', 'hostifybooking' ),
			'after'          => __( 'When enabled, reviews will display their source platform (e.g., "Airbnb" or "Booking.com")', 'hostifybooking' ),
		],

		[
			'content' => '<div class="x-title">'.__( 'Payment', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_3',
			'type'    => 'notice',
		],

		[
            'title'          => __( 'Show discount', 'hostifybooking' ),
            'id'             => 'show_discount',
            'type'           => 'checkbox',
			'default_option' => HFY_SHOW_DISCOUNT ? 'yes' : 'no',
			'label'          => __( 'Show discount/coupon controls', 'hostifybooking' ),
		],

		[
			'title'          => __('Redirect on payment success', 'hostifybooking'),
			'id'             => 'redirect_on_payment',
			'type'           => 'radio',
			'default_option' => (string) HFY_REDIRECT_ON_PAYMENT,
			'options'        => [
				'0' => 'No redirect',
				'1' => 'Redirect to Payment charge page (which contains [hfy_payment_charge] shortcode)',
				'2' => 'Redirect to custom URL',
			],
		],
		[
            'title'          => ' ',
            'id'             => 'redirect_on_payment_url',
            'type'           => 'text',
			'before'         => __('Custom URL', 'hostifybooking' ),
			'class'          => 'text-class',
			'attributes'     => [
				'placeholder' => 'https://www.mybsite.com/'
			]
		],

		[
            'title'          => 'Show payment extras',
            'id'             => 'show_payment_extras',
            'type'           => 'checkbox',
			'default_option' => HFY_SHOW_PAYMENT_EXTRAS ? 'yes' : 'no',
			'label'          => __('Show optional extras on payment page', 'hostifybooking'),
		],

		[
            'title'          => 'Default phone code area',
            'id'             => 'payment_phone_code_default',
			'type'           => 'radio',
			'default_option' => HFY_PAYMENT_PHONE_CODE_DEFAULT == 1 ? '1' : '0',
            'options'        => [
				'0' => __('Use current WP language setting', 'hostifybooking'),
				'1' => __('Use custom:', 'hostifybooking'),
			],
		],
		[
            'title'   => '',
            'id'      => 'payment_phone_code',
            'type'    => 'text',
			'before'  => __('2-char country code like <code>us</code>', 'hostifybooking' ),
			'default' => HFY_PAYMENT_PHONE_CODE,
		],

		/*
		[
			'content' => '<div class="x-title">'.__( 'Other', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1_4',
			'type'    => 'notice',
		],

		[
			'title'          => __( 'Show image preloader', 'hostifybooking' ),
			'id'             => 'show_img_loader',
			'type'           => 'checkbox',
			'default_option' => 'yes',
			'label' => __( 'Show animated loader when loading images', 'hostifybooking' ),
		],
		*/

		[
			'content' => '<div class="x-title">'.__('Miscellaneous', 'hostifybooking').'</div>',
			'id'      => 'scdesc1_8',
			'type'    => 'notice',
		],

		[
            'title'          => 'Google Maps API key',
            'id'             => 'google_maps_api_key',
            'type'           => 'text',
			'class' => 'text-class',
		],
		[
            'title'          => 'Google reCaptcha Site key',
            'id'             => 'google_recaptcha_site_key',
            'type'           => 'text',
			'class' => 'text-class',
			'after' => '<br/><b>Note:</b> This is your public site key from Google reCAPTCHA. Required for the reCAPTCHA widget to appear on your forms.',
		],
		[
            'title'          => 'Google reCaptcha Secret key',
            'id'             => 'google_recaptcha_secret_key',
            'type'           => 'text',
			'class' => 'text-class',
			'after' => '<br/><b>Note:</b> This is your private secret key from Google reCAPTCHA. Required for server-side verification. Keep this secret and do not share it publicly.',
		],
		[
            'title'          => 'reCAPTCHA Version',
            'id'             => 'google_recaptcha_version',
            'type'           => 'radio',
			'default_option' => HFY_GOOGLE_RECAPTCHA_VERSION,
			'before' => 'Select reCAPTCHA version:',
            'options' => [
				'v2' => 'reCAPTCHA v2 (Checkbox)',
				'v3' => 'reCAPTCHA v3 (Invisible)',
			],
			'after' => '<br/><b>Note:</b> v2 shows a checkbox, v3 is invisible but requires score-based validation. <strong>Both Site Key and Secret Key must be configured above for the selected version to work.</strong> If no keys are provided, no reCAPTCHA will appear on the form (regardless of version selected).',
		]
	],
];

$fields[] = [
	'name'   => 'options_seo',
	'title'  => __( 'SEO', 'hostifybooking' ),
	'icon'   => 'dashicons-google',
	'fields' => [
		[
            'title' => __( 'SEO for listings', 'hostifybooking' ),
            'id'    => 'seo_listings',
            'type'  => 'checkbox',
            // 'default_option' => 'no',
			'label' => '<a href="https://wordpress.org/plugins/wordpress-seo/" target="_blank">Yoast SEO</a> (<a href="'.get_wpp_search_link('yoast%20seo').'">find and install it</a>) and Rank Math plugins support. Adding SEO meta tags for single listing pages and listings URLs in XML sitemap',
		],
		[
            'title' => __( 'Listing slug', 'hostifybooking' ),
            'id'    => 'seo_listing_slug',
            'type'  => 'radio',
			'default_option' => HFY_SEO_LISTING_SLUG == 1 ? '1' : '0',
			'before' => 'Generate a listing slug for URL using:',
            'options' => [
				'0' => 'listing name',
				'1' => 'listing nickname',
			],
		],
		[
			'title' => '',
			'id'    => 'seo_table_placeholder',
			'type'  => 'content',
			'content' => '<div id="hfy-seo-table-placeholder" class="hfy-admin-table-container"></div>',
		],
		[
			'title' => '',
			'before' => '<span style="color:red">(beta)</span> replace string:',
            'id'    => 'seo_listing_slug_find',
            'type'  => 'text'
		],
		[
			'title' => '',
			'before' => '<span style="color:red">(beta)</span> to string:',
            'id'    => 'seo_listing_slug_replace',
            'type'  => 'text'
		],
		[
            'title'   => __( 'NOINDEX for selected listings', 'hostifybooking' ),
			'after'      => 'Listings IDs, comma separated',
            'id'      => 'seo_noindex',
            'type'    => 'text',
			'default' => '',
		],
		[
            'title'   => __( 'Redirect 301', 'hostifybooking' ),
            'id'      => 'listing_not_found_url',
            'type'    => 'text',
			'default' => '',
			'before' => __('If the requested listing is not found, the page will be redirected to the specified URL, instead of "No listing" message', 'hostifybooking' ),
			'after' => __('Clear this field if you do not need to do this check', 'hostifybooking' ),
		],
		[
            'title' => __( 'GA Events', 'hostifybooking' ),
            'id'    => 'seo_events',
			'type'  => 'checkbox',
			'default_option' => hfyUseGA() ? 'yes' : 'no',
			'label' => __('Send custom events for Google Analytics using the Google tag (gtag) or Google Tag Manager (GTM).', 'hostifybooking' ) . '<br/><br/>',
			'after' =>
				  'These custom events will be sent on the checkout page:'
				. '<br/><br/><code>hfy_payment_success</code>'
				. '<br/><code>hfy_payment_error</code>'
				. '<br/>'
				. '<br/>'
				. __('<b>NOTES</b>: <ol><li>You need to add the gtag.js or gtm snippet. Please refer: <a href="https://developers.google.com/analytics/devguides/collection/ga4/events?client_type=gtag" target="_blank">Set up events for Google tag</a>.'
				.'</li><li>You can install one of ready-to-use plugins: <a href="'.get_wpp_search_link('gtag').'" target="_blank">search in WP plugins repository</a>, or just add the snippet code to your theme files.'
				.'</li><li>Or, for example, your current WP theme may already have gtag/gtm support, please refer your current theme options.', 'hostifybooking') . '</li></ol>',
		],

	],
];

$fields[] = [
	'name'   => 'options_adm',
	'title'  => __( 'Admin options', 'hostifybooking' ),
	'icon'   => 'dashicons-admin-generic',
	'fields' => [
		[
            'title'          => __( 'Turn off Boostrap', 'hostifybooking' ),
            'id'             => 'no_bs',
            'type'           => 'checkbox',
            // 'default_option' => 'no',
			'label'          => __( 'Do not load Bootstrap library that comes with the plugin', 'hostifybooking' ),
			'after'          => '<br/><b>'.__('Note').':</b> ' . __( 'This plugin uses the Bootstrap CSS framework. If there are any problems or conflicts with the installed plugins or theme, you can prevent the Hostiy plugin from loading this framework.', 'hostifybooking' ),
		],
		/*
		[
            'title'          => __( 'Turn off CSS', 'hostifybooking' ),
            'id'             => 'no_css',
            'type'           => 'checkbox',
            // 'default_option' => 'no',
			'label'          => __( 'Do not load all the CSS that comes with the plugin, including Bootstrap', 'hostifybooking' ),
			'after'          => '<br/>'.__( 'If you don\'t really need the styles that a plugin offers, you can disable loading all of the plugin\'s CSS.', 'hostifybooking' ),
		],
		*/
		[
			'title'          => __( 'Disable data caching', 'hostifybooking' ),
            'id'             => 'disable_cache',
            'type'           => 'checkbox',
            // 'default_option' => 'no',
			'label'          => __( 'Do not use cache - it can be useful for testing or troubleshooting', 'hostifybooking' ),
			'after'          => '<br/><b>Note:</b> ' . __('The plugin uses data caching both on the Hostify API and WordPress side. Sometimes during development and configuration process, it can interfere or confuse.', 'hostifybooking' ),
		],
		[
			'title'          => __( 'Show links on admin bar', 'hostifybooking' ),
			'id'             => 'show_on_bar',
			'type'           => 'checkbox',
			'default_option' => HFY_SHOW_ON_BAR ? 'yes' : 'no',
			'label'          => __( 'Show links for quick access', 'hostifybooking' ),
		],
		[
			'title' => __( 'Custom template path', 'hostifybooking' ),
			'id'    => 'hfy_tpl_path',
            'type'  => 'text',
			'after' => '<br/>'.__('You can place your templates not only in the current theme folder, but also in another place on the server that is available for you. In this case, you can specify the path to this folder. You can also use a special WP filter in your code, see "Read me first".', 'hostifybooking' ),
		],

		[
			'type'           => 'notice',
			'content' => '<span style="color:red">Beta options</span><hr/>This is currently an experimental features<br/><br/> <div class="update-message notice notice-error notice-alt"><big><b>Please, consult with Hostify support before changing it!</b></big></div>',
		],

		[
			'title'          => __('Use API v3', 'hostifybooking'),
			'id'             => 'use_api_v3',
			'type'           => 'checkbox',
			'default_option' => HFY_USE_API_V3 ? 'yes' : 'no',
			'label' => 'New API',
		],
		[
			'title'          => __('Stripe payment element form', 'hostifybooking'),
			'id'             => 'use_stripe_element',
			'type'           => 'checkbox',
			'default_option' => HFY_USE_STRIPE_ELEMENT ? 'yes' : 'no',
			'label' => 'Allows new payment methods - Google Pay, Apple Pay, etc.',
		],
		[
			'title'          => __('Use Calendar v2', 'hostifybooking'),
			'id'             => 'use_new_calendar',
			'type'           => 'checkbox',
			'default_option' => HFY_USE_NEW_CALENDAR ? 'yes' : 'no',
			'label' => 'Use Version 2 of the calendar - Soon to be deprecated',
		],
		[
			'title'          => __('Use Calendar v3', 'hostifybooking'),
			'id'             => 'use_v3_calendar',
			'type'           => 'checkbox',
			'default_option' => HFY_USE_V3_CALENDAR ? 'yes' : 'no',
			'label' => 'Use Version 3 of the calendar',
		],
		[
			'title' => __('Disable payment templates', 'hostifybooking'),
			'label' => __('Disable overwriting of the payment templates', 'hostifybooking'),
			'id'    => 'disable_templates_payment_override',
			'type'  => 'checkbox',
			'default_option' => HFY_DISABLE_TEMPLATES_PAYMENT_OVERRIDE ? 'yes' : 'no',
		],
		[
			'title'          => __('Use New Inquiry Form', 'hostifybooking'),
			'id'             => 'use_new_inquiry_form',
			'type'           => 'checkbox',
			// Ensure default_option matches the constant's default for saving
			'default_option' => (defined('HFY_USE_NEW_INQUIRY_FORM') && HFY_USE_NEW_INQUIRY_FORM) ? 'yes' : 'no',
			'label'          => __('Enable the new reservation inquiry modal design.', 'hostifybooking'),
		],
	]
];

$fields[] = [
	'title'  => __( 'Shortcodes', 'hostifybooking' ),
	'name'   => 'shortcodes',
	'icon'   => 'dashicons-shortcode',
	'fields' => [

		[
			// 'title'   => '',
			'id'      => 'scdesc',
			'type'    => 'notice',
			'content' => '<div><p>'.__( 'Copy the shortcode and place it on post, page, text, code widget, etc. Refer the plugin Readme and documentation for more info about shortcodes and parameters.', 'hostifybooking' ).'</p></div>'
		],

		[
			'content' => '<div class="x-title">'.__( 'Complex:', 'hostifybooking' ).'</div>',
			'id'      => 'scdesc1',
			'type'    => 'notice',
		],

		[
			'title'      => __( 'Listings', 'hostifybooking' ),
			'id'         => 'scc1',
			'content'    => '<code class="ctcc">[hfy_listings]</code><br/><br/><code class="ctcc">[hfy_listings ids="" cities="" city="" monthly="" tags="" neighbourhood="" with_amenities="false"]</code>',
			'description'      => __('List of Listings, show the search result', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __('Listings info', 'hostifybooking'),
			'id'         => 'scc1_2',
			'content'    => '<code class="ctcc">[hfy_listings_info]</code><br/></code>',
			'description' => __('Short info about current search', 'hostifybooking'),
			'after' => __('Can be used, for example, inside title (H1 tag) on the listing search page', 'hostifybooking'),
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __( 'Listings map', 'hostifybooking' ),
			'id'         => 'scc2',
			'content'    => '<code class="ctcc">[hfy_listings_map]</code><br/><br/><code class="ctcc">[hfy_listings_map ids="" cities="" city="" monthly="" tags="" neighbourhood="" closebutton=""]</code>',
			'description' => __('Show listings on map', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __( 'Listings map show/hide button', 'hostifybooking' ),
			'id'         => 'scc2',
			'content'    => '<code class="ctcc">[hfy_listings_map_toggle]</code><br/><br/><code class="ctcc">[hfy_listings_map_toggle mobile="true" tablet="true"]</code>',
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __('Listings sort control', 'hostifybooking' ),
			'id'         => 'scc2_s',
			'content'    => '<code class="ctcc">[hfy_listings_sort]</code>',
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __( 'Single listing', 'hostifybooking' ),
			'id'         => 'scc3',
			'content'    => '<code class="ctcc">[hfy_listing]</code><br/><br/><code class="ctcc">[hfy_listing id="" max=""]</code>',
			'description'      => __( 'Show single listing by ID', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __( 'Search form', 'hostifybooking' ),
			'id'         => 'scc4',
			'content'    => '<code class="ctcc">[hfy_booking_search]</code><br/><br/><code class="ctcc">[hfy_booking_search advanced="true|false" neighbourhood="" tagsmenu=""]</code>',
			'description'      => __( 'Listing search form', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => '',
		],
		[
			'title'      => __( 'Selected listings', 'hostifybooking' ),
			'id'         => 'scc5',
			'content'    => '<code>[hfy_listings_selected ids="" cities="" paramcity="" currentlistingcity="" max=""]</code>',
			'after'      => __( 'Show listings by ID, city ID', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Top listings', 'hostifybooking' ),
			'id'         => 'scc5',
			'content'    => '<code>[hfy_top_listings]</code>',
			'after'      => __( 'Show top listings', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Recent listings', 'hostifybooking' ),
			'id'         => 'scc52',
			'content'    => '<code>[hfy_recent_listings]</code>',
			'after'      => __( 'Show recent listings', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Payment page', 'hostifybooking' ),
			'id'         => 'scc6',
			'content'    => '<code>[hfy_payment]</code>',
			'after'      => __( 'Show payment summary and form', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		// [
		// 	'title'      => __( 'Payment result page', 'hostifybooking' ) .' '. __( '(deprecated)', 'hostifybooking' ),
		// 	'id'         => 'scc7',
		// 	'content'    => '<code>[hfy_payment_charge]</code>',
		// 	'after'      => __( 'Show payment result', 'hostifybooking' ),
		// 	'type'       => 'notice',
		// 	'class'      => 'ctcc',
		// ],

		[
			'title'      => __('Predefined extras block', 'hostifybooking'),
			'description' => __('for payment page', 'hostifybooking'),
			'id'         => 'scc8',
			'content'    => '<code>[hfy_payment_extras_set id="" ids="" detailed="" selected=""] ...custom content... [/hfy_payment_extras_set]</code>',
			'after'      => 'id - listing ID<br/>ids - extras IDs, comma separated<br/>detailed - true or false<br/>selected - true or false',
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

		[
			'title'      => __('Optional extras block with checkboxes', 'hostifybooking'),
			'description' => __('for payment page', 'hostifybooking'),
			'id'         => 'scc8',
			'content'    => '<code>[hfy_payment_extras_optional id="" except="" checked=""]</code>',
			'after'      => 'id - listing ID<br/>except - extras IDs, comma separated<br/>checked - true or false',
			'type'       => 'notice',
			'class'      => 'ctcc',
		],




		//////////////

		[
			'content' => '<div class="x-title">'.__( 'Listing parts:' ).'</div>',
			'id'      => 'scdesc2',
			'type'    => 'notice',
		],

		[
			'title'      => __( 'Booking form', 'hostifybooking' ),
			'id'         => 'sc21',
			'content'    => '<code>[hfy_listing_booking_form]</code>',
			'after'      => __( 'Show booking form for listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

		[
			'title'      => __( 'Title', 'hostifybooking' ),
			'id'         => 'sc14',
			'content'    => '<code>[hfy_listing_title]</code>',
			'after'      => __( 'Show listing title', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

		[
			'title'      => __( 'Gallery', 'hostifybooking' ),
			'id'         => 'sc15',
			'content'    => '<code>[hfy_listing_gallery view="ab"]</code>',
			'after'      => __( 'Show all images for one listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Main image', 'hostifybooking' ),
			'id'         => 'sc16',
			'content'    => '<code>[hfy_listing_image]</code>',
			'after'      => __( 'Show main image for one listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

		[
			'title'      => __( 'Info', 'hostifybooking' ),
			'id'         => 'sc17',
			'content'    => '<code>[hfy_listing_info]</code>',
			'after'      => __( 'Show info block for listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Part of hfy_listing_info', 'hostifybooking' ),
			'id'         => 'sc17_1',
			'content'    => '
<table>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_summary]</code></div></td>
<td>listing summary info</td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_space]</code></div></td>
<td>space details</td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_guest_access]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_interaction]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_notes]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_transit]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_neighbourhood]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_house_rules]</code></div></td>
<td></td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_address]</code></div></td>
<td>property address</td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_prices]</code></div></td>
<td>detailed prices</td>
</tr>
<tr>
<td><div class="ctcc"><code>[hfy_listing_info_permit]</code></div></td>
<td>permit / tax ID / license</td>
</tr>
</table>
',
			'type'       => 'notice',
		],

		[
			'title'      => __( 'Room type', 'hostifybooking' ),
			'id'         => 'sc18',
			'content'    => '<code>[hfy_listing_room_type]</code>',
			'after'      => __( 'Show room type info for listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Facilities', 'hostifybooking' ),
			'id'         => 'sc19',
			'content'    => '<code>[hfy_listing_facilities]</code>',
			'after'      => __( 'Show listing facilities for listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Amenities', 'hostifybooking' ),
			'id'         => 'sc20',
			'content'    => '<code>[hfy_listing_amenities]</code>',
			'after'      => __( 'Show listing amenities for listing', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Location', 'hostifybooking' ),
			'id'         => 'sc22',
			'content'    => '<code>[hfy_listing_location]</code>',
			'after'      => __( 'Show listing location on map. And there is a synonym: [hfy_listing_map].', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

		[
			'title'      => __( 'Reviews summary', 'hostifybooking' ),
			'id'         => 'sc23',
			'content'    => '<code>[hfy_listing_reviews_summary]</code>',
			'after'      => __( 'Show listing reviews summary', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Reviews count', 'hostifybooking' ),
			'id'         => 'sc24',
			'content'    => '<code>[hfy_listing_reviews_count]</code>',
			'after'      => __( 'Show listing reviews count number', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Reviews comments', 'hostifybooking' ),
			'id'         => 'sc25',
			'content'    => '<code>[hfy_listing_reviews_comments]</code>',
			'after'      => __( 'Show listing reviews comment', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __( 'Virtual tour', 'hostifybooking' ),
			'id'         => 'sc25',
			'content'    => '<code>[hfy_listing_virtual_tour]</code>',
			'after'      => __( 'Render the iframe with link to the virtual tour', 'hostifybooking' ),
			'type'       => 'notice',
			'class'      => 'ctcc',
		],
		[
			'title'      => __('Availability', 'hostifybooking'),
			'id'         => 'sc25_a',
			'content'    => '<code>[hfy_listing_availability]</code>',
			'type'       => 'notice',
			'class'      => 'ctcc',
		],

	],
];

$fields[] = [
	'title'  => __('Dictionaries', 'hostifybooking' ),
	'name'   => 'dict',
	'icon'   => 'dashicons-info',
	'fields' => [
		[
			'id'      => 'tabbed_dict',
			'type'    => 'tab',
			'options' => [ 'equal_width' => false ],
			'tabs'    => [
				[
					'title'  => '<span class="exopite-sof-nav-icon dashicons-before dashicons-admin-site-alt3"></span>'.__('Cities', 'hostifybooking'),
					'fields' => [
						[
							'before'   => '<b><big>'.__('ID codes of available cities', 'hostifybooking').'</big></b><hr/>',
							'id'      => 'txtcitiesid1',
							'type'    => 'text',
							'type'    => 'notice',
							'content' => '<div class="help-cities-list">'.$citiesList.'</div>',
						],
					],
				],
				[
					'title'  => '<span class="exopite-sof-nav-icon dashicons-before dashicons-coffee"></span>'.__('Amenities', 'hostifybooking' ),
					'fields' => [
						[
							'before'   => '<b><big>'.__('ID codes of available amenities', 'hostifybooking').'</big></b><hr/>',
							'id'      => 'txtamid1',
							'type'    => 'notice',
							'content' => '<div class="help-dict-list">'.$amenitiesList.'</div>',
						]
					]
				],
			],
		],
	],
];

$fields[] = [
	'title'  => __( 'Changelog', 'hostifybooking' ),
	'name'   => 'changelog',
	'icon'   => 'dashicons-editor-ul',
	'fields' => [
		[
			'id'      => 'txt2',
			'type'    => 'notice',
			'content' => '<style>.readme-md p{line-height: 120%; margin: 0; white-space:pre-wrap} .readme-md h4,.readme-md ul,.readme-md li{margin:0}</style><pre class="readme-md" style="white-space:pre-line">'.$changelog.'</pre>',
		],
	],
];

$fields[] = [
	'title'  => __( 'Translation manager', 'hostifybooking' ),
	'name'   => 'translation_manager',
	'icon'   => 'dashicons-translation',
	'fields' => [
		[
			'id'      => 'translation_manager_content',
			'type'    => 'notice',
			'content' => '<div id="translation-manager-content">' . 
				'<h2>' . __( 'Extract Translatable Strings', 'hostifybooking' ) . '</h2>' .
				'<p>' . __( 'This tool will scan all PHP files in the plugin and extract translatable strings. It will update the following files:', 'hostifybooking' ) . '</p>' .
				'<ul>' .
				'<li><code>lang/strings_to_translate.php</code> - ' . __( 'All translatable strings', 'hostifybooking' ) . '</li>' .
				'<li><code>lang/hostifybooking.pot</code> - ' . __( 'Template file for translations', 'hostifybooking' ) . '</li>' .
				'<li><code>lang/*.po</code> - ' . __( 'Existing translation files (will add missing strings)', 'hostifybooking' ) . '</li>' .
				'</ul>' .
				'<p><button type="button" id="extract-strings-btn" class="button button-primary">' . __( 'Extract Strings', 'hostifybooking' ) . '</button></p>' .
				'<div id="extraction-results" style="display:none;"></div>' .
				'<h3>' . __( 'Translation Files Location', 'hostifybooking' ) . '</h3>' .
				'<p>' . __( 'Translation files are located in:', 'hostifybooking' ) . '</p>' .
				'<code>' . HOSTIFYBOOKING_DIR . 'lang/</code>' .
				'<h3>' . __( 'Available Languages', 'hostifybooking' ) . '</h3>' .
				'<div id="available-languages" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; align-items: flex-start; justify-content: flex-start;"></div>' .
				'<p><button type="button" id="add-new-language-btn" class="button button-secondary">' . __( 'Add New Language', 'hostifybooking' ) . '</button></p>' .
				'<h3>' . __( 'How to Use', 'hostifybooking' ) . '</h3>' .
				'<ol>' .
				'<li>' . __( 'Click "Extract Strings" to scan the plugin for all translatable strings.', 'hostifybooking' ) . '</li>' .
				'<li>' . __( 'Review the updated translation files in the lang/ directory.', 'hostifybooking' ) . '</li>' .
				'<li>' . __( 'Use our Inline Translation Editor (below) to translate strings directly in the admin interface, or use external tools like Poedit or Loco Translate to edit the .po files.', 'hostifybooking' ) . '</li>' .
				'<li>' . __( 'MO files are automatically generated when you save translations through our editor.', 'hostifybooking' ) . '</li>' .
				'</ol>' .
				'<h3>' . __( 'Maintenance', 'hostifybooking' ) . '</h3>' .
				'<p>' . __( 'Compile all PO files to MO files (useful if you edited files externally):', 'hostifybooking' ) . '</p>' .
				'<p><button type="button" id="compile-mo-btn" class="button button-secondary">' . __( 'Compile MO Files', 'hostifybooking' ) . '</button></p>' .
				'</div>',
		],
		[
			'id'      => 'translation_editor_content',
			'type'    => 'notice',
			'content' => '<div id="translation-editor-content">' .
				'<h2>' . __( 'Inline Translation Editor', 'hostifybooking' ) . '</h2>' .
				'<p>' . __( 'Edit translations directly in the admin interface. Select a language file and edit the translations below.', 'hostifybooking' ) . '</p>' .
				'<div class="translation-editor-controls">' .
				'<label for="po-file-selector">' . __( 'Select Language File:', 'hostifybooking' ) . '</label> ' .
				'<select id="po-file-selector" style="min-width: 200px;">' .
				'<option value="">' . __( '-- Select a language file --', 'hostifybooking' ) . '</option>' .
				'</select> ' .
				'<button type="button" id="load-translations-btn" class="button" disabled>' . __( 'Load Translations', 'hostifybooking' ) . '</button> ' .
				'<button type="button" id="save-translations-btn" class="button button-primary" disabled>' . __( 'Save Changes', 'hostifybooking' ) . '</button> ' .
				// '<button type="button" id="clear-comments-btn" class="button button-secondary" disabled>' . __( 'Clear Comments', 'hostifybooking' ) . '</button> ' .
				'<button type="button" id="download-po-btn" class="button" disabled>' . __( 'Download .po File', 'hostifybooking' ) . '</button>' .
				'</div>' .
				'<div id="translation-editor-status" style="margin: 10px 0; display: none;"></div>' .
				'<div id="translation-editor-form" style="display: none;">' .
				'<div class="translation-filters">' .
				'<label for="translation-filter">' . __( 'Filter translations:', 'hostifybooking' ) . '</label> ' .
				'<input type="text" id="translation-filter" placeholder="' . __( 'Search translations...', 'hostifybooking' ) . '" style="width: 300px;"> ' .
				'<label><input type="checkbox" id="show-untranslated-only"> ' . __( 'Show untranslated only', 'hostifybooking' ) . '</label>' .
				'<span id="translation-counter" style="margin-left: 15px; font-weight: bold; color: #0073aa;"></span>' .
				'</div>' .
				'<div id="translations-list" style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; padding: 0 10px; margin-top: 10px;"></div>' .
				'</div>' .
				'</div>',
		],
		[
			'id'      => 'translation_backup_content',
			'type'    => 'notice',
			'content' => '<div id="translation-backup-content">' .
				'<h2>' . __( 'Backup & Restore Translations', 'hostifybooking' ) . '</h2>' .
				'<p>' . __( 'Protect your translation work from being lost during plugin updates. Create backups before updates and restore them afterward.', 'hostifybooking' ) . '</p>' .
				'<div class="backup-controls">' .
				'<button type="button" id="create-backup-btn" class="button button-primary">' . __( 'Create Backup Now', 'hostifybooking' ) . '</button> ' .
				'<button type="button" id="refresh-backups-btn" class="button">' . __( 'Refresh List', 'hostifybooking' ) . '</button> ' .
				'<button type="button" id="cleanup-backups-btn" class="button button-secondary">' . __( 'Delete All Backups', 'hostifybooking' ) . '</button>' .
				'</div>' .
				'<div id="backup-status" style="margin: 10px 0; display: none;"></div>' .
				'<div id="backups-list" style="margin-top: 20px;"></div>' .
				'<div class="backup-help" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #0073aa; border-radius: 3px;">' .
				'<h4>' . __( 'Backup Actions Explained:', 'hostifybooking' ) . '</h4>' .
				'<ul style="margin: 10px 0; padding-left: 20px;">' .
				'<li><strong>Restore:</strong> ' . __( 'Restore all translation files from this backup (overwrites current files)', 'hostifybooking' ) . '</li>' .
				'<li><strong>Download ZIP:</strong> ' . __( 'Download all translation files as a ZIP archive for external storage', 'hostifybooking' ) . '</li>' .
				'<li><strong>Open Location:</strong> ' . __( 'View the server path and web URL where backup files are stored', 'hostifybooking' ) . '</li>' .
				'<li><strong>Delete:</strong> ' . __( 'Permanently delete this backup (cannot be undone)', 'hostifybooking' ) . '</li>' .
				'</ul>' .
				'<p class="tip-notice"><strong>' . __( 'Tip:', 'hostifybooking' ) . '</strong> ' . __( 'Download backups before plugin updates and store them safely. You can then upload individual .po files or restore entire backups after updates.', 'hostifybooking' ) . '</p>' .
				'</div>' .
				'<h3>' . __( 'Upload Translation File', 'hostifybooking' ) . '</h3>' .
				'<p>' . __( 'Upload a .po file to restore translations after a plugin update:', 'hostifybooking' ) . '</p>' .
				'<div class="upload-controls">' .
				'<label for="upload-po-file">' . __( 'Select .po file:', 'hostifybooking' ) . '</label> ' .
				'<input type="file" id="upload-po-file" accept=".po" style="margin: 0 10px;"> ' .
				'<button type="button" id="upload-po-btn" class="button" disabled>' . __( 'Upload & Restore', 'hostifybooking' ) . '</button>' .
				'<br><br>' .
				'<label><input type="checkbox" id="create-backup-on-upload" checked> ' . __( 'Create backup of existing file before replacing', 'hostifybooking' ) . '</label>' .
				'</div>' .
				'<div id="upload-status" style="margin: 10px 0; display: none;"></div>' .
				'</div>',
		],
	],
];

$fields[] = [
	'title'  => __( 'Documentation', 'hostifybooking' ),
	'name'   => 'documentation',
	'icon'   => 'dashicons-book',
	'fields' => [
		[
			'id'      => 'txt3',
			'type'    => 'notice',
			'content' => '<ol style="line-height:40px"><li><a href="'.admin_url('/options-general.php?page=hostifybooking-plugin').'">Read first &rarr;</li><li><a target="_blank" href="'.HOSTIFYBOOKING_URL.'doc/1_Initial_setup.docx"><img alt=".docx" src="'.HOSTIFYBOOKING_URL.'public/res/images/w-ico.png" width="33" align="absmiddle" /> '.__('Initial setup', 'hostifybooking').' <span class="dashicons dashicons-download" style="vertical-align:middle;text-decoration:none;font-size:14px"></span></a></li><li><a target="_blank" href="'.HOSTIFYBOOKING_URL.'doc/2_Guest_area_setup.docx"> <img alt=".docx" src="'.HOSTIFYBOOKING_URL.'public/res/images/w-ico.png" width="33" align="absmiddle" /> '.__('Guest area setup', 'hostifybooking').' <span class="dashicons dashicons-download" style="vertical-align:middle;text-decoration:none;font-size:14px"></span> </a></li></ol>',
		],
	],
];

$options_panel = new Exopite_Simple_Options_Framework( $config_submenu, $fields );

//

add_action('admin_bar_menu', 'hfy_add_toolbar_items', 100);

function hfy_add_toolbar_items($bar)
{
	// $options = get_option('hostifybooking-plugin');
	if (HFY_SHOW_ON_BAR) {

		$updnotif = '';

		$updplg = get_site_transient('update_plugins');
		if (isset($updplg->response['hostify-booking/hostifybooking.php'])) {
			$updnotif = ' <span class="awaiting-mod"><span class="feature-count">1</span></span> ';
		}

		$bar->add_menu([
			'id'    => 'hfy-fast-link',
			'title' => '<svg style="vertical-align:text-bottom" width="20" height="20" viewBox="0 0 73 75" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path opacity="0.4" d="M0.160156 34.8858C0.160156 33.9251 0.608394 33.0185 1.37423 32.4301L29.9578 8.51945C31.0871 7.65188 32.6652 7.65188 33.7945 8.51945L62.3781 32.4301C63.144 33.0185 63.5922 33.9251 63.5922 34.8858V71.0272C63.5922 72.7428 62.1898 74.1336 60.4597 74.1336H3.2926C1.5626 74.1336 0.160156 72.7428 0.160156 71.0272V34.8858Z" fill="#16A9FC"/><path d="M9.15234 27.017C9.15234 26.0563 9.60058 25.1497 10.3664 24.5613L38.95 0.650676C40.0793 -0.216891 41.6574 -0.216893 42.7867 0.650675L71.3703 24.5613C72.1361 25.1497 72.5844 26.0563 72.5844 27.017V63.1584C72.5844 64.874 71.1819 66.2648 69.4519 66.2648H12.2848C10.5548 66.2648 9.15234 64.874 9.15234 63.1584V27.017Z" fill="#167FFC"/><path fill-rule="evenodd" clip-rule="evenodd" d="M58.8177 28.9951L35.2706 52.5424L22.3555 40.0488L27.294 34.9436L35.188 42.5799L53.7951 23.9725L58.8177 28.9951Z" fill="white"/></svg> Hostify'.$updnotif,
			'meta' => [
				'title' => 'Hostify Booking Settings',
				// 'class' => ''
			],
			'href'  => get_admin_url() . 'options-general.php?page=hostifybooking-plugin',
		]);

		$bar->add_menu([
			'parent' => 'hfy-fast-link',
			'id' => 'hfy-fast-link-m',
			'title' => 'Settings',
			'href' => get_admin_url() . 'options-general.php?page=hostifybooking-plugin#optionsapi',
			'meta' => [
				'title' => 'Hostify Booking Settings',
			],
		]);

		if (HFY_DISABLE_CACHE) {
			$bar->add_menu([
				'parent' => 'hfy-fast-link',
				'id' => 'hfy-fast-link-cc',
				'title' => 'Cache is disabled now',
			]);
		} else {
			$bar->add_menu([
				'parent' => 'hfy-fast-link',
				'id' => 'hfy-fast-link-cc',
				'title' => 'Clear cache',
				'href' => get_admin_url() . 'options-general.php?page=hostifybooking-plugin&clr=1&redir='.urlencode(home_url($_SERVER['REQUEST_URI'])),
				'meta' => [
					'title' => 'Clear Hostify Cache',
				],
			]);
		}

		if (!empty(HFY_PAGE_LISTINGS_URL)) {
			$bar->add_menu([
				'parent' => 'hfy-fast-link',
				'id' => 'hfy-fast-link-lp',
				'title' => 'View listings page',
				'href' => HFY_PAGE_LISTINGS_URL,
			]);
		}

		if (!empty($updnotif)) {
			$bar->add_menu([
				'parent' => 'hfy-fast-link',
				'id' => 'hfy-fast-link-upd',
				'title' => 'Plugin update '.$updnotif,
				'href' => get_admin_url() . 'plugins.php?s=Hostify%20Booking%20Engine&plugin_status=search',
				'meta' => [
					'title' => 'Hostify Booking Plugin Update',
				],
			]);
		}

	}
}
