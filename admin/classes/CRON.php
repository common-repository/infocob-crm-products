<?php
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	use Infocob\CRM\Products\Admin\Controllers\ImportProducts;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CRON {
		public static $import_product_hook = "icp_cron_import_products";
		
		public static function schedule(int $post_id, string $recurrence, string $hook = "icp_cron_import_products") {
			$api_cron_recurrences = wp_get_schedules();
			$next_execution = time();
			if(isset($api_cron_recurrences[$recurrence]["interval"])) {
				$next_execution = $next_execution + $api_cron_recurrences[$recurrence]["interval"];
			}
			
			$cron_scheduled_timestamp = wp_next_scheduled($hook, [$post_id]);
			// and cron scheduled, unschedule the previous and re-schedule
			if($cron_scheduled_timestamp !== false) {
				wp_unschedule_event($cron_scheduled_timestamp, $hook, [$post_id]);
				wp_schedule_event($next_execution, $recurrence, $hook, [$post_id]);
			} else {
				wp_schedule_event($next_execution, $recurrence, $hook, [$post_id]);
			}
			
			update_post_meta($post_id, "api-cron-enable", true);
		}
		
		public static function unschedule(int $post_id, string $hook = "icp_cron_import_products") {
			$cron_scheduled_timestamp = wp_next_scheduled($hook, [$post_id]);
			if($cron_scheduled_timestamp !== false) {
				wp_unschedule_event($cron_scheduled_timestamp, $hook, [$post_id]);
				update_post_meta($post_id, "api-cron-enable", false);
			}
		}
		
		public static function importProducts(int $post_id) {
			$options = get_option('infocob-crm-products-settings');
			$enable_max_execution_time = $options["import"]["enable-max-execution-time"] ?? false;
			if($enable_max_execution_time) {
				$max_execution_time = $options["import"]["max-execution-time"] ?? false;
			}
			$enable_memory_limit = $options["import"]["enable-memory-limit"] ?? false;
			if($enable_memory_limit) {
				$memory_limit = $options["import"]["memory-limit"] ?? false;
			}
			
			if(($max_execution_time ?? false) !== false) {
				ini_set('max_execution_time', $max_execution_time);
				set_time_limit((int)$max_execution_time);
			}
			if(($memory_limit ?? false) !== false) {
				ini_set('memory_limit', $memory_limit);
			}
			
			$import = new ImportProducts($post_id);
			$import->start();
		}
		
	}
