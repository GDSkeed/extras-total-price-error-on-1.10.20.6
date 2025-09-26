<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */

class Hostifybooking_i18n {

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'hostifybooking',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/lang/'
		);
	}

}
