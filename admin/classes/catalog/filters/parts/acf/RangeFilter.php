<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class RangeFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\Filter implements Filter{
		protected $min = 0;
		protected $max = 0;
		protected $average = 0;
		
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
			
			if(!empty($acf_values)) {
				$min = min($acf_values);
				$max = max($acf_values);
				$average = 0;
				if (($min + $max) > 0) {
					$average = ($min + $max) / 2;
				}
				
				$this->min = $min;
				$this->max = $max;
				$this->average = $average;
				$this->step = $step;
			}
		}
		
		/**
		 * @return string
		 */
		public function get() {
			$min_value = $this->min;
			$max_value = $this->max;
			if(is_array($this->_get) && isset($this->_get["min"]) && isset($this->_get["max"])) {
				$min_value = $this->_get["min"];
				$max_value = $this->_get["max"];
			}
			
			if($min_value !== $max_value) {
				// @TODO hook
				
				return '
					<div class="filter filter-slider acf ' . $this->acf_field . '">
						<label for="left-filters-' . $this->post_id . '-range-' . $this->acf_field . '">' . $this->title . '</label>
						<input id="icp-filters-' . $this->post_id . '-range-' . $this->acf_field . '" class="fake" type="text" readonly>
						
						<input type="hidden" name="infocob-crm-products[acf][' . $this->acf_field . '][min]" class="min">
						<input type="hidden" name="infocob-crm-products[acf][' . $this->acf_field . '][max]" class="max">
						
						<div class="slider-range" data-min="' . $this->min . '" data-max="' . $this->max . '" data-average="' . $this->average . '" data-unit="' . $this->unit . '" data-step="' . $this->step . '" data-min-value="' . $min_value . '" data-max-value="' . $max_value . '"></div>
					</div>
				';
			} else {
				return '';
			}
		}
	}
