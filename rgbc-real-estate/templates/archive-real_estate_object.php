<?php
get_header(); ?>

<?php
$rgbc_real_estate_Template = new real_estate_object_Template_Loader();
$rgbc_real_estate_Template->get_template_part( 'parts/filter' ); ?>

	<div class="wrapper archive_real_estate">
		<?php

		if ( ! empty( $_GET['submit'] ) ) {

			$args = array(
				'post_type'     => 'real_estate_object',
				'post_per_page' => - 1,
				'meta_query'    => array( 'relation' => 'AND' ),
				'tax_query'     => array( 'relation' => 'AND' ),
			);

			if ( isset( $_GET['real_estate_type'] ) && $_GET['real_estate_type'] != '' ) {
				array_push( $args['meta_query'], array(
					'key'   => 'real_estate_type',
					'value' => esc_attr( $_GET['real_estate_type'] ),
				) );
			}

			if ( isset( $_GET['real_estate_price'] ) && $_GET['real_estate_price'] != '' ) {
				array_push( $args['meta_query'], array(
					'key'     => 'real_estate_price',
					'value'   => esc_attr( $_GET['real_estate_price'] ),
					'type'    => 'numeric',
					'compare' => '<=',
				) );
			}

			if ( isset( $_GET['real_estate_location'] ) && $_GET['real_estate_location'] != '' ) {
				array_push( $args['tax_query'], array(
					'taxonomy' => 'location',
					'terms'    => $_GET['real_estate_location'],
				) );
			}
			//custom Query
			$real_estate_object = new WP_Query( $args );

			if ( $real_estate_object->have_posts() ) {

				// Load posts loop.
				while ( $real_estate_object->have_posts() ) {
					$real_estate_object->the_post();
					$rgbc_real_estate_Template->get_template_part( 'parts/content' );
				}


			} else {
				echo '<p>' . esc_html__( 'No Real Estate Projects', 'real_estate_object' ) . '</p>';
			}

		} else {

			if ( have_posts() ) {

				// Load posts loop.
				while ( have_posts() ) {
					the_post();
					$rgbc_real_estate_Template->get_template_part( 'parts/content' );
				}

				//Pagination
				posts_nav_link();

			} else {
				echo '<p>' . esc_html__( 'No Real Estate Projects', 'real_estate_object' ) . '</p>';
			}
		}
		?>
	</div>

<?php
get_footer(); ?>