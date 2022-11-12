<?php

class Real_Estate_Shortcodes {

	public $RGBC_RE;

	public function __construct() {
		$this->RGBC_RE = new RGBC_RE();
		$this->register();
	}

	public function register() {
		add_action( 'init', [
			$this,
			'register_shortcode',
		] );
	}

	public function register_shortcode() {
		add_shortcode( 'real_estate_filter', [
			$this,
			'filter_shortcode',
		] );
	}

	public function filter_shortcode( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'location' => '0',
				'price'    => '0',
				'type'     => '0',
			),
			$atts
		);

		$output = '';
		$output .= '<div class="wrapper filter_form">';
		$output .= '<form method="get" action="' . get_post_type_archive_link( 'real_estate_object' ) . '">';
		if ( $atts['location'] === '1' ) {
			$output .= '
			<select name="real_estate_location">
				<option value="">Select Location</option>
				' . $this->RGBC_RE->get_terms_hierarchical( 'location', '' ) . '
			</select>
			';
		};

		if ( $atts['price'] === '1' ) {
			$output .= '<input
			type="text"
			placeholder="Maximum Price"
			name="real_estate_price"
			value=""/>';
		};

		if ( $atts['type'] === '1' ) {
			$output .= '<select name="real_estate_type">
			<option value=""> Select Type </option>
			<option value="sale" > For Sale </option>
			<option value="rent" > For Rent </option>
			<option value="sold" > Sold </option>
			</select>';
		};

		$output .= '<input type="submit" name="submit" value="Filter"/>';

		$output .= '</form></div>';


		return $output;

	}
}

$Real_Estate_Shortcodes = new Real_Estate_Shortcodes();
