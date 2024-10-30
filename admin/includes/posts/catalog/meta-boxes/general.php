<table class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="general-post-type"><?php echo esc_html_x("Post type", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="general-post-type" id="general-post-type" class="all-witdh">
					<?php foreach (($general_post_types ?? []) as $post_type): ?>
						<?php if($post_type instanceof WP_Post_Type): ?>
							<option value="<?php echo esc_attr($post_type->name); ?>" <?php echo ($post_type->name === ($general_post_type ?? "")) ? "selected" : ""; ?>><?php echo esc_html($post_type->label . " (" . $post_type->name . ")"); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="general-override-styles" data-tippy-content="<?php echo esc_attr_x("Disables the default styles provided by the plugin", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Override styles", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="general-override-styles" type="checkbox" id="general-override-styles" value="1" <?php if($general_override_styles ?? false): ?>checked<?php endif; ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php echo esc_html_x("Theme files", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></label>
			</th>
			<td id="general-theme-files">
				<?php if(!empty($general_post_type)): ?>
					<span class="btn">
						<button type="button" class="generate-all" data-post_id="<?php echo esc_attr(get_the_ID()); ?>"><?php echo esc_html_x("Generate all theme's files", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></button>
					</span>
					<div class="content">
						<span class="dashicons"></span>
						<span class="path"></span>
					</div>
					
					<?php foreach ($general_theme_files ?? [] as $type => $files): ?>
						<?php foreach ($files as $path => $file): ?>
								<div class="file">
									<span class="btn">
										<button type="button" data-post_id="<?php echo esc_attr(get_the_ID()); ?>" data-file="<?php echo esc_attr($path); ?>" data-type="<?php echo esc_attr($type); ?>" data-generated="<?php echo $file["exists"] ? "true" : "false"; ?>"><?php echo esc_html_x("Generate theme's file", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></button>
									</span>
									<div class="content">
										<span class="dashicons <?php echo $file["exists"] ? "dashicons-yes" : "dashicons-no"; ?>"></span>
										<span class="path"><?php echo esc_html($path); ?> <span class="date"><?php if($file["date"] ?? false): ?> (<?php echo esc_html_x(sprintf("Last update : %s", esc_html($file["date"] ?? "")), "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?>)<?php endif; ?></span></span>
									</div>
								</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="error"><?php echo esc_html_x("You have to choose the post type and save the configuration to allow to generate theme files", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"); ?></div>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>

