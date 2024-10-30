<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use WP_Query;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsInventory {
		
		private ?\WP_Post $post;
		private           $product;
		private           $inventories = [];
		
		/**
		 * @param \WP_Post                                              $post
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 */
		public function __construct(\WP_Post $post, $product) {
			$this->post = $post;
			$this->product = $product;
			
			$inventoriesLoader = new InventoriesLoader($post);
			$this->inventories = $inventoriesLoader->get($product->getID());
		}
		
		public function insert() {
			if (!empty($this->post) && !empty($this->inventories)) {
				$product_post_type = get_post_type($this->product->getWpId());
				
				$post_inventory_base64 = get_post_meta($this->post->ID, "post-inventory", true);
				$posts_inventory = Tools::decodeConfig($post_inventory_base64);
				$posts_inventory = $posts_inventory["post_inventory"] ?? [];
				
				$acf_rows = [];
				// Get data to be inserted
				foreach ($this->inventories as $inventory) {
					if ($inventory instanceof InventaireProduit) {
						
						foreach ($posts_inventory as $post_inventory) {
							$post_type = $post_inventory["post_type"] ?? "";
							$acf_repeater = $post_inventory["acf_repeater"] ?? "";
							$acf_fields = $post_inventory["acf_fields"] ?? [];
							$langs = $post_inventory["langs"] ?? [substr(get_locale(), 0, 2)];
							
							$correct_lang = true;
							if (Polylang::isPostTypeMultilanguages($product_post_type)) {
								if (in_array($this->product->getLang(), $langs)) {
									$correct_lang = true;
								} else {
									$correct_lang = false;
								}
							}
							
							if ($correct_lang && $product_post_type === $post_type) {
								
								$acf_row = [];
								foreach ($acf_fields as $acf_field) {
									$field_name = $acf_field["name"] ?? "";
									$field_value = $acf_field["value"] ?? "";
									
									if($field_name !== "") {
										$field_value = Tools::setFieldInfocobFromString($field_value, $inventory);
										$acf_row[$field_name] = $field_value;
									}
								}
								
								$acf_rows[$acf_repeater][] = $acf_row;
							}
						}
					}
				}
				
				foreach ($acf_rows as $acf_repeater => $rows) {
					foreach ($rows as $row) {
						add_row($acf_repeater, $row, $this->product->getWpId());
					}
				}
			}
		}
		
		public function update() {
			if (!empty($this->post)) {
				$product_post_type = get_post_type($this->product->getWpId());
				
				$post_inventory_base64 = get_post_meta($this->post->ID, "post-inventory", true);
				$posts_inventory = Tools::decodeConfig($post_inventory_base64);
				$posts_inventory = $posts_inventory["post_inventory"] ?? [];
				
				$acf_rows = [];
				// Get data to be updated
				foreach ($this->inventories as $inventory) {
					if ($inventory instanceof InventaireProduit) {
						
						foreach ($posts_inventory as $post_inventory) {
							$post_type = $post_inventory["post_type"] ?? "";
							$acf_repeater = $post_inventory["acf_repeater"] ?? "";
							$acf_fields = $post_inventory["acf_fields"] ?? [];
							$acf_update = $post_inventory["update"] ?? true;
							$langs = $post_inventory["langs"] ?? [substr(get_locale(), 0, 2)];
							
							$correct_lang = true;
							if (Polylang::isPostTypeMultilanguages($product_post_type)) {
								if (in_array($this->product->getLang(), $langs)) {
									$correct_lang = true;
								} else {
									$correct_lang = false;
								}
							}
							
							if ($correct_lang && $product_post_type === $post_type && $acf_update) {
								$this->deleteRepeaterRows($acf_repeater, $this->product->getWpId());
								
								$acf_row = [];
								foreach ($acf_fields as $acf_field) {
									$field_name = $acf_field["name"] ?? "";
									$field_value = $acf_field["value"] ?? "";
									
									if($field_name !== "") {
										$field_value = Tools::setFieldInfocobFromString($field_value, $inventory);
										$acf_row[$field_name] = $field_value;
									}
								}
								
								$acf_rows[$acf_repeater][] = $acf_row;
							}
						}
						
					}
				}
				
				foreach ($acf_rows as $acf_repeater => $rows) {
					foreach ($rows as $row) {
						add_row($acf_repeater, $row, $this->product->getWpId());
					}
				}
			}
		}
		
		/**
		 * @param string $acf_repeater_field_key
		 * @param int    $postID
		 *
		 * @return void
		 */
		private function deleteRepeaterRows(string $acf_repeater_field_key, int $postID) {
			reset_rows();
			$fieldValue = get_field($acf_repeater_field_key, $postID);
			if (is_array($fieldValue)){
				$remainingRows = count($fieldValue);
				while (have_rows($acf_repeater_field_key, $postID)) {
					the_row();
					delete_row($acf_repeater_field_key, $remainingRows--, $postID);
				}
			}
		}
		
		/**
		 * @return int|\WP_Post|null
		 */
		public function getPost() {
			return $this->post;
		}
		
		/**
		 * @return ProduitModeleFiche|ProduitFiche|TypeInventaireProduit
		 */
		public function getProduct() {
			return $this->product;
		}
	}
