<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\CheckboxFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\RadioFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\RangeFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\SelectFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta\SelectMultipleFilter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class PostMeta implements Filter {
		protected int   $post_id;
		protected array $filter;
		
		protected string $meta_key    = "";
		protected array  $meta_values = [];
		protected string $display     = "";
		protected string $title       = "";
		protected string $unit        = "";
		protected int    $step        = 1;
		protected array  $defaults    = [];
		
		protected array $_get = [];
		
		/**
		 * @param int   $post_id
		 * @param array $filter
		 * @param array $meta_values
		 */
		public function __construct(int $post_id, array $filter, array $meta_values) {
			$this->post_id = $post_id;
			$this->filter = $filter;
			
			$current_language = \Infocob\CRM\Products\Admin\Classes\Polylang::getCurrentLanguage(get_post_type($post_id));
			
			$this->display = $filter["display"] ?? "";
			$this->title = $filter["title"][$current_language] ?? "";
			$this->unit = $filter["unit"][$current_language] ?? "";
			$this->step = $filter["step"] ?? 1;
			$this->defaults = $filter["defaults"] ?? [];
			$this->meta_key = $filter["meta_key"] ?? "";
			$this->meta_values = $meta_values;
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
			if ($this->meta_key === "") {
				return "";
			}
			
			switch ($this->display) {
				case "select":
					$selectFilter = new SelectFilter($this->post_id, $this->meta_key, $this->meta_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$selectFilter->setGet($this->_get);
					return $selectFilter->get();
				case "select-multiple":
					$selectMultipleFilter = new SelectMultipleFilter($this->post_id, $this->meta_key, $this->meta_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$selectMultipleFilter->setGet($this->_get);
					return $selectMultipleFilter->get();
				case "checkbox":
					$checkboxFilter = new CheckboxFilter($this->post_id, $this->meta_key, $this->meta_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$checkboxFilter->setGet($this->_get);
					return $checkboxFilter->get();
				case "radio":
					$radioFilter = new RadioFilter($this->post_id, $this->meta_key, $this->meta_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$radioFilter->setGet($this->_get);
					return $radioFilter->get();
				case "range":
					$rangeFilter = new RangeFilter($this->post_id, $this->meta_key, $this->meta_values, $this->display, $this->title, $this->unit, $this->step, $this->defaults);
					$rangeFilter->setGet($this->_get);
					return $rangeFilter->get();
				default:
					return "";
			}
		}
	}
