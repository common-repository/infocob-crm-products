<?php
	
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	// don't load directly
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\Settings\Database;
	use Infocob\CRM\Products\Admin\Controllers\Settings\Import;
	use Infocob\CRM\Products\Admin\Controllers\Settings\Webservice;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Settings extends Controller {
		
		public function __construct() {
			new Database();
			new Webservice();
			new Import();
		}
		
		public function render() {
			Tools::include('settings/settings.php');
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			// Cuustom post type configuration
			if($screen->id === "infocob-crm-products_page_infocob-crm-products-settings") {
				/*
				 * File main.js
				 */
				wp_register_script('infocob_crm_products_settings_main_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/settings/main.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_settings_main_js');
				wp_set_script_translations('infocob_crm_products_settings_main_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
			}
		}
		
	}
