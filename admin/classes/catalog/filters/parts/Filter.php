<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	interface Filter {
		
		/**
		 * @return String;
		 */
		public function get();
	
	}
