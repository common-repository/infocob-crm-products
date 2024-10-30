<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	interface Filters {
		
		/**
		 * @return String;
		 */
		public function get();
	
	}
