<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="products-per-page"><?php echo esc_html_x("Per page", "Admin view catalog post, meta-box 'products'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input type="number" name="products-per-page" id="products-per-page" class="all-witdh" min="0" step="1" value="<?php echo esc_attr($products_per_page ?? ""); ?>">
			</td>
		</tr>
	</tbody>
</table>

<h1><?php echo esc_html_x("Default order by", "Admin view catalog post, meta-box 'products'", "infocob-crm-products"); ?></h1>
<div id="products-order-by" class="container-products-order-by" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="products-order-by" value="<?php echo esc_attr($products_order_by ?? ""); ?>">
	
	<div class="content-products-order-by">
	
	</div>
	
	<div class="actions-container add">
		<button type="button" class="add-order-by"><?php echo esc_html_x("Add order by", "Admin view catalog post, meta-box 'products'", "infocob-crm-products"); ?></button>
	</div>
</div>
