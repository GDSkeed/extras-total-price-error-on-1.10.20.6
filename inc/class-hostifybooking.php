<?php

/**
 * main class
 */
class HostifyBooking
{

	public $loader;
	protected $plugin_name;
	protected $version;

	/**
	 */
	function __construct()
	{
		$this->version     = defined('HOSTIFYBOOKING_VERSION') ? HOSTIFYBOOKING_VERSION : '1.0.0';
		$this->plugin_name = 'hostifybooking-plugin';
		$this->load_dependencies();
		$this->set_locale();
		$this->set_admin_hooks();
		$this->set_public_hooks();
	}

	/**
	 */
	private function load_dependencies()
	{
		$path = plugin_dir_path(dirname(__FILE__));
		require_once $path . 'inc/class-hostifybooking-ajax.php';
		require_once $path . 'inc/class-hostifybooking-widget.php';
		require_once $path . 'inc/class-hostifybooking-loader.php'; // actions and filters
		require_once $path . 'inc/class-hostifybooking-i18n.php';
		require_once $path . 'admin/class-hostifybooking-admin.php';
		require_once $path . 'inc/class-hostifybooking-public.php';
		require_once $path . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';
		$this->loader = new Hostifybooking_Loader();
	}

	/**
	 */
	public function get_listing_info($id = null)
	{
		return $this->loader->get_listing_info($id);
	}

	/**
	 */
	private function set_locale()
	{
		$plugin_i18n = new Hostifybooking_i18n();
		$this->loader->add_action('init', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 */
	private function set_admin_hooks()
	{
		$plugin_admin = new Hostifybooking_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('init', $plugin_admin, 'create_menu', 999);
	}

	/**
	 */
	private function set_public_hooks()
	{
		$plugin_public = new Hostifybooking_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 */
	public function get_version()
	{
		return $this->version;
	}

}
