<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class RadioFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\Filter implements Filter{
		
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
			
			$nb_radios = 0;
			$empty_value = '<input name="infocob-crm-products[post_meta][' . $this->meta_key . ']" type="hidden" value="__EMPTY__">';
			$radios = '';
			foreach ($this->meta_values as $index => $meta_value) {
				if($this->_get !== false) {
					$selected = ($meta_value === $this->_get) ? "checked" : "";
				} else {
					$selected = in_array($meta_value, ($this->defaults ?? [])) ? "checked" : "";
				}
				$unit = !empty($this->unit ?? "") ? " " . $this->unit : "";
				
				$meta_value_display = $meta_value;
				$meta_value_display = apply_filters("icp_post_meta_radio_" . Tools::cleanString($this->meta_key), $meta_value_display, [
					"post_id" => $this->post_id,
					"meta_key" => $this->meta_key,
					"lang" => Polylang::getCurrentLanguage(get_post_type($this->post_id)),
				]);
				
				$radios .= '
					<div class="radio">
						<input name="infocob-crm-products[post_meta][' . $this->meta_key . ']" id="icp-filters-' . $this->post_id . '-radio-' . $this->meta_key . '-' . $meta_value . '-' . $index . '" type="radio" value="' . $meta_value . '" ' . $selected . '>
						<label for="left-filters-' . $this->post_id . '-radio-' . $this->meta_key . '-' . $meta_value . '-' . $index . '">' . $meta_value_display . $unit . '</label>
					</div>
				';
				
				$nb_radios++;
			}
			
			if($nb_radios > 0) {
				return '
					<div class="filter filter-radios post-meta ' . $this->meta_key . '">
						<label>' . $this->title . '</label>
						<div class="radios ' . $this->meta_key . '">
							' . $empty_value . '
							' . $radios . '
						</div>
					</div>
				';
			} else {
				return '';
			}
		}
	}
