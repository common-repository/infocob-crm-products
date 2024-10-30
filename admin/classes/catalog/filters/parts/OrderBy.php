<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\OrderBy\SelectFilter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class OrderBy extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\OrderBy\Filter implements Filter {
		protected int $id;
		protected int $post_id;
		protected array $filter;
		protected array $orders_by = [];
		
		protected $_get = [];
		
		/**
		 * @param int   $post_id
		 * @param array $filter
		 * @param array $meta_values
		 */
		public function __construct(int $post_id, array $filter) {
			$this->post_id = $post_id;
			$this->filter = $filter;
			
			$this->orders_by = $filter["orders_by"] ?? [];
			
			$this->id = OrderBy::$filter_id++;
		}
		
		/**
		 * @param array $get
		 */
		public function setGet(array $get) {
			if (isset($get["infocob-crm-products"]["order_by"][$this->id])) {
				$this->_get = $get["infocob-crm-products"]["order_by"][$this->id];
			} else {
				$this->_get = false;
			}
		}
		
		/**
		 * @return string
		 */
		public function get() {
			$empty_value = '<option value="__EMPTY__">' . esc_html_x('Sort by default', "Order filter", "infocob-crm-products") . '</option>';
			$options = '';
			foreach ($this->orders_by as $value) {
				$order_by = $value["order_by"] ?? "";
				$order = $value["order"] ?? "";
				$meta_key = $value["meta_key"] ?? "";
				$title = $value["title"] ?? [];
				$unit = $value["unit"] ?? [];
				
				$orderByClass = new SelectFilter($this->post_id, $order_by, $order, $meta_key, $title, $unit);
				$orderByClass->setGet($this->_get);
				$options .= $orderByClass->get();
			}
			
			return '
					<div class="filter filter-select order-by ' . $this->id . '">
						<label for="left-filters-' . $this->post_id . '-order-' . $this->id . '"></label>
						<select name="infocob-crm-products[order_by][' . $this->id . ']" id="icp-filters-' . $this->post_id . '-select-' . $this->id . '" class="select">
							' . $empty_value . '
							' . $options . '
						</select>
					</div>
				';
		}
	}
