<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use WP_REST_Request;
	use WP_REST_Response;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class RestApi extends Controller {
		
		/**
		 * @param $post_id
		 *
		 * @return bool
		 */
		private static function isAuthorizeRequest($post_id) {
			$api_authorize_ip = get_post_meta($post_id, "api-authorize-ip", true);
			if(InfocobDB::testConnection()) {
				$enable_rest_api_key = get_post_meta($post_id, "rest-api-key-enable", true);
				
				$rest_api_authorized = true;
				if($enable_rest_api_key) {
					$rest_api_key = get_post_meta($post_id, "rest-api-key", true);
					$client_api_key = "";
					
					$apacheHeaders = array_change_key_case(apache_request_headers(), CASE_UPPER);
					$auth = (isset($apacheHeaders["AUTHORIZATION"])) ? trim($apacheHeaders["AUTHORIZATION"]) : null;
					if (preg_match("/(?<=\ )(.*)$/", $auth, $apikey) && !is_null($auth)) {
						$client_api_key = $apikey[0] ?? "";
					}
					
					$rest_api_authorized = ($client_api_key === $rest_api_key);
				}
				
				if (empty($api_authorize_ip) && $rest_api_authorized) {
					return true;
				} else {
					$authorize_ips = explode(";", $api_authorize_ip);
					$authorize_ips = array_map("trim", $authorize_ips);
					if (in_array($_SERVER['REMOTE_ADDR'], $authorize_ips)) {
						return true;
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		}
		
		/**
		 * @param WP_REST_Request $request
		 *
		 * @return WP_REST_Response
		 */
		public static function import(WP_REST_Request $request) {
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
			
			$id = (int)$request->get_param('id');
			
			if (!static::isAuthorizeRequest($id)) {
				update_post_meta($id, "infocob-import-success", false);
				update_post_meta($id, "infocob-import-status", 401);
				$response = new WP_REST_Response([
					"success" => true,
					"message" => "Unauthorized",
					"id"      => $id
				]);
				$response->set_status(401);
				
				return $response;
			}
			
			try {
				$import = new ImportProducts($id);
				$import->start();
				
				update_post_meta($id, "infocob-import-success", true);
				update_post_meta($id, "infocob-import-status", 200);
				$response = new WP_REST_Response([
					"success" => true,
					"message" => "Import success !",
					"id"      => $id
				]);
				$response->set_status(200);
			} catch (\Exception $exception) {
				update_post_meta($id, "infocob-import-success", false);
				update_post_meta($id, "infocob-import-status", $exception->getCode());
				$response = new WP_REST_Response([
					"success" => false,
					"message" => $exception->getMessage(),
					"id"      => $id
				]);
				$response->set_status($exception->getCode());
			}
			
			return $response;
		}
		
		/**
		 * @param WP_REST_Request $request
		 *
		 * @return WP_REST_Response
		 */
		public static function imports(WP_REST_Request $request) {
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
				set_time_limit($max_execution_time);
			}
			if(($memory_limit ?? false) !== false) {
				ini_set('memory_limit', $memory_limit);
			}
			
			$posts = get_posts([
				"post_type" => "icp-configuration"
			]);
			
			$responses = [];
			foreach ($posts as $post) {
				$id = $post->ID ?? false;
				if ($id !== false) {
					if (!static::isAuthorizeRequest($id)) {
						update_post_meta($id, "infocob-import-success", false);
						update_post_meta($id, "infocob-import-status", 401);
						$responses[] = [
							"success" => true,
							"code"    => 401,
							"message" => "Unauthorized",
							"id"      => $id
						];
						
					} else {
						try {
							$import = new ImportProducts($id);
							$import->start();
							
							update_post_meta($id, "infocob-import-success", true);
							update_post_meta($id, "infocob-import-status", 200);
							$responses[] = [
								"success" => true,
								"code"    => 200,
								"message" => "Import success !",
								"id"      => $id
							];
						} catch (\Exception $exception) {
							update_post_meta($id, "infocob-import-success", false);
							update_post_meta($id, "infocob-import-status", (int)$exception->getCode());
							$responses[] = [
								"success" => false,
								"code"    => $exception->getCode(),
								"message" => $exception->getMessage(),
								"id"      => $id
							];
						}
					}
				}
			}
			
			$response = new WP_REST_Response([
				"success"   => true,
				"responses" => $responses
			]);
			$response->set_status(200);
			
			return $response;
		}
		
		/**
		 * @param WP_REST_Request $request
		 *
		 * @return WP_REST_Response
		 */
		public static function infoImport(WP_REST_Request $request) {
			$id = (int)$request->get_param('id');
			
			if (!static::isAuthorizeRequest($id)) {
				$response = new WP_REST_Response([
					"success" => true,
					"code"    => 401,
					"message" => "Unauthorized",
					"id"      => $id
				]);
				$response->set_status(401);
				
				return $response;
			}
			
			try {
				new ImportProducts($id);
				
				$api_cron_enable = get_post_meta($id, "api-cron-enable", true);
				
				$last_import = "";
				$date_last_import = get_post_meta($id, ProductMeta::P_DATE_IMPORT_META_KEY, true);
				if (!empty($date_last_import)) {
					$datetime = DateTimeFr::createFromFormat("Y-m-d H:i:s", $date_last_import, new \DateTimeZone("Europe/Paris"));
					$last_import = $datetime->format("Y-m-d H:i:s");
				}
				
				$next_import = "";
				if ($api_cron_enable) {
					$cron_scheduled_timestamp = wp_next_scheduled(CRON::$import_product_hook, [$id]);
					if ($cron_scheduled_timestamp !== false) {
						$datetime = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
						$datetime = $datetime->setTimestamp($cron_scheduled_timestamp);
						$next_import = $datetime->format("Y-m-d H:i:s");
					}
				}
				
				$last_import_success = !empty(get_post_meta($id, "infocob-import-success", true));
				$last_import_status = (int)get_post_meta($id, "infocob-import-status", 200);
				
				$response = new WP_REST_Response([
					"success"             => true,
					"code"                => 200,
					"id"                  => $id,
					"title"               => get_the_title($id),
					"last-import"         => $last_import,
					"next-import"         => $next_import,
					"last-import-success" => $last_import_success,
					"last-import-status"  => $last_import_status,
				]);
				$response->set_status(200);
			} catch (\Exception $exception) {
				$response = new WP_REST_Response([
					"success" => false,
					"code"    => $exception->getCode(),
					"message" => $exception->getMessage(),
					"id"      => $id
				]);
				$response->set_status($exception->getCode());
			}
			
			return $response;
		}
		
		/**
		 * @param WP_REST_Request $request
		 *
		 * @return WP_REST_Response
		 */
		public static function infoImports(WP_REST_Request $request) {
			$posts = get_posts([
				"post_type" => "icp-configuration"
			]);
			
			$responses = [];
			foreach ($posts as $post) {
				$id = $post->ID ?? false;
				if ($id !== false) {
					if (!static::isAuthorizeRequest($id)) {
						$responses[] = [
							"success" => true,
							"code"    => 401,
							"message" => "Unauthorized",
							"id"      => $id
						];
						
					} else {
						try {
							new ImportProducts($id);
							
							$api_cron_enable = get_post_meta($id, "api-cron-enable", true);
							
							$last_import = "";
							$date_last_import = get_post_meta($id, ProductMeta::P_DATE_IMPORT_META_KEY, true);
							if (!empty($date_last_import)) {
								$datetime = DateTimeFr::createFromFormat("Y-m-d H:i:s", $date_last_import, new \DateTimeZone("Europe/Paris"));
								$last_import = $datetime->format("Y-m-d H:i:s");
							}
							
							$next_import = "";
							if ($api_cron_enable) {
								$cron_scheduled_timestamp = wp_next_scheduled(CRON::$import_product_hook, [$id]);
								if ($cron_scheduled_timestamp !== false) {
									$datetime = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
									$datetime = $datetime->setTimestamp($cron_scheduled_timestamp);
									$next_import = $datetime->format("Y-m-d H:i:s");
								}
							}
							
							$last_import_success = !empty(get_post_meta($id, "infocob-import-success", true));
							$last_import_status = (int)get_post_meta($id, "infocob-import-status", 200);
							
							$responses[] = [
								"success"             => true,
								"code"                => 200,
								"id"                  => $id,
								"title"               => get_the_title($id),
								"last-import"         => $last_import,
								"next-import"         => $next_import,
								"last-import-success" => $last_import_success,
								"last-import-status"  => $last_import_status,
							];
						} catch (\Exception $exception) {
							$responses[] = [
								"success" => false,
								"code"    => $exception->getCode(),
								"message" => $exception->getMessage(),
								"id"      => $id
							];
						}
					}
				}
			}
			
			$response = new WP_REST_Response([
				"success"   => true,
				"responses" => $responses,
				"id"        => $id
			]);
			$response->set_status(200);
			
			return $response;
		}
		
	}
