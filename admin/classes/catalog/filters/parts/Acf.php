<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\CheckboxFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\RadioFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\SelectFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\SelectMultipleFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf\RangeFilter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Acf implements Filter {
		protected int   $post_id;
		protected array $filter;
		
		protected string $acf_group  = "";
		protected string $acf_field  = "";
		protected array  $acf_values = [];
		protected string $display    = "";
		protected string $title      = "";
		protected string $unit       = "";
		protected int    $step        = 1;
		protected array  $defaults   = [];
		
		protected array $_get = [];
		
		/**
		 * @param int   $post_id
		 * @param array $filter
		 * @param array $meta_values
		 */
		public function __construct(int $post_id, array $filter, array $acf_values) {
			$this->post_id = $post_id;
			$this->filter = $filter;
			
			$current_language = \Infocob\CRM\Products\Admin\Classes\Polylang::getCurrentLanguage(get_post_type($post_id));
			
			$this->display = $filter["display"] ?? "";
			$this->title = $filter["title"][$current_language] ?? "";
			$this->unit = $filter["unit"][$current_language] ?? "";
			$this->step = $filter["step"] ?? 1;
			$this->defaults = $filter["defaults"] ?? [];
			$this->acf_group = $filter["acf_group"] ?? "";
			$this->acf_field = $filter["acf_field"] ?? "";
			$this->acf_values = $acf_values ?? [];
		}
		
		/**
		 * @param array $get
		 */
		public function setGet(array $get) {
			$this->_get = $get;
		}
		
		/**
		 * @return string
		 */
		public function get() {
			if($this->acf_field === "") {
				return "";
			}
			
			switch ($this->display) {
				case "select":
					$selectFilter = new SelectFilter($this->post_id, $this->acf_group, $this->acf_field, $this->acf_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$selectFilter->setGet($this->_get);
					return $selectFilter->get();
				case "select-multiple":
					$selectMultipleFilter = new SelectMultipleFilter($this->post_id, $this->acf_group, $this->acf_field, $this->acf_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$selectMultipleFilter->setGet($this->_get);
					return $selectMultipleFilter->get();
				case "checkbox":
					$checkboxFilter = new CheckboxFilter($this->post_id, $this->acf_group, $this->acf_field, $this->acf_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$checkboxFilter->setGet($this->_get);
					return $checkboxFilter->get();
				case "radio":
					$radioFilter = new RadioFilter($this->post_id, $this->acf_group, $this->acf_field, $this->acf_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$radioFilter->setGet($this->_get);
					return $radioFilter->get();
				case "range":
					$rangeFilter = new RangeFilter($this->post_id, $this->acf_group, $this->acf_field, $this->acf_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$rangeFilter->setGet($this->_get);
					return $rangeFilter->get();
				default:
					return "";
			}
		}
	}
