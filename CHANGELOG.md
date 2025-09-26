# Changelog

## ATTENTION PLEASE!
## Please read the changes carefully before each update.
## After updating, check and save the plugin settings, clear the cache.
## If some templates have been changed (overwritten) in your custom theme, compare them with the changes from the new version of the plugin.

= 1.10.20.6 =
* Added reviews for VBRO and internal Hosify for single listing.

= 1.10.20.5 =
* Custom Search fix to show listings and map correcly
* Lisings from map to open with the selected dates


= 1.10.20.4 =
* Control location filter visibility for v3 API (Can't be similar to v2, added checkbox for it in the Hostify options)

= 1.10.20.3 =
* Location selector for search bar fixed on v3, it did not show. Now it's showing when checkd in the hosify options.

= 1.10.20.2 =
* Enhanced the Keywords search (map it's showing location of the search and the serch is more specific)
* Added price display fix for v3 (Problem: Nightly rates weren't showing for v3 API listings; Solution: Added fallback mechanism for v3 pricing structure; Result: Price display now works correctly for both v2 and v3)
* Added Listing count discrepancy fix: Problem: Different property counts between search list and map; Solution: Removed redundant client-side price filtering; Result: Consistent listing counts across all views
* Added styling to the restart button

= 1.10.20.1 =
* Fixed: [hfy_listings_map tags="tag1,tag2,tag3"]
* Fixed: [hfy_listings tags="tag1,tag2,tag3"]
* Fixed: Translation Manager "Available Languages" - Update language translation percentage when you press the "Extract Strings" button and "Save Changes" button on "Inline Translation"
* Added: from Datepicker to translation files : - Datepicker month names (full) - Datepicker month names (short) - Datepicker day names (full) - Datepicker day names (short)
* Added: singular/plural translation option
* Added: Translations
	he_IL
	zh_CN

= 1.10.20 =
* Added: Review System (Single Listing) - intelligent fallback from API v3 to v2 when channel data is empty

= 1.10.19.9 =
* Added: Automatic BGN/EUR dual currency display for all price outputs when the listing currency is BGN
* Fixed: Prevent HotelDatepicker initialization error when input element is missing
* Added: Null checks in datepicker.js to prevent "parentElement" TypeError
* Added: Early return with warning when hotel-datepicker input element doesn't exist
* Added: HotelDatepicker function availability check
* Added: Null checks for dependent event listeners and V3 calendar initialization

= 1.10.19.8 =
* Fixed: Rating Scale Conversion: Proper conversion from Booking.com's 10-star to 5-star display
* Fixed: Review Filtering: Skip reviews that have no stars and no comments
* Added: Admin Configuration Options for Reviews
* Added: Source Display: Support for showing review source (Airbnb/Booking.com)
	Affected templates:
		listing-reviews-comments.php
		listing-reviews-summary.php

= 1.10.19.7 =
* Added: Google reCAPTCHA Secret Key: Private key for server-side verification
* Added: reCAPTCHA Version Support
	v2 (Checkbox): Traditional "I'm not a robot" checkbox
	v3 (Invisible): Background verification with score-based validation
	Graceful Fallback: If no keys are configured, forms work without reCAPTCHA
* Removed: hardcoded v2 implementation

= 1.10.19.6 =
* Fixed: Notice: Translation loading for the hostifybooking domain was triggered too early.

= 1.10.19.5 =
* Fixed: Notice: Translation loading for the hostifybooking domain was triggered too early.

= 1.10.19.4 =
* Added: View more reviews
	A view more reviews button if listing has more then 10 reviews

= 1.10.19.3 =
* Added: Translations
	ro_RO

= 1.10.19.2 =
* Update: Translations
	ru_RU
	sv_SE

= 1.10.19.1 =
* Update: Translations
	bg_BG
	de_DE
	es_ES
	fr_FR
	it_IT
	pl_PL
	pt_PT

= 1.10.19.0 =
* Added: Translation Manager
	String Extraction System
	Inline Translation Editor
	Backup Management System
	File Management
	Advanced Features

= 1.10.18.9 =
* Edit: listing Helpers "1 => 'Entire home/flat'" to 1 => 'Entire home'
* Edit: ES Translation

= 1.10.18.8 =
* Edit: ES Translation

= 1.10.18.7 =
* Added: BG Translation
* Edit: DE Translation
* Fixed: If the update server is unreachable or returns an error - FIX

= 1.10.18.6 =
* Added: Keyword Search in Search Form - Works with API v3 for comprehensive search across all listings
	Affected template:
		element/booking-search.php

= 1.10.18.5 =
* Fixed: Single Listing - Review Rating issue fix

= 1.10.18.4 =
* Fixed: URL issue with Polylang plugin
* Fixed: Single Listing - Review Rating scale from 1-10 into 1-5

= 1.10.18.3 =
* Fixed: Single Listing - Review Rating
* Added: [hfy_listing_availability] in Read me first - Parts of a single listing

= 1.10.18.2 =
* Fixed: Phone View V3 Datepicker
* Fixed: Listing Info Prices
* Added: V3 datepicker in hfy_listing_availability for Single Listing Location
	Affected templates:
		listing/listing-availability.php
		listing/listing-info-prices.php

= 1.10.18.1 =

* Booking form price issue - FIX
	Affected template:
		element/price-block-accounting.php

= 1.10.18 =

* Improved shortlink redirection

= 1.10.17.9 =

* Added: Inquiry Form v2

= 1.10.17.4 =

* Added: Listing Card Slider Lazy Load
* Improved: SEO Listing Slug Module

= 1.10.17 =

* Added: BETA: Calendar v3 ( Enable through Admin Options )
```
	Affected templates:
		element/booking-search.php
		listing/listing-booking-form-v2.php
		listing/listing-booking-form.php
```

= 1.10.16.3 =
* Fixed inquiry form date visualisation

= 1.10.16.2 =

* Added: Gallery Support in Top Listings
* Added: Support for Extras Description in Payment Flow
* Added: Option to select Static or Dynamic Map for Single Listing Location
* Added: Static Map on Single Listing Location
* Added: API V3 Payment Schedule Support
* Added: API V3 Discounts Support
* Updated: Display Logic of the Discounts in the Listing Booking Form
* Updated: Single Listing Location Map Pins and Radius
* Updated: Swedish Translations
* Updated: Translations
* Fixed a bug with extras and payment
* Fixed a bug causing the listing booking form to not automatically display the prices if the user had selected the dates previously
* Fixed a bug with the direct inquiry modal displaying a blank checkbox for the terms and conditions if none were selected in the PMS
```
	Affected templates:
		element/price-block-accounting.php
		element/price-block-default.php
		element/direct-inquiry-modal.php
		listing/listing-booking-form-v2.php
		listing/listing-location.php
		listing/top-listings.php
		payment/extras-optional.php
		payment/extras-set.php
		payment/stripe-form-3ds-element-step-3.php


```


= 1.10.15.7 =

* Fixed calendar CSS 
* Modified plugin textdomain loading

= 1.10.15.6 =

* Fixed an undefined array key warning

= 1.10.15.5 =

* Added: Tablet view support with map toggle functionality
* Added: New tablet option in shortcode attributes
* Added: Tablet-specific CSS and styling
* Added: Show/hide map functionality for tablet devices
* Added: Automatic tablet resolution detection
* Added: Created At date for reservations
* Fixed: Map type handling
* Added: Initial translations for Portuguese (PT) and Swedish (SE)
* Added: Plugin version logging in API calls
* Added: Custom map type support

= 1.10.15.4 =

* Fixed: disabled date selection in calendar

= 1.10.15.3 =

* Fixed: number of images in gallery template

= 1.10.15.2 =

* Added: custom map styles option

= 1.10.15 =

* Fixed: layout mistakes in payments templates
* Added: new option: Listings - Open in new window
* Added: select default phone area code in payment forms
* Updated: intl-tel-input js component

= 1.10.14.2 =

* Fixed: API cache timeout for getAvailableListings

= 1.10.14 =

* Fixed: default dates
* Fixed: click behavior on images in slider

= 1.10.13 =

* Fixed: selected dates in search/booking forms
* Fixed: moment.js locales

= 1.10.12.2 =

* Fixed: payment page issues (stripe loading)

= 1.10.12 =

* Fixed: google maps loading

= 1.10.11 =

* Fixed: guest dialog closing on the search form
* Added: timeout on the payment page
```
	Affected templates:
		payment/*
```
* Fixed: empty images in reviews
```
	Affected templates:
		listing/listing-reviews-comments.php
```
* Minor js/css fixes and cleanup

= 1.10.10 =

* Fixed: cta/ctd issue

= 1.10.9 =

* Fixed: select phone field for paypal templates
* Updated: intl-tel-input js library
* Added: new parameter "mobile" for [hfy_listings_map_toggle] shortcode
```
	Affected templates:
		listing/listings-map-toggle
```

= 1.10.8 =

* Fixed: min stay hints

= 1.10.7 =

* Fixed: inquiry dialog
* Added: 'hfy-listings-updated' JS custom event

= 1.10.6 =

* Fixed: api v3 prices
* Fixed: payment templates

= 1.10.4 =

* Fixed: api v3 discounts
* Added: Revyoos Widget on the single listing page
```
	Affected templates:
		listing/listing
```

= 1.10.3 =

* Fixed: Images slider for listings

= 1.10.2 =

* Fixed: Selecting a second date in the listing calendar

= 1.10.1 =

> ## Critical Update Information
> ### IMPORTANT: This is a major update that introduces breaking changes.
> ### We strongly recommend testing this update on a staging environment before applying it to your production site.

* Added: New option: Redirect if no listing
* Added: New option: Disable payment templates (default is ON - disable)
* Added: New shortcode [hfy_listings_info], please check the plugin options for details
* Added: New shortcode [hfy_listings_sort]
* Added: New shortcode [hfy_listing_availability]
* Added: "Neighbourhood, City name" option for Location selector
* Added: "Custom key" option for google maps & recaptcha
* Added: Amenities codes list: Settings - Hostify Booking Engine - Dictionaries - Amenitites
* Added: Catch 'source' for reservations
* Added: Map zoom option
* Added: "Reset" button on mobile calendar
* Fixed: Map style
* Fixed: Minor map issues
* Fixed: Tags menu option
* Fixed: Images slider option
* Fixed: Live search option
* Fixed: Selecting filtered location
* Fixed: Guest selection dialog closing issue
* Fixed: Show/hide details on payment page (mobile)
* Fixed: gtag item_id, item_name
* Fixed: Send gtag "purchase" event for PayPal form
* Fixed: PayPal payment issues
* Fixed: Search & Booking forms js handlers
* Fixed: Admin options
* Fixed: API v3 issues
* Updated: SumoSelect component
* Deprecated: "action scheduler" is turned off now (performance issue)
* Deprecated: jquery-lazy plugin for images is deactivated now, check and fix yours overrided templates
* Improved: Payment page templates
```
	Affected templates:
		payment/*
```
* Improved: Date picker design & behavior for desktop and mobile
```
	Affected templates:
		listing/...
```
* Added: New option: Options - Listings - Images slider (adds an image slider for each listing item on the listings page)
```
	Affected templates:
		listing/listing
		listing/listing-item
```

= 1.9.22.6 =

* Fixed: listing name on payment page

= 1.9.22.5 =

* Fixed: minor css issues

= 1.9.22.4 =

* Added: option to hide age hints in the guests selection dialog
```
	Affected templates:
		element/guests-block.php
```

= 1.9.22.3 =

* Fixed: minstay hint

= 1.9.22.2 =

* Fixed: payment layout
```
	Affected templates:
		payment/netpay.php
		payment/stripe-form-3ds.php
		payment/stripe-form-3ds-element.php
```
* Added: additional custom info on payment page
```
	New template:
		payment/additional-info.php
	(see example inside the template)
```

= 1.9.22.1 =

* Fixed: stripe payment

= 1.9.21.15 =

* Fixed: stripe payment compatibility

= 1.9.21.14 =

* Fixed: showing areas on the map

= 1.9.21.13 =

* Fixed: location filter option (APP - WordPress - Accomodation search)

= 1.9.21.12 =

* Fixed: initial update of price block on single listing page

= 1.9.21.11 =

* Fixed: paypal payment integration issues

= 1.9.21.10 =

* Fixed: extras on payment page
* Added: 'show extras' option in setting

= 1.9.21.9 =

* Fixed: performance issue ('is_changed' processing)

= 1.9.21.8 =

* Fixed: reviews templates
```
	Affected templates:
		listing/listings-item.php
		listing/listing-reviews-stars.php
```

= 1.9.21.7 =

* Added: property type
```
	Affected templates:
		listing/listing.php
```

= 1.9.21.6 =

* Fixed: start of week in the calendars, now it matches the WP Settings - General - Week Starts On (only Synday & Monday are accepted)
* Fixed: GA gtag event 'purchase'

= 1.9.21.5 =

* Added: GA gtag event 'purchase'

= 1.9.21.3 =

* Fixed: single listing map issue

= 1.9.21.2 =

* Added: $listingData and $listingDescription objects for payment page templates

= 1.9.21 =

* Fixed: select dates on mobile
* Fixed: languages (ES, FR, IT, DE)
* Updated: Nuxy component

= 1.9.20.2 =

* Fixed: reviews sorting

= 1.9.20.1 =

* Fixed: some api calls
* Fixed: some php warns/notices

= 1.9.18.15 =

* Fixed: caching optimization

= 1.9.18.14 =

* Fixed: show seasonal promotions on listings/listing/payment
* Fixed: API v3 calls
* Fixed: minor issues

= 1.9.18.13 =

* Added: new shortcode [hfy_recommended_listings max="4" tags="tag1,tag2"]
* Fixed: button 'Close' on guests selector for mobile screens
* Fixed: inquiry form calendars issue
* Fixed: minor css issues

= 1.9.18.12 =

* Added: new option "Redirect on payment success"
* Added: new option "Max guests number"
* Fixed: guests selector behavior for mobile screens
```
	Affected templates:
		element/guests-block.php
```

= 1.9.18.11 =

* Fixed: guests selector

= 1.9.18.10 =

* Added: custom events using GTM
* Fixed: php warnings
* Minor changes

= 1.9.18.9 =

* Fixed: pagination issues
* Fixed: php warnings

= 1.9.18.8 =

* Fixed: getAvailableListings cache

= 1.9.18.7 =

* Fixed: getAvailableListings/map with API V3
* Fixed: priceOnRequest issue
* Fixed: guests selector
* Fixed: map zoom/tracking/results issue

= 1.9.18.6 =

* Fixed: CTA/CTD/minstay for seasons

= 1.9.18.5 =

* Fixed: listing SEO title generation

= 1.9.18.4 =

* Added: new listing objects: $cancellationPolicy, $paymentSchedule
* Added: new shortcode [hfy_listing_cancellation_policy]

= 1.9.18.3 =

* Fixed: round prices on the map
* Fixed: mobile date picker behavior
* Fixed: date picker hints
* Fixed: show CTA (closed to arrival) and CTD (closed to departure) days on date picker calendar
* Fixed: listings permalinks generation (SEO)
* Fixed: minor issues (admin)

= 1.9.18.2.6 =

* Fixed: pretty listing URL for child pages
* Fixed: options
* Fixed: remove "Image preloader" option

= 1.9.18.2.4 =

* Added: using custom WP menu as tags menu for location selector, new option in settings: Tags menu

= 1.9.18.2.3 =

* Fixed: Phone number length validation on payment page
* Fixed: minor issues
```
	Affected templates:
		payment/stripe-form-3ds.php
```

= 1.9.18.2.2 =

* Fixed: Listing info summary content
* Fixed: SEO noindex tag handling

= 1.9.18.2 =

* Added: New SEO option: set NoIndex tag for seleted listings (Settings - Hostify Booking Engine - SEO)
* Added: SEO: added 'transaction_amount' parameter to 'hfy_payment_success' GTAG event
* Fixed: Google Analytics GTAG events for PayPal and Netpay
```
	Affected templates:
		payment/paypal-form.php
		payment/netpay-form.php
```

= 1.9.17.9 =

* Fixed: redirect by hfylisting URL parameter

= 1.9.17.8 =

* Fixed: PHP warning messages
* Fixed: PHP 8.3 fatal error issue
* Fixed: error in redirect_to_listing_page
* Fixed: End date selection on booking form
* Fixed: Live search (Filter locations while typing in selector)
```
 	Affected templates:
		element/booking-search.php
```
* Added: New option for shortcode [hfy_listing_gallery view="ab"]
```
 	Affected templates:
		listing/listing-gallery-abnb.php
```
* Added: New option for shortcode [hfy_listings max="3"]
```
	Maximum items for show, default value is "Items (listings) per page" (20)
```
* Added: Search by listing name (only with option "Live search" and
using API v3)

= 1.9.17 =

* Added: NetPay payment integration

= 1.9.16 =

* Fixed: readme
* Added: experimental option to use API v3

= 1.9.15 =

* Fixed: slow rendering of calendar (date picker) on iOS devices
* Fixed: unavailable days were not shown in the calendar on iOS devices

= 1.9.14 =

* Fixed: The pet selector is now correctly applied for the search results

= 1.9.13 =

* Fixed: issue with booking form on listing page when opening Inquiry dialog

= 1.9.12 =

* Fixed: html layout mistake in tpl/listing/listings template

= 1.9.11 =

* Fixed: JS error on listing page

= 1.9.10 =

* Fixed: old style guest selector (NaN issue)

= 1.9.9 =

* Fixed: update of the booking form when closing the calendar on listing page
* Fixed: SEO settings options.
```
	Please check out: Settings -> Hostify Booking Engine -> SEO -> GA Events
```

= 1.9.8 =

* Fixed: processing of long_term_mode variables and settings

= 1.9.7 =

* Fixed: date picker js issue in iOS
* Optimized: maps scripts loading

= 1.9.6 =

* Fixed: show selected/default for radio option in the plugin settings
* Added: option to change the 'Select location' placeholder text in the search form
* Added: option to sort listings by title or nickname

= 1.9.5 =

* Added: option to set default listings sorting behavior: Settings - Options - Listings - Default sorting

= 1.9.4 =

* Fixed: php warnings
* Fixed: redirect hfylisting= to pretty listing URL

= 1.9.3 =

* Fixed: selecting Adults and Children in the search form
* Fixed: Option to move Pets select to the advanced search form

= 1.9.2 =

* Added: $total_items variable in tpl/listing/listings template

= 1.9.1 =

* Added: adults, children, infants, pets select in search form
```
Changed templates:
	element/booking-search
	listing/listing-booking-form-v2
Added templates:
	element/guests-block
```

= 1.9.0 =

* Added: check for plugin updates
* Added: SEO option: If the requested listing is not found, the page will be redirected (301) to the specified URL, instead of "No listing" message.

= 1.8.21 =

* Fixed: end_date issue

= 1.8.20 =

* Fixed: monthly price fallback on listings page

= 1.8.19 =

* Fixed: "monthly" parameter for [hfy_listings] shortcode

= 1.8.18 =

* Added: "with_amenities" parameter to [hfy_listings] shortcode
```
	Example: [hfy_listings with_amenities="true"]
```

= 1.8.17 =

* Fixed: jquery.modal issue
* Fixed: js error "meImg is not defined"

= 1.8.16 =

* Added option: Show location on map as point or area
* Fixed: rendering JS variables once for listing page
* Fixed: parsing response from api
* Fixed: php notices

= 1.8.15 =

* Added: perform search text with accents/diacritics in the live search dropdown

= 1.8.14 =

* Fixed: sort cities in the live search dropdown
* Fixed: Nuxy component excluded for public rendering

= 1.8.13 =

* Fixed: missed parameters for listings filter

= 1.8.12 =

* Fixed: uncomment weekly/monthly discounts in templates

= 1.8.11 =

* Fixed: CSS classes names for jquery.modal plugin, to prevent collisions with the theme or other plugins

= 1.8.10 =

* Fixed: discount code missed at payment

= 1.8.9 =

* Added: pets and infants options: Options - Listing - Use new guests template
```
Changed templates:
	listing/listing-booking-form-v2
```

= 1.8.8 =

* Fixed: listing title translation

= 1.8.7 =

* Fixed: payment automations issue

= 1.8.6 =

* Fixed: use listing currency for PayPal payment
* Added: check for currencies supported by PayPal on payment page

= 1.8.5 =

* Fixed: display an error on the payment page if the payment service is not available
* Fixed: doGtagEvent hfy_payment_success

= 1.8.4 =

* Added: sitemap support for Rank Math SEO plugin

= 1.8.3 =

* Fixed: parameters for ajax loading on listings page
* Updated: Nuxy component
```
Changed templates:
	- element/price-block
	- payment/preview
	If you previously overridden it, please check the changes.
```

= 1.8.2 =

* Fixed: plugin options
* Fixed: handling of pets option

= 1.8.1 =

* Fixed: ungrouped amenities added to available for use
* Fixed: sort listings
* Fixed: log CURL actions, see readme.txt to detailed explanation (Frequently Asked Questions -> Debug)
* Fixed: cleanup ActionScheduler logs
* Fixed: dynamically update content of the 'listings' shortcode on map moving, with pagination (when "Map tracking" option is turned on)
* Changed: data for 'listings map' shortcode is now loaded separately and do not depend on the data for the 'listings' shortcode
* Added option: group markers to clusters on the listings map
* Fixed: templates for error messages
```
Breaking changes:

	* listing/listings-item-map
	  - template has been deprecated since this version.
	    Please use the new template 'listing/listings-map-marker-info'.

	* no-cities
	  - template has been moved to: 'element/error-no-listings-available'.

Changed templates (if you previously overridden them, please check the changes):

	* listing/listings-item
	* element/pagination-block

Added templates:

    * element/error
    * element/error-no-listings-found
```

= 1.8.0 =

* Сhanged Stripe payment process handling to avoid timeout error

= 1.7.14 =

* Сhanged: payment page now receives initial data from GET
* Fixed: user bookings list template: remove link if listing is no longer available
* Added: loading state for search button
* Fixed: API calls
* Fixed: caching/logging
* Fixed: readme

= 1.7.13 =

* Fixed: discount code applying
* Fixed: security deposit show condition
* Fixed: css for the dates selector
* Fixed: php notices

= 1.7.12 =

* Fixed template path generation (hfy_tpl)

= 1.7.11 =

* Fixed default options
* Fixed conversion of date pattern format php->moment.js
* Fixed js error (Uncaught TypeError: Assignment to constant variable)

= 1.7.10 =

* Fixed guests increment/decrement selector
* Fixed displaying of "guests", depending on the selected template (see 1.7.1)
* Fixed some php notices
* Added support for Google Analytics GTAG events (see plugin settings for details)
* Added status on the payment page to help track results with third-party tools.
```
The state is displayed in the URL with the corresponding hashes:
	[payment-page-url]#wait
	[payment-page-url]#error
	[payment-page-url]#success
```

= 1.7.9 =

* Fixed option "book on airbnb" handling
* Fixed pagination template

= 1.7.8 =

* Fixed JS injection for booking form on listing page
* Added security deposit value on the listing booking form
* Fixed some php notices
* Fixed readme/changelog/documentation
* Added option 'Admin options' / 'Custom template path'
* Added filter 'hfy_tpl_path', see readme.txt (thanks to @HYFM)

= 1.7.7 =

* Fixed JS issue with booking form for some cases in Elementor
* Hide session_start php notice

= 1.7.6 =

* Added pets selector to guests block
* Added adults/children/infants/pets selectors to inquiry dialog

= 1.7.5 =

* Fixed: [hfy_listing_selected] issue (thanks to @oomph)
* Fixed: modal css z-index
* Fixed: pagination template
* Fixed: sort cities/locations list

= 1.7.4 =

* Fixed: pagination block template

= 1.7.3 =

* Fixed minor JS and PHP issues
* Double calendar to select dates

= 1.7.2 =

* Fixed price for nights on payment page (markup issue)

= 1.7.1 =

* Show shared baths info in the facilities block
* Added option to be able to differentiate between Adults/Children/Infants for Guests field in booking form
```
	Note:
	By default the new template for the "guests" field is used (listing-booking-form-v2).
	If you want to keep the old version (listing-booking-form), check the corresponding option in the settings.
```
= 1.6.15.2 =

* Fixed dates format and locale: search form, booking form, inquiry form, url parameters
* Fixed pagination template
* Fixed listing price fallback value used in templates: listings (listings-item), recent-listings, top-listings

= 1.6.14.2 =

* Fixed pagination template

= 1.6.14 =

* Fixed JS warnings on payment page
* Fixed getting parameters from API
* Added pagination for listings

= 1.6.13 =

* Changed API Google Maps key

= 1.6.12 =

* Fixed show/hide map functionality (shortcodes [hfy_listings_map_toggle] and "closebutton" parameter for [hfy_listings_map])
* Date pickers now use date format from WordPress general options

= 1.6.11 =

* Show card brand icon on Stripe payment form
* Admin options minor fixes

= 1.6.10 =

* Fixed potential xss vulnerability https://security.snyk.io/vuln/SNYK-JS-PROTO-1316301

= 1.6.9 =

* Fixed css issue

= 1.6.8 =

* Fixed: load settings for certain listing
* Fixed: listing-info template
* Fixed: terms checkbox css on payment page
* Admin options minor changes

= 1.6.7 =

* Fixed: "cities" parameter processing

= 1.6.6 =

* Fixed: price display in booking form and in the payment preview

= 1.6.5 =

* Fixed PHP notices
* Fixed minor templates issues
* Added new listing template and shortcode: [hfy_listing_info_permit]
* Added the ability to show or hide the "Studio" option in number of rooms selector

= 1.6.4 =

* Hide unwanted URL parameters on mobile

= 1.6.3 =

* Fixed user bookings templates

= 1.6.2 =

* Fixed weekly discount issue
* Fixed country selector CSS on payment page (Stripe)

= 1.6.1 =

* Added Guest Area (aka "User account" or "Member") functionality for logged in user:
	- automatic substitution of the user's names and personal data in the payment or inquiry form
	- wishlist
	- view own reservations
* Fixed: show selected city in search form
* Added: option to turn off the image preloader

= 1.5.14 =

* Fixed total price
* Changed: do not sort listings by default for [hfy_listings]

= 1.5.13 =

* Fixed PayPal payment
* Fixed cache clearing
* Small improvements in the admin interface
* Updated readme.txt

= 1.5.12 =

* Fixed API cache issues
* Fixed payment issues
* Fixed preloader issue on payment page when using 3D Secure payment method
* Fixed minor issues

= 1.5.11 =

* Fixed cache timeouts for some API points
* Minor changes in Admin menu
* Minor changes in styles and templates

= 1.5.10 =

* Fixed: listing summary and notes output - add BR for new lines
* Fixed: don't render the same JS variables many times
* Fixed: PHP notices
* Added new listing templates and shortcodes:
	- [hfy_listing_info_address]
	- [hfy_listing_info_prices]
	- [hfy_listing_reviews_stars]
	- [hfy_listing_field]
	- [hfy_listing_details_field]
* Added a common parameter for all shortcodes: nowrap="1"

= 1.5.9 =

* Updated: lightGallery component

= 1.5.8 =

* Fixed: UrlHelper parameter type checking

= 1.5.7 =

* Fixed: inquiry mail
* Fixed: total value on payment

= 1.5.6 =

* Fixed: booking flow

= 1.5.5 =

* Fixed "security deposit"

= 1.5.4 =

* Added "security deposit" support

= 1.5.3 =

* Improved Yoast SEO plugin support
* Added custom meta box "Hostify settings" for page, with custom parameter:	"Apply SEO settings (such as a pretty URL) to the Listing on this page"

= 1.5.2 =

* Added options for more convenient management of amenities in advanced search

= 1.5.1 =

* Added "max" parameter to [hfy_top_listings] shortcode
* Added "samepage" parameter to [hfy_booking_search] shortcode
* Fixed minor issues with templates and CSS

= 1.5.0 =

* Fixed: PHP 8 compatibility
* Fixed: session_start warning
* Fixed: price on request

= 1.4.34 =

* Fixed: payment form

= 1.4.33 =

* Fixed zip and country fields in payment form

= 1.4.32 =

* Fixed inquiry form submit button
* Fixed minor issues in admin

= 1.4.31 =

* Added new listing templates:
	- [hfy_listing_info_summary]
	- [hfy_listing_info_space]
	- [hfy_listing_info_guest_access]
	- [hfy_listing_info_interaction]
	- [hfy_listing_info_notes]
	- [hfy_listing_info_transit]
	- [hfy_listing_info_neighbourhood]
	- [hfy_listing_info_house_rules]

= 1.4.30 =

* Fixed minor template issues

= 1.4.29 =

* Added new option: show "long-term/short-term bookings" selector for search form
* Some fixes and improvements in the admin area

= 1.4.28.2 =

* Added new option: you can choose the locations selector type for hfy_booking_search shortcode
* Added new parameter 'bathrooms' in search
* Changed label for bedrooms selector: All -> Bedrooms

= 1.4.27 =

* Added new option: Disable data caching

= 1.4.26 =

* Added "ids" attribute to hfy_listings shortcode
* Fixed "tags" attribute
* Fixed selected amenities in advanced search

= 1.4.25 =

* Fixed some minor issues

= 1.4.24 =

* Fixed API response handling upon making a payment
* Fixed potential CURL issue
* Fixed 'connecting to API' notice for admin

= 1.4.23 =

* Optimized API call caching

= 1.4.22 =

* Fixed markers on the map, new option in settings: Show price labels on the map instead of pins

= 1.4.21 =

* Fixed payment issues
* Markers on the map now are shown as a text with listing price
* Fixed minor issues in admin interface

= 1.4.20 =

* Fixed bedrooms filter issue

= 1.4.19 =

* Added "Virtual tour" support, new shortcode [hfy_listing_virtual_tour]
* Fixed minor issues

= 1.4.18 =

* Fixed: "Reservation inquiry" button and dialog opening

= 1.4.17 =

* Added: automatic flush the cache when any settings in PMS was changed

= 1.4.16 =

* Fixed top_listings shortcode

= 1.4.15 =

* Fixed js error on payment screen

= 1.4.14 =

* Fixed 'bedrooms' parameter in search form

= 1.4.13 =

* Added new parameters to [hfy_listings], [hfy_listings_map], [hfy_booking_search] shortcodes

= 1.4.12 =

* Added show/hide map functionality on listings search page: new shortcode [hfy_listings_map_toggle] and "closebutton" parameter for [hfy_listings_map]
* Fixed search form CSS for mobile screens
* Fixed some PHP notices and compatibility issues
* Fixed minor CSS issues

= 1.4.11 =

* Added messages about configuration errors and checking the connection to the Hostify API
* Fixed minor issues

= 1.4.10 =

* Added a magic URL parameter for redirect to a specific listing (?hfylisting=)
* Fixed minor issues

= 1.4.9 =

* Fixed issue with search by number of guests

= 1.4.8 =

* Fixed "cities" and "city" parameters in [hfy_listings] and [hfy_listings_map] shortcodes
* Fixed minor issues

= 1.4.7 =

* Added label "guests" to booking form
* Fixed issue with number of guests select (booking form)
* Fixed minor CSS issues

= 1.4.6 =

* Fixed taxes display in price block and in payment preview
* Show more detailed information if error occured on the listings page
* Fixed minor PHP/WP notices
* Fixed minor CSS issues

= 1.4.5 =

* Added taxes display in price block and in payment preview
* Fixed minor CSS issues

= 1.4.4 =

* Fixed bug appears when you try to activate plugin

= 1.4.3 =

* Added hints on dates: minimum stay, if the date unavailable for check-in/check-out

= 1.4.2 =

* Fixed: displayed price on listings page

= 1.4.1 =

* Added cities ID list
* Fixed PHP notices

= 1.4 =

* Fixed code that was potentially vulnerable to XSS attacks
* Added Yoast SEO support option for listings pages (see plugin settings)
* Added "Default term mode" option to admin (short term | long term)
* Fixed templates (Please check and fix your templates in theme that override the plugin templates)
* Added new shortcode [hfy_booking_search_popup] and related template
* Added PayPal payment support
* Added partial payment support

= 1.3.4 =

* Updated readme.txt
* Fixed minor CSS issues on admin side

= 1.3.3 =

* Fixed passing current selected site language to JS components
* Updated mobile-detect.js
* Fixed minor issues related to php notices

= 1.3.2 =

* Fixed admin bar quick links
* Fixed Hostify logo
* Fixed options framework minor issues
* Added settings search

= 1.3.1 =

* Fixed search by price

= 1.3 =

* Added [hfy_recent_listings] shortcode
* Added quick links to admin bar
* Added 'price on request' for selected listings
* Added 'alt' attributes to images
* Fixed min stay tips on calendars
* Added 'hfy-price-loaded' event in JS
* Fixed minor issues
* Fixed min stay restrictions in date picker

= 1.2.7 =

* Added pricing breakdown

= 1.2.6 =

* Added passing languages code to API

= 1.2.5 =

* Fixed passing selected dates to the Inquiry dialog

= 1.2.4 =

* Added the listings sorting option

= 1.2.3 =

* Improved API caching
* Fixed php notices

= 1.2.2 =

* Minor fixes

= 1.2 =

* Changed connect to API settings
* License
* Removed unneeded static resources

= 1.1.13.2 =

* Added Stripe 3D secure support

= 1.1.12 =

* Added weekly discount display
* Fixed price per night display
* Fixed top listings shortcode
* Fixed reset button in the advanced search form
* Fixed reset button in the booking form
* Added window.doUpdatePriceBlock custom user js function call
* Map: disabled auto pan on click to marker
* Map: show default place if no listings found

= 1.1.11 =

* CSS fixes: remove default bootstrap colors, default font size
* Fixed payment result data
* Search by neighbourhoods

= 1.1.10 =

* Payment processing fixes
* Fixed function hfy_amenity_name used by Hostify theme

= 1.1.9 =

* Added option to change of the search logic by the number of bedrooms

= 1.1.8 =

* Split amenities by groups in advanced search

= 1.1.7 =

* Fixed bedrooms filter in search
* Updated readme
* Updated and fixed Admin menu

= 1.1.6 =

* Hide discount field on Inquiry form in case if discount showing is disabled in the settings

= 1.1.5 =

* Fixed CSS

= 1.1.4 =

* Fixed 'Book on Airbnb' button

= 1.1.3 =

* Fixed discount

= 1.1.2 =

* Updated 'broken picture' image for lazy loading
* Updated documentation
* Added bathrooms count

= 1.1.1 =

* Added caching API responses

= 1.1.0 =

* Public version
* Added language translations
* Refactored src/sass files structure

= 1.0 =

* Initial beta version. Last beta version is 1.0.beta.65.

