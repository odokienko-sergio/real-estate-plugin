<?php

class Real_Estate_Bookingform {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [
			$this,
			'enqueue',
		] );
		add_action( 'init', [
			$this,
			'real_estate_booking_shortcode',
		] );

		add_action( 'wp_ajax_booking_form', [
			$this,
			'booking_form',
		] );/*тільки для авторизованих користувачів, wp_ajax_booking_form - це мною створений єкшон*/
		add_action( 'wp_ajax_nopriv_booking_form', [
			$this,
			'booking_form',
		] );/*не тільки для авторизованих*/
	}

	public function enqueue() { /* підключення скриптів */
		wp_enqueue_script(
			'real_estate_bookingform',
			plugins_url( 'rgbc-real-estate/assets/js/front/bookingform.js' ),
			array( 'jquery' ),
			RGBC_RE_VERSION,
			true
		);

		wp_localize_script(
			'real_estate_bookingform',
			'real_estate_bookingform_var',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( '_wpnonce' ),
				/*nonce це така штука у wp яка дозволяє убезпечити передачу даних. робить перевірку*/
				'title'   => esc_html__( 'Booking Form', 'real_estate_object' ),
			)
		);
	}

	public function real_estate_booking_shortcode() {
		add_shortcode(
			'real_estate_booking',
			[
				$this,
				'booking_form_html',
			] /*в якості callback-а функції*/
		);
	}

	public function booking_form_html( $atts, $content ) { /* дві ці змінні потім треба створити */

		$atts = shortcode_atts(
			array(
				'location' => '',
				'price'    => '',
				'type'     => '',
			),
			$atts
		);

		$price = get_post_meta( get_the_ID(), 'real_estate_price', true );
		?>
		<div class="ajax-form filter_form">
			<h4 class="ajax-title">Ajax test form</h4>
			<form method="post" action="booking_form">
				<p><span>Name:</span>
					<input type="text" name="name">
				</p>
				<p><span>Email:</span>
					<input type="email" name="email">
				</p>
				<p><span>Phone:</span>
					<input type="text" name="phone">
				</p>

				<?php if ( $price != '' ) : ?>
					<p>
						<input type="hidden" name="price" value="<?php echo esc_attr( $price ); ?>">
					</p>
				<?php endif; ?>

				<p>
					<button type="submit">Submit</button>
				</p>
				<div class="js-form-message"></div>
			</form>
		</div>

		<?php
	}

	function booking_form() {

		check_ajax_referer( '_wpnonce', 'nonce' );

		if ( ! empty( $_POST ) ) {
			if ( isset( $_POST['name'] ) ) {
				$name = sanitize_text_field( $_POST['name'] );
			}
			if ( isset( $_POST['email'] ) ) {
				$email = sanitize_text_field( $_POST['email'] );
			}
			if ( isset( $_POST['phone'] ) ) {
				$phone = sanitize_text_field( $_POST['phone'] );
			}
			if ( isset( $_POST['price'] ) ) {
				$price = sanitize_text_field( $_POST['price'] );
			}
			//echo $name ." ".$phone;

			//email Admin
			$data_message = '';

			$data_message .= 'Name: ' . esc_html( $name ) . '<br>';
			$data_message .= 'Email: ' . esc_html( $email ) . '<br>';
			$data_message .= 'Phone: ' . esc_html( $phone ) . '<br>';
			$data_message .= 'Price: ' . esc_html( $price ) . '<br>';

			echo $data_message; // потім це коментуємо
			$result_admin = wp_mail( get_option( 'admin_email' ), 'New Reservation', $data_message );

			if ( $result_admin ) {
				echo "All right";
			}

			//email client
			$message = esc_html__( 'Thank you for your reservation. We will contact you soon!' );
			wp_mail( $email, esc_html__( 'Booking', 'real_estate_object' ), $message );/*це відправить кліенту повідомленя*/

		} else {
			echo 'something wrong';
		}

		wp_die();
	}

}

$booking_form = new Real_Estate_Bookingform(); /*створюємо інстенс для цього класу*/