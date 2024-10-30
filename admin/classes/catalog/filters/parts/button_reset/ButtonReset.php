<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonReset;
	
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ButtonReset extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonReset\Filter implements Filter{
		
		public function __construct(int $post_id, string $title) {
			parent::__construct($post_id, $title);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			return '
				<div class="filter button-reset">
					<input type="button" value="' . $this->title . '" class="reset">
				</div>
			';
		}
	}
