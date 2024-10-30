<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonFilter;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ButtonFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonFilter\Filter implements Filter{
		
		public function __construct(int $post_id, string $title) {
			parent::__construct($post_id, $title);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			return '
				<div class="filter button-filter">
					<input type="button" value="' . $this->title . '" class="submit-filter">
				</div>
			';
		}
	}
