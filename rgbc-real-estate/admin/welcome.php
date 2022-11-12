<h1>
	<?php
	esc_html_e( 'Welcome', 'real_estate_object' );
	?>
</h1>
<div class="content">
	<?php //settings_errors(); ?>
	<form method="post" action="options.php">
		<?php
			settings_fields('real_estate_settings');
			do_settings_sections('real_estate_settings_page');
			submit_button();
		?>
	</form>
</div>