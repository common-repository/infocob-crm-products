<form action="options.php" method="post">
    <?php
		settings_fields('infocob-crm-products');
		do_settings_sections('infocob-crm-products');
		submit_button();
	?>
</form>
