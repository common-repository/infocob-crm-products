<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\ConfigurationPost;
	use PDO;
	use WP_Post;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsManager {
		
		private ?\WP_Post  $post;
		private ?InfocobDB $db;
		private ?PDO       $wp_db;
		
		private array $_inSQL = [];
		
		private array  $config_post = [];
		private string $post_status;
		private string $post_status_update;
		private string $post_deleted_status;
		private string $post_deleted_status_update;
		private string $post_author;
		private string $post_author_update;
		private array  $post_title;
		private string $post_title_update;
		
		private string $post_local_photo_name;
		
		private array $wp_products = [];
		
		private array $posts_created = [];
		private array $posts_updated = [];
		
		/**
		 * @param WP_Post $post
		 * @param array   $wp_products
		 */
		public function __construct(\WP_Post $post, array $wp_products) {
			$this->post = $post;
			$this->wp_products = $wp_products;
			$this->db = InfocobDB::getInstance();
			$this->wp_db = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASSWORD);
			
			$post_meta_base64 = get_post_meta($this->post->ID, "post-meta", true);
			$post_meta = Tools::decodeConfig($post_meta_base64);
			if (!empty($post_meta["post_meta"])) {
				$this->config_post = $post_meta["post_meta"];
			}
			
			$post_yoast_base64 = get_post_meta($this->post->ID, "post-yoast", true);
			if (!empty($post_yoast_base64)) {
				$post_yoast = Tools::decodeConfig($post_yoast_base64);
			}
			
			if (!empty($post_yoast)) {
				// Combine post_meta wordpress and yoast if provided
				$this->config_post = array_merge($this->config_post, ($post_yoast["post_meta"] ?? []));
			}
			
			$post_woocommerce_base64 = get_post_meta($this->post->ID, "post-woocommerce", true);
			if (!empty($post_woocommerce_base64)) {
				$post_woocommerce = Tools::decodeConfig($post_woocommerce_base64);
			}
			
			if (!empty($post_woocommerce)) {
				// Combine post_meta wordpress and woocommerce if provided
				$this->config_post = array_merge($this->config_post, ($post_woocommerce["post_meta"] ?? []));
			}
			
			$post_status = get_post_meta($this->post->ID, "post-status", true);
			$this->post_status = $post_status;
			
			$post_status_update = get_post_meta($this->post->ID, "post-status-update", true);
			$this->post_status_update = $post_status_update;
			
			$post_deleted_status = get_post_meta($this->post->ID, "post-deleted-status", true);
			$this->post_deleted_status = $post_deleted_status;
			
			$post_deleted_status_update = get_post_meta($this->post->ID, "post-deleted-status-update", true);
			$this->post_deleted_status_update = $post_deleted_status_update;
			
			$post_author = get_post_meta($this->post->ID, "post-author", true);
			$this->post_author = $post_author;
			
			$post_author_update = get_post_meta($this->post->ID, "post-author-update", true);
			$this->post_author_update = $post_author_update;
			
			$post_title_base64 = get_post_meta($this->post->ID, "post-title", true);
			if (!empty($post_title_base64)) {
				$this->post_title = Tools::decodeConfig($post_title_base64);
			}
			
			$post_title_update = get_post_meta($this->post->ID, "post-title-update", true);
			$this->post_title_update = $post_title_update;
			
			$this->post_local_photo_name = get_post_meta($this->post->ID, "files-local-photos-name", true);
		}
		
		public function update() {
			$this->posts_created = [];
			$this->posts_updated = [];
			
			// Duplicate products by langs
			$wp_products_with_langs = [];
			foreach ($this->wp_products as $post_type => $wp_product) {
				if (function_exists("pll_languages_list") && Polylang::isPostTypeMultilanguages($post_type)) {
					$languages = pll_languages_list([
						'hide_empty' => false,
						"fields"     => "slug"
					]);
				} else {
					$languages = [substr(get_locale(), 0, 2)];
				}
				
				foreach ($languages as $lang) {
					foreach ($wp_product as $product) {
						if ($product instanceof ProduitFiche || $product instanceof ProduitModeleFiche || $product instanceof TypeInventaireProduit) {
							$new_product = clone($product); // To avoid reference
							$new_product->setLang($lang);
							$wp_products_with_langs[$post_type][$lang][$product->getID()] = $new_product;
						}
					}
				}
			}
			
			$this->wp_products = $wp_products_with_langs;
			
			// Insert/update posts
			foreach ($this->wp_products as $post_type => $wp_product) {
				$this->reActivateProductsWordpress($post_type);
				$this->crossProductsWordpress($post_type);
				
				$posts_translations = [];
				foreach ($wp_product as $lang => $products) {
					foreach ($products as $code => $product) {
						if ($product instanceof ProduitFiche || $product instanceof ProduitModeleFiche || $product instanceof TypeInventaireProduit) {
							$this->updateProductsWordpress($product, $post_type);
							$posts_translations[$product->getID()][$lang] = $product->getWpId();
						}
					}
				}
				
				if (function_exists("pll_save_post_translations")) {
					foreach ($posts_translations as $code => $translation) {
						pll_save_post_translations($translation); // Define translation relation between posts
					}
				}
			}
			
			$post_types = ConfigurationPost::getUsedPostTypes($this->post->ID);
			foreach ($post_types as $post_type) {
				$this->disableWordpressProducts($post_type);
			}
			$this->updateDisabledPostTypes($post_types);
			
			do_action('icp_import_products_updated', $this->wp_products, $this->post->ID ?? false);
			
			Logger::infoImport("Products created : " . count($this->posts_created), $this->posts_created);
			Logger::infoImport("Products updated : " . count($this->posts_updated), $this->posts_updated);
		}
		
		public function get() {
			return $this->wp_products;
		}
		
		/**
		 * (re)activate products that will be imported
		 * case where old has been reactivated
		 *
		 * @param string $post_type
		 *
		 * @return void
		 */
		public function reActivateProductsWordpress(string $post_type) {
			global $wpdb;
			
			$sql = "UPDATE `" . $wpdb->prefix . "postmeta` pm
					JOIN `" . $wpdb->prefix . "postmeta` pm2 ON pm2.`post_id` = pm.`post_id`
					JOIN `" . $wpdb->prefix . "postmeta` pm3 ON pm3.`post_id` = pm.`post_id`
					SET pm.`meta_value` = 0
					WHERE pm.`meta_key` = :p_supp_meta_key
					AND pm2.`meta_key` = :p_code_meta_key
					AND pm2.`meta_value` IN (";
			
			if (Polylang::isPostTypeMultilanguages($post_type)) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			} else {
				$languages = [substr(get_locale(), 0, 2)];
			}
			
			$args = $this->inSQL($post_type)["args"];
			$args[":p_supp_meta_key"] = ProductMeta::P_SUPP_META_KEY;
			$args[":p_code_meta_key"] = ProductMeta::P_CODE_META_KEY;
			$args[":p_lang_meta_key"] = ProductMeta::P_LANG_META_KEY;
			
			$inSQL = $this->inSQL($post_type)["sql"];
			$inSQL .= ")";
			$inSQL .= " AND pm3.`meta_key` = :p_lang_meta_key ";
			$inSQL .= " AND (";
			foreach ($languages as $index => $language) {
				$key_arg_lang = ":icp_meta_lang_" . $index;
				$args[$key_arg_lang] = $language;
				
				if ($index > 0) {
					$inSQL .= " OR pm3.`meta_value` = " . $key_arg_lang . " ";
				} else {
					$inSQL .= " pm3.`meta_value` = " . $key_arg_lang . " ";
				}
			}
			$inSQL .= ")";
			
			$sql .= $inSQL;
			$req = $this->wp_db->prepare($sql);
			$req->execute($args);
		}
		
		/**
		 * @param string $post_type
		 */
		public function crossProductsWordpress(string $post_type) {
			global $wpdb;
			
			$inSQL = "SELECT pm.`post_id`, pm.`meta_value`, pm2.`meta_value` as lang, pj.`post_title`,
                    COALESCE((
                        SELECT sp.`meta_value`
                        FROM `" . $wpdb->prefix . "postmeta` sp
                        WHERE sp.`post_id` = pm.`post_id`
                            AND sp.`meta_key` = :p_supp_meta_key
                        LIMIT 0, 1
                    ), 0) as is_supprime,
                    COALESCE((
                        SELECT pt.`meta_value`
                        FROM `" . $wpdb->prefix . "postmeta` pt
                        WHERE pt.`post_id` = pm.`post_id`
                            AND pt.`meta_key` = '_infocob_p_table'
                        LIMIT 0, 1
                    ), 'produitfiche') as infocob_table
                    FROM `" . $wpdb->prefix . "postmeta` pm
                    JOIN `" . $wpdb->prefix . "posts` pj ON pj.`ID` = pm.`post_id`
                    JOIN `" . $wpdb->prefix . "postmeta` pm2 ON pm2.`post_id` = pj.`ID`
                    WHERE pj.`post_type` = :icp_post_type
                        AND pm.`meta_key` = :p_code_meta_key
                        AND pm.`meta_value` IN (";
			
			if (Polylang::isPostTypeMultilanguages($post_type)) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			} else {
				$languages = [substr(get_locale(), 0, 2)];
			}
			
			$args = $this->inSQL($post_type)["args"];
			$args[":p_supp_meta_key"] = ProductMeta::P_SUPP_META_KEY;
			$args[":p_code_meta_key"] = ProductMeta::P_CODE_META_KEY;
			$args[":p_lang_meta_key"] = ProductMeta::P_LANG_META_KEY;
			$args[":icp_post_type"] = $post_type;
			
			$inSQL .= $this->inSQL($post_type)["sql"];
			$inSQL .= ")";
			$inSQL .= " AND pm2.`meta_key` = :p_lang_meta_key ";
			$inSQL .= " AND (";
			foreach ($languages as $index => $language) {
				$key_arg_lang = ":icp_meta_lang_" . $index;
				$args[$key_arg_lang] = $language;
				
				if ($index > 0) {
					$inSQL .= " OR pm2.`meta_value` = " . $key_arg_lang . " ";
				} else {
					$inSQL .= " pm2.`meta_value` = " . $key_arg_lang . " ";
				}
			}
			$inSQL .= ")";
			
			$req = $this->wp_db->prepare($inSQL);
			$req->execute($args);
			while ($res = $req->fetch(\PDO::FETCH_ASSOC)) {
				if (!empty($this->wp_products[$post_type][$res["lang"]][$res["meta_value"]])) {
					$this->wp_products[$post_type][$res["lang"]][$res["meta_value"]]->setWpId($res["post_id"] ?? "");
					$this->wp_products[$post_type][$res["lang"]][$res["meta_value"]]->setWpTitle($res["post_title"] ?? "");
					$this->wp_products[$post_type][$res["lang"]][$res["meta_value"]]->setWpSupp(boolval($res["is_supprime"] ?? 0));
					$this->wp_products[$post_type][$res["lang"]][$res["meta_value"]]->setLang($res["lang"] ?? "fr");
					$this->wp_products[$post_type][$res["lang"]][$res["meta_value"]]->setWpTable($res["infocob_table"] ?? "");
				}
			}
		}
		
		/**
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 * @param string                                                $post_type
		 */
		public function updateProductsWordpress($product, string $post_type) {
			$post_status = (empty($product->getWpSupp())) ? $this->getPostStatus() : $this->getPostDeletedStatus();
			$post_title = $this->post_title[$product->getLang()] ?? "";
			
			if (!$product->getWpId() && !empty($post_type)) {
				$meta_input = [
					ProductMeta::P_CODE_META_KEY => $product->getID(),
					ProductMeta::P_LANG_META_KEY => $product->getLang(),
					ProductMeta::P_TYPE_META_KEY => $product->getWpTable(),
					ProductMeta::P_SUPP_META_KEY => 0
				];
				$meta_input = array_merge($meta_input, $this->getConfigPostMeta($product, $post_type));
				$meta_input[ProductMeta::P_LOCAL_PHOTO_NAME] = Tools::setFieldInfocobFromString($this->post_local_photo_name, $product);
				$meta_input[ProductMeta::P_ID_IMPORT_META_KEY] = $this->post->ID;
				
				/*
				 * Product insertion
				 */
				$post_config = [
					"post_author"    => $this->post_author,
					"post_title"     => Tools::setFieldInfocobFromString($post_title, $product),
					"post_status"    => $post_status,
					"post_type"      => $post_type,
					"comment_status" => 'closed',
					"ping_status"    => 'closed',
					"meta_input"     => $meta_input,
				];
				$post_config = array_merge($post_config, $this->getConfigPost($product, $post_type));
				
				$post_id = wp_insert_post($post_config, true);
				
				if (is_int($post_id) && !($post_id instanceof \WP_Error)) {
					$product->setWpId($post_id);
					
					if ($product->getWpCategories()) {
						foreach ($product->getWpCategories() as $taxonomy => $categories) {
							wp_set_post_terms($product->getWpId(), $categories, $taxonomy);
						}
					}
					
					if (function_exists('pll_set_post_language')) {
						pll_set_post_language($post_id, $product->getLang()); // Define post language
					}
					
					if (function_exists("update_field")) {
						$productsACF = new ProductsACF($this->post, $product);
						$productsACF->insert();
					}
					
					if (function_exists("update_field")) {
						$productsInventory = new ProductsInventory($this->post, $product);
						$productsInventory->insert();
					}
					
					$this->posts_created[] = $product->getWpId();
					
					do_action('icp_import_product', false, $post_id, $product->getID());
					
					Logger::infoImport("Insert product success", [
						"p_code"     => $product->getID(),
						"categories" => $product->getWpCategories(),
						"lang"       => $product->getLang(),
						"type"       => $product->getWpTable(),
						"post"       => $post_config
					]);
				} else {
					Logger::errorImport("Insert product failed", [
						"p_code"     => $product->getID(),
						"categories" => $product->getWpCategories(),
						"lang"       => $product->getLang(),
						"type"       => $product->getWpTable(),
						"post"       => array_merge($post_config, [
							"wp_error" => [
								"codes"    => $post_id->get_error_codes(),
								"messages" => $post_id->get_error_messages(),
								"data"     => $post_id->get_all_error_data(),
							]
						])
					]);
				}
				
			} else {
				$post_id = $product->getWpId();
				
				/*
				 * Update post
				 */
				
				$update_product["ID"] = $post_id;
				if (($this->post_status_update && !$product->getWpSupp()) || ($this->post_deleted_status_update && $product->getWpSupp())) {
					$update_product["post_status"] = $post_status;
				}
				
				if ($this->post_title_update) {
					$update_product["post_title"] = Tools::setFieldInfocobFromString($post_title, $product);
				}
				
				if ($this->post_author_update) {
					$update_product["post_author"] = $this->post_author;
				}
				
				$update_product = array_merge($update_product, $this->getConfigPost($product, $post_type));
				$update_result = wp_update_post($update_product, true);
				
				/*
				 * Update post_meta
				 */
				$post_meta = [];
				if ($product->getWpSupp()) {
					$post_meta[ProductMeta::P_SUPP_META_KEY] = 1;
					update_post_meta($product->getWpId(), ProductMeta::P_SUPP_META_KEY, 1);
				} else {
					$post_meta[ProductMeta::P_LANG_META_KEY] = $product->getLang();
					$post_meta[ProductMeta::P_TYPE_META_KEY] = $product->getWpTable();
					$post_meta[ProductMeta::P_SUPP_META_KEY] = 0;
					$post_meta[ProductMeta::P_LOCAL_PHOTO_NAME] = Tools::setFieldInfocobFromString($this->post_local_photo_name, $product);
					$post_meta[ProductMeta::P_ID_IMPORT_META_KEY] = $this->post->ID;
					foreach ($this->getConfigPostMeta($product, $post_type, true) as $key => $value) {
						update_post_meta($product->getWpId(), $key, $value);
						$post_meta[$key] = $value;
					}
				}
				
				if ($product->getWpCategories()) {
					foreach ($product->getWpCategories() as $taxonomy => $categories) {
						wp_set_post_terms($product->getWpId(), $categories, $taxonomy);
					}
				}
				
				if (function_exists('pll_set_post_language')) {
					pll_set_post_language($post_id, $product->getLang()); // Define post language
				}
				
				if (function_exists("update_field")) {
					$productsACF = new ProductsACF($this->post, $product);
					$productsACF->update();
				}
				
				if (function_exists("update_field")) {
					$productsInventory = new ProductsInventory($this->post, $product);
					$productsInventory->update();
				}
				
				$this->posts_updated[] = $product->getWpId();
				
				do_action('icp_import_product', true, $post_id, $product->getID());
				
				$post_log = array_merge($update_product, $post_meta);
				if ($update_result instanceof \WP_Error) {
					$post_log = array_merge($post_log, [
						"wp_error" => [
							"codes"    => $update_result->get_error_codes(),
							"messages" => $update_result->get_error_messages(),
							"data"     => $update_result->get_all_error_data(),
						]
					]);
					
					Logger::infoImport("Update product failed", [
						"p_code"     => $product->getID(),
						"categories" => $product->getWpCategories(),
						"lang"       => $product->getLang(),
						"type"       => $product->getWpTable(),
						"post"       => $post_log,
					]);
					
				} else {
					Logger::infoImport("Update product success", [
						"p_code"     => $product->getID(),
						"categories" => $product->getWpCategories(),
						"lang"       => $product->getLang(),
						"type"       => $product->getWpTable(),
						"post"       => array_merge($update_product, $post_meta),
					]);
				}
			}
		}
		
		/**
		 * @param string $post_type
		 */
		protected function disableWordpressProducts(string $post_type) {
			global $wpdb;
			
			$args = [];
			$inSQL = "UPDATE `" . $wpdb->prefix . "postmeta` pm
                    LEFT JOIN `" . $wpdb->prefix . "postmeta` p2
                        ON p2.`post_id` = pm.`post_id` AND p2.`meta_key` = :p_code_meta_key
                    JOIN `" . $wpdb->prefix . "posts` p ON p.`ID` = pm.`post_id`
                    SET pm.`meta_value` = 1
                    WHERE p.`post_type` = :icp_post_type
                        AND pm.`meta_key` = :p_supp_meta_key
            ";
			
			$inSQLParts = $this->inSQL($post_type);
			if (!empty(trim($inSQLParts["sql"]))) {
				$inSQL .= " AND p2.`meta_value` NOT IN (";
				$args = $inSQLParts["args"];
				$inSQL .= $inSQLParts["sql"];
				$inSQL .= ")";
			}
			
			$args[":p_supp_meta_key"] = ProductMeta::P_SUPP_META_KEY;
			$args[":p_code_meta_key"] = ProductMeta::P_CODE_META_KEY;
			$args[":icp_post_type"] = $post_type;
			
			$req = $this->wp_db->prepare($inSQL);
			$req->execute($args);
			
			Logger::infoImport("Products disabled : " . $req->rowCount());
		}
		
		/**
		 * @param string $post_type
		 *
		 * @return mixed
		 */
		protected function inSQL(string $post_type) {
			$this->_inSQL["sql"] = "";
			$this->_inSQL["args"] = [];
			$c = 0;
			
			if (Polylang::isPostTypeMultilanguages($post_type) && function_exists("pll_languages_list")) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			} else {
				$languages = [substr(get_locale(), 0, 2)];
			}
			
			foreach ($languages as $lang) {
				if (isset($this->wp_products[$post_type][$lang])) {
					foreach ($this->wp_products[$post_type][$lang] as $product) {
						if ($c) {
							$this->_inSQL["sql"] .= ", ";
						}
						
						$this->_inSQL["sql"] .= ":id_" . $c . "_product";
						$this->_inSQL["args"][":id_" . $c . "_product"] = $product->getID();
						
						$c++;
					}
				}
			}
			
			return $this->_inSQL;
		}
		
		/**
		 * @param array $post_types
		 */
		private function updateDisabledPostTypes(array $post_types) {
			global $wpdb;
			
			if (!empty($post_types)) {
				$inSQL = "";
				foreach ($post_types as $index => $post_type) {
					$inSQL = ":post_type_" . $index . ",";
					$args[":post_type_" . $index] = $post_type;
				}
				$inSQL = trim($inSQL, ",");
				
				$sql = "SELECT p.`ID`
					FROM `" . $wpdb->prefix . "postmeta` pm
					JOIN `" . $wpdb->prefix . "posts` p ON p.`ID` = pm.`post_id`
					WHERE p.`post_type` IN(" . $inSQL . ")
					AND pm.`meta_key` = :p_supp_meta_key
					AND pm.`meta_value` = 1
				";
				
				$args[":p_supp_meta_key"] = ProductMeta::P_SUPP_META_KEY;
				
				$req = $this->wp_db->prepare($sql);
				$req->execute($args);
				while ($result = $req->fetch(\PDO::FETCH_ASSOC)) {
					if (!empty($result["ID"])) {
						$update_product = [
							"ID"          => $result["ID"],
							"post_status" => $this->getPostDeletedStatus(),
						];
						wp_update_post($update_product);
						
						do_action('icp_product_disabled', $result["ID"] ?? false);
					}
				}
			}
		}
		
		/**
		 * @return WP_Post|null
		 */
		public function getPost(): ?WP_Post {
			return $this->post;
		}
		
		/**
		 * @return InfocobDB|null
		 */
		public function getDb(): ?InfocobDB {
			return $this->db;
		}
		
		/**
		 * @return PDO|null
		 */
		public function getWpDb(): ?PDO {
			return $this->wp_db;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getPostStatus() {
			return $this->post_status;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getPostDeletedStatus() {
			return $this->post_deleted_status;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getPostAuthor() {
			return $this->post_author;
		}
		
		/**
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 *
		 * @return array
		 */
		public function getConfigPostMeta($product, $post_type, $update = null) {
			$lang = $product->getLang();
			
			$meta_inputs = [];
			foreach ($this->config_post as $config) {
				$config_post_type = $config["post_type"] ?? "";
				$config_update = $config["update"] ?? false;
				$meta_key = $config["meta_key"] ?? "";
				$meta_value = $config["meta_value"] ?? "";
				$langs = !empty($config["langs"]) ? $config["langs"] : [substr(get_locale(), 0, 2)];
				
				if (!str_starts_with($meta_key, ".") && $post_type === $config_post_type && ($update === null || $config_update === $update) && in_array($lang, $langs) && !empty($meta_key)) {
					// Security check for reserved keywords
					if (!in_array(strtolower($meta_key), [
						ProductMeta::P_CODE_META_KEY,
						ProductMeta::P_SUPP_META_KEY,
						ProductMeta::P_LANG_META_KEY
					])) {
						$meta_value = Tools::setFieldInfocobFromString($meta_value, $product);
						$meta_inputs[$meta_key] = $meta_value;
					}
				}
			}
			
			return $meta_inputs;
		}
		
		/**
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 *
		 * @return array
		 */
		public function getConfigPost($product, $post_type, $update = null) {
			$lang = $product->getLang();
			
			$config_post = [];
			foreach ($this->config_post as $config) {
				$config_post_type = $config["post_type"] ?? "";
				$config_update = $config["update"] ?? false;
				$meta_key = $config["meta_key"] ?? "";
				$meta_value = $config["meta_value"] ?? "";
				$langs = !empty($config["langs"]) ? $config["langs"] : [substr(get_locale(), 0, 2)];
				
				if (str_starts_with($meta_key, ".") && $post_type === $config_post_type && ($update === null || $config_update === $update) && in_array($lang, $langs)) {
					$meta_input = explode(".", $meta_key, 2);
					if (count($meta_input) > 1) {
						$key = $meta_input[1] ?? "";
						// Security check for reserved keywords
						if (!in_array(strtolower($key), [
							"ID", "post_status", "post_type", "meta_input", "post_title", "post_author"
						])) {
							$value = Tools::setFieldInfocobFromString($meta_value, $product);
							$config_post[$key] = $value;
						}
					}
				}
			}
			
			return $config_post;
		}
	}
