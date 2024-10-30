<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Configuration {
		
		public static function initDatabase() {
			$options = get_option('infocob-crm-products-settings');
			
			InfocobDB::$host = $options["database"]["host"] ?? "";
			InfocobDB::$base = $options["database"]["base"] ?? "";
			InfocobDB::$user = $options["database"]["user"] ?? "";
			InfocobDB::$pswd = $options["database"]["password"] ?? "";
		}
		
	}
