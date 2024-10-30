<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Champ;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\ChampLibre;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use MongoDB\BSON\Type;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsSorter {
		
		private ?\WP_Post $post;
		private array     $products;
		private array     $config         = [];
		private array     $sortedProducts = [];
		
		/**
		 * @param \WP_Post $post
		 * @param array    $products
		 */
		public function __construct(\WP_Post $post, array $products = []) {
			$this->post = $post;
			$this->products = $products;
			
			$configBase64 = get_post_meta($this->post->ID, "mappings", true);
			if (!empty($configBase64)) {
				$config = Tools::decodeConfig($configBase64);
				if (!empty($config) && is_array($config)) {
					$this->config = $config["rows"] ?? [];
				}
			}
		}
		
		/**
		 * @return array
		 */
		public function get() {
			foreach ($this->products as $code => $product) {
				if ($product instanceof ProduitFiche || $product instanceof ProduitModeleFiche || $product instanceof TypeInventaireProduit) {
					$this->categorize($product);
				}
			}
			
			$sortedProducts = $this->sortedProducts;
			apply_filters('icp_import_products_sorted', $sortedProducts, $this->post->ID ?? false);
			$this->sortedProducts = $sortedProducts;
			
			return $this->sortedProducts;
		}
		
		/**
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 */
		public function categorize($product) {
			$code = $product->getID();
			
			foreach ($this->config as $row_index => $row) {
				$post = $row["post"] ?? [];
				$post_type = $post["post_type"] ?? "";
				$taxonomy = $post["taxonomy"] ?? "";
				$categories = $post["categories"] ?? [];
				
				$next_condition = null;
				$sub_next_condition = null;
				$group_next_condition = null;
				
				$main_cond = true;
				
				$conds = $row["conds"] ?? [];
				foreach ($conds as $cond) {
					$sub_conds = $cond["conds"] ?? false;
					
					if ($sub_conds !== false && is_array($sub_conds) && count($sub_conds) > 0) {
						$sub_next_condition = null;
						$main_sub_cond = true;
						
						foreach ($sub_conds as $sub_cond) {
							$sub_field_name = $sub_cond["field_name"] ?? "";
							$sub_operator = $sub_cond["operator"] ?? "";
							$sub_value = $sub_cond["value"] ?? "";
							
							if (ChampLibre::isChampLibre($sub_field_name)) {
								$champLibre = new ChampLibre($product, $sub_field_name);
								if($champLibre->isChampListeDeroulante() || $champLibre->isChampListeRadio()) {
									$sub_field_value = $champLibre->getValue()["code"] ?? "";
								} else {
									$sub_field_value = $champLibre->getValue()["value"] ?? "";
								}
							} else {
								$sub_field_value = $product->getAuto($sub_field_name);
							}
							
							$operator_result = $this->testOperator($sub_field_value, $sub_operator, $sub_value);
							if ($sub_next_condition !== null) {
								$main_sub_cond = $this->testCondition($main_sub_cond, $sub_next_condition, $operator_result);
							} else {
								$main_sub_cond = $operator_result;
							}
							
							$sub_next_condition = $sub_cond["next_condition"] ?? "";
						}
						
						if($next_condition === null) {
							$main_cond = $main_sub_cond;
						} else if($next_condition !== null) {
							$main_cond = $this->testCondition($main_cond, $next_condition, $main_sub_cond);
						}
						
						$group_next_condition = $cond["next_condition"] ?? null;
						if($group_next_condition !== null) {
							$next_condition = $group_next_condition;
						}
						
					} else {
						$field_name = $cond["field_name"] ?? "";
						$operator = $cond["operator"] ?? "";
						$value = $cond["value"] ?? "";
						
						if (ChampLibre::isChampLibre($field_name)) {
							$champLibre = new ChampLibre($product, $field_name);
							if($champLibre->isChampListeDeroulante() || $champLibre->isChampListeRadio()) {
								$field_value = $champLibre->getValue()["code"] ?? "";
							} else {
								$field_value = $champLibre->getValue()["value"] ?? "";
							}
						} else {
							$field_value = $product->getAuto($field_name);
						}
						
						$operator_result = $this->testOperator($field_value, $operator, $value);
						if ($next_condition !== null) {
							$main_cond = $this->testCondition($main_cond, $next_condition, $operator_result);
						} else {
							$main_cond = $operator_result;
						}
						
						$next_condition = $cond["next_condition"] ?? "";
					}
				}
				
				if ($main_cond) {
					$product->addWpCategories([$taxonomy => $categories]);
					
					$this->sortedProducts[$post_type][$code] = $product;
				}
			}
		}
		
		/**
		 * @param mixed  $value_1
		 * @param string $operator
		 * @param mixed  $value_2
		 *
		 * @return bool
		 */
		private function testOperator($value_1, string $operator, $value_2) {
			if ($operator === "=") {
				return ($value_1 == $value_2);
			} else if ($operator === "!=") {
				return ($value_1 != $value_2);
			} else if ($operator === ">") {
				return ($value_1 > $value_2);
			} else if ($operator === "<") {
				return ($value_1 < $value_2);
			} else if ($operator === ">=") {
				return ($value_1 >= $value_2);
			} else if ($operator === "<=") {
				return ($value_1 <= $value_2);
			} else if ($operator === "regex") {
				return (preg_match("/" . $value_2 . "/mi", $value_1) === 1);
			} else if ($operator === "not_regex") {
				return !(preg_match("/" . $value_2 . "/mi", $value_1) === 1);
			} else if ($operator === "is_null") {
				return ($value_1 === null);
			} else if ($operator === "is_not_null") {
				return ($value_1 !== null);
			} else {
				return false;
			}
		}
		
		private function testCondition($value_1, $condition, $value_2) {
			if ($condition === "and") {
				return ($value_1 && $value_2);
			} else if ($condition === "or") {
				return ($value_1 || $value_2);
			} else {
				return false;
			}
		}
		
		/**
		 * @return \WP_Post|null
		 */
		public function getPost(): ?\WP_Post {
			return $this->post;
		}
		
		/**
		 * @return array
		 */
		public function getProducts(): array {
			return $this->products;
		}
		
		/**
		 * @return array
		 */
		public function getConfig() {
			return $this->config;
		}
		
		/**
		 * @return array
		 */
		public function getSortedProducts(): array {
			return $this->sortedProducts;
		}
		
	}
