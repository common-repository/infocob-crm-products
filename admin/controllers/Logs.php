<?php
	
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	// don't load directly
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Logs extends Controller {
		
		public function __construct() {
		
		}
		
		public function render() {
			$logs = Logger::getLogs();
			
			Tools::include('pages/logs.php', [
				"logs" => $logs
			]);
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			if($screen->id === "infocob-crm-products_page_infocob-crm-products-logs") {
				/*
				 * Datatables
				 */
				wp_register_script('infocob_crm_products_datatables_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/datatables.net/js/jquery.dataTables.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_datatables_js');
				
				//wp_register_script('infocob_crm_products_datatables_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/datatables.net-dt/js/dataTables.dataTables.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				//wp_enqueue_script('infocob_crm_products_datatables_js');
				
				/*
				 * File main.js
				 */
				wp_register_script('infocob_crm_products_logs_main_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/logs/main.js', [
					'jquery',
					'wp-i18n',
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_logs_main_js');
				wp_set_script_translations('infocob_crm_products_logs_main_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * Styles
				 */
				wp_register_style('infocob_crm_products_datatables_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/datatables.net-dt/css/jquery.dataTables.min.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_datatables_css');
				
				wp_register_style('infocob_crm_products_logs_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/logs.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_logs_css');
			}
		}
		
		public function wp_ajax_get_logs_file() {
			$level = sanitize_text_field($_GET["level"] ?? "infos");
			$filename = sanitize_text_field($_GET["filename"] ?? "");
			
			$logs = [];
			$base_path = Logger::getLogsFolder();
			$path = $base_path . "/logs/imports/" . $level . "/" . $filename . ".log";
			if(!empty($filename) && file_exists($path)) {
				$handle = fopen($path, "r");
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						$logs[] = $line;
					}
					
					fclose($handle);
				}
			}
			
			if(check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($logs);
			} else {
				wp_send_json_error(_x("Unable to retrieve data", "wp_ajax_get_logs_file", "infocob-crm-products"));
			}
		}
		
	}
