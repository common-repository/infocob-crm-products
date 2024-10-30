<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ThemeFiles {
		private int $catalog_id;
		private string $post_type;
		private string $archive_template_file = "template-archive.php";
		private string $taxonomy_template_file = "template-taxonomy.php";
		private string $entry_template_file = "template-entry.php";
		
		/**
		 * @param int $catalog_id
		 */
		public function __construct(int $catalog_id) {
			$this->catalog_id = $catalog_id;
			$this->post_type = get_post_meta($catalog_id, "general-post-type", true);
		}
		
		
		public function generateArchiveTemplate() {
			if(!empty($this->post_type)) {
				$template_path = ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/admin/includes/themes/' . $this->archive_template_file;
				$archive_path = get_stylesheet_directory() . "/archive-" . $this->post_type . ".php";
				if (file_exists($template_path)) {
					$copied = copy($template_path, $archive_path);
					if (!$copied) {
						throw new \Exception(sprintf("Unable to generate the archive to %s", $archive_path), 500);
					}
					
					$this->setVariables($archive_path);
					return [
						"success" => true,
						"date" => Tools::getDateModifiedFile($archive_path)
					];
					
				} else {
					throw new \Exception(sprintf("Unable to find the template file %s", $template_path), 500);
				}
			}
			
			return [
				"success" => false,
			];
		}
		
		public function generateTaxonomyTemplate($taxonomy) {
			$template_path = ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/admin/includes/themes/' . $this->taxonomy_template_file;
			$taxonomy_path = get_stylesheet_directory() . "/" . $taxonomy;
			if (file_exists($template_path)) {
				$copied = copy($template_path, $taxonomy_path);
				if (!$copied) {
					throw new \Exception(sprintf("Unable to generate the archive to %s", $taxonomy_path), 500);
				}
				
				$this->setVariables($taxonomy_path);
				return [
					"success" => true,
					"date" => Tools::getDateModifiedFile($taxonomy_path)
				];
				
			} else {
				throw new \Exception(sprintf("Unable to find the template file %s", $template_path), 500);
			}
		}
		
		public function generateEntryTemplate() {
			if(!empty($this->post_type)) {
				$template_path = ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH . '/admin/includes/themes/' . $this->entry_template_file;
				$archive_path = get_stylesheet_directory() . "/entry-" . $this->post_type . ".php";
				if (file_exists($template_path)) {
					$copied = copy($template_path, $archive_path);
					if (!$copied) {
						throw new \Exception(sprintf("Unable to generate the entry to %s", $archive_path), 500);
					}
					
					$this->setVariables($archive_path);
					return [
						"success" => true,
						"date" => Tools::getDateModifiedFile($archive_path)
					];
					
				} else {
					throw new \Exception(sprintf("Unable to find the template file %s", $template_path), 500);
				}
			}
			
			return [
				"success" => false,
			];
		}
		
		private function setVariables($file_path) {
			if(file_exists($file_path)) {
				$file_content = file_get_contents($file_path);
				
				$shortcode_top_filter = "[infocob_crm_products_catalog_top_filters id='" . filter_var($this->catalog_id, FILTER_SANITIZE_NUMBER_INT) . "']";
				$shortcode_left_filter = "[infocob_crm_products_catalog_left_filters id='" . filter_var($this->catalog_id, FILTER_SANITIZE_NUMBER_INT) . "']";
				$shortcode_right_filter = "[infocob_crm_products_catalog_right_filters id='" . filter_var($this->catalog_id, FILTER_SANITIZE_NUMBER_INT) . "']";
				
				$to_find = [
					"{{infocob_crm_products_catalog_top_filters}}",
					"{{infocob_crm_products_catalog_left_filters}}",
					"{{infocob_crm_products_catalog_right_filters}}",
					"{{post_type}}",
				];
				
				$to_replace = [
					$shortcode_top_filter,
					$shortcode_left_filter,
					$shortcode_right_filter,
					$this->post_type,
				];
				
				$file_content = str_replace($to_find, $to_replace, $file_content);
				
				file_put_contents($file_path, $file_content);
			}
		}
		
	}
