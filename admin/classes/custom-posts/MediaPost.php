<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes\CustomPosts;
	
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class MediaPost {
		
		public static function add_meta_boxes() {
			add_meta_box("meta-box-infocob-media", esc_html_x("Infocob CRM Products", "Meta box title post type media", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\MediaPost(),
				"renderMetaBoxMedia"
			], ['attachment']);
		}
		
		/*
		 * Add columns to post_type grid
		 */
		public static function manage_posts_columns() {
			// Add the custom columns to the post type
			add_filter('manage_media_columns', function ($columns) {
				$columns['icp_last_update'] = __('Last update', 'infocob-crm-products');
				$columns['icp_code_infocob'] = __('Infocob code', 'infocob-crm-products');
				
				return $columns;
			});
			
			// Add the data to the custom columns for the post type:
			add_action('manage_media_custom_column', function ($column, $post_id) {
				switch ($column) {
					case 'icp_code_infocob' :
						echo esc_html(get_post_meta($post_id, ProductMeta::FC_CODE_META_KEY, true));
						break;
					case 'icp_last_update' :
						echo esc_html(get_the_modified_date("", $post_id) . " " . get_the_modified_time("", $post_id));
						break;
				}
			}, 10, 2);
			
			add_filter('manage_upload_sortable_columns', function ($columns) {
				$columns['icp_last_update'] = 'icp_last_update';
				$columns['icp_code_infocob'] = 'icp_code_infocob';
				
				return $columns;
			});
			
			add_filter("posts_search", function ($search, $query) {
				global $wpdb;
				
				if ($query->is_main_query() && !empty($query->query['s'])) {
					if ($query->query['post_type'] !== "attachment")
						return $search;
					
					$sql    = "
							or exists (
								select * from " . $wpdb->postmeta . " where post_id=" . $wpdb->posts . ".ID
								and meta_key in ('" . ProductMeta::FC_CODE_META_KEY . "')
								and meta_value like %s
							)
						";
					$like   = '%' . $wpdb->esc_like($query->query['s']) . '%';
					$search = preg_replace("#\(" . $wpdb->posts . ".post_title LIKE [^)]+\)\K#", $wpdb->prepare($sql, $like), $search);
					
				}
				
				return $search;
			}, 10, 2);
			
			add_filter('request', function ($vars) {
				$screen = get_current_screen();
				
				if($screen->id === "upload") {
					if (isset($vars['orderby']) && 'icp_last_update' === $vars['orderby']) {
						$vars = array_merge($vars, [
								'order'   => $vars['order'] ?? "desc",
								'orderby' => 'modified',
							]
						);
					} else if (isset($vars['orderby']) && 'icp_code_infocob' === $vars['orderby']) {
						$vars = array_merge($vars, [
								'meta_key' => ProductMeta::FC_CODE_META_KEY,
								'orderby'  => 'meta_value',
							]
						);
					}
				}
				
				return $vars;
			});
		}
		
	}
