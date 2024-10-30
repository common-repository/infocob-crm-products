<?php
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Imports\ProductsFilesManager;
	use Infocob\CRM\Products\Admin\Classes\Imports\ProductsLoader;
	use Infocob\CRM\Products\Admin\Classes\Imports\ProductsManager;
	use Infocob\CRM\Products\Admin\Classes\Imports\ProductsSorter;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ImportProducts extends Controller {
		
		private ?\WP_Post $post = null;
		private ?InfocobDB $db = null;
		
		private array $products = [];
		private array $wp_products = [];
		
		/**
		 * @param int $id
		 *
		 * @throws \Exception
		 */
		public function __construct(int $id) {
			$post = get_post($id);
			if(get_post_type($post) === "icp-configuration" && get_post_status($post) === "publish") {
				$this->post = $post;
			} else {
				throw new \Exception("Invalid ID parameter", 404);
			}
			
			$this->db = InfocobDB::getInstance();
		}
		
		public function start(): void {
			if($this->post->ID ?? false) {
				$current_datetime = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
				$current_datetime_string = $current_datetime->format("Y-m-d H:i:s");
				update_post_meta($this->post->ID, ProductMeta::P_DATE_IMPORT_META_KEY, $current_datetime_string);
				
				// Re-schedule cron import if cron enable
				$api_cron_enable = get_post_meta($this->post->ID, "api-cron-enable", true);
				$api_cron_recurrence = get_post_meta($this->post->ID, "api-cron-recurrence", true);
				if (empty($api_cron_recurrence)) {
					$api_cron_recurrence = "hourly";
				}
				
				if($api_cron_enable) {
					CRON::unschedule($this->post->ID);
					CRON::schedule($this->post->ID, $api_cron_recurrence);
				}
			}
			
			Logger::infoImport("########## START IMPORT " . ($this->post->ID ?? "ERROR") . " ##########");
			do_action('icp_before_import', $this->post->ID);
			
			$productsLoader = new ProductsLoader($this->post);
			$this->products = $productsLoader->get();
			
			$productsSorter    = new ProductsSorter($this->post, $this->products);
			$this->wp_products = $productsSorter->get();
			
			$productsManager = new ProductsManager($this->post, $this->wp_products);
			$productsManager->update();
			$this->wp_products = $productsManager->get();
			
			$productsFilesManager = new ProductsFilesManager($this->post, $this->wp_products);
			$productsFilesManager->update();
			
			do_action('icp_after_import', ($this->post->ID ?? false));
			Logger::infoImport("########## END IMPORT " . ($this->post->ID ?? "ERROR") . " ##########", [], true);
			
			Logger::cleanLogs();
		}
		
		/**
		 * @return array|\WP_Post|null
		 */
		public function getPost() {
			return $this->post;
		}
		
		/**
		 * @param \WP_Post $post
		 */
		public function setPost(\WP_Post $post): void {
			$this->post = $post;
		}
		
		/**
		 * @return InfocobDB|null
		 */
		public function getDb(): ?InfocobDB {
			return $this->db;
		}
		
		/**
		 * @return array
		 */
		public function getProducts(): array {
			return $this->products;
		}
		
		/**
		 * @param array $products
		 */
		public function setProducts(array $products): void {
			$this->products = $products;
		}
		
	}
