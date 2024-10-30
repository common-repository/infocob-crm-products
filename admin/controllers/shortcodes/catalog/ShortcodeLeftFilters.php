<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Catalog;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\LeftFilters;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ShortcodeLeftFilters {
		
		/**
		 * @param $atts
		 * @param $content
		 * @param $tag
		 *
		 * @return string
		 */
		public static function init($atts = [], $content = null, $tag = '') {
			$shortcode_output = "";
			
			// normalize attribute keys, lowercase
			$atts = array_change_key_case((array)$atts, CASE_LOWER);
			// override default attributes with user attributes
			$infocob_atts = shortcode_atts([
				'id' => ''
			], $atts, $tag);
			
			$post_id = !empty($infocob_atts['id']) ? sanitize_text_field($infocob_atts['id']) : false;
			
			if ($post_id) {
				$override_styles = (bool)get_post_meta($post_id, "general-override-styles", true);
				static::enqueue_scripts($override_styles);
				
				$catalog = new Catalog($post_id);
				$catalog->loadLeftFilters();
				$shortcode_output = $catalog->get();
			}
			
			return $shortcode_output;
		}
		
		private static function enqueue_scripts($override_styles = false) {
			/*
			 * Scripts
			 */
			wp_enqueue_script('infocob_crm_products_public_es6_promise_js');
			wp_enqueue_script('infocob_crm_products_public_popper_js');
			wp_enqueue_script('infocob_crm_products_public_tippy_js');
			wp_enqueue_script('infocob_crm_products_public_flatpickr_js');
			wp_enqueue_script('infocob_crm_products_public_flatpickr_fr_js');
			wp_enqueue_script('infocob_crm_products_public_multiple_select_js');
			
			wp_enqueue_script('infocob_crm_products_catalog_public_main_js');
			
			wp_enqueue_script('infocob_crm_products_catalog_public_jquery_ui_touch_punch_js');
			wp_set_script_translations('infocob_crm_products_catalog_public_jquery_ui_touch_punch_js', "infocob-crm-products", ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . 'languages');
			
			/*
			 * Styles
			 */
			wp_enqueue_style('infocob_crm_products_public_jquery_ui_css');
			wp_enqueue_style('infocob_crm_products_public_multiple_select_css');
			
			$range_ui_path_file = ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/public/assets/css/range-ui.css';
			if(file_exists($range_ui_path_file)) {
				wp_add_inline_style('infocob_crm_products_public_range_ui_css', file_get_contents($range_ui_path_file));
				wp_enqueue_style('infocob_crm_products_public_range_ui_css');
			}
			
			if(!$override_styles) {
				wp_enqueue_style('infocob_crm_products_public_main_css');
			}
		}
		
	}
