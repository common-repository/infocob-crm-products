<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers\Settings;
	
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\Controller;
	use Infocob\CRM\Products\Admin\Controllers\Settings;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Database extends Controller {
		protected $options;
		
		public function __construct() {
			$this->options = get_option('infocob-crm-products-settings');
			
			add_settings_section(
				'infocob-crm-products-database-section',
				_x('Database', "add_settings_section", "infocob-crm-products"),
				[$this, 'section'],
				'infocob-crm-products'
			);
			
			add_settings_field(
				'enable',
				_x('Enable', "add_settings_field", "infocob-crm-products"),
				[$this, 'enableField'],
				'infocob-crm-products',
				'infocob-crm-products-database-section'
			);
			
			add_settings_field(
				'host',
				_x('Host', "add_settings_field", "infocob-crm-products"),
				[$this, 'hostField'],
				'infocob-crm-products',
				'infocob-crm-products-database-section'
			);
			
			add_settings_field(
				'base',
				_x('Base', "add_settings_field", "infocob-crm-products"),
				[$this, 'baseField'],
				'infocob-crm-products',
				'infocob-crm-products-database-section'
			);
			
			add_settings_field(
				'user',
				_x('User', "add_settings_field", "infocob-crm-products"),
				[$this, 'userField'],
				'infocob-crm-products',
				'infocob-crm-products-database-section'
			);
			
			add_settings_field(
				'password',
				_x('Password', "add_settings_field", "infocob-crm-products"),
				[$this, 'passwordField'],
				'infocob-crm-products',
				'infocob-crm-products-database-section'
			);
		
		}
		
		public function section() {
			Tools::include('settings/database/section.php');
		}
		
		public function enableField() {
			Tools::include('settings/database/enable.php', [
				"enable" => filter_var($this->options['database']['enable'] ?? false, FILTER_VALIDATE_BOOLEAN)
			]);
		}
		
		public function hostField() {
			Tools::include('settings/database/host.php', [
				"host" => $this->options['database']['host'] ?? ""
			]);
		}
		
		public function baseField() {
			Tools::include('settings/database/base.php', [
				"base" => $this->options['database']['base'] ?? ""
			]);
		}
		
		public function userField() {
			Tools::include('settings/database/user.php', [
				"user" => $this->options['database']['user'] ?? ""
			]);
		}
		
		public function passwordField() {
			Tools::include('settings/database/password.php', [
				"password" => $this->options['database']['password'] ?? ""
			]);
		}
		
	}
