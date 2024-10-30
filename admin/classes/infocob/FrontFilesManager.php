<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FrontFilesManager {
		protected int    $post_id;
		protected string $infocob_id;
		protected int    $config_id;
		
		public function __construct($post_id) {
			$this->post_id = $post_id;
			$this->infocob_id = get_post_meta($post_id, ProductMeta::P_CODE_META_KEY, true);
			$this->config_id = (int)get_post_meta($post_id, ProductMeta::P_ID_IMPORT_META_KEY, true);
		}
		
		/**
		 * @param $number int Only works with cloud files
		 *
		 * @return array
		 */
		public function getFiles($number = -1) {
			if (empty($this->infocob_id)) {
				trigger_error("Unable to retrieve files, product not linked with Infocob", E_USER_WARNING);
				
				return [];
			}
			
			$files_use_local = get_post_meta($this->config_id, "files-use-local", true);
			$files_use_cloud = get_post_meta($this->config_id, "files-use-cloud", true);
			
			if ($files_use_cloud) {
				$extensions = get_post_meta($this->config_id, "files-cloud-files-ext", true);
				$mime_types = Tools::getMimeTypesFromExtensions($extensions);
				$default_mime_types = [
					"application/pdf",
					"application/msword",
					"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				];
				$mime_types = !empty($mime_types) ? $mime_types : $default_mime_types;
				
				$wp_query_icp_files = new \WP_Query([
					'post_parent'    => $this->post_id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'posts_per_page' => $number,
					'post_mime_type' => $mime_types,
					'meta_query'     => [
						'relation' => 'AND',
						[
							'key'     => ProductMeta::FC_TYPE_META_KEY,
							'value'   => 'documents',
							'compare' => '=',
						]
					],
					'orderby'        => 'meta_value',
					'meta_key'       => ProductMeta::FC_ORDER_META_KEY,
				]);
				
				// The Loop
				$files = [];
				if ($wp_query_icp_files->have_posts()) {
					while ($wp_query_icp_files->have_posts()) {
						$wp_query_icp_files->the_post();
						
						$alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true);
						
						$files[] = [
							"ID"      => get_the_ID(),
							"title"   => get_the_title(),
							"content" => get_the_content(),
							"alt"     => $alt,
							"url"        => wp_get_attachment_url(get_the_ID()),
							"path"       => get_attached_file(get_the_ID()),
							"caption"    => wp_get_attachment_caption(get_the_ID()),
						];
					}
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				
				return $files;
				
			} else if ($files_use_local) {
				$folderFilesPath = trim(get_post_meta($this->config_id, "files-local-files-path", true), "/\\");
				
				$folderPath = $folderFilesPath . "/" . $this->infocob_id . "/";
				$folderURL = get_home_url() . "/" . $folderPath;
				
				$frontFilesManager = new LocalFilesManager();
				return $frontFilesManager->exploreDirectory($folderPath, $folderURL, "");
				
			} else {
				return [];
			}
		}
	}
