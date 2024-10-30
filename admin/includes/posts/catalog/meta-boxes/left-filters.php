<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="left-filters-shortcode"><?php echo esc_html_x("Shortcode", "Admin view catalog post, meta-box 'left-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input type="text" id="left-filters-shortcode" class="all-witdh" value="<?php echo esc_attr($left_filters_shortcode ?? ""); ?>" readonly>
			</td>
		</tr>
		<tr>
			<th>
				<label for="left-filters-enable"><?php echo esc_html_x("Enable", "Admin view catalog post, meta-box 'left-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="left-filters-enable" type="checkbox" id="left-filters-enable" value="1" <?php if($left_filters_enable ?? true): ?>checked<?php endif; ?>>
			</td>
		</tr>
	</tbody>
</table>

<h1><?php echo esc_html_x("Filters", "Admin view catalog post, meta-box 'left-filters'", "infocob-crm-products"); ?></h1>
<div id="left-filters" class="container-left-filters" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>" data-acf="<?php echo function_exists("update_field") ? "true" : "false"; ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="left-filters" value="<?php echo esc_attr($left_filters ?? ""); ?>">
	
	<div class="content-left-filters">
	
	</div>
	
	<div class="actions-container add">
		<button type="button" class="add-left-filter"><?php echo esc_html_x("Add filter", "Admin view catalog post, meta-box 'left-filters'", "infocob-crm-products"); ?></button>
	</div>
</div>
