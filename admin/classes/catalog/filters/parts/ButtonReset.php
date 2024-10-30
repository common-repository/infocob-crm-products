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
	
	class ButtonReset implements Filter {
		protected int $post_id;
		protected array $filter;
		
		protected string $title       = "";
		
		/**
		 * @param int   $post_id
		 * @param array $filter
		 * @param array $meta_values
		 */
		public function __construct(int $post_id, array $filter) {
			$this->post_id = $post_id;
			$this->filter = $filter;
			
			$current_language = \Infocob\CRM\Products\Admin\Classes\Polylang::getCurrentLanguage(get_post_type($post_id));
			
			$this->title = $filter["title"][$current_language] ?? "";
		}
		
		/**
		 * @return string
		 */
		public function get() {
			$buttonReset = new ButtonReset\ButtonReset($this->post_id, $this->title);
			return $buttonReset->get();
		}
	}
