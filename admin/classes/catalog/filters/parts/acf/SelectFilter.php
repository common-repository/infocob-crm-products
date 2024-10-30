<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class SelectFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\Filter implements Filter{
		
		/**
		 * @param int    $post_id
		 * @param string $acf_group
		 * @param string $acf_field
		 * @param array  $acf_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param int    $step
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $acf_group, string $acf_field, array $acf_values, string $display, string $title, string $unit, int $step, array $defaults) {
			if(empty($title)) {
				$acf_field_object = get_field_object($acf_field, $post_id);
				if(!empty($acf_field_object)) {
					$title = $acf_field_object["label"] ?? "";
				}
			}
			
			parent::__construct($post_id, $acf_group, $acf_field, $acf_values, $display, $title, $unit, $step, $defaults);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->_get === true) {
				$this->_get = "";
			}
			
			$nb_options = 0;
			$empty_value = '<option value="__EMPTY__"></option>';
			$options = '';
			foreach ($this->acf_values as $acf_value) {
				if($this->_get !== false) {
					$selected = ($acf_value === $this->_get) ? "selected" : "";
				} else {
					$selected = in_array($acf_value, ($this->defaults ?? [])) ? "selected" : "";
				}
				$unit = !empty($this->unit ?? "") ? " " . $this->unit : "";
				
				$acf_value_display = $acf_value;
				$acf_value_display = apply_filters("icp_post_meta_select_" . Tools::cleanString($this->acf_field), $acf_value_display, [
					"post_id" => $this->post_id,
					"meta_key" => $this->acf_field,
					"lang" => Polylang::getCurrentLanguage(get_post_type($this->post_id)),
				]);
				
				$options .= '<option value="' . $acf_value . '" ' . $selected . '>' . $acf_value_display . $unit . '</option>';
				
				$nb_options++;
			}
			
			if($nb_options > 0) {
				return '
					<div class="filter filter-select acf ' . $this->acf_field . '">
						<label for="left-filters-' . $this->post_id . '-select-' . $this->acf_field . '">' . $this->title . '</label>
						<select name="infocob-crm-products[acf][' . $this->acf_field . ']" id="icp-filters-' . $this->post_id . '-select-' . $this->acf_field . '" class="select">
							' . $empty_value . '
							' . $options . '
						</select>
					</div>
				';
			} else {
				return '';
			}
		}
	}
