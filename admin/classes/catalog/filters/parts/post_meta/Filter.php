<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Filter {
		protected int $post_id;
		
		protected string $meta_key    = "";
		protected array  $meta_values = [];
		protected string $display     = "";
		protected string $title       = "";
		protected string $unit        = "";
		protected int    $step        = 1;
		protected array  $defaults    = [];
		
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
		public function __construct(int $post_id, string $meta_key, array $meta_values, string $display, string $title, string $unit, int $step, array $defaults) {
			$this->post_id = $post_id;
			$this->meta_key = $meta_key;
			$this->meta_values = $meta_values;
			$this->display = $display;
			$this->title = $title;
			$this->unit = $unit;
			$this->step = $step;
			$this->defaults = $defaults;
		}
		
		/**
		 * @param bool|mixed $get
		 */
		public function setGet($get) {
			if (isset($get["infocob-crm-products"]["post_meta"][$this->meta_key])) {
				$_get = $get["infocob-crm-products"]["post_meta"][$this->meta_key];
				if ($_get === "__EMPTY__") {
					$this->_get = true;
				} else {
					$this->_get = $get["infocob-crm-products"]["post_meta"][$this->meta_key];
				}
			}
		}
		
	}
