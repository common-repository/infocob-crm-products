<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers\Settings;
	
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\Controller;
	use Infocob\CRM\Products\Admin\Controllers\Settings;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Webservice extends Controller {
		protected $options;
		
		public function __construct() {
			$this->options = get_option('infocob-crm-products-settings');
			
			add_settings_section(
				'infocob-crm-products-webservice-section',
				_x('Webservice (coming soon)', "add_settings_section", "infocob-crm-products"),
				[$this, 'section'],
				'infocob-crm-products'
			);
			
			add_settings_field(
				'enable',
				_x('Enable', "add_settings_field", "infocob-crm-products"),
				[$this, 'enableField'],
				'infocob-crm-products',
				'infocob-crm-products-webservice-section'
			);
			
			add_settings_field(
				'url',
				_x('URL', "add_settings_field", "infocob-crm-products"),
				[$this, 'urlField'],
				'infocob-crm-products',
				'infocob-crm-products-webservice-section'
			);
			
			add_settings_field(
				'api-key',
				_x('Api key', "add_settings_field", "infocob-crm-products"),
				[$this, 'apiKeyField'],
				'infocob-crm-products',
				'infocob-crm-products-webservice-section'
			);
		
		}
		
		public function section() {
			Tools::include('settings/webservice/section.php');
		}
		
		public function enableField() {
			Tools::include('settings/webservice/enable.php', [
				"enable" => filter_var($this->options['webservice']['enable'] ?? false, FILTER_VALIDATE_BOOLEAN)
			]);
		}
		
		public function urlField() {
			Tools::include('settings/webservice/url.php', [
				"url" => $this->options['webservice']['url'] ?? ""
			]);
		}
		
		public function apiKeyField() {
			Tools::include('settings/webservice/api-key.php', [
				"api_key" => $this->options['webservice']['api-key'] ?? ""
			]);
		}
		
	}
