<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Widgets;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	abstract class Widget {
		protected $widget_id;
		protected $title;
		protected $args = [];
		protected $control = false;
		protected $context = "normal";
		protected $priority = "core";
		
		/**
		 * @param string $widget_id
		 * @param string $title
		 */
		public function __construct(string $widget_id, string $title, $args = [], $control = false, $context = "normal", $priority = "core") {
			$this->widget_id = $widget_id;
			$this->title = $title;
			$this->args = $args;
			$this->control = $control;
			$this->context = $context;
			$this->priority = $priority;
			
			$this->init();
		}
		
		protected function init() {
			$control_callback = null;
			if($this->control) {
				$control_callback = [$this, 'control'];
			}
			
			wp_add_dashboard_widget(
				$this->widget_id, 	// Widget ID (used in the 'id' attribute for the widget).
				$this->title,   	// Title of the widget.
				[$this, 'render'], 	// Function that fills the widget with the desired content
				$control_callback,  // Function that outputs controls for the widget.
				$this->args, 		// Args (as second parameters of the callback
				$this->context,		// Context 'normal', 'side', 'column3', or 'column4'. | Default value: 'normal'
				$this->priority		// Priority 'high', 'core', 'default', or 'low' | Default value: 'core'
			);
		}
		
		public function render($arg1, $arg2) {
		
		}
		
		public function control() {
		
		}
		
		public static function wp_admin_register_scripts() {
			//get the current screen
			$screen = get_current_screen();
			
			if (in_array($screen->id, ["dashboard"]) && current_user_can('update_core')) {
				/*
				 * ES6 Promise
				 * https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.js
				 */
				wp_register_script('infocob_crm_products_es6_promise_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/libs/es6-promise.auto.js');
				wp_enqueue_script('infocob_crm_products_es6_promise_js');
				
				/*
				 * Sweetalert2
				 */
				wp_register_script('infocob_crm_products_sweetalert2_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'node_modules/sweetalert2/dist/sweetalert2.all.min.js', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_sweetalert2_js');
				
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
				
				wp_register_script('infocob_crm_products_dashboard_widgets_js', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/js/widgets.js', [
					'jquery',
					'wp-i18n'
				], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_script('infocob_crm_products_dashboard_widgets_js');
				wp_set_script_translations('infocob_crm_products_dashboard_widgets_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
				
				/*
				 * File styles
				 */
				wp_register_style('infocob_crm_products_dashboard_widgets_css', ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL . 'admin/assets/css/dashboard-widgets.css', [], INFOCOB_CRM_PRODUCTS_ASSETS_VERSION);
				wp_enqueue_style('infocob_crm_products_dashboard_widgets_css');
			}
		}
		
	}
