<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsACF {
		
		private ?\WP_Post $post;
		private $product;
		
		/**
		 * @param \WP_Post                        $post
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit $product
		 */
		public function __construct(\WP_Post $post, $product) {
			$this->post    = $post;
			$this->product = $product;
		}
		
		public function insert() {
			if(!empty($this->post)) {
				$product_post_type = get_post_type($this->product->getWpId());
				
				$post_acf_base64 = get_post_meta($this->post->ID, "post-acf", true);
				$posts_acf = Tools::decodeConfig($post_acf_base64);
				$posts_acf       = $posts_acf["post_acf"] ?? [];
				
				foreach($posts_acf as $post_acf) {
					$post_type  = $post_acf["post_type"] ?? "";
					$acf_field  = $post_acf["acf_field"] ?? "";
					$acf_string = $post_acf["acf_value"] ?? "";
					$langs      = $post_acf["langs"] ?? [substr(get_locale(), 0, 2)];
					
					$correct_lang = true;
					if(Polylang::isPostTypeMultilanguages($product_post_type)) {
						if(in_array($this->product->getLang(), $langs)) {
							$correct_lang = true;
						} else {
							$correct_lang = false;
						}
					}
					
					if($correct_lang && $product_post_type === $post_type) {
						$acf_value = Tools::setFieldInfocobFromString($acf_string, $this->product);
						update_field($acf_field, $acf_value, $this->product->getWpId());
					}
				}
			}
		}
		
		public function update() {
			if(!empty($this->post)) {
				$product_post_type = get_post_type($this->product->getWpId());
				
				$post_acf_base64 = get_post_meta($this->post->ID, "post-acf", true);
				$posts_acf = Tools::decodeConfig($post_acf_base64);
				$posts_acf       = $posts_acf["post_acf"] ?? [];
				
				foreach($posts_acf as $post_acf) {
					$update     = $post_acf["update"] ?? false;
					$post_type  = $post_acf["post_type"] ?? "";
					$acf_field  = $post_acf["acf_field"] ?? "";
					$acf_string = $post_acf["acf_value"] ?? "";
					$langs      = $post_acf["langs"] ?? [substr(get_locale(), 0, 2)];
					
					if($update) {
						$correct_lang = true;
						if(Polylang::isPostTypeMultilanguages($product_post_type)) {
							if(in_array($this->product->getLang(), $langs)) {
								$correct_lang = true;
							} else {
								$correct_lang = false;
							}
						}
						
						if($correct_lang && $product_post_type === $post_type) {
							$acf_value = Tools::setFieldInfocobFromString($acf_string, $this->product);
							update_field($acf_field, $acf_value, $this->product->getWpId());
						}
					}
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
