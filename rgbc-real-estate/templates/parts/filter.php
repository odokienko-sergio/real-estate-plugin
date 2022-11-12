<?php
$price = isset( $_GET['real_estate_price'] ) && $_GET['real_estate_price'] ? sanitize_text_field( $_GET['real_estate_price'] ) : '';
?>

<div class="wrapper filter_form">
	<?php $RGBC_RE = new RGBC_RE();
	//$RE_LOCATION = new RE_LOCATION();
	?>


	<?php /* відображення тайтлу налаштування*/
	//var_dump( $RGBC_RE );
	$filter_title = get_option( 'filter_title', '' );
	if ( $filter_title ) {
		echo esc_html( $filter_title );
	}
	?>

	<form method="get" action="<?php get_post_type_archive_link( 'real_estate_object' ); ?>">
		<select name="real_estate_location">
			<option value="">Select Location</option>
			<?php $RGBC_RE->get_terms_hierarchical( 'location', $_POST['real_estate_location'] ); ?>
		</select>

		<input
			type="text"
			placeholder="Maximum Price"
			name="real_estate_price"
			value="<?php echo esc_attr( $price ); ?>"/>
		<select name="real_estate_type">
			<option value="">Select Type</option>
			<option
				value="sale"
				<?php
				if ( isset( $_GET['real_estate_type'] ) and $_GET['real_estate_type'] == 'sale' ) {
					echo 'selected';
				}
				?> > For Sale
			</option>
			<option
				value="rent"
				<?php
				if ( isset( $_GET['real_estate_type'] ) and $_GET['real_estate_type'] == 'rent' ) {
					echo 'selected';
				}
				?> > For Rent
			</option>
			<option
				value="sold"
				<?php
				if ( isset( $_GET['real_estate_type'] ) and $_GET['real_estate_type'] == 'sold' ) {
					echo 'selected';
				}
				?> > Sold
			</option>
		</select>
		<input type="submit" name="submit" value="Filter"/>

	</form>
</div>