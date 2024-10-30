<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="right-filters-shortcode"><?php echo esc_html_x("Shortcode", "Admin view catalog post, meta-box 'right-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input type="text" id="right-filters-shortcode" class="all-witdh" value="<?php echo esc_attr($right_filters_shortcode ?? ""); ?>" readonly>
			</td>
		</tr>
		<tr>
			<th>
				<label for="right-filters-enable"><?php echo esc_html_x("Enable", "Admin view catalog post, meta-box 'right-filters'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="right-filters-enable" type="checkbox" id="right-filters-enable" value="1" <?php if($right_filters_enable ?? true): ?>checked<?php endif; ?>>
			</td>
		</tr>
	</tbody>
</table>

<h1><?php echo esc_html_x("Filters", "Admin view catalog post, meta-box 'right-filters'", "infocob-crm-products"); ?></h1>
<div id="right-filters" class="container-right-filters" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>" data-acf="<?php echo function_exists("update_field") ? "true" : "false"; ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="right-filters" value="<?php echo esc_attr($right_filters ?? ""); ?>">
	
	<div class="content-right-filters">
	
	</div>
	
	<div class="actions-container add">
		<button type="button" class="add-right-filter"><?php echo esc_html_x("Add filter", "Admin view catalog post, meta-box 'right-filters'", "infocob-crm-products"); ?></button>
	</div>
</div>
