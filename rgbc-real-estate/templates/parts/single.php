<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( get_the_post_thumbnail( get_the_ID(), 'large' ) ) {
		echo get_the_post_thumbnail( get_the_ID(), 'large' );
	}
	?>

	<?php

	$price = get_post_meta( get_the_ID(), 'real_estate_price', true );

	echo do_shortcode('[real_estate_booking price="'.esc_html($price).'"]');

	?>


	<div class="single-description">
		<h2><?php the_title(); ?></h2>
		<?php
		if (!is_single()) {
			the_excerpt();
		}
		?>
		<?php the_excerpt(); ?>
	</div>
	<div class="real_estate_info">
		<div class="location">
			<?php
			$locations = get_the_terms( get_the_ID(), 'location' );
			if ( is_array( $locations ) && ! is_wp_error( $locations ) ) {
				esc_html_e( 'Location:', 'real_estate_object' );
				foreach ( $locations as $location ) {
					echo " " . esc_html( $location->name );
				}
			}
			?>
		</div>
		<div class="type">
			<?php
			$types = get_the_terms( get_the_ID(), 'real_estate_type' );
			if ( is_array( $types ) && ! is_wp_error( $types ) ) {
				esc_html_e( 'Type:', 'real_estate_object' );
				foreach ( $types as $type ) {
					echo " " . esc_html( $type->name );
				}
			}
			?>
		</div>
		<div class="price">
			<?php
			if ($price !== '' && (int)$price > 0) {
				esc_html_e( 'Price:', 'real_estate_object' );
				echo ' ' . $price;
			}
			?>
		</div>
		<div class="agent">
			<?php
			esc_html_e( 'Agent:', 'real_estate_object' );

			$agent_id = get_post_meta( get_the_ID(), 'real_estate_object_agent', true );
			$agent    = get_post( $agent_id );

			echo " " . esc_html( $agent->post_title );
			?>
		</div>
	</div>
	<a href="<?php the_permalink(); ?>"> Open This Real Estate Object </a>
</article>
