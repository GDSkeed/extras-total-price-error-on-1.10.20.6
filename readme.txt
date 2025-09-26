=== Hostify Booking Engine plugin ===
Stable tag:        1.9
Contributors:      hostify
Requires at least: 5.5
Tested up to:      6.6
Requires PHP:      7.4
License:           Proprietary
License URI:       license.txt
Text Domain:       hostifybooking
Tags:              hostify, booking, property management, pms, reservations, booking engine, booking website, airbnb, booking.com, expedia, homeaway, agoda, tripadvisor

== Description ==

Get your own booking website for free! Start converting direct bookings and building your loyal guest base.

[Hostify.com](https://hostify.com)

Make your own booking website using Hostify Booking Engine API.


# Please Read Before Updating!
We're excited to announce a new version of our plugin that brings significant improvements and changes. However, this update includes several breaking changes that require careful attention before upgrading.

## Critical Update Information
IMPORTANT: This is a major update that introduces breaking changes. We strongly recommend testing this update on a staging environment before applying it to your production site.

##Recommended Update Process
1) Backup: Create a full backup of your website
2) Stage: Create a staging environment
3) Test: Install the update on your staging site
4) Verify: Test all plugin functionality thoroughly
5) Review: Check for any conflicts with other plugins or themes
6) Plan: Schedule the production update during low-traffic hours
7) Update: Only proceed with the production update after successful staging tests

== Requirements ==

⚠️ NOTE:
`
The plugin does not work correctly on the wordpress.com platform, so you need to use some different WP hosting instead.
We recommend kinsta.com for an easy start.
`

PHP version: 7.4+, 8+
PHP extensions: curl, json, mbstring.

The production website should work via HTTPS.

