<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\OrderBy;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class SelectFilter {
		protected int $post_id;
		
		protected string $order_by = "";
		protected string $order    = "";
		protected string $meta_key = "";
		protected string $title    = "";
		protected string $unit     = "";
		
		protected $_get = false;
		
		/**
		 * @param int    $post_id
		 * @param string $meta_key
		 * @param array  $meta_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $order_by, string $order, string $meta_key, array $title, array $unit) {
			$this->post_id = $post_id;
			$this->order_by = $order_by;
			$this->order = $order;
			$this->meta_key = $meta_key;
			
			$current_language = \Infocob\CRM\Products\Admin\Classes\Polylang::getCurrentLanguage(get_post_type($post_id));
			
			$this->title = $title[$current_language] ?? "";
			$this->unit = $unit[$current_language] ?? "";
		}
		
		/**
		 * @param bool|mixed $get
		 */
		public function setGet($get) {
			if ($get === "__EMPTY__") {
				$this->_get = true;
			} else {
				$this->_get = $get;
			}
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->_get === true) {
				$this->_get = "";
			}
			
			$option_value = base64_encode(json_encode([
				"order_by" => $this->order_by,
				"order" => $this->order,
				"meta_key" => $this->meta_key,
			]));
			
			if($this->_get !== false) {
				$selected = ($option_value === $this->_get) ? "selected" : "";
			} else {
				$selected = "";
			}
			$unit = !empty($this->unit ?? "") ? " " . $this->unit : "";
			
			$option = '<option value="' . $option_value . '" ' . $selected . '>' . $this->title . $unit . '</option>';
			
			return $option;
		}
		
	}
