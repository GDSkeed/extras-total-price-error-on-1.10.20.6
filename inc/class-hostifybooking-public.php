<?php

class Hostifybooking_Public
{

	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles()
	{
		$options = get_option('hostifybooking-plugin', [
			'no_bs' => 'no',
			'no_css' => 'no',
			'rich_select_loc' => 'no',
		]);

		$nobs = isset($options['no_bs']) && $options['no_bs'] == 'yes';
		$no_css = false; // todo // isset($options['no_css']) && $options['no_css'] == 'yes';
		$rich_select = isset($options['rich_select_loc']) && $options['rich_select_loc'] == 'yes';

		if (!$nobs || !$no_css) {
			wp_enqueue_style('hfybs', HOSTIFYBOOKING_URL . 'public/res/bs.css', [], HOSTIFYBOOKING_VERSION);
		}

		if (!$no_css) {
			if ($rich_select) {
				wp_enqueue_style('hfysumo', HOSTIFYBOOKING_URL . 'public/res/lib/sumo/sumoselect.css', [], HOSTIFYBOOKING_VERSION);
			}

			wp_enqueue_style('hfylg', HOSTIFYBOOKING_URL . 'public/res/lib/lightgallery/dist/css/lightgallery.min.css', [], HOSTIFYBOOKING_VERSION);

			wp_enqueue_style('hfytipso', HOSTIFYBOOKING_URL . 'public/res/lib/tipso/tipso.min.css', [], HOSTIFYBOOKING_VERSION);
			wp_enqueue_style('hfymain', HOSTIFYBOOKING_URL . 'public/res/main.css', [], HOSTIFYBOOKING_VERSION);
			wp_enqueue_style('hfytheme', HOSTIFYBOOKING_URL . 'public/res/theme.css', [], HOSTIFYBOOKING_VERSION);
		}

		wp_enqueue_style('hfytel', HOSTIFYBOOKING_URL . 'public/res/lib/intl-tel-input/css/intlTelInput.min.css', [], HOSTIFYBOOKING_VERSION);
		wp_enqueue_style('datepickerstyles', HOSTIFYBOOKING_URL . 'public/res/lib/datepicker/dist/css/hotel-datepicker.css', [], HOSTIFYBOOKING_VERSION);

		// Conditionally load the new inquiry form styles and scripts
		// Always enqueue for both old and new inquiry forms
		wp_enqueue_style('hfy-inquiry-v2', HOSTIFYBOOKING_URL . 'public/res/inquiry-form-v2.css', [], HOSTIFYBOOKING_VERSION);
		wp_enqueue_script('hfy-inquiry-v2', HOSTIFYBOOKING_URL . 'public/res/inquiry-form-v2.js', ['jquery'], HOSTIFYBOOKING_VERSION, true);


	}

	public function enqueue_scripts()
	{
		// wp_enqueue_script('bs4', HOSTIFYBOOKING_URL . 'public/js/bootstrap.js', ['jquery'], '1.15', true);
		// --> the rest in class-hostifybooking-loader/hfy_scripts
	}
}
