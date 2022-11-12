<?php get_header(); ?>

	<div class="wrapper single_real_estate">
		<?php
		if ( have_posts() ) {

			// Load posts loop.
			while ( have_posts() ) {
				the_post();
				include( RGBC_RE_PATH . 'templates/parts/single.php');
			 }

		}
		?>
	</div>

<?php get_footer(); ?>