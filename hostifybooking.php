<?php
/**
 * Plugin Name:  Hostify Booking Engine
 * Description:  Make your own booking website using Hostify.com API
 * Author:       Hostify
 * Author URI:   https://hostify.com
 * Plugin URI:   https://hostify.com
 * License:      Proprietary
 * License URI:  license.txt
 * Text Domain:  hostifybooking
 * Domain Path:  /lang
 * Update URI:   https://wp-update.hostify.com/info/update.json
 * Version:      1.10.20.6
 */

if (!defined('WPINC')) die;

define('HOSTIFYBOOKING_VERSION', '1.10.20.6');
define('HOSTIFYBOOKING_URL', plugin_dir_url( __FILE__ ));
define('HOSTIFYBOOKING_DIR', plugin_dir_path( __FILE__ ));
define('HOSTIFYBOOKING_CRON_NAME', 'hostifybooking_cron');
define('HOSTIFYBOOKING_PLUGIN_BASE', plugin_basename( __FILE__ ) );

function activate_hostifybooking()
{
	// require_once HOSTIFYBOOKING_DIR . 'inc/class-hostifybooking-activator.php';
	// HOSTIFYBOOKING_Activator::activate();

	// add_rewrite_rule('^\.well-known\/apple-developer-merchantid-domain-association', HOSTIFYBOOKING_URL . 'apple-merchant-id-dom.txt', 'top');
	// flush_rewrite_rules();

	if (defined('ABSPATH')) {
		$txt = file_get_contents(HOSTIFYBOOKING_DIR . 'apple-merchant-id-dom.txt');
		$dir = ABSPATH . '.well-known/apple-developer-merchantid-domain-association';
		$parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) mkdir($dir);
		}
        file_put_contents("$dir/$file", $txt);
	}
}

function deactivate_hostifybooking()
{
	// require_once HOSTIFYBOOKING_DIR . 'inc/class-hostifybooking-deactivator.php';
	// HOSTIFYBOOKING_Deactivator::deactivate();

	// flush_rewrite_rules();
}

/**
 * Go!
 */

if (!session_id()) {
	@session_start([ 'read_and_close' => true ]);
}

register_activation_hook( __FILE__, 'activate_hostifybooking' );
// register_deactivation_hook( __FILE__, 'deactivate_hostifybooking' );

if (is_admin()) {
	require_once HOSTIFYBOOKING_DIR . 'inc/nuxy/NUXY.php';
}

require_once HOSTIFYBOOKING_DIR . 'inc/helpers.php';
require_once HOSTIFYBOOKING_DIR . 'inc/defines.php';

require HOSTIFYBOOKING_DIR . 'inc/class-hostifybooking.php';

try {
	$HostifyBookingPlugin = new HostifyBooking();
	$HostifyBookingPlugin->run();
} catch (\Exception $e) {
	$msg = $e->getMessage();
	add_action('admin_notices', function() use($msg) {
		?><div class="error notice"><p><?= $msg ?></p></div><?php
	});
}

