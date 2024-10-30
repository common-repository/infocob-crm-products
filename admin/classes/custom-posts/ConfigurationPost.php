<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes\CustomPosts;
	
	use Cassandra\Date;
	use DateTime;
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Classes\Widgets\IcpConfigurationDashboardWidget;
	use Infocob\CRM\Products\Admin\Controllers\RestApi;
	use WP_Screen;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ConfigurationPost {
		
		public static function register() {
			// @TODO capabilities admin
			//Posts Configurations
			$labels = [
				'name'           => esc_html_x('Infocob CRM Products', "Registration custom post type label name", "infocob-crm-products"),
				'singular_name'  => esc_html_x('Infocob CRM Products', "Registration custom post type label singular_name", "infocob-crm-products"),
				'menu_name'      => esc_html_x('Infocob CRM Products', "Registration custom post type label menu_name", "infocob-crm-products"),
				'name_admin_bar' => esc_html_x('Infocob CRM Products', "Registration custom post type label name_admin_bar", "infocob-crm-products"),
				'add_new'        => esc_html_x('Add', "Registration custom post type label add_new", "infocob-crm-products"),
				'add_new_item'   => esc_html_x('Create configuration', "Registration custom post type label add_new_item", "infocob-crm-products"),
				'new_item'       => esc_html_x('Create configuration', "Registration custom post type label new_item", "infocob-crm-products"),
				'edit_item'      => esc_html_x('Edit', "Registration custom post type label edit_item", "infocob-crm-products"),
				'view_item'      => esc_html_x('See', "Registration custom post type label view_item", "infocob-crm-products"),
				'all_items'      => esc_html_x('Configurations', "Registration custom post type label all_items", "infocob-crm-products"),
				'search_items'   => esc_html_x('Search', "Registration custom post type label search_items", "infocob-crm-products"),
			];
			
			$args = [
				'labels'             => $labels,
				'description'        => esc_html_x('Configuration', "Registration custom post type description", "infocob-crm-products"),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => false,
				'capability_type'    => 'post',
				'capabilities'       => [
					'edit_post'          => 'update_core',
					'read_post'          => 'update_core',
					'delete_post'        => 'update_core',
					'edit_posts'         => 'update_core',
					'edit_others_posts'  => 'update_core',
					'delete_posts'       => 'update_core',
					'publish_posts'      => 'update_core',
					'read_private_posts' => 'update_core'
				],
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-networking',
				'show_in_rest'       => true,
				'supports'           => ['title']
			];
			
			register_post_type('icp-configuration', $args);
		}
		
		public static function add_rest_api_endpoint() {
			add_action('rest_api_init', function () {
				register_rest_route('infocob-crm-products/v1', '/import/(?P<id>\d+)', [
					'methods'             => 'GET',
					'callback'            => [RestApi::class, "import"],
					'permission_callback' => '__return_true'
				]);
				
				register_rest_route('infocob-crm-products/v1', '/imports', [
					'methods'             => 'GET',
					'callback'            => [RestApi::class, "imports"],
					'permission_callback' => '__return_true'
				]);
				
				register_rest_route('infocob-crm-products/v1', '/info/import/(?P<id>\d+)', [
					'methods'             => 'GET',
					'callback'            => [RestApi::class, "infoImport"],
					'permission_callback' => '__return_true'
				]);
				
				register_rest_route('infocob-crm-products/v1', '/info/imports', [
					'methods'             => 'GET',
					'callback'            => [RestApi::class, "infoImports"],
					'permission_callback' => '__return_true'
				]);
			});
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			if ($screen->id === "edit-icp-configuration") {
				/*
				 * Sweetalert2
				 */
				wp_register_script('infocob_crm_products_sweetalert2_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.all.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_sweetalert2_js');
				
				/*
				 * File list.js
				 */
				wp_register_script('infocob_crm_products_configuration_list_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/list.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_list_js');
				wp_set_script_translations('infocob_crm_products_configuration_list_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Styles
				 */
				wp_register_style('infocob_crm_products_configuration_list_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/configuration-list.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_configuration_list_css');
				
				
			} // Custom post type configuration
			else if ($screen->id === "icp-configuration") {
				/*
				 * Sweetalert2
				 */
				wp_register_script('infocob_crm_products_sweetalert2_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.all.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_sweetalert2_js');
				
				/*
				 * Multiple-select
				 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.js
				 */
				wp_register_script('infocob_crm_products_multiple_select_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/multiple-select.min.js');
				wp_enqueue_script('infocob_crm_products_multiple_select_js');
				
				/*
				 * Horsey
				 */
				wp_register_script('infocob_crm_products_tributejs_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/tributejs/dist/tribute.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_tributejs_js');
				
				/*
				 * File main.js
				 */
				wp_register_script('infocob_crm_products_configuration_main_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/main.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_main_js');
				wp_set_script_translations('infocob_crm_products_configuration_main_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				
				/*
				 * Meta boxe Infocob
				 */
				// File configuration/meta-boxes/infocob.js
				wp_register_script('infocob_crm_products_configuration_infocob_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/infocob.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_infocob_js');
				wp_set_script_translations('infocob_crm_products_configuration_infocob_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Mappings
				 */
				// File configuration/meta-boxes/mappings.js
				wp_register_script('infocob_crm_products_configuration_mappings_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/mappings.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_mappings_js');
				wp_set_script_translations('infocob_crm_products_configuration_mappings_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Post
				 */
				// File configuration/meta-boxes/post.js
				wp_register_script('infocob_crm_products_configuration_post_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/post.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_post_js');
				wp_set_script_translations('infocob_crm_products_configuration_post_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Inventory
				 */
				// File configuration/meta-boxes/inventory.js
				wp_register_script('infocob_crm_products_configuration_inventory_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/inventory.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_inventory_js');
				wp_set_script_translations('infocob_crm_products_configuration_inventory_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Files
				 */
				// File configuration/meta-boxes/files.js
				wp_register_script('infocob_crm_products_configuration_files_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/files.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_files_js');
				wp_set_script_translations('infocob_crm_products_configuration_files_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe API
				 */
				
				// File configuration/meta-boxes/api.js
				wp_register_script('infocob_crm_products_configuration_api_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/configuration/meta-boxes/api.js', [
					'jquery',
					'wp-i18n',
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_configuration_api_js');
				wp_set_script_translations('infocob_crm_products_configuration_api_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Styles
				 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css
				 */
				wp_register_style('infocob_crm_products_multiple_select_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/libs/multiple-select.min.css');
				wp_enqueue_style('infocob_crm_products_multiple_select_css');
				
				wp_register_style('infocob_crm_products_tributejs_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/tributejs/dist/tribute.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_tributejs_css');
				
				wp_register_style('infocob_crm_products_sweetalert2_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_sweetalert2_css');
				
				wp_register_style('infocob_crm_products_configuration_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/configuration.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_configuration_css');
			}
		}
		
		public static function add_meta_boxes() {
			$current_screen = get_current_screen();
			if ($current_screen->id === "icp-configuration") {
				if (InfocobDB::testConnection()) {
					add_meta_box("meta-box-infocob", esc_html_x("Step 1 : Get the products (Refers to all products)", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxInfocob"
					], ['icp-configuration']);
					add_meta_box("meta-box-mappings", esc_html_x("Step 2 : Attribute products to their categories", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxMappings"
					], ['icp-configuration']);
					add_meta_box("meta-box-post", esc_html_x("Step 3 : Define product properties", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxPost"
					], ['icp-configuration']);
					add_meta_box("meta-box-inventory", esc_html_x("Step 4 (optional) : Add additional data from Infocob's inventories (plugin 'ACF' required)", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxInventory"
					], ['icp-configuration']);
					add_meta_box("meta-box-files", esc_html_x("Step 5 : Configure media files", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxFiles"
					], ['icp-configuration']);
					add_meta_box("meta-box-api", esc_html_x("Step 6 (optional) : API & CRON", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
						new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
						"renderMetaBoxApi"
					], ['icp-configuration']);
					
					$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
					if (!empty($post_id)) {
						add_meta_box("meta-box-import", esc_html_x("Final step", "Meta box title custom post type icp-configuration", "infocob-crm-products"), [
							new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
							"renderMetaBoxImport"
						], ['icp-configuration'], "side");
					}
				} else {
					Tools::include("notices/database-connection.php");
				}
			}
		}
		
		public static function add_help_tabs() {
			$current_screen = get_current_screen();
			if ($current_screen->id === "icp-configuration") {
				if (InfocobDB::testConnection()) {
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 1", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-1",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabInfocob"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 2", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-2",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabMappings"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 3", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-3",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabPost"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 4 (optional)", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-4",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabInventory"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 5", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-5",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabFiles"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Step 6 (optional)", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-6",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabApi"],
					]);
					
					$current_screen->add_help_tab([
						"title"    => esc_html_x("Final step", "Help tab title custom post type product", "infocob-crm-products"),
						"id"       => "icp-step-7",
						"callback" => [new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(), "renderHelpTabImport"],
					]);
				}
			}
		}
		
		/*
		 * Add columns to post_type grid
		 */
		public static function manage_posts_columns() {
			// Add the custom columns to the post type
			add_filter('manage_icp-configuration_posts_columns', function ($columns) {
				$columns['icp_last_update'] = __('Last update', 'infocob-crm-products');
				$columns['icp_last_import'] = __('Last import', 'infocob-crm-products');
				$columns['icp_next_cron'] = __('Next CRON', 'infocob-crm-products');
				$columns['icp_post_id'] = __('ID', 'infocob-crm-products');
				
				return $columns;
			});
			
			// Add the data to the custom columns for the post type:
			add_action('manage_icp-configuration_posts_custom_column', function ($column, $post_id) {
				$wp_date_format = get_option('date_format');
				$wp_time_format = get_option('time_format');
				
				switch ($column) {
					case 'icp_last_import' :
						$date_last_import = get_post_meta($post_id, ProductMeta::P_DATE_IMPORT_META_KEY, true);
						if ($date_last_import !== "") {
							$datetime = DateTimeFr::createFromFormat("Y-m-d H:i:s", $date_last_import, new \DateTimeZone("Europe/Paris"));
							echo esc_html($datetime->format($wp_date_format) . " " . $datetime->format($wp_time_format));
						}
						break;
					case 'icp_last_update' :
						echo esc_html(get_the_modified_date("", $post_id) . " " . get_the_modified_time("", $post_id));
						break;
					case 'icp_next_cron' :
						$cron_scheduled_timestamp = wp_next_scheduled(CRON::$import_product_hook, [$post_id]);
						if ($cron_scheduled_timestamp !== false) {
							$datetime = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
							$datetime = $datetime->setTimestamp($cron_scheduled_timestamp);
							echo esc_html($datetime->format($wp_date_format) . " " . $datetime->format($wp_time_format));
						} else {
							_ex("Unplanned", "Post configuration column", "infocob-crm-products");
						}
						break;
					case 'icp_post_id' :
						echo esc_html($post_id);
						break;
				}
			}, 10, 2);
			
			add_filter('manage_edit-icp-configuration_sortable_columns', function ($columns) {
				$columns['icp_last_update'] = 'icp_last_update';
				$columns['icp_last_import'] = 'icp_last_import';
				$columns['icp_next_cron'] = 'icp_next_cron';
				$columns['icp_post_id'] = 'icp_post_id';
				
				return $columns;
			});
			
			add_filter('request', function ($vars) {
				$screen = get_current_screen();
				
				if ($screen->id !== "upload") {
					if (isset($vars['orderby']) && 'icp_last_update' === $vars['orderby']) {
						$vars = array_merge($vars, [
								'order'   => $vars['order'] ?? "desc",
								'orderby' => 'modified',
							]
						);
					} else if (isset($vars['orderby']) && 'icp_code_infocob' === $vars['orderby']) {
						$vars = array_merge($vars, [
								'meta_key' => ProductMeta::P_CODE_META_KEY,
								'orderby'  => 'meta_value',
							]
						);
					}
				}
				
				return $vars;
			});
			
			/*
			 * Loop through all custom post types that have meta Infocob
			 */
			global $wpdb;
			
			$results = $wpdb->get_results($wpdb->prepare("select distinct p.post_type as POST_TYPE from {$wpdb->prefix}posts p inner join {$wpdb->prefix}postmeta pm on pm.post_id = p.ID where pm.meta_key = %s ", ProductMeta::P_CODE_META_KEY));
			foreach ((array)$results as $result) {
				$post_type = $result->POST_TYPE;
				
				// Add the custom columns to the post type
				add_filter('manage_' . $post_type . '_posts_columns', function ($columns) {
					$columns['icp_last_update'] = __('Last update', 'infocob-crm-products');
					$columns['icp_code_infocob'] = __('Infocob code', 'infocob-crm-products');
					
					return $columns;
				});
				
				// Add the data to the custom columns for the post type:
				add_action('manage_' . $post_type . '_posts_custom_column', function ($column, $post_id) {
					switch ($column) {
						case 'icp_code_infocob' :
							$is_delete = get_post_meta($post_id, ProductMeta::P_SUPP_META_KEY, true);
							
							if ($is_delete) {
								echo "<span style='text-decoration: line-through;'>" . esc_html(get_post_meta($post_id, ProductMeta::P_CODE_META_KEY, true)) . "</span>";
							} else {
								echo esc_html(get_post_meta($post_id, ProductMeta::P_CODE_META_KEY, true));
							}
							break;
						case 'icp_last_update' :
							echo esc_html(get_the_modified_date("", $post_id) . " " . get_the_modified_time("", $post_id));
							break;
					}
				}, 10, 2);
				
				add_filter('manage_edit-' . $post_type . '_sortable_columns', function ($columns) {
					$columns['icp_last_update'] = 'icp_last_update';
					$columns['icp_code_infocob'] = 'icp_code_infocob';
					
					return $columns;
				});
				
				add_filter("posts_search", function ($search, $query) use ($post_type) {
					global $wpdb;
					
					if ($query->is_main_query() && !empty($query->query['s'])) {
						if ($query->query['post_type'] !== $post_type)
							return $search;
						
						$sql = "
							or exists (
								select * from " . $wpdb->postmeta . " where post_id=" . $wpdb->posts . ".ID
								and meta_key in (%s)
								and meta_value like %s
							)
						";
						$like = '%' . $wpdb->esc_like($query->query['s']) . '%';
						$search = preg_replace("#\(" . $wpdb->posts . ".post_title LIKE [^)]+\)\K#", $wpdb->prepare($sql, ProductMeta::P_CODE_META_KEY, $like), $search);
					}
					
					return $search;
				}, 10, 2);
				
				
				/*
				 * Add filter
				 */
				add_action("restrict_manage_posts", function () use ($post_type) {
					$screen = get_current_screen();
					
					/** Ensure this is the correct Post Type*/
					if ($screen->id !== 'edit-' . $post_type) {
						return; //filter your post
					}
					
					// get selected option if there is one selected
					$selectedName = sanitize_text_field($_GET['icp-delete'] ?? "");
					
					/** Grab all of the options that should be shown */
					$options[] = sprintf('<option value="" %s>%s</option>', (($selectedName === "") ? "selected" : ""), __('All status', 'infocob-crm-products'));
					$options[] = sprintf('<option value="icp_delete" %s>%s</option>', (($selectedName === "icp_delete") ? "selected" : ""), __('Deleted', 'infocob-crm-products'));
					$options[] = sprintf('<option value="icp_visible" %s>%s</option>', (($selectedName === "icp_visible") ? "selected" : ""), __('Visible', 'infocob-crm-products'));
					
					/** Output the dropdown menu */
					echo '<select class="" id="icp-delete" name="icp-delete">';
					echo join("\n", $options);
					echo '</select>';
				});
				
				add_filter('parse_query', function ($query) use ($post_type) {
					$icp_delete = sanitize_text_field($_GET["icp-delete"] ?? "");
					
					global $pagenow;
					$current_page = isset($_GET['post_type']) ? $_GET['post_type'] : "";
					
					if (is_admin() && $post_type == $current_page && 'edit.php' == $pagenow && $icp_delete !== "") {
						$meta_value = ($icp_delete === "icp_delete") ? 1 : 0;
						
						$query->query_vars['meta_query'] = array_merge($query->query_vars['meta_query'] ?? [], [
							'relation' => 'AND',
							[
								'key' => ProductMeta::P_SUPP_META_KEY,
								'value' => $meta_value,
								'compare' => '=',
								'type' => 'NUMERIC',
							]
						]);
					}
				});
			}
		}
		
		public static function add_dashboard_widgets() {
			$title = esc_html_x('Infocob CRM Products', 'Dashboard widget', 'infocob-crm-products');
			new IcpConfigurationDashboardWidget("icp_configuration_dashboard_widget", $title);
		}
		
	}
