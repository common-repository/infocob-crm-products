<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers\Settings;
	
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Controllers\Controller;
	use Infocob\CRM\Products\Admin\Controllers\Settings;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Import extends Controller {
		protected $options;
		
		public function __construct() {
			$this->options = get_option('infocob-crm-products-settings');
			$max_execution_time = ini_get('max_execution_time');
			$memory_limit = ini_get('memory_limit');
			
			add_settings_section(
				'infocob-crm-products-import-section',
				_x('Import', "add_settings_section", "infocob-crm-products"),
				[$this, 'section'],
				'infocob-crm-products'
			);
			
			add_settings_field(
				'enable-max-execution-time',
				_x('Enable max execution time', "add_settings_field", "infocob-crm-products"),
				[$this, 'enableMaxExecutionTimeField'],
				'infocob-crm-products',
				'infocob-crm-products-import-section'
			);
			
			add_settings_field(
				'max-execution-time',
				sprintf(_x('Max execution time (%s)', "add_settings_field", "infocob-crm-products"), $max_execution_time),
				[$this, 'maxExecutionTimeField'],
				'infocob-crm-products',
				'infocob-crm-products-import-section'
			);
			
			add_settings_field(
				'enable-memory-limit',
				_x('Enable memory limit', "add_settings_field", "infocob-crm-products"),
				[$this, 'enableMemoryLimitField'],
				'infocob-crm-products',
				'infocob-crm-products-import-section'
			);
			
			add_settings_field(
				'memory-limit',
				sprintf(_x('Memory limit (%s)', "add_settings_field", "infocob-crm-products"), $memory_limit),
				[$this, 'memoryLimitField'],
				'infocob-crm-products',
				'infocob-crm-products-import-section'
			);
		}
		
		public function section() {
			Tools::include('settings/import/section.php');
		}
		
		public function enableMaxExecutionTimeField() {
			Tools::include('settings/import/enable-max-execution-time.php', [
				"enable_max_execution_time" => filter_var($this->options['import']['enable-max-execution-time'] ?? false, FILTER_VALIDATE_BOOLEAN)
			]);
		}
		
		public function maxExecutionTimeField() {
			$max_execution_time = ini_get('max_execution_time');
			
			Tools::include('settings/import/max-execution-time.php', [
				"max_execution_time" => $this->options['import']['max-execution-time'] ?? $max_execution_time
			]);
		}
		
		public function enableMemoryLimitField() {
			Tools::include('settings/import/enable-memory-limit.php', [
				"enable_memory_limit" => filter_var($this->options['import']['enable-memory-limit'] ?? false, FILTER_VALIDATE_BOOLEAN)
			]);
		}
		
		public function memoryLimitField() {
			$memory_limit = ini_get('memory_limit');
			
			Tools::include('settings/import/memory-limit.php', [
				"memory_limit" => $this->options['import']['memory-limit'] ?? $memory_limit
			]);
		}
		
	}
