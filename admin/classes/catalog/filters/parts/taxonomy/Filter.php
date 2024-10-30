<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Filter {
		protected int $post_id;
		
		protected string $taxonomy   = "";
		protected array  $categories = [];
		protected string $display    = "";
		protected string $title      = "";
		protected string $unit       = "";
		protected array  $defaults   = [];
		
		protected $_get = false;
		
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
			$this->post_id = $post_id;
			$this->taxonomy = $taxonomy;
			$this->categories = $categories;
			$this->display = $display;
			$this->title = $title;
			$this->unit = $unit;
			$this->defaults = $defaults;
		}
		
		/**
		 * @param bool|mixed $get
		 */
		public function setGet($get) {
			if(isset($get["infocob-crm-products"]["taxonomy"][$this->taxonomy])) {
				$_get = $get["infocob-crm-products"]["taxonomy"][$this->taxonomy];
				if($_get === "__EMPTY__") {
					$this->_get = true;
				} else {
					$this->_get = $get["infocob-crm-products"]["taxonomy"][$this->taxonomy];
				}
			}
		}
		
	}
