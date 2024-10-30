<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class SelectMultipleFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\Filter implements Filter{
		
		/**
		 * @param int    $post_id
		 * @param string $meta_key
		 * @param array  $meta_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param int    $step
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $meta_key, array $meta_values, string $display, string $title, string $unit, int $step, array $defaults) {
			parent::__construct($post_id, $meta_key, $meta_values, $display, $title, $unit, $step, $defaults);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->_get === true) {
				$this->_get = [];
			}
			
			$nb_options = 0;
			$empty_value = '<input name="infocob-crm-products[post_meta][' . $this->meta_key . ']" type="hidden" value="__EMPTY__">';
			$options = '';
			foreach ($this->meta_values as $meta_value) {
				if($this->_get !== false) {
					$selected = in_array($meta_value, ($this->_get ?? [])) ? "selected" : "";
				} else {
					$selected = in_array($meta_value, ($this->defaults ?? [])) ? "selected" : "";
				}
				$unit = !empty($this->unit ?? "") ? " " . $this->unit : "";
				
				$meta_value_display = $meta_value;
				$meta_value_display = apply_filters("icp_post_meta_select_multiple_" . Tools::cleanString($this->meta_key), $meta_value_display, [
					"post_id" => $this->post_id,
					"meta_key" => $this->meta_key,
					"lang" => Polylang::getCurrentLanguage(get_post_type($this->post_id)),
				]);
				
				$options .= '<option value="' . $meta_value . '" ' . $selected . '>' . $meta_value_display . $unit . '</option>';
				
				$nb_options++;
			}
			
			if($nb_options > 0) {
				return '
					' . $empty_value . '
					<div class="filter filter-selectmultiple post-meta ' . $this->meta_key . '">
						<label for="left-filters-' . $this->post_id . '-select-multiple-' . $this->meta_key . '">' . $this->title . '</label>
						<select name="infocob-crm-products[post_meta][' . $this->meta_key . '][]" id="icp-filters-' . $this->post_id . '-select-multiple-' . $this->meta_key . '" class="select-multiple" multiple="multiple">
							' . $options . '
						</select>
					</div>
				';
			} else {
				return '';
			}
		}
	}
