<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Catalog;
	use Infocob\CRM\Products\Admin\Classes\CustomPosts\CatalogPost;
	use Infocob\CRM\Products\Admin\Classes\CustomPosts\ConfigurationPost;
	use Infocob\CRM\Products\Admin\Classes\CustomPosts\MediaPost;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Widgets\Widget;
	use Infocob\CRM\Products\Admin\Controllers\InfocobCRMProducts;
	use Infocob\CRM\Products\Admin\Controllers\Logs;
	use Infocob\CRM\Products\Admin\Controllers\Settings;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	include_once ABSPATH . '/wp-admin/includes/plugin.php';
	
	class Hooks {
		private $scripts_modules = [
			"infocob_crm_products_default_js",
			"infocob_crm_products_configuration_list_js",
			"infocob_crm_products_configuration_main_js",
			"infocob_crm_products_configuration_infocob_js",
			"infocob_crm_products_configuration_inventory_js",
			"infocob_crm_products_configuration_mappings_js",
			"infocob_crm_products_configuration_post_js",
			"infocob_crm_products_configuration_files_js",
			"infocob_crm_products_configuration_api_js",
			"infocob_crm_products_catalog_general_js",
			"infocob_crm_products_catalog_left_filters_js",
			"infocob_crm_products_catalog_right_filters_js",
			"infocob_crm_products_catalog_top_filters_js",
			"infocob_crm_products_catalog_products_js",
			"infocob_crm_products_logs_main_js",
			"infocob_crm_products_utils_js",
			"infocob_crm_products_dashboard_widgets_js",
			"infocob_crm_products_settings_main_js",
			
			"infocob_crm_products_catalog_public_main_js",
			"infocob_crm_products_catalog_public_jquery_ui_touch_punch_js",
		];
		
		public function init() {
			$status = session_status();
			if ($status == PHP_SESSION_NONE) {
				if (!session_id() && !(defined("DOING_CRON") && DOING_CRON)) {
					session_start();
				}
			}
			
			register_setting('infocob-crm-products', 'infocob-crm-products-settings');
			
			Configuration::initDatabase();
			
			ConfigurationPost::register();
			ConfigurationPost::add_rest_api_endpoint();
			if (is_admin()) {
				ConfigurationPost::manage_posts_columns();
				
				MediaPost::manage_posts_columns();
			}
			
			CatalogPost::register();
			CatalogPost::initShortcodes();
			if (is_admin()) {
				CatalogPost::manage_posts_columns();
			}
			
			Polylang::register_string();
			
			load_plugin_textdomain( 'infocob-crm-products', false, dirname(INFOCOB_CRM_PRODUCTS_BASENAME).'/languages');
		}
		
		/**
		 * @param array $links
		 *
		 * @return array
		 */
		public function settings_links(array $links) {
			$url = esc_url(add_query_arg(
				[
					"page" => "infocob-crm-products-settings"
				],
				get_admin_url() . 'admin.php'
			));
			
			$settings_link = "<a href='" . $url . "'>" . esc_html_x('Settings', "Setting link from plugins list", "infocob-crm-products") . "</a>";
			
			array_unshift(
				$links,
				$settings_link
			);
			
			return $links;
		}
		
		public function admin_menu() {
			$icon = "dashicons-database";
			add_menu_page(esc_html_x("Infocob CRM Products", "add_menu_page page_title", "infocob-crm-products"), esc_html_x("Infocob CRM Products", "add_menu_page menu_title", "infocob-crm-products"), 'update_core', "infocob-crm-products", [
				new InfocobCRMProducts(), "render"
			], $icon, 20);
			
			add_submenu_page('infocob-crm-products', esc_html_x('Configurations', "add_submenu_page page title", "infocob-crm-products"), esc_html_x('Configurations', "add_submenu_page menu title", "infocob-crm-products"), 'update_core', 'edit.php?post_type=icp-configuration');
			
			add_submenu_page('infocob-crm-products', esc_html_x('Catalogs', "add_submenu_page page title", "infocob-crm-products"), esc_html_x('Catalogs', "add_submenu_page menu title", "infocob-crm-products"), 'update_core', 'edit.php?post_type=icp-catalog');
			
			add_submenu_page('infocob-crm-products', esc_html_x('Settings', "add_submenu_page page title", "infocob-crm-products"), esc_html_x('Settings', "add_submenu_page menu title", "infocob-crm-products"), 'update_core', 'infocob-crm-products-settings', [
				new Settings(),
				'render'
			]);
			
			add_submenu_page('infocob-crm-products', esc_html_x('Logs', "add_submenu_page page title", "infocob-crm-products"), esc_html_x('Logs', "add_submenu_page menu title", "infocob-crm-products"), 'update_core', 'infocob-crm-products-logs', [
				new Logs(),
				'render'
			]);
		}
		
		public function admin_notices() {
			if (current_user_can('update_core')) {
				//get the current screen
				$screen = get_current_screen();
				
				// Settings page
				if ($screen->id === "infocob-crm-products_page_infocob-crm-products-settings") {
					$options = get_option('infocob-crm-products-settings');
					
					if (!empty($options["database"]["enable"])) {
						Tools::include("notices/database-connection.php");
					}
				}
			}
		}
		
		public function admin_enqueue_scripts() {
			if (current_user_can('update_core')) {
				ConfigurationPost::wp_admin_register_scripts();
				CatalogPost::wp_admin_register_scripts();
				Settings::wp_admin_register_scripts();
				Logs::wp_admin_register_scripts();
				InfocobCRMProducts::wp_admin_register_scripts();
				Widget::wp_admin_register_scripts();
				
				//get the current screen
				$screen = get_current_screen();
				
				// Scripts available everywhere
				if (in_array($screen->id, [
					"icp-configuration",
					"edit-icp-configuration",
					"icp-catalog",
					"edit-icp-catalog",
					"infocob-crm-products_page_infocob-crm-products-settings",
					"infocob-crm-products_page_infocob-crm-products-logs",
					"toplevel_page_infocob-crm-products",
				])) {
					/*
					 * ES6 Promise
					 * https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.js
					 */
					wp_register_script('infocob_crm_products_es6_promise_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/es6-promise.auto.js');
					wp_enqueue_script('infocob_crm_products_es6_promise_js');
					
					/*
					 * Popperjs
					 * https://unpkg.com/@popperjs/core@2
					 */
					wp_register_script('infocob_crm_products_popper_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/popper.min.js');
					wp_enqueue_script('infocob_crm_products_popper_js');
					
					/*
					 * Tippy.js
					 * https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js
					 */
					wp_register_script('infocob_crm_products_tippy_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/tippy-bundle.umd.min.js');
					wp_enqueue_script('infocob_crm_products_tippy_js');
					
					/*
					 * Flatpickr.js
					 * https://cdn.jsdelivr.net/npm/flatpickr
					 * https://npmcdn.com/flatpickr@4.6.9/dist/l10n/fr.js
					 */
					wp_register_script('infocob_crm_products_flatpickr_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/flatpickr.js');
					wp_enqueue_script('infocob_crm_products_flatpickr_js');
					
					wp_register_script('infocob_crm_products_flatpickr_fr_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/flatpickr-fr.js');
					wp_enqueue_script('infocob_crm_products_flatpickr_fr_js');
					
					/*
					 * File Utils.js
					 */
					wp_register_script('infocob_crm_products_utils_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/Utils.js', [
						'jquery',
						'wp-i18n'
					], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
					wp_enqueue_script('infocob_crm_products_utils_js');
					wp_set_script_translations('infocob_crm_products_utils_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
					
					/*
					 * AJAX requests
					 */
					wp_localize_script('infocob_crm_products_utils_js', 'admin_ajax_utils', [
						"url"      => admin_url('admin-ajax.php'),
						"security" => wp_create_nonce('icp-security-nonce')
					]);
					
					/*
					 * File default.js
					 */
					wp_register_script('infocob_crm_products_default_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/default.js', [
						'jquery',
						'wp-i18n'
					], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
					wp_enqueue_script('infocob_crm_products_default_js');
					wp_set_script_translations('infocob_crm_products_default_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
					
					/*
					 * File styles
					 * https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css
					 */
					wp_register_style('infocob_crm_products_flatpickr_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/libs/flatpickr.min.css');
					wp_enqueue_style('infocob_crm_products_flatpickr_css');
				}
			}
		}
		
		public function wp_enqueue_scripts() {
			CatalogPost::wp_register_scripts();
		}
		
		/**
		 * @param $post_id
		 */
		public function save_post_infocob_crm_products($post_id) {
			$custom_post_type = new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost();
			$custom_post_type->save($post_id);
		}
		
		/**
		 * @param $post_id
		 */
		public function save_post_post_media($post_id) {
			$custom_post_type = new \Infocob\CRM\Products\Admin\Controllers\MediaPost();
			$custom_post_type->save($post_id);
		}
		
		/**
		 * @param $post_id
		 */
		public function save_post_catalog($post_id) {
			$custom_post_type = new \Infocob\CRM\Products\Admin\Controllers\CatalogPost();
			$custom_post_type->save($post_id);
		}
		
		public function add_meta_boxes() {
			ConfigurationPost::add_meta_boxes();
			ConfigurationPost::add_help_tabs();
			MediaPost::add_meta_boxes();
			CatalogPost::add_meta_boxes();
		}
		
		public function add_dashboard_widgets() {
			if (current_user_can('update_core') && InfocobDB::testConnection()) {
				ConfigurationPost::add_dashboard_widgets();
			}
		}
		
		public function pre_get_posts($query) {
			if (!is_admin() && $query->is_main_query() && $query instanceof \WP_Query) {
				Catalog::pre_get_posts($query);
				
				if (is_tax() || is_archive() || $query->is_search) {
					$meta_query = [
						"relation" => "OR",
						[
							'key'     => ProductMeta::P_SUPP_META_KEY,
							'compare' => 'NOT EXISTS',
						],
						[
							"relation" => "AND",
							[
								'key'     => ProductMeta::P_SUPP_META_KEY,
								'compare' => 'EXISTS',
							],
							[
								'key'     => ProductMeta::P_SUPP_META_KEY,
								'value'   => 1,
								'compare' => '!='
							]
						]
					];
					
					if ($query->get("meta_query") !== false) {
						$existing_meta_query = (array)$query->get("meta_query");
						$existing_meta_query[] = $meta_query;
						
						$query->meta_query = $existing_meta_query;
					} else {
						$query->meta_query = $meta_query;
					}
					
					$query->set('meta_query', $query->meta_query);
				}
			}
		}
		
		/**
		 * @param $tag
		 * @param $handle
		 * @param $src
		 *
		 * @return mixed|string
		 */
		public function script_loader_tag($tag, $handle, $src) {
			if (in_array($handle, $this->scripts_modules)) {
				$tag = str_ireplace("text/javascript", "module", $tag);
				if(!str_contains($tag, "type")) {
					$tag = str_ireplace("src=", "type='module' src=", $tag);
				}
			}
			
			return $tag;
		}
		
		/**
		 * Add the duplicate link to action list for post_row_actions
		 * @link https://rudrastyh.com/wordpress/duplicate-post.html
		 */
		public function rd_duplicate_post_as_draft() {
			global $wpdb;
			if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == sanitize_text_field($_REQUEST['action'])))) {
				wp_die('No post to duplicate has been supplied!');
			}
			
			/*
			 * Nonce verification
			 */
			if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], "icp-configuration-duplicate")) {
				return;
			}
			
			/*
			 * get the original post id
			 */
			$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
			/*
			 * and all the original post data then
			 */
			$post = get_post($post_id);
			
			/*
			 * if you don't want current user to be the new post author,
			 * then change next couple of lines to this: $new_post_author = $post->post_author;
			 */
			//$current_user    = wp_get_current_user();
			//$new_post_author = $current_user->ID;
			$new_post_author = $post->post_author;
			
			/*
			 * if post data exists, create the post duplicate
			 */
			if (isset($post) && $post != null) {
				
				/*
				 * new post data array
				 */
				$args = [
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'draft',
					'post_title'     => $post->post_title,
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order
				];
				
				/*
				 * insert the post by wp_insert_post() function
				 */
				$new_post_id = wp_insert_post($args);
				
				/*
				 * get all current post terms ad set them to the new post draft
				 */
				$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
				foreach ($taxonomies as $taxonomy) {
					$post_terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
					wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				}
				
				/*
				 * duplicate all post meta just in two SQL queries
				 */
				$post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id));
				if (count($post_meta_infos) != 0) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ($post_meta_infos as $meta_info) {
						$meta_key = $meta_info->meta_key;
						$meta_value = addslashes($meta_info->meta_value);
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}
					$sql_query .= implode(" UNION ALL ", $sql_query_sel);
					$wpdb->query($sql_query);
				}
				
				
				/*
				 * finally, redirect to the edit post screen for the new draft
				 */
				wp_redirect(admin_url('post.php?action=edit&post=' . esc_html($new_post_id)));
				exit;
			} else {
				wp_die('Post creation failed, could not find original post: ' . esc_html($post_id));
			}
		}
		
		/**
		 * Add the duplicate link to action list for post_row_actions
		 * @link https://rudrastyh.com/wordpress/duplicate-post.html
		 *
		 * @param $actions
		 * @param $post
		 *
		 * @return mixed
		 */
		function rd_duplicate_post_link($actions, $post) {
			if ($post->post_type == 'icp-configuration' && current_user_can('edit_posts')) {
				$structure = get_option('permalink_structure');
				if ($structure === "/%postname%/") {
					$wp_api_url = get_site_url() . "/wp-json/";
				} else {
					$wp_api_url = get_site_url() . "/?rest_route=/";
				}
				
				$myActions = [];
				$myActions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&post=' . $post->ID . '&duplicate_nonce=' . wp_create_nonce('icp-configuration-duplicate') . '" title="Duplicate" rel="permalink">' . esc_html_x('Duplicate', "Duplicate button from custom post type product list", "infocob-crm-products") . '</a>';
				
				if ($post->post_status !== "trash") {
					$myActions['start-import'] = '<a class="icp-post-link-start-import" href="#" title="Start import" rel="permalink" target="_blank" data-post_id="' . ($post->ID ?? "") . '">' . esc_html_x('Start import', "Import configuration list page", "infocob-crm-products") . '</a>';
				}
				// Get the position of the trash in the array
				$offset = array_search("trash", array_keys($actions));
				// Insert before the position of the trash
				$actions = array_slice($actions, 0, $offset, true) +
					$myActions +
					array_slice($actions, $offset, count($actions) - 1, true);
			}
			
			return $actions;
		}
		
	}
