<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes\CustomPosts;
	
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\Shortcode;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\ShortcodeLeftFilters;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\ShortcodeProducts;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\ShortcodeRightFilters;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\ShortcodeTopFilters;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CatalogPost {
		
		public static function register() {
			//Posts Configurations
			$labels = [
				'name'           => esc_html_x('Catalog', "Registration custom post type label name", "infocob-crm-products"),
				'singular_name'  => esc_html_x('Catalog', "Registration custom post type label singular_name", "infocob-crm-products"),
				'menu_name'      => esc_html_x('Catalog', "Registration custom post type label menu_name", "infocob-crm-products"),
				'name_admin_bar' => esc_html_x('Catalog', "Registration custom post type label name_admin_bar", "infocob-crm-products"),
				'add_new'        => esc_html_x('Add', "Registration custom post type label add_new", "infocob-crm-products"),
				'add_new_item'   => esc_html_x('Create catalog', "Registration custom post type label add_new_item", "infocob-crm-products"),
				'new_item'       => esc_html_x('Create catalog', "Registration custom post type label new_item", "infocob-crm-products"),
				'edit_item'      => esc_html_x('Edit', "Registration custom post type label edit_item", "infocob-crm-products"),
				'view_item'      => esc_html_x('See', "Registration custom post type label view_item", "infocob-crm-products"),
				'all_items'      => esc_html_x('Catalogs', "Registration custom post type label all_items", "infocob-crm-products"),
				'search_items'   => esc_html_x('Search', "Registration custom post type label search_items", "infocob-crm-products"),
			];
			
			$args = [
				'labels'             => $labels,
				'description'        => esc_html_x('Catalog', "Registration custom post type description", "infocob-crm-products"),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => false,
				'capability_type'    => 'post',
				'capabilities' => [
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
				'menu_icon'          => 'dashicons-book',
				'show_in_rest'       => true,
				'supports'           => ['title']
			];
			
			register_post_type('icp-catalog', $args);
		}
		
		public static function wp_register_scripts() {
			/*
			 * ES6 Promise
			 * https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.js
			 */
			wp_register_script('infocob_crm_products_public_es6_promise_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/es6-promise.auto.js');
			
			/*
			 * Popperjs
			 * https://unpkg.com/@popperjs/core@2
			 */
			wp_register_script('infocob_crm_products_public_popper_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/popper.min.js');
			
			/*
			 * Tippy.js
			 * https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js
			 */
			wp_register_script('infocob_crm_products_public_tippy_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/tippy-bundle.umd.min.js');
			
			/*
			 * Flatpickr.js
			 * https://cdn.jsdelivr.net/npm/flatpickr
			 * https://npmcdn.com/flatpickr@4.6.9/dist/l10n/fr.js
			 */
			wp_register_script('infocob_crm_products_public_flatpickr_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/flatpickr.js');
			wp_register_script('infocob_crm_products_public_flatpickr_fr_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/flatpickr-fr.js');
			
			/*
			 * Multiple-select
			 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.js
			 */
			wp_register_script('infocob_crm_products_public_multiple_select_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/multiple-select.min.js', [
				'jquery',
			]);
			
			/*
			 * File main.js
			 */
			wp_register_script('infocob_crm_products_catalog_public_main_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'public/assets/js/main.js', [
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-slider',
				'wp-i18n'
			], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
			wp_set_script_translations('infocob_crm_products_catalog_public_main_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
			
			/*
			 * File jquery.ui.touch-punch.min.js
			 */
			wp_register_script('infocob_crm_products_catalog_public_jquery_ui_touch_punch_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/jquery.ui.touch-punch.min.js', [
				'infocob_crm_products_catalog_public_main_js'
			], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
			
			/*
			 * Styles
			 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css
			 * https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css
			 */
			wp_register_style('infocob_crm_products_public_multiple_select_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/libs/multiple-select.min.css');
			wp_register_style('infocob_crm_products_public_jquery_ui_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/libs/jquery-ui.css');
			wp_register_style('infocob_crm_products_public_main_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'public/assets/css/main.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
			wp_register_style('infocob_crm_products_public_range_ui_css', false, []); // Use for wp_add_inline_style
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			if($screen->id === "edit-icp-catalog") {
				/*
				 * File list.js
				 */
				wp_register_script('infocob_crm_products_catalog_list_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/list.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_list_js');
				wp_set_script_translations('infocob_crm_products_catalog_list_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Styles
				 */
				wp_register_style('infocob_crm_products_catalog_list_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/catalog-list.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_catalog_list_css');
			}
			
			if ($screen->id === "icp-catalog") {
				/*
				 * Multiple-select
				 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.js
				 */
				wp_register_script('infocob_crm_products_multiple_select_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/multiple-select.min.js');
				wp_enqueue_script('infocob_crm_products_multiple_select_js');
				
				/*
				 * Sweetalert2
				 */
				wp_register_script('infocob_crm_products_sweetalert2_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.all.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_sweetalert2_js');
				
				/*
				 * File main.js
				 */
				wp_register_script('infocob_crm_products_catalog_main_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/main.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_main_js');
				wp_set_script_translations('infocob_crm_products_catalog_main_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe General
				 */
				// File catalog/meta-boxes/general.js
				wp_register_script('infocob_crm_products_catalog_general_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/meta-boxes/general.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_general_js');
				wp_set_script_translations('infocob_crm_products_catalog_general_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Left filters
				 */
				// File catalog/meta-boxes/left-filters.js
				wp_register_script('infocob_crm_products_catalog_left_filters_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/meta-boxes/left-filters.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_left_filters_js');
				wp_set_script_translations('infocob_crm_products_catalog_left_filters_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Right filters
				 */
				// File catalog/meta-boxes/right-filters.js
				wp_register_script('infocob_crm_products_catalog_right_filters_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/meta-boxes/right-filters.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_right_filters_js');
				wp_set_script_translations('infocob_crm_products_catalog_right_filters_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Top filters
				 */
				// File catalog/meta-boxes/top-filters.js
				wp_register_script('infocob_crm_products_catalog_top_filters_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/meta-boxes/top-filters.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_top_filters_js');
				wp_set_script_translations('infocob_crm_products_catalog_top_filters_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Meta boxe Products
				 */
				// File catalog/meta-boxes/products.js
				wp_register_script('infocob_crm_products_catalog_products_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/catalog/meta-boxes/products.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_catalog_products_js');
				wp_set_script_translations('infocob_crm_products_catalog_products_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Styles
				 * https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css
				 */
				wp_register_style('infocob_crm_products_multiple_select_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/libs/multiple-select.min.css');
				wp_enqueue_style('infocob_crm_products_multiple_select_css');
				
				wp_register_style('infocob_crm_products_sweetalert2_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_sweetalert2_css');
				
				wp_register_style('infocob_crm_products_catalog_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/catalog.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_catalog_css');
			}
		}
		
		public static function add_meta_boxes() {
			add_meta_box("meta-box-general", esc_html_x("General", "Meta box title post type catalog", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\CatalogPost(),
				"renderMetaBoxGeneral"
			], ['icp-catalog']);
			add_meta_box("meta-box-left-filters", esc_html_x("Left filters", "Meta box title post type catalog", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\CatalogPost(),
				"renderMetaBoxLeftFilters"
			], ['icp-catalog']);
			add_meta_box("meta-box-right-filters", esc_html_x("Right filters", "Meta box title post type catalog", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\CatalogPost(),
				"renderMetaBoxRightFilters"
			], ['icp-catalog']);
			add_meta_box("meta-box-top-filters", esc_html_x("Top filters", "Meta box title post type catalog", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\CatalogPost(),
				"renderMetaBoxTopFilters"
			], ['icp-catalog']);
			add_meta_box("meta-box-products", esc_html_x("Products list", "Meta box title post type catalog", "infocob-crm-products"), [
				new \Infocob\CRM\Products\Admin\Controllers\CatalogPost(),
				"renderMetaBoxProducts"
			], ['icp-catalog']);
		}
		
		/*
		 * Add columns to post_type grid
		 */
		public static function manage_posts_columns() {
			// Add the custom columns to the post type
			add_filter('manage_icp-catalog_posts_columns', function ($columns) {
				$columns['icp_shortcode_left_filters'] = __('Shortcode left filters', 'infocob-crm-products');
				$columns['icp_shortcode_right_filters'] = __('Shortcode right filters', 'infocob-crm-products');
				$columns['icp_shortcode_top_filters'] = __('Shortcode top filters', 'infocob-crm-products');
				$columns['icp_last_update'] = __('Last update', 'infocob-crm-products');
				
				return $columns;
			});
			
			// Add the data to the custom columns for the post type:
			add_action('manage_icp-catalog_posts_custom_column', function ($column, $post_id) {
				switch ($column) {
					case 'icp_shortcode_left_filters' :
						$shortcode = "";
						if (!empty($post_id)) {
							$shortcode = "[infocob_crm_products_catalog_left_filters id='" . $post_id . "']";
						}
						echo esc_html($shortcode);
						break;
					case 'icp_shortcode_right_filters' :
						$shortcode = "";
						if (!empty($post_id)) {
							$shortcode = "[infocob_crm_products_catalog_right_filters id='" . $post_id . "']";
						}
						echo esc_html($shortcode);
						break;
					case 'icp_shortcode_top_filters' :
						$shortcode = "";
						if (!empty($post_id)) {
							$shortcode = "[infocob_crm_products_catalog_top_filters id='" . $post_id . "']";
						}
						echo esc_html($shortcode);
						break;
					case 'icp_last_update' :
						echo get_the_modified_date("", $post_id) . " " . get_the_modified_time("", $post_id);
						break;
				}
			}, 10, 2);
			
			add_filter('manage_edit-icp-catalog_sortable_columns', function ($columns) {
				$columns['icp_shortcode_left_filters'] = 'icp_shortcode_left_filters';
				$columns['icp_shortcode_right_filters'] = 'icp_shortcode_left_filters';
				$columns['icp_shortcode_top_filters'] = 'icp_shortcode_top_filters';
				$columns['icp_last_update'] = 'icp_last_update';
				
				return $columns;
			});
		}
		
		public static function initShortcodes() {
			add_shortcode('infocob_crm_products_catalog_left_filters', [ShortcodeLeftFilters::class, "init"]);
			add_shortcode('infocob_crm_products_catalog_right_filters', [ShortcodeRightFilters::class, "init"]);
			add_shortcode('infocob_crm_products_catalog_top_filters', [ShortcodeTopFilters::class, "init"]);
		}
		
	}
