<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="post-status"><?php _ex("Post status", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="post-status" id="post-status" class="all-witdh">
					<?php foreach (($post_statuses ?? []) as $value => $label): ?>
						<option value="<?php echo esc_attr($value); ?>" <?php if (strcasecmp($post_status ?? "", $value) === 0): ?>selected<?php endif; ?>><?php echo esc_html($label ?? ""); ?></option>
					<?php endforeach; ?>
				</select>
				<div class="sub-field">
					<input name="post-status-update" type="checkbox" id="post-status-update" value="1" <?php if ($post_status_update ?? true): ?>checked<?php endif; ?>>
					<label for="post-status-update"><?php _ex("Update", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="post-deleted-status"><?php _ex("Deleted post status", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="post-deleted-status" id="post-deleted-status" class="all-witdh">
					<?php foreach (($post_statuses ?? []) as $value => $label): ?>
						<option value="<?php echo esc_attr($value); ?>" <?php if (strcasecmp($post_deleted_status ?? "", $value) === 0): ?>selected<?php endif; ?>><?php echo esc_html($label ?? ""); ?></option>
					<?php endforeach; ?>
				</select>
				<div class="sub-field">
					<input name="post-deleted-status-update" type="checkbox" id="post-deleted-status-update" value="1" <?php if ($post_deleted_status_update ?? true): ?>checked<?php endif; ?>>
					<label for="post-deleted-status-update"><?php _ex("Update", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="post-author"><?php _ex("Post author", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="post-author" id="post-author" class="all-witdh">
					<?php foreach (($post_authors ?? []) as $post_author): ?>
						<?php if ($post_author instanceof WP_User): ?>
							<option value="<?php echo esc_attr($post_author->get("ID")); ?>" <?php if (($post_author ?? "") === $post_author->get("ID")): ?>selected<?php endif; ?>><?php echo esc_html($post_author->get("display_name") . " (" . implode(", ", $post_author->roles) . ")"); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<div class="sub-field">
					<input name="post-author-update" type="checkbox" id="post-author-update" value="1" <?php if ($post_author_update ?? true): ?>checked<?php endif; ?>>
					<label for="post-author-update"><?php _ex("Update", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="post-title" data-tippy-content="<?php echo esc_attr_x("You can also use 'Champs libres' <br/>(ex : {{P_ChampLibre10XXX}})", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?>"><?php _ex("Post title", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="post-title[<?php echo esc_attr($language); ?>]" type="text" id="post-title-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($post_title[$language] ?? "{{ P_NOM }}"); ?>" placeholder="{{ P_NOM }}" data-tippy-content="<?php echo esc_attr_x("Post title (" . $language . ")", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="post-title-update" type="checkbox" id="post-title-update" value="1" <?php if ($post_title_update ?? true): ?>checked<?php endif; ?>>
					<label for="post-title-update"><?php _ex("Update", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<h1><?php _ex("Post meta", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></h1>
<div id="post-meta" class="container-post-meta" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="post-meta" value="<?php echo esc_attr($post_meta ?? ""); ?>">
	
	<div class="content-post-meta">
	
	</div>
	
	<div class="actions-container">
		<button type="button" class="add-post_meta"><?php _ex("Add", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></button>
	</div>
</div>

<?php if (is_plugin_active("wordpress-seo/wp-seo.php")): ?>
	<h1><?php _ex("Yoast SEO", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></h1>
	<div id="post-yoast" class="container-post-meta" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
		<div class="icp-loader active">
			<div></div>
		</div>
		<input type="hidden" name="post-yoast" value="<?php echo esc_attr($post_yoast ?? ""); ?>">
		
		<div class="content-post-meta">
		
		</div>
		
		<div class="actions-container">
			<button type="button" class="add-post_meta"><?php _ex("Add", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></button>
		</div>
	</div>
<?php endif; ?>

<?php if (is_plugin_active("advanced-custom-fields-pro/acf.php") || is_plugin_active("advanced-custom-fields/acf.php")): ?>
	<h1><?php _ex("ACF", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></h1>
	<div id="post-acf" class="container-post-acf" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
		<div class="icp-loader active">
			<div></div>
		</div>
		<input type="hidden" name="post-acf" value="<?php echo esc_attr($post_acf ?? ""); ?>">
		
		<div class="content-post-acf">
		
		</div>
		
		<div class="actions-container">
			<button type="button" class="add-post_acf"><?php _ex("Add", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></button>
		</div>
	</div>
<?php endif; ?>

<?php if (is_plugin_active("woocommerce/woocommerce.php")): ?>
	<h1><?php _ex("Woocommerce", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></h1>
	<div id="post-woocommerce" class="container-post-meta" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>" data-weight_unit="<?php echo esc_attr(get_option('woocommerce_weight_unit')); ?>" data-dimension_unit="<?php echo esc_attr(get_option('woocommerce_dimension_unit')); ?>">
		<div class="icp-loader active">
			<div></div>
		</div>
		<input type="hidden" name="post-woocommerce" value="<?php echo esc_attr($post_woocommerce ?? ""); ?>">
		
		<div class="content-post-meta">
		
		</div>
		
		<div class="actions-container">
			<button type="button" class="add-post_meta"><?php _ex("Add", "Admin view configuration post, meta-box 'post'", "infocob-crm-products"); ?></button>
		</div>
	</div>
<?php endif; ?>
