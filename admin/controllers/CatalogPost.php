<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Google\Exception;
	use Infocob\CRM\Products\Admin\Classes\Catalog\ThemeFiles;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CatalogPost {
		
		/*
		 * Meta-boxes
		 */
		
		public function renderMetaBoxGeneral() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? (int)filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			$general_post_types = get_post_types([
				"_builtin" => false,
				"public"   => true
			], 'objects');
			usort($general_post_types, function ($a, $b) {
				return [Tools::removeStringAccents($a->label), $a->name]
					<=>
					[Tools::removeStringAccents($b->label), $b->name];
			});
			
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			$general_override_styles = (bool)filter_var(get_post_meta($post_id, "general-override-styles", true), FILTER_VALIDATE_BOOLEAN);
			
			$general_theme_files = [];
			if (!empty($general_post_type)) {
				// @TODO date themes files
				$file_date = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
				$wp_date_format = get_option('date_format');
				$wp_time_format = get_option('time_format');
				
				$post_type_file = get_stylesheet_directory() . "/archive-" . $general_post_type . ".php";
				if (file_exists($post_type_file)) {
					$post_type_file_date = Tools::getDateModifiedFile($post_type_file);
					$post_type_file_date = $post_type_file_date["format"] ?? "";
				}
				
				$entry_file = get_stylesheet_directory() . "/archive-" . $general_post_type . ".php";
				if (file_exists($post_type_file)) {
					$entry_file_date = Tools::getDateModifiedFile($entry_file);
					$entry_file_date = $entry_file_date["format"] ?? "";
				}
				
				$general_theme_files = [
					"post_type" => [
						"archive-" . $general_post_type . ".php" => [
							"exists" => file_exists(get_stylesheet_directory() . "/archive-" . $general_post_type . ".php"),
							"date"   => $post_type_file_date ?? ""
						]
					],
					"entry"     => [
						"entry-" . $general_post_type . ".php" => [
							"exists" => file_exists(get_stylesheet_directory() . "/entry-" . $general_post_type . ".php"),
							"date"   => $entry_file_date ?? ""
						]
					]
				];
				
				$taxonomies_object = get_object_taxonomies(sanitize_text_field($general_post_type), 'objects');
				foreach ($taxonomies_object as $taxonomy) {
					if ($taxonomy->public === true && $taxonomy->_builtin === false) {
						$taxonomy_file = get_stylesheet_directory() . "/taxonomy-" . $taxonomy->name . ".php";
						if (file_exists($taxonomy_file)) {
							$taxonomy_file_date = Tools::getDateModifiedFile($taxonomy_file);
							$taxonomy_file_date = $taxonomy_file_date["format"] ?? "";
						}
						
						$general_theme_files["taxonomy"]["taxonomy-" . $taxonomy->name . ".php"] = [
							"exists" => file_exists(get_stylesheet_directory() . "/taxonomy-" . $taxonomy->name . ".php"),
							"date" => $taxonomy_file_date ?? ""
						];
					}
				}
			}
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/catalog/meta-boxes/general.php", [
					"general_post_types"      => $general_post_types,
					"general_post_type"       => $general_post_type,
					"general_theme_files"     => $general_theme_files,
					"general_override_styles" => $general_override_styles,
				]);
				
			} else {
				Tools::include("posts/catalog/meta-boxes/error.php", [
					"metabox" => esc_html_x("General", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxLeftFilters() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			
			$left_filters_shortcode = "";
			if (!empty($post_id)) {
				$left_filters_shortcode = "[infocob_crm_products_catalog_left_filters id='" . $post_id . "']";
			}
			
			$left_filters_enable = (bool)filter_var(get_post_meta($post_id, "left-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			
			$left_filters = get_post_meta($post_id, "left-filters", true);
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list") && (($general_post_type === false) || ($general_post_type !== false && Polylang::isPostTypeMultilanguages($general_post_type)))) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/catalog/meta-boxes/left-filters.php", [
					"left_filters_shortcode" => $left_filters_shortcode,
					"left_filters_enable"    => $left_filters_enable,
					"left_filters"           => $left_filters,
					"b64JsonLanguages"       => $b64JsonLanguages,
				]);
				
			} else {
				Tools::include("posts/catalog/meta-boxes/error.php", [
					"metabox" => esc_html_x("Left filters", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxRightFilters() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			
			$right_filters_shortcode = "";
			if (!empty($post_id)) {
				$right_filters_shortcode = "[infocob_crm_products_catalog_right_filters id='" . $post_id . "']";
			}
			
			$right_filters_enable = (bool)filter_var(get_post_meta($post_id, "right-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			
			$right_filters = get_post_meta($post_id, "right-filters", true);
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list") && (($general_post_type === false) || ($general_post_type !== false && Polylang::isPostTypeMultilanguages($general_post_type)))) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/catalog/meta-boxes/right-filters.php", [
					"right_filters_shortcode" => $right_filters_shortcode,
					"right_filters_enable"    => $right_filters_enable,
					"right_filters"           => $right_filters,
					"b64JsonLanguages"        => $b64JsonLanguages,
				]);
				
			} else {
				Tools::include("posts/catalog/meta-boxes/error.php", [
					"metabox" => esc_html_x("Right filters", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxTopFilters() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			
			$top_filters_shortcode = "";
			if (!empty($post_id)) {
				$top_filters_shortcode = "[infocob_crm_products_catalog_top_filters id='" . $post_id . "']";
			}
			
			$top_filters_enable = (bool)filter_var(get_post_meta($post_id, "top-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			
			$top_filters = get_post_meta($post_id, "top-filters", true);
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list") && (($general_post_type === false) || ($general_post_type !== false && Polylang::isPostTypeMultilanguages($general_post_type)))) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/catalog/meta-boxes/top-filters.php", [
					"top_filters_shortcode" => $top_filters_shortcode,
					"top_filters_enable"    => $top_filters_enable,
					"top_filters"           => $top_filters,
					"b64JsonLanguages"      => $b64JsonLanguages,
				]);
				
			} else {
				Tools::include("posts/catalog/meta-boxes/error.php", [
					"metabox" => esc_html_x("Top filters", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxProducts() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			
			$products = get_post_meta($post_id, "products", true);
			$products_order_by = get_post_meta($post_id, "products-order-by", true);
			$products_per_page = get_post_meta($post_id, "products-per-page", true);
			if ($products_per_page === false) {
				$products_per_page = get_option('posts_per_page');
			}
			$products_per_page = (int)filter_var($products_per_page, FILTER_SANITIZE_NUMBER_INT);
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list") && (($general_post_type === false) || ($general_post_type !== false && Polylang::isPostTypeMultilanguages($general_post_type)))) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			$general_post_type = get_post_meta($post_id, "general-post-type", true);
			$post_meta_keys = [];
			if (!empty($general_post_type)) {
				$post_meta_values = Tools::getPostMetaValues($general_post_type);
				$post_meta_keys = array_keys($post_meta_values);
			}
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/catalog/meta-boxes/products.php", [
					"products"          => $products,
					"products_order_by" => $products_order_by,
					"b64JsonLanguages"  => $b64JsonLanguages,
					"languages"         => $languages,
					"products_per_page" => $products_per_page,
					"post_meta_keys"    => $post_meta_keys,
				]);
				
			} else {
				Tools::include("posts/catalog/meta-boxes/error.php", [
					"metabox" => esc_html_x("General", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		/**
		 * @param $post_id
		 */
		public function save($post_id) {
			/**
			 * Meta box General
			 */
			update_post_meta($post_id, "general-post-type", sanitize_text_field($_POST["general-post-type"] ?? ""));
			update_post_meta($post_id, "general-override-styles", filter_var(($_POST["general-override-styles"] ?? false), FILTER_VALIDATE_BOOLEAN));
			
			/**
			 * Meta box Products
			 */
			update_post_meta($post_id, "products", sanitize_text_field($_POST["products"] ?? ""));
			update_post_meta($post_id, "products-order-by", sanitize_text_field($_POST["products-order-by"] ?? ""));
			update_post_meta($post_id, "products-per-page", sanitize_text_field($_POST["products-per-page"] ?? 0));
			
			/**
			 * Meta box Left filters
			 */
			update_post_meta($post_id, "left-filters-enable", filter_var(($_POST["left-filters-enable"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "left-filters", sanitize_text_field($_POST["left-filters"] ?? ""));
			
			/**
			 * Meta box Right filters
			 */
			update_post_meta($post_id, "right-filters-enable", filter_var(($_POST["right-filters-enable"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "right-filters", sanitize_text_field($_POST["right-filters"] ?? ""));
			
			/**
			 * Meta box Top filters
			 */
			update_post_meta($post_id, "top-filters-enable", filter_var(($_POST["top-filters-enable"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "top-filters", sanitize_text_field($_POST["top-filters"] ?? ""));
		}
		
		/*
		 * AJAX
		 */
		
		public function wp_ajax_get_post_meta_values() {
			$meta_key_values = [];
			if (isset($_POST["post_type"])) {
				$post_type = sanitize_text_field($_POST["post_type"]);
				$meta_keys = array_map("sanitize_text_field", ($_POST["meta_keys"] ?? []));
				
				$meta_key_values = Tools::getPostMetaValues($post_type, $meta_keys);
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($meta_key_values);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_post_meta_values", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_acf_fields_values() {
			$acf_fields_values = [];
			if (isset($_POST["post_type"]) && isset($_POST["acf_fields"])) {
				$post_type = sanitize_text_field($_POST["post_type"]);
				$acf_fields = array_map("sanitize_text_field", $_POST["acf_fields"]);
				
				$acf_fields_values = Tools::getAcfFieldsValues($post_type, $acf_fields);
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($acf_fields_values);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_acf_fields_values", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_generated_theme_file() {
			$response = [
				"success" => false,
			];
			$catalog_id = sanitize_text_field($_POST["post_id"] ?? "");
			$file = sanitize_text_field($_POST["file"]);
			$type = sanitize_text_field($_POST["type"]);
			
			if ($catalog_id !== "") {
				$theme = new ThemeFiles($catalog_id);
				
				try {
					if ($type === "post_type") {
						$response = $theme->generateArchiveTemplate();
					} else if ($type === "entry") {
						$response = $theme->generateEntryTemplate();
					} else if ($type === "taxonomy" && !empty($file)) {
						$response = $theme->generateTaxonomyTemplate($file);
					}
				} catch (\Exception $exception) {
					$response["success"] = false;
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($response);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_generated_theme_file", "infocob-crm-products"));
			}
		}
		
	}
