<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Functions;
	use Infocob\CRM\Products\Admin\Classes\Hooks;
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use WP_Query;
	use WpOrg\Requests\Exception;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	include_once ABSPATH . '/wp-admin/includes/plugin.php';
	
	class InfocobCRMProducts extends Controller {
		
		public function __construct() {
			// nothing to do
		}
		
		public function initialize() {
			$timezone_string = wp_timezone_string();
			if(in_array($timezone_string, \DateTimeZone::listIdentifiers())) {
				date_default_timezone_set($timezone_string); // CDT
			}
			
			register_activation_hook(ROOT_INFOCOB_CRM_PRODUCTS_FULL_PATH, [self::class, 'onActivate']);
			register_uninstall_hook(ROOT_INFOCOB_CRM_PRODUCTS_FULL_PATH, [self::class, 'onUninstall']);
			register_deactivation_hook(ROOT_INFOCOB_CRM_PRODUCTS_FULL_PATH, [self::class, 'onDeactivate']);
			
			$this->add_actions();
			$this->add_filters();
			
			Functions::register();
		}
		
		public function add_actions() {
			add_action('init', [new Hooks(), 'init']);
			add_action('admin_menu', [new Hooks(), 'admin_menu']);
			add_action('admin_notices', [new Hooks(), 'admin_notices']);
			add_action('wp_enqueue_scripts', [new Hooks(), 'wp_enqueue_scripts']);
			add_action('admin_enqueue_scripts', [new Hooks(), 'admin_enqueue_scripts']);
			add_action('admin_action_rd_duplicate_post_as_draft', [new Hooks(), 'rd_duplicate_post_as_draft']);
			
			add_action('save_post_icp-configuration', [new Hooks(), 'save_post_infocob_crm_products']);
			add_action('edit_attachment', [new Hooks(), 'save_post_post_media'], 10, 1);    // Use to save meta-box on post media
			add_action('save_post_icp-catalog', [new Hooks(), 'save_post_catalog'], 10, 1); // Use to save meta-box on post media
			add_action('add_meta_boxes', [new Hooks(), 'add_meta_boxes']);
			
			add_action('pre_get_posts', [new Hooks(), 'pre_get_posts']);
			
			add_action('wp_dashboard_setup', [new Hooks(), 'add_dashboard_widgets']);
			
			add_action('plugins_loaded', [self::class, 'onUpdate']);
			
			//add_action('upgrader_process_complete', [self::class, 'onUpdate'], 10, 2);
			
			/*
			 * CRON
			 */
			add_action('delete_post', [
				new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
				"onPostDelete"
			], 10, 2);
			add_action('wp_trash_post', [
				new \Infocob\CRM\Products\Admin\Controllers\ConfigurationPost(),
				"onPostTrash"
			], 10, 2);
			add_action(CRON::$import_product_hook, [CRON::class, "importProducts"], 10, 1);
			
			add_action('transition_post_status', [new ConfigurationPost(), 'onPostStatusChange'], 10, 3);
			
			/*
			 * AJAX requests
			 */
			add_action('wp_ajax_get_champs_infocob', [new ConfigurationPost(), 'wp_ajax_get_champs_infocob']);
			add_action('wp_ajax_get_champs_inventaires_infocob', [new ConfigurationPost(), 'wp_ajax_get_champs_inventaires_infocob']);
			add_action('wp_ajax_get_post_types_from_taxonomy', [
				new ConfigurationPost(),
				'wp_ajax_get_post_types_from_taxonomy'
			]);
			add_action('wp_ajax_get_taxonomies_from_post_type', [
				new ConfigurationPost(),
				'wp_ajax_get_taxonomies_from_post_type'
			]);
			add_action('wp_ajax_get_categories_from_taxonomy', [
				new ConfigurationPost(),
				'wp_ajax_get_categories_from_taxonomy'
			]);
			add_action('wp_ajax_get_post_meta_values', [
				new CatalogPost(),
				'wp_ajax_get_post_meta_values'
			]);
			add_action('wp_ajax_get_acf_fields_values', [
				new CatalogPost(),
				'wp_ajax_get_acf_fields_values'
			]);
			add_action('wp_ajax_generated_theme_file', [
				new CatalogPost(),
				'wp_ajax_generated_theme_file'
			]);
			add_action('wp_ajax_get_post_types', [new ConfigurationPost(), 'wp_ajax_get_post_types']);
			add_action('wp_ajax_get_langs', [new ConfigurationPost(), 'wp_ajax_get_langs']);
			add_action('wp_ajax_get_langs', [new CatalogPost(), 'wp_ajax_get_langs']);
			add_action('wp_ajax_get_acf_field_groups_from_post_type', [
				new ConfigurationPost(),
				'wp_ajax_get_acf_field_groups_from_post_type'
			]);
			add_action('wp_ajax_get_acf_fields_from_group', [
				new ConfigurationPost(),
				'wp_ajax_get_acf_fields_from_group'
			]);
			add_action('wp_ajax_get_acf_repeater_fields_from_group', [
				new ConfigurationPost(),
				'wp_ajax_get_acf_repeater_fields_from_group'
			]);
			add_action('wp_ajax_get_acf_sub_fields_from_field', [
				new ConfigurationPost(),
				'wp_ajax_get_acf_sub_fields_from_field'
			]);
			add_action('wp_ajax_start_import', [
				new ConfigurationPost(),
				'wp_ajax_start_import'
			]);
			
			add_action('wp_ajax_get_logs_file', [
				new Logs(),
				'wp_ajax_get_logs_file'
			]);
			
			add_action('wp_ajax_get_translations', [
				new Tools(),
				'wp_ajax_get_translations'
			]);
		}
		
		public function add_filters() {
			add_filter('plugin_action_links_infocob-crm-products/infocob-crm-products.php', [
				new Hooks(),
				'settings_links'
			]);
			add_filter('post_row_actions', [new Hooks(), 'rd_duplicate_post_link'], 10, 2);
			add_filter('script_loader_tag', [new Hooks(), 'script_loader_tag'], 10, 3);
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			if ($screen->id === "toplevel_page_infocob-crm-products") {
				/*
				 * Styles
				 */
				wp_register_style('infocob_crm_products_main_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/main.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_main_css');
			}
		}
		
		public function render() {
			$structure = get_option('permalink_structure');
			if ($structure === "/%postname%/") {
				$wp_api_url = get_site_url() . "/wp-json/";
			} else {
				$wp_api_url = get_site_url() . "/?rest_route=/";
			}
			
			Tools::include("pages/index.php", [
				"wp_api_url" => $wp_api_url
			]);
		}
		
		public static function onUpdate() {
			$saved_version = get_option("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION");
			if (!empty($saved_version)) {
				// Upgrade system
				for ($i = ($saved_version + 1); $i <= INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION; $i++) {
					$upgrade_class = "\Infocob\CRM\Products\Admin\Classes\Upgrades\Versions\Upgrade_version_" . $i;
					if (class_exists($upgrade_class)) {
						new $upgrade_class();
					}
				}
			}
			
			update_option("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION", INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION);
		}
		
		public static function onActivate() {
			$saved_version = get_option("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION");
			if (empty($saved_version)) {
				update_option("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION", INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION);
			}
		}
		
		public static function onDeactivate() {
			// Do nothing
		}
		
		public static function onUninstall() {
			$wp_query_infocob_crm_products = new WP_Query([
				'post_type'      => 'icp-configuration',
				'posts_per_page' => -1,
			]);
			
			// The Loop
			if ($wp_query_infocob_crm_products->have_posts()) {
				while ($wp_query_infocob_crm_products->have_posts()) {
					$wp_query_infocob_crm_products->the_post();
					
					// Delete post
					wp_delete_post(get_the_ID(), true);
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
			
			// Delete infocob crm products options
			delete_option('infocob-crm-products-settings');
			
			// Unregister all infocob crm products custom post type
			unregister_post_type('icp-configuration');
			
			
			delete_option("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION");
		}
		
	}
