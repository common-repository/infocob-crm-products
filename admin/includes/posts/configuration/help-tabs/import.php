<p>
	<?php echo esc_html_x("Save your configuration then start your import by clicking on the \"Start import\" button on this page.", "Admin view configuration post, help-tab 'import'", "infocob-crm-products"); ?>
	<?php if(!empty($post_id)): ?>
		<br>
		<?php echo esc_html_x("Or by calling the following URL", "Admin view configuration post, help-tab 'import'", "infocob-crm-products"); ?>
	<?php endif; ?>
</p>

<?php if(!empty($post_id)): ?>
	<ul>
		<li><a href="<?php echo esc_attr($wp_api_url ?? "") . "infocob-crm-products/v1/import/" . esc_attr($post_id ?? ""); ?>" target="_blank"><?php echo esc_attr($wp_api_url ?? "") . "infocob-crm-products/v1/import/" . esc_attr($post_id ?? ""); ?></a></li>
	</ul>
<?php endif; ?>

<p>
	<?php echo esc_html_x("Moreover you can look at the logs page to check if everything went well", "Admin view configuration post, help-tab 'import'", "infocob-crm-products"); ?>
	<br>
	<a href="<?php echo esc_url(admin_url("admin.php?page=infocob-crm-products-logs")); ?>" target="_blank"><?php echo esc_html_x("See logs", "Admin view configuration post, help-tab 'import'", "infocob-crm-products"); ?></a>
</p>
