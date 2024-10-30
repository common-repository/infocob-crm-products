<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Filters;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\LeftFilters;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\RightFilters;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\TopFilters;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use WP_Query;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Catalog {
		protected $topFilters   = false;
		protected $leftFilters  = false;
		protected $rightFilters = false;
		protected $productsList = false;
		
		protected int $post_id;
		protected string $post_type;
		
		protected array $_get = [];
		
		public function __construct(int $post_id) {
			$this->post_id = $post_id;
			
			$this->post_type = get_post_meta($post_id, "general-post-type", true);
			if (isset($_GET["infocob-crm-products"])) {
				$_get = [];
				$_get_base64 = sanitize_text_field($_GET["infocob-crm-products"] ?? "");
				if ($_get_base64 !== false) {
					$_get_decoded = base64_decode($_get_base64);
					parse_str($_get_decoded, $_get);
				}
				
				$this->_get = $_get;
			}
		}
		
		public function loadProductsList() {
			$productsList = new ProductsList($this->post_id, []);
			$this->productsList = $productsList;
		}
		
		public function loadTopFilters() {
			$top_filters_enable = (bool)filter_var(get_post_meta($this->post_id, "top-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			if($top_filters_enable) {
				$topFilters = new TopFilters($this->post_id);
				$topFilters->setGet($this->_get);
				$topFilters->loadFilters();
				$this->topFilters = $topFilters;
			}
		}
		
		public function loadLeftFilters() {
			$left_filters_enable = (bool)filter_var(get_post_meta($this->post_id, "left-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			if($left_filters_enable) {
				$leftFilters = new LeftFilters($this->post_id);
				$leftFilters->setGet($this->_get);
				$leftFilters->loadFilters();
				$this->leftFilters = $leftFilters;
			}
		}
		
		public function loadRightFilters() {
			$right_filters_enable = (bool)filter_var(get_post_meta($this->post_id, "right-filters-enable", true), FILTER_VALIDATE_BOOLEAN);
			if($right_filters_enable) {
				$rightFilters = new RightFilters($this->post_id);
				$rightFilters->setGet($this->_get);
				$rightFilters->loadFilters();
				$this->rightFilters = $rightFilters;
			}
		}
		
		public function getDefaultFilters() {
			$filters = [];
			$left_filters_config = get_post_meta($this->post_id, "left-filters", true);
			$left_filters_config = Tools::decodeConfig($left_filters_config);
			if(!empty($left_filters_config)) {
				$filters = array_merge($filters, $left_filters_config["filters"] ?? []);
			}
			
			$right_filters_config = get_post_meta($this->post_id, "right-filters", true);
			$right_filters_config = Tools::decodeConfig($right_filters_config);
			if(!empty($right_filters_config)) {
				$filters = array_merge($filters, $right_filters_config["filters"] ?? []);
			}
			
			$top_filters_config = get_post_meta($this->post_id, "top-filters", true);
			$top_filters_config = Tools::decodeConfig($top_filters_config);
			if(!empty($top_filters_config)) {
				$filters = array_merge($filters, $top_filters_config["filters"] ?? []);
			}
			
			return $filters;
		}
		
		/**
		 * @return string
		 */
		public function get() {
			$html = "";
			
			if ($this->topFilters instanceof Filters) {
				$html .= $this->topFilters->get();
			}
			if ($this->leftFilters instanceof Filters) {
				$html .= $this->leftFilters->get();
			}
			if ($this->productsList instanceof ProductsList) {
				$html .= $this->productsList->get();
			}
			if ($this->rightFilters instanceof Filters) {
				$html .= $this->rightFilters->get();
			}
			
			return $html;
		}
		
		/**
		 * @param $config
		 *
		 * @return array
		 */
		public static function getMetaKeysFromConfigFilters($config) {
			$meta_keys = [];
			foreach (($config["filters"] ?? []) as $filter) {
				$type = $filter["type"] ?? false;
				if ($type !== false) {
					foreach (($filter["filter"] ?? []) as $meta_key => $meta_value) {
						if ($meta_key === "meta_key") {
							$meta_keys[] = $meta_value;
						}
					}
				}
			}
			
			$meta_keys = array_unique($meta_keys);
			return $meta_keys;
		}
		
		protected function getTopFilters() {
			return $this->topFilters;
		}
		
		protected function setTopFilters($topFilters) {
			if ($this->topFilters instanceof Filters) {
				$this->topFilters = $topFilters;
			}
		}
		
		protected function getLeftFilters() {
			return $this->leftFilters;
		}
		
		protected function setLeftFilters($leftFilters) {
			if ($this->leftFilters instanceof Filters) {
				$this->leftFilters = $leftFilters;
			}
		}
		
		protected function getRightFilters() {
			return $this->rightFilters;
		}
		
		protected function setRightFilters($rightFilters) {
			if ($this->rightFilters instanceof Filters) {
				$this->rightFilters = $rightFilters;
			}
		}
		
		public static function pre_get_posts(&$query) {
			
			if($query instanceof WP_Query && !is_admin() && $query->is_main_query()) {
				$query_post_type = $query->get("post_type");
				
				$catalog = false;
				
				// If taxonomy page
				if(empty($query_post_type) && !empty($query->tax_query)) {
					
					// Getting values available from taxonomy (if in taxonomy)
					$taxonomy = false;
					$term_id = false;
					$queried_object = get_queried_object();
					if(is_tax($queried_object)) {
						$taxonomy = $queried_object->taxonomy;
						$term_id = $queried_object->term_id;
					}
					
					if($taxonomy !== false && $term_id !== false) {
						$wp_taxonomy = get_taxonomy($taxonomy);
						if(!empty($wp_taxonomy)) {
							$post_types = $wp_taxonomy->object_type ?? [];
							foreach ($post_types as $post_type) {
								$catalog = Catalog::getCatalogByPostType($post_type);
							}
						}
					}
				}
				// If archive page (post_type)
				else if(!empty($query_post_type) && is_post_type_archive($query_post_type)) {
					$catalog = Catalog::getCatalogByPostType($query_post_type);
				}
				
				if ($catalog !== false) {
					$default_filters = $catalog->getDefaultFilters();
					
					$get_catalog = false;
					$get_catalog_base64 = sanitize_text_field($_GET["infocob-crm-products"] ?? "");
					if ($get_catalog_base64 !== "") {
						$get_catalog_decoded = base64_decode($get_catalog_base64);
						parse_str($get_catalog_decoded, $get_catalog);
					}
					
					$productsList = new ProductsList($catalog->post_id, $default_filters);
					
					if($get_catalog !== false) {
						$productsList->setQuery($query, $get_catalog);
					} else {
						$productsList->setDefaultQuery($query);
					}
				}
			}
		}
		
		public static function getCatalogByPostType($post_type) {
			$catalog = false;
			$wp_query_args = [
				'post_type' => 'icp-catalog',
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'meta_query' => [
					'relation' => 'AND',
					'general-post-type' => [
						'key' => 'general-post-type',
						'value' => $post_type,
						'compare' => '='
					]
				],
			];
			
			$wp_query_catalog = new WP_Query($wp_query_args);
			
			// The Loop
			if($wp_query_catalog->have_posts()) {
				while($wp_query_catalog->have_posts()) {
					$wp_query_catalog->the_post();
					
					$catalog = new Catalog(get_the_ID());
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
			
			return $catalog;
		}
		
	}
