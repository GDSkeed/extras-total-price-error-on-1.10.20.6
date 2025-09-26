<?php

/**
 * Widget class
 */
class HostifybookingWidget1 extends WP_Widget {

	/**
	 */
	public function __construct()
	{
		parent::__construct(
			'hostifybooking_widget_1', // ID of widget.
			// __( 'Hostify Booking 1', 'hostifybooking' ),
			// array( 'description' => __( 'Hostify Booking Widget 1', 'hostifybooking' ) )
			'Hostify Booking 1',
			array( 'description' => 'Hostify Booking Widget 1' )
		);
	}

	/**
	 */
	public function widget( $args, $instance )
	{
		$title = apply_filters( 'widget_title', $instance['title'] );
		// Before and after widget arguments are defined by themes.
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		ob_start();
		// include 'shortcodes/hostifybooking-widget-1.php';
		$out = ob_get_contents();
		ob_end_clean();
		echo $out;
	}

	/**
	 * Backend
	 */
	public function form( $instance )
	{
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			// $title = __( 'Hostify', 'hostifybooking' );
			$title = 'Hostify';
		}
		// admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	/**
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}
