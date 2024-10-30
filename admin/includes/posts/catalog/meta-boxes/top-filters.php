<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="top-filters-shortcode"><?php echo esc_html_x("Shortcode", "Admin view catalog post, meta-box 'top-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input type="text" id="top-filters-shortcode" class="all-witdh" value="<?php echo esc_attr($top_filters_shortcode ?? ""); ?>" readonly>
			</td>
		</tr>
		<tr>
			<th>
				<label for="top-filters-enable"><?php echo esc_html_x("Enable", "Admin view catalog post, meta-box 'top-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="top-filters-enable" type="checkbox" id="top-filters-enable" value="1" <?php if($top_filters_enable ?? true): ?>checked<?php endif; ?>>
			</td>
		</tr>
	</tbody>
</table>

<h1><?php echo esc_html_x("Filters", "Admin view catalog post, meta-box 'top-filters'", "infocob-crm-products"); ?></h1>
<div id="top-filters" class="container-top-filters" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>" data-acf="<?php echo function_exists("update_field") ? "true" : "false"; ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="top-filters" value="<?php echo esc_attr($top_filters ?? ""); ?>">
	
	<div class="content-top-filters">
	
	</div>
	
	<div class="actions-container add">
		<button type="button" class="add-top-filter"><?php echo esc_html_x("Add filter", "Admin view catalog post, meta-box 'top-filters'", "infocob-crm-products"); ?></button>
	</div>
</div>
