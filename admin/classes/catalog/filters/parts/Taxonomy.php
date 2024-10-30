<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts;
	
	use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Tax;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\CheckboxFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\ListFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\RadioFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\SelectFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\SelectMultipleFilter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\Shortcodes\Catalog\Shortcode;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Taxonomy implements Filter {
		protected int   $post_id;
		protected array $filter;
		
		protected string $taxonomy   = "";
		protected array  $categories = [];
		protected string $display    = "";
		protected string $title      = "";
		protected string $unit       = "";
		protected array  $defaults   = [];
		
		protected array $_get = [];
		
		/**
		 * @param int   $post_id
		 * @param array $filter
		 * @param array $meta_values
		 */
		public function __construct(int $post_id, array $filter) {
			$this->post_id = $post_id;
			$this->filter = $filter;
			
			$current_language = \Infocob\CRM\Products\Admin\Classes\Polylang::getCurrentLanguage(get_post_type($post_id));
			
			$this->display = $filter["display"] ?? "";
			$this->title = $filter["title"][$current_language] ?? "";
			$this->unit = $filter["unit"][$current_language] ?? "";
			$this->defaults = $filter["defaults"] ?? [];
			$this->taxonomy = $filter["taxonomy"] ?? "";
			
			$this->defaults = array_filter($this->defaults, function ($default_value) {
				return $default_value !== null;
			});
			
			if (function_exists("pll_default_language") && function_exists("pll_languages_list") && function_exists("pll_get_term") && Polylang::isPostTypeMultilanguages(get_post_type($post_id))) {
				$default_language = pll_default_language();
				
				$categories = [];
				$categories_object = Tools::getTaxonomyHierarchy($this->taxonomy, 0, $current_language, true);
				
				foreach ($categories_object as $category) {
					$languages = pll_languages_list([
						'hide_empty' => false,
						"fields"     => "slug"
					]);
					
					$category->term_ids = [$category->term_id];
					foreach ($languages as $language) {
						if ($language !== $default_language) {
							$pll_category_id = pll_get_term($category->term_id, $language);
							if ($pll_category_id !== false) {
								$pll_category = get_term($pll_category_id, $this->taxonomy);
								$category->term_ids[] = $pll_category->term_id;
							}
						}
					}
					
					$categories[] = $category;
				}
			} else {
				$categories = Tools::getTaxonomyHierarchy($this->taxonomy, 0, "", true);
			}
			
			foreach ($categories as &$category) {
				$level = Tools::getLevelParentCategory($category, $category->taxonomy);
				$category->level = $level;
			}
			
			$this->categories = $categories;
		}
		
		/**
		 * @param array $get
		 */
		public function setGet(array $get) {
			$this->_get = $get;
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->taxonomy === "") {
				return "";
			}
			
			switch ($this->display) {
				case "list":
					$listFilter = new ListFilter($this->post_id, $this->taxonomy, $this->categories, $this->display, $this->title, $this->unit, $this->defaults);
					return $listFilter->get();
				case "select":
					$selectFilter = new SelectFilter($this->post_id, $this->taxonomy, $this->categories, $this->display, $this->title, $this->unit, $this->defaults);
					$selectFilter->setGet($this->_get);
					return $selectFilter->get();
				case "select-multiple":
					$selectMultipleFilter = new SelectMultipleFilter($this->post_id, $this->taxonomy, $this->categories, $this->display, $this->title, $this->unit, $this->defaults);
					$selectMultipleFilter->setGet($this->_get);
					return $selectMultipleFilter->get();
				case "checkbox":
					$checkboxFilter = new CheckboxFilter($this->post_id, $this->taxonomy, $this->categories, $this->display, $this->title, $this->unit, $this->defaults);
					$checkboxFilter->setGet($this->_get);
					return $checkboxFilter->get();
				case "radio":
					$radioFilter = new RadioFilter($this->post_id, $this->taxonomy, $this->categories, $this->display, $this->title, $this->unit, $this->defaults);
					$radioFilter->setGet($this->_get);
					return $radioFilter->get();
				default:
					return "";
			}
		}
		
		public static function countParentCategories($term_id, $taxonomy = "") {
			$count = 0;
			$category = get_term($term_id, $taxonomy);
			if($category->parent !== 0) {
				$count++;
				$count += Taxonomy::countParentCategories($category->parent, $taxonomy);
			}
			
			return $count;
		}
	}
