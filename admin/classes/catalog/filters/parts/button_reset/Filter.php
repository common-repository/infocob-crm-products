<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\ButtonReset;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Filter {
		protected int $post_id;
		
		protected string $title = "";
		
		/**
		 * @param int    $post_id
		 * @param string $meta_key
		 * @param array  $meta_values
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $title) {
			$this->post_id = $post_id;
			$this->title = $title;
		}
		
	}