You need to know how to work with pages and posts in WordPress:
→ [Post vs. Page](https://wordpress.com/support/post-vs-page/)
→ [What is the Difference Between Posts vs. Pages in WordPress](https://www.wpbeginner.com/beginners-guide/what-is-the-difference-between-posts-vs-pages-in-wordpress/)
→ [WordPress Pages vs Posts: What's the Difference?](https://ithemes.com/blog/wordpress-pages-vs-posts-whats-the-difference/)

You need to know what shortcodes are:
→ [Shortcodes in WordPress](https://codex.wordpress.org/Shortcode)

== Installation ==

1. Unpack the zip file and upload the extracted "hostify-booking" folder to "/wp-content/plugins/".
Or you can use the WordPress plugin installer: Plugins → Add New → Upload Plugin,
choose the zip file and press the button "Install Now".

2. Activate the plugin through the "Plugins" menu

3. Set up the configuration for website in Hostify PMS first ("Apps" → "WordPress"):

	3.1. Click on "Create New Website" and type new configuration nickname, ex "my new website" and save it

	3.2. Now you are on website settings, turn on switch on top to mode "ON" first

	3.3. On "General" page, tab "Connection", you can find out two grayed fields:

		API URL
		API WPKEY

	Please copy their content, this information you will use in further.

	3.4. Fill the address of the website in field "Domain", (ex: https://mysite.com)

	3.5. On "General"/"Main" tab select ailable at least one City

	3.6. On "General"/"Payment/Prices" tab fill in the "Direct Inquiries Email" and tune up rest of payment settings, click "Save" button

	3.7. Open tab "Listings" and select the Listings will be available on the website, click "Save"

4. Now go back to the WP admin of your new website and check out "Settings" → "Hostify Booking Plugin"

  	4.1. "API settings" tab:

	4.1.1. Fill both fields API URL and API WPKEY using copied data earlier

		For example, it can be look like this:
			API URL: https://pmsapi.hostify.com/
			API WPKEY: x1NSd0xN6RzdcmFVWwYzRictMyLThwegHGRj=

	4.1.2. Press the "Save Settings" button

	4.2. "Select pages" tab:

	4.2.1. Сlick these links in sequence:

	- Create search result page
	- Create single listing page
	- Create payment page
	- Create charge page

	this will create the necessary pages with the necessary shortcodes on them.

	4.2.2. Later you can change any of these selections, you are free to use any page on your site.

	4.3. On "Shortcodes" tab you can see all shortcodes available in the plugin.

	4.3.1. On page where you want to show list of all available Listings,
	place the shortcode [hfy_listings] (if not added automatically on step 4.2.1)

	4.3.2. Place the shortcode [hfy_listing] on page "Listing" (if not added automatically on step 4.2.1)

	4.3.3. Place the shortcode [hfy_payment] on page "Payment" (if not added automatically on step 4.2.1)

	4.3.4. Place the shortcode [hfy_payment_charge] on page "Payment result" (if not added automatically on step 4.2.1)

	4.3.5. Any of shortcodes you can place on any page, post or widget where you want

5. If everything is configured correctly, you can open the Listings page and see the list of your Properties on your WP website.

== Guest Area ==

If you use the ability to register/login users on your site
(plugins like Ultimate Member, ProfilePress, BuddyPress, S2Member, WP User Manager, bbPress, etc),
the Hostify plugin allows to add some functions for registered users:

	- automatic substitution of the user's names and personal data in the payment/inquiry form
	- user can see a list of his bookings: upcoming, current and past
	- user can add listings to his own wishlist
	- user can cancel upcoming reservation (TODO)

New shortcodes added:

	[hfy_user_bookings_list type=""]
	[hfy_user_booking_manage]
	[hfy_user_wishlist]

Please read the documentation for a quick guide.

== List of all available shortcodes ==

List of shortcodes that this plugin provides:

Show the search result:

[hfy_listings]
[hfy_listings_map]
[hfy_listings_map_toggle]
[hfy_listings_selected]
[hfy_top_listings]
[hfy_recent_listings]

Search form:

[hfy_booking_search]
[hfy_booking_search_popup]

One separate listing:

[hfy_listing]

Payment page:

[hfy_payment]

Payment options and extras:

	Predefined extras block:

		[hfy_payment_extras_set id='<listing_id>' ids='' detailed='' selected='']
			... custom text ...
		[/hfy_payment_extras_set]

	Optional extras block with checkboxes:

		[hfy_payment_extras_optional id='<listing_id>' except='54,56,57' checked='']

Payment result page (NetPay):

[hfy_payment_charge]

Parts of a single listing:

[hfy_listing_info]
[hfy_listing_title]
[hfy_listing_room_type]
[hfy_listing_facilities]
[hfy_listing_gallery]
[hfy_listing_image]
[hfy_listing_amenities]
[hfy_listing_booking_form]
[hfy_listing_map]
[hfy_listing_availability]
[hfy_listing_reviews_count]
[hfy_listing_reviews_summary]
[hfy_listing_reviews_comments]
[hfy_listing_virtual_tour]

Sub-items of [hfy_listing_info]:

[hfy_listing_info_summary]
[hfy_listing_info_space]
[hfy_listing_info_guest_access]
[hfy_listing_info_interaction]
[hfy_listing_info_notes]
[hfy_listing_info_transit]
[hfy_listing_info_neighbourhood]
[hfy_listing_info_house_rules]
[hfy_listing_info_prices]
[hfy_listing_info_permit]

Display the value of any accessible field from the Listing object:

[hfy_listing_field]

Display the value of any accessible field from the Listing Details object:

[hfy_listing_details_field]


== Shortcodes parameters ==

[hfy_listings]

	cities=""
		get listings by city ID(s)
		Ex: [hfy_listings cities="1,2"] - will show listings by cities 1 and 2

	tags=""
		get listings by tags
		Ex: [hfy_listings tags="tag1,tag2"]

	sort=""
		0 - No sort (default)
		1 - Sort by listing price, descending order (high to low)
		2 - Sort by listing price, ascending order (low to high)
		3 - Sort by listing title
		4 - Sort by listing nickname
		Ex: [hfy_listings sort="1"]
		The sort parameter can be passed in the URL parameter (...&sort=)

	ids=""
		show one or more listings by their ID(s)
		Ex: [hfy_listings ids="1000,1050"]
		Note: By default, result is sorted according to passed IDs order.
		If the "sort" option is added, sorting the result will match the specified option,
		for example: [hfy_listings ids="1050,1000" sort="2"] - the result will be sorted by price, not in IDs order.

	with_amenities=""
		false - without amenities (default)
		true - with amenities
		Ex: [hfy_listings amenities="true"]

[hfy_listings_map]

	city=""
	cities=""
		get listings by city ID(s)
		Ex: [hfy_listings_map cities="1,2"] - will show listings by cities 1 and 2

	ids=""
		show listings on the map by listings ID(s)
		Ex: [hfy_listings_map ids="1000,1050"]
		Note: if you use the "ids" attribute for [hfy_listings], use it here too.

	closebutton=""
		show the button to close map
		default value is "false"
		Ex: [hfy_listings_map closebutton="true"]

	tags=""
		get listings by tags
		Ex: [hfy_listings_map tags="tag1,tag2"]

[hfy_listing]
[hfy_listing_info]
[hfy_listing_info_summary]
[hfy_listing_info_space]
[hfy_listing_info_guest_access]
[hfy_listing_info_interaction]
[hfy_listing_info_notes]
[hfy_listing_info_transit]
[hfy_listing_info_neighbourhood]
[hfy_listing_info_house_rules]

	id=""
		can be passed to get the result for a specified Listing by ID,
		ex: [hfy_listing id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL,
		ex: https://site.com/listing/?id=1234

[hfy_listings_selected]

	cities=""
		get listings by city ID(s)
		Ex: [hfy_listings_selected cities="1,2" max="4"] - will show listings by cities 1 and 2

	paramcity="true"
		get by city ID given in the url parameter (...&city_id=)
		Ex: [hfy_listings_selected paramcity="true"] - will show listings by parameter in url

	currentlistingcity="true"
		get by same city, to which linked the listing specified in the url parameter (...&id=)
		Ex: [hfy_listings_selected currentlistingcity="true"]

	max=""
		limit the output.
		Default value is 4.
		If "0" passed then all elements will be output, without limit.
		Ex: [hfy_listings_selected cities="1,2,3,4" max="8"]

[hfy_top_listings]

	max=""
		limit the output.
		Default value is 4.
		If "0" passed then all elements will be output, without limit.
		Ex: [hfy_top_listings max="8"]

[hfy_listing_reviews_comments]

	max=""
		limit the output.
		Default value is 3.
		If "0" passed then all elements will be output, without limit.
		Ex: [hfy_listing_reviews_comments max="8"]

	layout=""
		reviews list display mode.
		layout="vertical" (default)
		layout="horizontal"
		Ex: [hfy_listing_reviews_comments layout="horizontal"]

[hfy_listing_reviews_summary]

	id=""
		Get the reviews summary info for a specified Listing by ID
		ex: [hfy_listing_reviews_summary id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL

[hfy_listing_reviews_count]

	id=""
		Get the number of reviews for a specified Listing by ID
		ex: [hfy_listing_reviews_count id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL

[hfy_booking_search]

 	advanced="true"
 		Show additional fields in the search form for advanced search.
 		Ex: [hfy_booking_search advanced="true"]

	tagsmenu=""
		Replace locations dropdown menu with tags passed.
 		Ex: [hfy_booking_search tagsmenu="tag1,tag2"]

	samepage=""
		Submit the search form to the same page instead of the one selected in plugin setting ("Select pages" → "Listings page").
 		Ex: [hfy_booking_search samepage="true"]

[hfy_listing_virtual_tour]

	id=""
		Render the virtual tour for a specified Listing by ID
		ex: [hfy_listing_virtual_tour id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL

[hfy_listing_field]

	id=""
		Listing ID
		ex: [hfy_listing_field id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL

	name=""
		Name of the field in $listing->listing object
		ex: [hfy_listing_field name="street"]

		The full list of fields can be seen, for example, in the overridden listing template, dump the object:
		<?php var_dump($listing->listing); ?>

[hfy_listing_details_field id="" name=""]

	id=""
		Listing ID
		ex: [hfy_listing_details_field id="1234"]
		If id parameter is not specified, the Listing code will be taken from the URL

	name=""
		Name of the field in $listingDetails object
		ex: [hfy_listing_details_field name="person_capacity"]

		The full list of fields can be seen, for example, in the overridden listing template, dump the object:
		<?php var_dump($listingDetails); ?>

Common parameters for all shortcodes:

	nowrap="1"
		Disables rendering of the wrapping code for shortodes: <div class="hfy-wrap ...
		ex: [hfy_booking_search nowrap="1"]


== Styles and templates ==

You can override styles and change the layout of almost all the blocks that the plugin renders.

Override CSS:

Just use the CSS class names of blocks in your theme or custom CSS.
You can refer and use the sources: `wp-content/plugins/hostify-booking/src/sass/`

Override the plugin template files - you can use one of the following methods:

**1. Override with files placed in your current theme folder**

Just copy the files you need, keeping the nested directories, from directory:
```
wp-content/plugins/hostify-booking/tpl/*
```
to directory:
```
wp-content/themes/YOUR-ACTIVE-THEME-FOLDER/hostify-booking-templates/*
```

Do not copy files that you do not plan to change, so as not to complicate the work.

**2. Specify the path to the directory with templates to override in the plugin options**

Settings → Hostify Booking Engine → Admin options → Custom template path

**3. Use the WP filter**

In your custom code you can use the hfy_tpl_path filter. For example:
`
function my_custom_hfy_tpl_path($args) {
	// $args['file'] like '/www/website1/wp-content/plugins/hostify-booking/tpl/listing/listing-amenities.php'
	// $args['path'] like '/www/website1/wp-content/plugins/hostify-booking/tpl/'
	// $args['name'] like 'listing/listing-amenities'
    return '/home/user/my-path-to-templates/' . $args['name'] . '.php';
}

add_filter('hfy_tpl_path', 'my_custom_hfy_tpl_path');
`

So you can safely fix or change the necessary parts of the layout according to your needs
and not be afraid that updating the plugin will erase your changes.


== Frequently Asked Questions ==

= Where I can learn more about this plugin? =
[Hostify.com](https://hostify.com)

= After change the settings/configuration, I see don't see any changes on website, =
= I see the message "Try again later" =
Press the 'Clear cache' button on "Settings" → "Hostify Booking Engine" page.
If you have any caching WP plugin, clear the caches in this plugin.
If that doesn't help, try to delete the website cookies in the browser and refresh the page.
If that doesn't help either, please contact Hostify.

= Problems with Google Maps =
We use 3rd party script gmap3.js -
make sure you don't block it with other plugins or actions
for example, plugins or services for security, optimization, and so on.

= Debug =

You can turn on the request log to the Hostify API.
In wp-config:

define('HFY_API_LOG', './curl.log'); // turn on log

The event log will be collected in the specified file in the following format:

[time][process id][status:OK|ERR][method:GET|POST|...<CACHED>][elapsed/time_elapsed/request_elapsed (sec)] url \n post parameters \n curl error info

You can also add these constants to see extended information:

define('HFY_API_LOG_DETAILED', true); // log curl headers and response
define('HFY_API_LOG_TRACE', true); // log wp_debug_backtrace


= Selected dates are not appears after selection in the booking form =
Check that [hfy_listing_booking_form] shortcode is used only once on the page.

= SEO? =
The plugin includes support for the Yoast SEO and Rank Math plugins: meta, URL, XML sitemap for listings.
Check out the plugin settings → SEO.

= I am not receiving any emails from the site =
This is a problem of a specific server or site.
This does not apply to the operation of this plugin.
Check if email settings is correct on the site/server, or contact the server and site support.

== Upgrade Notice ==

Since version 1.9.0, the plugin supports checking for updates and give the ability to update just by click.

= Manual update steps: =

1. Plugins → Add New → Upload PLugin → "Replace current with uploaded" button.

2. Open settings page: Settings → Hostify Booking Engine,
check the options and press "Save settings" button.

3. Click "Clear cache" button.

= After update =

In case if you have any redefined Hostify plugin templates in your theme, compare the changes and fix/update your code if necessary.

== Changelog ==

See CHANGELOG.md
