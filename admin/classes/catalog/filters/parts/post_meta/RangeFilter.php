<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class RangeFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\Filter implements Filter{
		protected $min = 0;
		protected $max = 0;
		protected $average = 0;
		
		/**
		 * @param int    $post_id
		 * @param string $meta_key
		 * @param array  $meta_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $meta_key, array $meta_values, string $display, string $title, string $unit, int $step, array $defaults) {
			parent::__construct($post_id, $meta_key, $meta_values, $display, $title, $unit, $step, $defaults);
			
			if(!empty($meta_values)) {
				$min = min($meta_values);
				$max = max($meta_values);
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
					<div class="filter filter-slider post-meta ' . $this->meta_key . '">
						<label for="left-filters-' . $this->post_id . '-range-' . $this->meta_key . '">' . $this->title . '</label>
						<input id="icp-filters-' . $this->post_id . '-range-' . $this->meta_key . '" class="fake" type="text" readonly>
						
						<input type="hidden" name="infocob-crm-products[post_meta][' . $this->meta_key . '][min]" class="min">
						<input type="hidden" name="infocob-crm-products[post_meta][' . $this->meta_key . '][max]" class="max">
						
						<div class="slider-range" data-min="' . $this->min . '" data-max="' . $this->max . '" data-average="' . $this->average . '" data-unit="' . $this->unit . '" data-step="' . $this->step . '" data-min-value="' . $min_value . '" data-max-value="' . $max_value . '"></div>
					</div>
				';
			} else {
				return '';
			}
		}
	}
