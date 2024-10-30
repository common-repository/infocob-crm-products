<h1><?php echo esc_html_x('Extension page (in progress)', "Page extension", "infocob-crm-products"); ?></h1>
<p>
	<?php echo esc_html_x("Link your Infocob's products with your Wordpress.", "Page extension", "infocob-crm-products"); ?><br/>
	<?php echo esc_html_x("This plugin support the plugins 'ACF', 'POLYLANG' and 'YOAST SEO'", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Requirements', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Enter your database informations in the settings page", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Step 1 : Get the products', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Choose from which Infocob's database table your data come from ('PRODUITFICHE', 'PRODUITMODELEFICHE').", "Page extension", "infocob-crm-products"); ?>
	<br>
	<?php echo esc_html_x("Then set your filters to limit the results to only necessary products", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Step 2 : Attribute products to their categories', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Associate your products get from above to specific posts types, taxonomies and categories.", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Step 3 : Define product properties', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Customize your posts types with title, description, etc... with data from Infocob", "Page extension", "infocob-crm-products"); ?>
	<br>
	<?php echo esc_html_x("If you have the plugin 'ACF' installed and enabled, you can also set ACF fields from Infocob's data", "Page extension", "infocob-crm-products"); ?>
	<br>
	<?php echo esc_html_x("Plugin also supported : Yoast, Woocommerce", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x("Step 4 (optional) : Add additional data from Infocob's inventories", "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Add additional informations to your posts types with data from Infocob's inventories.", "Page extension", "infocob-crm-products"); ?>
	<br>
	<?php echo esc_html_x("Warning ! You MUST have the plugin 'ACF' installed and enabled as well as at least one ACF field of type 'repeater' configured.", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Step 5 : Configure media files', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Choose how you want manage the media files (photos, documents, etc...) linked to your products (Cloud files recommended)", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Step 6 (optional) : API & CRON', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Enter IP adresse(s) from which you can call the Wordpress API to start importations.", "Page extension", "infocob-crm-products"); ?>
	<br>
	<?php echo esc_html_x("Define your CRON task", "Page extension", "infocob-crm-products"); ?>
</p>

<h2 id="final-step"><?php echo esc_html_x('Final step : Start to import', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	<?php echo esc_html_x("Start your import by calling the following URL", "Page extension", "infocob-crm-products"); ?>
</p>
<ul>
	<li><a href="#final-step"><?php echo esc_html($wp_api_url ?? "") . "infocob-crm-products/v1/import/POST_ID"; ?></a></li>
</ul>
<p>
	<?php echo esc_html_x("Where POST_ID is the post_id of your post configuration.", "Page extension", "infocob-crm-products"); ?><br/>
	<?php echo esc_html_x("Or you can also call the following URL to start all imports regardless of post_id", "Page extension", "infocob-crm-products"); ?>
</p>
<ul>
	<li><a href="#final-step"><?php echo esc_html($wp_api_url ?? "") . "infocob-crm-products/v1/imports"; ?></a></li>
</ul>
<p>
	<?php echo esc_html_x("Moreover you can look at the logs page to check if everything went well", "Page extension", "infocob-crm-products"); ?>
</p>

<h2><?php echo esc_html_x('Hooks list', "Page extension", "infocob-crm-products"); ?></h2>
<p>
	Executed after post insert/update : <code>do_action('icp_import_product', $update, $post_id, $infocob_id);</code><br/>
	Executed before import start : <code>do_action('icp_before_import', $post_import_id);</code><br/>
	Executed after import start : <code>do_action('icp_after_import', $post_import_id);</code><br/>
	Executed after post disabled : <code>do_action('icp_product_disabled', $post_id);</code><br/>
	
	Executed after products to import are loaded : <code>apply_filters('icp_import_products_loaded', $products, $post_import_id);</code><br/>
</p>
