<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Field: Editor
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_editor' ) ) {

	class Exopite_Simple_Options_Framework_Field_editor extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );
		}

		public function output() {

			$classes = ( isset( $this->field['class'] ) ) ? explode( ' ', $this->field['class'] ) : array();// $this->element_class()
			$editor  = ( isset( $this->field['editor'] ) ) ? $this->field['editor'] : 'tinymce';

			echo $this->element_before();

			if ( $editor == 'tinymce' && isset( $this->field['sub'] ) && $this->field['sub'] ) {

				$classes[] = 'tinymce-js';
				$classes   = implode( ' ', $classes );

				echo '<textarea id="' . $this->field['id'] . '" name="' . $this->element_name() . '" class="' . $classes . '"' . $this->element_attributes() . '>' . $this->element_value() . '</textarea>';

			} else {

				$args = array(
					'textarea_rows' => 15,
					'editor_class'  => implode( ' ', $classes ),
					'textarea_name' => $this->element_name(),
					'teeny'         => false,
					// output the minimal editor config used in Press This
					'dfw'           => false,
					// replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
					'tinymce'       => true,
					// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
					'quicktags'     => true,
					// load Quicktags, can be used to pass settings directly to Quicktags using an array()
				);
				wp_editor( $this->element_value(), $this->field['id'], $args );
			}
			echo $this->element_after();
		}

		public static function enqueue( $args ) {
			if ( isset( $args['field'] ) && isset( $args['field']['editor'] ) && is_array( $args['field']['editor'] ) ) {
				foreach ( $args['field']['editor'] as $editor ) {
				}
			}
		}

	}

}
