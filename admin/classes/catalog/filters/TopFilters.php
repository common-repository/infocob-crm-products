<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Catalog;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Acf;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonFilter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonReset;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\OrderBy;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\PostMeta;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class TopFilters implements Filters {
		protected int $post_id;
		protected string $post_type;
		protected array $config = [];
		
		protected $filters          = [];
		protected $post_meta_values = [];
		
		protected array $_get = [];
		
		/**
		 * @param int $post_id
		 */
		public function __construct(int $post_id) {
			$this->post_id = $post_id;
			
			$this->post_type = get_post_meta($post_id, "general-post-type", true);
			$config = get_post_meta($this->post_id, "top-filters", true);
			$config = Tools::decodeConfig($config);
			if(!empty($config)) {
				$this->config = $config;
				
				// Getting values available from taxonomy (if in taxonomy)
				$taxonomy = false;
				$term_id = false;
				$queried_object = get_queried_object();
				if(is_tax($queried_object)) {
					$taxonomy = $queried_object->taxonomy;
					$term_id = $queried_object->term_id;
				}
				
				// For post_meta
				$meta_keys = Catalog::getMetaKeysFromConfigFilters($this->config);
				$this->post_meta_values = Tools::getPostMetaValues($this->post_type, $meta_keys, $taxonomy, [$term_id]);
			}
		}
		
		/**
		 * @return void
		 */
		public function loadFilters() {
			$filters = $this->config["filters"] ?? [];
			foreach ($filters as $filter) {
				$type = $filter["type"] ?? false;
				$filter_props = $filter["filter"] ?? [];
				
				if ($type !== false) {
					if($type === "post_meta") {
						$meta_key = $filter_props["meta_key"] ?? false;
						$meta_values = $this->post_meta_values[$meta_key] ?? [];
						$postMeta = new PostMeta($this->post_id, $filter_props, $meta_values);
						$postMeta->setGet($this->_get);
						$this->addFilter($postMeta);
						
					} else if($type === "taxonomy") {
						$taxonomyFilter = new Taxonomy($this->post_id, $filter_props);
						$taxonomyFilter->setGet($this->_get);
						$this->addFilter($taxonomyFilter);
						
					} else if($type === "order_by") {
						$orderBy = new OrderBy($this->post_id, $filter_props);
						$orderBy->setGet($this->_get);
						$this->addFilter($orderBy);
						
					} else if($type === "button_filter") {
						$buttonFilter = new ButtonFilter($this->post_id, $filter_props);
						$this->addFilter($buttonFilter);
						
					} else if($type === "button_reset") {
						$buttonReset = new ButtonReset($this->post_id, $filter_props);
						$this->addFilter($buttonReset);
						
					} else if($type === "acf") {
						// Getting values available from taxonomy (if in taxonomy)
						$taxonomy = false;
						$term_id = false;
						$queried_object = get_queried_object();
						if(is_tax($queried_object)) {
							$taxonomy = $queried_object->taxonomy;
							$term_id = $queried_object->term_id;
						}
						
						$acf_field  = $filter_props["acf_field"] ?? "";
						$acf_values = Tools::getAcfFieldsValues($this->post_type, [$acf_field], $taxonomy, [$term_id]);
						$acf_values = $acf_values[$acf_field] ?? [];
						
						$acf = new Acf($this->post_id, $filter_props, $acf_values);
						$acf->setGet($this->_get);
						$this->addFilter($acf);
					}
				}
			}
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
			$filter_html = "";
			foreach ($this->filters as $filter) {
				if($filter instanceof Filter) {
					$filter_html .= $filter->get();
				}
			}
			
			$html = '
			<div class="infocob-crm-products ' . $this->post_id . ' filters top" data-post_id="' . $this->post_id . '">
				<form action="" method="get">
					<input type="hidden" name="infocob-crm-products-post-id" value="' . $this->post_id . '">
					
					<div class="content">
						' . $filter_html . '
					</div>
				</form>
			';
			
			$html .= '</div>';
			
			return $html;
		}
		
		/**
		 * @param Filter $filter
		 *
		 * @return void
		 */
		private function addFilter(Filter $filter) {
			$this->filters[] = $filter;
		}
	}
