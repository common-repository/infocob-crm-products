<?php
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Functions {
		
		public static function register() {
			global $post;
			
			Tools::include("functions/langs.php", [
				"wp_post" => $post
			]);
			Tools::include("functions/products.php", [
				"wp_post" => $post
			]);
		}
		
	}
