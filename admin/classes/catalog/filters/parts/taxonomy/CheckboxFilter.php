<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CheckboxFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\Filter implements Filter{
		
		/**
		 * @param int    $post_id
		 * @param string $taxonomy
		 * @param array  $categories
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $taxonomy, array $categories, string $display, string $title, string $unit, array $defaults) {
			parent::__construct($post_id, $taxonomy, $categories, $display, $title, $unit, $defaults);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->_get === true) {
				$this->_get = [];
			}
			
			$nb_checkboxes = 0;
			$empty_value = '<input name="infocob-crm-products[taxonomy][' . $this->taxonomy . ']" type="hidden" value="__EMPTY__">';
			$checkboxes = '';
			foreach ($this->categories as $index => $category) {
				if($category instanceof \WP_Term) {
					if ($this->_get !== false) {
						$selected = in_array($category->term_id, ($this->_get ?? [])) ? "checked" : "";
					} else {
						$selected = in_array($category->term_id, ($this->defaults ?? [])) ? "checked" : "";
					}
					
					$unit = !empty($this->unit ?? "") ? " " . $this->unit : "";
					
					$category_display = $category->name;
					$category_display = apply_filters("icp_taxonomy_checkbox_" . Tools::cleanString($this->taxonomy), $category_display, [
						"post_id"  => $this->post_id,
						"taxonomy" => $this->taxonomy,
						"lang"     => Polylang::getCurrentLanguage(get_post_type($this->post_id)),
					]);
					
					$level_text = "";
					$level = $category->level ?? 0;
					for($i = 0; $i < $level; $i++) {
						$level_text .= "-";
					}
					
					$checkboxes .= '
					<div class="checkbox">
						<input name="infocob-crm-products[taxonomy][' . $this->taxonomy . '][' . $index . ']" id="icp-filters-' . $this->post_id . '-checkbox-' . $this->taxonomy . '-' . $category->term_id . '-' . $index . '" type="checkbox" value="' . $category->term_id . '" ' . $selected . '>
						<label for="left-filters-' . $this->post_id . '-checkbox-' . $this->taxonomy . '-' . $category->term_id . '-' . $index . '">' . $level_text . " " . $category_display . $unit . '</label>
					</div>
				';
					
					$nb_checkboxes++;
				}
			}
			
			if($nb_checkboxes > 1) {
				return '
					<div class="filter filter-checkboxes taxonomy ' . $this->taxonomy . '">
						<label>' . $this->title . '</label>
						<div class="checkboxes ' . $this->taxonomy . '">
							' . $empty_value . '
							' . $checkboxes . '
						</div>
					</div>
				';
			} else {
				return '';
			}
		}
	}
