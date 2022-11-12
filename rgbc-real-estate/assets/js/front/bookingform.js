jQuery( document ).ready( function( $ ) {

	$( 'form[action="booking_form"]' ).on( 'submit', function( e ) {
		console.log( this );
		const $form    = $( this );
		const $name    = $form.find( '[name="name"]' );
		const $email   = $form.find( '[name="email"]' );
		const $phone   = $form.find( '[name="phone"]' );
		const $price   = $form.find( '[name="price"]' );
		const $message = $form.find( '.js-form-message' );


		e.preventDefault();
		$message.html( '' );
		$.ajax( {
			url: real_estate_bookingform_var.ajaxurl,
			type: 'post',
			data: {
				action: 'booking_form',
				nonce: real_estate_bookingform_var.nonce,
				name: $name.val(),
				email: $email.val(),
				phone: $phone.val(),
				price: $price.val(),
			},
			success: function( data ) {
				$message.html( data );
			},
			error: function( errorThrown ) {
				console.log( errorThrown );
			}
		} );
	} );
} );