<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\CloudFichier;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\FamilleTypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\ChampLibre;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Controllers\ImportProducts;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\Shortcode;
	use WP_Query;
	use WP_Term;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Tools {
		
		/**
		 * @param $view
		 * @param $vars
		 * @param $scope
		 *
		 * @return void
		 * @throws \Exception
		 */
		public static function include($view, $vars = [], $scope = "admin") {
			if (file_exists(ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/' . $scope . '/includes/' . $view)) {
				extract($vars);
				include ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/' . $scope . '/includes/' . $view;
				
			} else {
				throw new \Exception("The file " . ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/' . $scope . '/includes/' . $view . " can't be found", 500);
			}
		}
		
		/**
		 * @param $view
		 * @param $vars
		 * @param $scope
		 *
		 * @return false|string
		 * @throws \Exception
		 */
		public static function get($view, $vars = [], $scope = "admin") {
			if (file_exists(ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/' . $scope . '/includes/' . $view)) {
				ob_start();
				Tools::include($view, $vars, $scope);
				$file_content = ob_get_clean();
				return $file_content;
			} else {
				throw new \Exception("The file " . ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/' . $scope . '/includes/' . $view . " can't be found", 500);
			}
		}
		
		public static function redirectIfNotAllowed($cap) {
			if(!current_user_can($cap)) {
				wp_redirect(get_dashboard_url());
				exit;
			}
		}
		
		/**
		 * @param $base64Config
		 *
		 * @return array|mixed
		 */
		public static function decodeConfig($base64Config) {
			if (is_string($base64Config)) {
				$json_config = urldecode(base64_decode($base64Config));
				if (!mb_check_encoding($json_config, 'UTF-8')) {
					$json_config = utf8_encode($json_config);
				}
				if ($json_config !== false) {
					$config = json_decode($json_config, true);
					if (!empty($config)) {
						return $config;
					}
				}
			}
			
			return [];
		}
		
		public static function encodeConfig($config) {
			$json_config = json_encode($config);
			if ($json_config !== false) {
				$config_json = base64_encode(urlencode($json_config));
				if (!empty($config_json)) {
					return $config_json;
				}
			}
			
			return "";
		}
		
		/**
		 * @param string $str
		 * @param string $charset
		 *
		 * @return string
		 */
		public static function removeStringAccents($str, $charset = 'utf-8') {
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
			
			$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
			$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // e.g. '&oelig;'
			$str = preg_replace('#&[^;]+;#', '', $str);
			
			return $str;
		}
		
		/**
		 * @param string                                                                               $string
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit|CloudFichier|InventaireProduit $product
		 *
		 * @return string
		 */
		public static function setFieldInfocobFromString($string, $product) {
			if ($product instanceof InventaireProduit) {
				$prefix_regex = "(IP|TIP|FTI)";
			} else if ($product instanceof TypeInventaireProduit) {
				$prefix_regex = "(TIP|FTI)";
			} else if ($product instanceof CloudFichier) {
				$prefix_regex = "(FC)";
			} else {
				$prefix_regex = "(P)";
			}
			
			preg_match_all("/({{\s?(" . $prefix_regex . "_\w+)\s?}})/mi", $string, $matches);
			if (!empty($matches) && !empty($matches[1]) && !empty($matches[2])) {
				$stringsToReplace = $matches[1];
				foreach ($stringsToReplace as $index => $stringToReplace) {
					$field_name = !empty($matches[2][$index]) ? $matches[2][$index] : "";
					$field_value = "";
					
					if ($product instanceof InventaireProduit) {
						$field_valid = (isset($product::$champsDefinitions[$field_name]) || isset(TypeInventaireProduit::$champsDefinitions[$field_name]) || isset(FamilleTypeInventaireProduit::$champsDefinitions[$field_name]));
					} else if ($product instanceof TypeInventaireProduit) {
						$field_valid = (isset($product::$champsDefinitions[$field_name]) || isset(FamilleTypeInventaireProduit::$champsDefinitions[$field_name]));
					} else {
						$field_valid = isset($product::$champsDefinitions[$field_name]);
					}
					
					if (ChampLibre::isChampLibre($field_name)) {
						$champLibre = new ChampLibre($product, $field_name);
						if ($champLibre->isLoaded()) {
							$champLibre_value = $champLibre->getValue();
							if($champLibre->isChampListeDeroulante() || $champLibre->isChampListeRadio()) {
								$field_value = trim($champLibre_value["name"]);
							} else if (isset($champLibre_value["value"])) {
								$field_value = trim($champLibre_value["value"]);
							}
						}
					} else if ($field_valid) {
						$field_value = trim($product->getAuto($field_name, "d/m/Y", false, [0, ".", ""], [2, ".", ""]));
					}
					
					$string = str_replace($stringToReplace, $field_value, $string);
				}
			}
			
			return $string;
		}
		
		public static function getMyIp() {
			$my_ip = false;
			$request_url = "https://api.myip.com";
			
			$my_ip = wp_remote_retrieve_body(wp_remote_get($request_url));
			
			return $my_ip;
		}
		
		public static function cleanString($string) {
			$string = str_replace([' ', '-'], ['', '_'], sanitize_text_field($string));
			return preg_replace('/[^A-Za-z0-9_]/', '', $string); // Removes special chars.
		}
		
		public static function sanitize_recursive($value) {
			if(is_numeric($value)) {
				$value = sanitize_text_field($value);
				
				if(ctype_digit($value)) {
					$value = (int)$value;
				} else {
					$value = (float)$value;
				}
			} else if(is_array($value)) {
				foreach ($value as $key => $val) {
					$value[$key] = Tools::sanitize_recursive($val);
				}
			} else {
				$value = sanitize_text_field($value);
			}
			
			return $value;
		}
		
		/**
		 * Translate text with specific text-domain
		 * @return void
		 */
		public function wp_ajax_get_translations() {
			$translated_values = [];
			if (isset($_POST["translations"])) {
				$translations = Tools::sanitize_recursive($_POST["translations"]);
				
				if (is_array($translations)) {
					foreach ($translations as $translation) {
						$text = $translation["text"] ?? "";
						$args = $translation["args"] ?? [];
						$text_domain = $translation["text-domain"] ?? "";
						
						if (!empty($args)) {
							$translated_value = $text;
							foreach ($args as $arg) {
								$translated_value = sprintf(__($translated_value, $text_domain), $arg);
							}
							$translated_values[$text] = $translated_value;
						} else {
							$translated_values[$text] = __($text, $text_domain);
						}
					}
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($translated_values);
			} else {
				wp_send_json_error(_x("Unable to retrieve data", "wp_ajax_get_translations", "infocob-crm-products"));
			}
		}
		
		
		public static function areNumeric(array $array) {
			foreach ($array as $value) {
				if (!is_numeric($value)) {
					return false;
				}
			}
			return true;
		}
		
		public static function getPostMetaValues($post_type, $meta_keys = [], $taxonomy = false, $term_ids = false) {
			$post_meta_values = [];
			$wp_query_args = [
				'post_type'      => $post_type,
				'posts_per_page' => -1,
			];
			
			if ($taxonomy !== false && $term_ids !== false) {
				$wp_query_args['tax_query'] = [
					'relation' => 'AND',
					[
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_ids
					],
				];
			}
			
			$wp_query_infocob_crm_products = new WP_Query($wp_query_args);
			
			// The Loop
			if ($wp_query_infocob_crm_products->have_posts()) {
				while ($wp_query_infocob_crm_products->have_posts()) {
					$wp_query_infocob_crm_products->the_post();
					
					if (!empty($meta_keys)) {
						foreach ($meta_keys as $meta_key) {
							$meta_value = get_post_meta(get_the_ID(), $meta_key, true);
							if ($meta_value !== "") {
								if (!isset($post_meta_values[$meta_key]) || !in_array($meta_value, $post_meta_values[$meta_key])) {
									$post_meta_values[$meta_key][] = $meta_value;
								}
							}
						}
					} else {
						$meta_values = get_post_meta(get_the_ID());
						foreach ($meta_values as $meta_key => $meta_value) {
							$post_meta_values[$meta_key][] = $meta_value;
						}
					}
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
			
			foreach ($post_meta_values as $meta_key => $values) {
				if (Tools::areNumeric($values)) {
					sort($post_meta_values[$meta_key], SORT_NUMERIC);
				} else {
					sort($post_meta_values[$meta_key]);
				}
			}
			
			return $post_meta_values;
		}
		
		public static function getAcfFieldsValues($post_type, $acf_fields = [], $taxonomy = false, $term_ids = false) {
			$acf_fields_values = [];
			if (function_exists("update_field")) {
				$wp_query_args = [
					'post_type'      => $post_type,
					'posts_per_page' => -1,
				];
				
				if ($taxonomy !== false && $term_ids !== false) {
					$wp_query_args['tax_query'] = [
						'relation' => 'AND',
						[
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => $term_ids
						],
					];
				}
				
				$wp_query_infocob_crm_products = new WP_Query($wp_query_args);
				
				// The Loop
				if ($wp_query_infocob_crm_products->have_posts()) {
					while ($wp_query_infocob_crm_products->have_posts()) {
						$wp_query_infocob_crm_products->the_post();
						
						foreach ($acf_fields as $acf_field) {
							$acf_value = get_field($acf_field, get_the_ID(), true);
							if ($acf_value !== "") {
								if (!isset($acf_fields_values[$acf_field]) || !in_array($acf_value, $acf_fields_values[$acf_field])) {
									$acf_fields_values[$acf_field][] = $acf_value;
								}
							}
						}
					}
				}
				
				foreach ($acf_fields_values as $acf_field => $values) {
					$acf_field_object = get_field_object($acf_field);
					if (!empty($acf_field_object)) {
						$acf_field_type = $acf_field_object["type"] ?? "text";
						if ($acf_field_type === "number") {
							sort($acf_fields_values[$acf_field], SORT_NUMERIC);
						} else {
							sort($acf_fields_values[$acf_field]);
						}
					}
				}
				
				/* Restore original Post Data */
				wp_reset_postdata();
			}
			
			return $acf_fields_values;
		}
		
		public static function getCategoriesList($parent, $taxonomy, $hideEmpty = false, $langs = []) {
			$args = [
				'taxonomy'   => $taxonomy,
				'hide_empty' => $hideEmpty,
				'parent'     => $parent
			];
			
			if (!empty($langs)) {
				$args["lang"] = $langs;
			}
			
			$current_level_categories = get_terms($args);
			$result = [];
			if ($current_level_categories) {
				foreach ($current_level_categories as $cat) {
					if ($cat instanceof WP_Term) {
						$cat->childs = Tools::getCategoriesList($cat->term_id, $taxonomy, $hideEmpty, $langs);
						$result[$cat->term_id] = $cat;
					}
				}
			}
			
			return $result;
		}
		
		/**
		 * Recursively get taxonomy and its children
		 *
		 */
		public static function getTaxonomyHierarchy($taxonomy, $parent = 0, $lang = "", $flat = false) {
			$taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
			$terms = get_terms([
				"taxonomy"   => $taxonomy,
				"hide_empty" => false,
				"parent"     => $parent,
				"lang"       => $lang
			]);
			
			$children = [];
			if(empty($terms->errors)) {
				foreach ($terms as $term) {
					$children[] = $term;
					
					if ($flat) {
						$children = array_merge($children, Tools::getTaxonomyHierarchy($taxonomy, $term->term_id, $lang, $flat));
					} else {
						$term->children = Tools::getTaxonomyHierarchy($taxonomy, $term->term_id, $lang, $flat);
						$children[$term->term_id] = $term;
					}
				}
			}
			
			return $children;
		}
		
		public static function getAcfFieldGroupsFromPostType($post_type) {
			$acf_field_groups = [];
			if (function_exists("get_field_objects")) {
				$acf_field_groups = [];
				$acf_field_groups_object = get_posts([
					"post_type"   => "acf-field-group",
					"numberposts" => -1
				]);
				
				foreach ($acf_field_groups_object as $acf_field_group) {
					$post_content = $acf_field_group->post_content;
					$field = unserialize($post_content);
					
					if ($field !== false) {
						$locations = $field["location"] ?? [];
						
						foreach ($locations as $location) {
							foreach ($location as $condition) {
								$operator = $condition["operator"] ?? "";
								$param = $condition["param"] ?? "";
								$value = $condition["value"] ?? "";
								
								if (strcasecmp($param, "post_type") === 0) {
									if (strcasecmp($operator, "==") === 0) {
										if (strcasecmp($post_type, $value) === 0) {
											$acf_field_groups[] = $acf_field_group;
										}
									}
								}
							}
						}
					}
				}
			}
			
			return $acf_field_groups;
		}
		
		public static function getAcfFieldsFromGroup($post_id, $types = []) {
			$acf_fields = [];
			if (function_exists("get_field_objects")) {
				$acf_fields = [];
				
				$acf_posts = get_posts([
					"post_type"   => "acf-field",
					"post_parent" => $post_id,
					"numberposts" => -1
				]);
				
				foreach ($acf_posts as $acf_post) {
					$post_content = $acf_post->post_content;
					$field = unserialize($post_content);
					
					if ($field !== false) {
						$field_title = $acf_post->post_title;
						$field_name = $acf_post->post_name;
						$type = $field["type"] ?? "";
						
						if (in_array($type, !empty($types) ? $types : [
							"text",
							"textarea",
							"number",
							"email",
							"url",
							"password",
							"select",
							"true_false"
						])) {
							$acf_fields[] = [
								"title" => $field_title,
								"name"  => $field_name
							];
						}
					}
				}
			}
			
			return $acf_fields;
		}
		
		public static function getCRONImports() {
			$crons = [];
			$wp_query_infocob_crm_products = new WP_Query([
				'post_type'      => 'icp-configuration',
				'posts_per_page' => -1,
			]);
			
			// The Loop
			if ($wp_query_infocob_crm_products->have_posts()) {
				while ($wp_query_infocob_crm_products->have_posts()) {
					$wp_query_infocob_crm_products->the_post();
					
					$post_id = get_the_ID();
					
					$cron_timestamp = wp_next_scheduled("icp_cron_import_products", [$post_id]);
					if ($cron_timestamp !== false) {
						$crons[$post_id] = $cron_timestamp;
					}
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
			
			return $crons;
		}
		
		public static function getMimeTypesFromExtensions($extensions = []) {
			$mimes_types = [];
			$extensions = array_map("strtolower", $extensions);
			
			$allowed_mime_types = get_allowed_mime_types();
			foreach ($allowed_mime_types as $allowed_ext => $mime_type) {
				$exts = explode("|", $allowed_ext);
				foreach ($exts as $ext) {
					if (in_array($ext, $extensions)) {
						$mimes_types[] = $mime_type;
					}
				}
			}
			
			$mimes_types = array_unique($mimes_types);
			
			return $mimes_types;
		}
		
		public static function getLevelParentCategory($term, $taxonomy) {
			$level = 0;
			// Start from the current term
			$parent = get_term($term, $taxonomy);
			// Climb up the hierarchy until we reach a term with parent = '0'
			while ($parent->parent != '0') {
				$level++;
				$term_id = $parent->parent;
				$parent = get_term($term_id, $taxonomy);
			}
			
			return $level;
		}
		
		public static function getDateModifiedFile($file_path) {
			$file_date = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
			$wp_date_format = get_option('date_format');
			$wp_time_format = get_option('time_format');
			
			if (file_exists($file_path)) {
				$file_date = $file_date->setTimestamp(filemtime($file_path));
				$file_date = $file_date->format($wp_date_format) . " " . $file_date->format($wp_time_format);
			}
			
			return [
				"timestamp" => filemtime($file_path),
				"format" => $file_date
			];
		}
		
		public static function copyDirectory($source, $destination) {
			if (!is_dir($destination)) {
				mkdir($destination, 0755, true);
			}
			
			$dirContent = scandir($source);
			
			foreach ($dirContent as $item) {
				if ($item !== '.' && $item !== '..') {
					$sourcePath = $source . '/' . $item;
					$destinationPath = $destination . '/' . $item;
					
					if (is_dir($sourcePath)) {
						Tools::copyDirectory($sourcePath, $destinationPath);
					} else {
						copy($sourcePath, $destinationPath);
					}
				}
			}
		}
		
		public static function deleteDirectory($directory) {
			if (!is_dir($directory)) {
				return false;
			}
			
			$dirContent = scandir($directory);
			
			foreach ($dirContent as $item) {
				if ($item !== '.' && $item !== '..') {
					$itemPath = $directory . '/' . $item;
					
					if (is_dir($itemPath)) {
						Tools::deleteDirectory($itemPath);
					} else {
						unlink($itemPath);
					}
				}
			}
			
			return rmdir($directory);
		}
		
	}
