<?php if (is_plugin_active("advanced-custom-fields-pro/acf.php") || is_plugin_active("advanced-custom-fields/acf.php")): ?>
	<h1><?php echo esc_html_x("Get inventories (refers to the imported product)", "Admin view configuration post, meta-box 'inventory'", "infocob-crm-products"); ?></h1>

	<div id="inventory-filters" class="container-filters" data-module="<?php echo esc_attr($infocob_type_produit ?? ""); ?>">
		<div class="icp-loader active"><div></div></div>
		<input type="hidden" name="inventory-filters" value="<?php echo esc_attr($inventory_filters ?? ""); ?>">
		
		<div class="content-filter">
		
		</div>
		
		<div class="input">
			<button type="button" class="add-row"><?php echo esc_html_x("Add condition","Admin view configuration post, meta-box 'inventory'", "infocob-crm-products"); ?></button>
			<button type="button" class="add-group"><?php echo esc_html_x("Add group","Admin view configuration post, meta-box 'inventory'", "infocob-crm-products"); ?></button>
		</div>
	</div>
	
	<h1><?php echo esc_html_x("Define product's inventory", "Admin view configuration post, meta-box 'inventory'", "infocob-crm-products"); ?></h1>
	
	<div id="post-inventory" class="container-post-inventory" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
		<div class="icp-loader active">
			<div></div>
		</div>
		<input type="hidden" name="post-inventory" value="<?php echo esc_attr($post_inventory ?? ""); ?>">
		
		<div class="content-post-inventory">
		
		</div>
		
		<div class="actions-container">
			<button type="button" class="add-post_inventory"><?php echo esc_html_x("Add post inventory", "Admin view configuration post, meta-box 'inventory'", "infocob-crm-products"); ?></button>
		</div>
	</div>
<?php endif; ?>
