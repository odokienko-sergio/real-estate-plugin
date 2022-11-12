<?php
/**
 * Template Name: Add Real Estate Object
 * */

function real_estate_image_validation( $file_name ) { /*функція валідації для відправки поля file*/
	$valid_extensions = array(
		'jpg',
		'jpeg',
		'gif',
		'png',
	);
	$exploded_array   = explode( '.', $file_name );
	if ( ! empty( $exploded_array ) && is_array( $exploded_array ) ) {
		$ext = array_pop( $exploded_array );

		return in_array( $ext, $valid_extensions );
	} else {
		return false;
	}
}

function real_estate_insert_attachment( $file_handler, $post_id, $setthumb = false ) {
	if ( $_FILES[ $file_handler ]['error'] !== UPLOAD_ERR_OK ) {
		__return_false();
	} /*перевіряєм чи загрузка пройшла успішно*/

	/*треба підключити декілька файлів від вордпреса*/
	require_once( ABSPATH . "wp-admin" . "/includes/image.php" );
	require_once( ABSPATH . "wp-admin" . "/includes/file.php" );
	require_once( ABSPATH . "wp-admin" . "/includes/media.php" );

	$attach_id = media_handle_upload( $file_handler, $post_id );

	if ( $setthumb ) {
		update_post_meta( $post_id, '_thumbnail_id', $attach_id );
	}

	return $attach_id;
}

$success = '';

if ( isset( $_POST['action'] ) && is_user_logged_in() ) {
	if ( wp_verify_nonce( $_POST['real_estate_nonce'], 'submit_real_estate' ) ) {

		$real_estate_item = array();

		$real_estate_item['post_title']   = sanitize_text_field( $_POST['re_object_title'] );
		$real_estate_item['post_type']    = 'real_estate_object';
		$real_estate_item['post_content'] = sanitize_textarea_field( $_POST['re_object_desc'] );

		global $current_user;
		wp_get_current_user();
		$real_estate_item['post_author'] = $current_user->ID;

		$real_estate_action = $_POST['action'];

		if ( $real_estate_action == 'add_real_estate_object' ) {
			$real_estate_item['post_status'] = 'pending';
			$real_estate_item_id             = wp_insert_post( $real_estate_item );

			if ( $real_estate_item_id > 0 ) {
				do_action( 'wp_insert_post', 'wp_insert_post' );
				$success = 'Real Estate Successfully published';
			}
		}
		//metabox
		if ( $real_estate_item_id > 0 ) {

			if ( isset( $_POST['real_estate_type'] ) && $_POST['real_estate_type'] != '' ) {
				update_post_meta( $real_estate_item_id, 'real_estate_type', trim( $_POST['real_estate_type'] ) );
			}
			if ( isset( $_POST['real_estate_price'] ) ) {
				update_post_meta( $real_estate_item_id, 'real_estate_price', trim( $_POST['real_estate_price'] ) );
			}
			if ( isset( $_POST['real_estate_period'] ) ) {
				update_post_meta( $real_estate_item_id, 'real_estate_period', trim( $_POST['real_estate_period'] ) );
			}
			if ( isset( $_POST['real_estate_object_agent'] ) && $_POST['real_estate_object_agent'] != 'disable' ) {
				update_post_meta( $real_estate_item_id, 'real_estate_object_agent', trim( $_POST['real_estate_object_agent'] ) );
			}
			// taxonomy
			if ( isset( $_POST['re_location'] ) ) {
				wp_set_object_terms( $real_estate_item_id, intval( $_POST['re_location'] ), 'location' );
			}
			// featured image
			if ( $_FILES ) {
				foreach ( $_FILES as $submitted_file => $file_array ) {
					if ( real_estate_image_validation( $_FILES[ $submitted_file ]['name'] ) ) {
						$size = intval( $_FILES[ $submitted_file ]['size'] );

						if ( $size > 0 ) {
							real_estate_insert_attachment( $submitted_file, $real_estate_item_id, true );
						}
					}
				}
			}
		}
	}
}

get_header(); ?>

	<div class="wrapper">
		<?php

		if ( have_posts() ) {
			// Load posts loop.
			while ( have_posts() ) {
				the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2><?php the_title(); ?></h2>
					<div class="description"><?php the_content(); ?></div>
				</article>

				<?php if ( is_user_logged_in() ) {

					if (!empty($success)) {
						echo esc_html($success);
					} else {
					?>
					<div class="add_form">
						<form method="post" id="add_re_object" enctype="multipart/form-data">
							<p>
								<label for="re_object_title">Title</label>
								<input
									type="text"
									name="re_object_title"
									id="re_object_title"
									placeholder="Add the RE Object Title"
									value=""
									required
									tabindex="1"
								>
							</p>
							<p>
								<label for="re_object_desc">Description</label>
								<textarea
									name="re_object_desc"
									id="re_object_desc"
									placeholder="Add the Description"
									required
									tabindex="2"
								>
							</textarea>
							</p>
							<p>
								<label for="re_image">Featured Image</label>
								<input type="file" name="re_image" id="re_image" tabindex="3">
							</p>
							<p>
								<label for="re_location">Select Location</label>
								<select id="re_location" name="re_location" tabindex="4">
									<?php
									$locations = get_terms( array( 'location' ), array( 'hide_empty' => false ) );

									if ( ! empty( $locations ) ) {
										foreach ( $locations as $location ) {
											echo '<option value="' . $location->term_id . '">' . $location->name . '</option>';
										}
									}
									?>
								</select>
							</p>
							<p>
								<label for="real_estate_type">Select Type</label>
								<select id="real_estate_type" name="real_estate_type" tabindex="5">
									<option selected value=""> Not Selected</option>
									<option value="sale">For Sale</option>
									<option value="sold">For Sold</option>
									<option value="rent">For Rent</option>
								</select>
							</p>
							<p>
								<label for="real_estate_price">Price</label>
								<input type="text" name="real_estate_price" id="real_estate_price" tabindex="6"
									value="">
							</p>
							<p>
								<label for="real_estate_period">Period</label>
								<input type="text" name="real_estate_period" id="real_estate_period" tabindex="7"
									value="">
							</p>
							<p>
								<?php global $current_user;
								wp_get_current_user(); ?>
								<label for="real_estate_object_agent">Agent</label>
								<select id="real_estate_object_agent" name="real_estate_object_agent" tabindex="8">
									<option selected value="disable">Disable Agents, Use USer</option>
									<?php
									$agents = get_posts( array(
										'post_type'   => 'agent',
										'numberposts' => - 1,
									) );

									if ( ! empty( $agents ) ) {
										foreach ( $agents as $agent ) {
											echo '<option value="' . $agent->ID . '">' . $agent->post_title . '</option>';
										}
									}
									?>
								</select>
							</p>
							<p>
								<?php wp_nonce_field( 'submit_real_estate', 'real_estate_nonce' ); ?>
								<input type="submit" name="submit" tabindex="9" value="Add New Real Estate Object">
								<input type="hidden" name="action" value="add_real_estate_object">
							</p>
						</form>
					</div>
						<?php } ?>
				<?php } ?>
			<?php }
		}
		?>
	</div>

<?php
get_footer();