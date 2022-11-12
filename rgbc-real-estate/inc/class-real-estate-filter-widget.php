<?php

class Real_Estate_Filter_Widget extends WP_Widget {

	function __construct() {
		parent::__construct( 'real_estate_filter_widget',
			esc_html__( 'Filter', 'real_estate_object' ),
			array( 'description' => 'Filter form' ) );
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( $title ) {
			echo esc_html( $title );
		}

		$fields = '';

		if ( $instance['location'] ) {
			$fields .= ' location="1" ';
		}
		if ( $instance['price'] ) {
			$fields .= ' price="1" ';
		}
		if ( $instance['type'] ) {
			$fields .= ' type="1" ';
		}
		echo do_shortcode( '[real_estate_filter ' . $fields . ']' );
	}

	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		if ( isset( $instance['location'] ) ) {
			$location = $instance['location'];
		} else {
			$location = '';
		}
		if ( isset( $instance['type'] ) ) {
			$type = $instance['type'];
		} else {
			$type = '';
		}
		if ( isset( $instance['price'] ) ) {
			$price = $instance['price'];
		} else {
			$price = '';
		}

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				Title
			</label>
			<input class="widefat"
				type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>">
				Show location Field
			</label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"
				<?php checked( $location, 'on' ); ?>
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				Show Type Field
			</label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"
				<?php checked( $type, 'on' ); ?>
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'price' ) ); ?>">
				Show Price Field
			</label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'price' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'price' ) ); ?>"
				<?php checked( $price, 'on' ); ?>
			/>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['location'] = strip_tags( $new_instance['location'] );
		$instance['type']     = strip_tags( $new_instance['type'] );
		$instance['price']    = strip_tags( $new_instance['price'] );

		return $instance;
	}
}
