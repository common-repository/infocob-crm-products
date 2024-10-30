<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Filter {
		protected int $post_id;
		
		protected string $acf_group  = "";
		protected string $acf_field  = "";
		protected array  $acf_values = [];
		protected string $display    = "";
		protected string $title      = "";
		protected string $unit       = "";
		protected int    $step        = 1;
		protected array  $defaults   = [];
		
		protected $_get = false;
		
		/**
		 * @param int    $post_id
		 * @param string $acf_group
		 * @param string $acf_field
		 * @param array  $acf_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $acf_group, string $acf_field, array $acf_values, string $display, string $title, string $unit, int $step, array $defaults) {
			$this->post_id = $post_id;
			$this->acf_group = $acf_group;
			$this->acf_field = $acf_field;
			$this->acf_values = $acf_values;
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
			if(isset($get["infocob-crm-products"]["acf"][$this->acf_field])) {
				$_get = $get["infocob-crm-products"]["acf"][$this->acf_field];
				if($_get === "__EMPTY__") {
					$this->_get = true;
				} else {
					$this->_get = $get["infocob-crm-products"]["acf"][$this->acf_field];
				}
			}
		}
		
		
	}
