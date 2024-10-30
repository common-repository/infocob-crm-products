<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\LocalImagesManager;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FrontImagesManager {
		protected int    $post_id;
		protected string $infocob_id;
		protected int    $config_id;
		
		protected static array $imagesNames = [];
		
		/**
		 * @var LocalImagesManager|null
		 */
		protected $localImagesManager = null;
		
		public function __construct($post_id) {
			$this->post_id = $post_id;
			$this->infocob_id = get_post_meta($post_id, ProductMeta::P_CODE_META_KEY, true);
			$this->config_id = (int)get_post_meta($post_id, ProductMeta::P_ID_IMPORT_META_KEY, true);
		}
		
		/**
		 * @param $sizes  array
		 * @param $number int Only works with cloud files
		 *
		 * @return array
		 */
		public function getImages($sizes = [], $number = -1, $inf_sizes = []) {
			if (empty($this->infocob_id)) {
				trigger_error("Unable to retrieve images, product not linked with Infocob", E_USER_WARNING);
				
				return [];
			}
			
			if (empty($sizes)) {
				$sizes = ["full"];
			} elseif (is_string($sizes)) {
				$sizes = [$sizes];
			}
			
			$files_use_local = get_post_meta($this->config_id, "files-use-local", true);
			$files_use_cloud = get_post_meta($this->config_id, "files-use-cloud", true);
			
			if ($files_use_cloud) {
				$extensions = get_post_meta($this->config_id, "files-cloud-photos-ext", true);
				$mime_types = Tools::getMimeTypesFromExtensions($extensions);
				$default_mime_types = [
					"image/jpeg",
					"image/gif",
					"image/png",
					"image/bmp",
					"image/tiff",
					"image/webp",
					"image/x-icon",
					"image/heic",
				];
				$mime_types = !empty($mime_types) ? $mime_types : $default_mime_types;
				
				$wp_query_icp_images = new \WP_Query([
					'post_parent'    => $this->post_id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'posts_per_page' => $number,
					'post_mime_type' => $mime_types,
					'meta_query'     => [
						'relation' => 'AND',
						[
							'key'     => ProductMeta::FC_TYPE_META_KEY,
							'value'   => 'photos',
							'compare' => '=',
						]
					],
					'orderby'        => 'meta_value',
					'meta_key'       => ProductMeta::FC_ORDER_META_KEY,
				]);
				
				// The Loop
				$images = [];
				if ($wp_query_icp_images->have_posts()) {
					while ($wp_query_icp_images->have_posts()) {
						$wp_query_icp_images->the_post();
						
						$alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true);
						
						foreach ($sizes as $size) {
							$images[$size][] = [
								"ID"      => get_the_ID(),
								"title"   => get_the_title(),
								"content" => get_the_content(),
								"alt"     => $alt,
								"caption" => wp_get_attachment_caption(get_the_ID()),
								"url"     => wp_get_attachment_image_url(get_the_ID(), $size),
								"path"    => get_attached_file(get_the_ID()),
								"thumb_url"  => wp_get_attachment_thumb_url(get_the_ID()),
							];
						}
					}
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				
				return $images;
				
			} else if ($files_use_local) {
				if ($this->localImagesManager === null) {
					$this->loadImageManager();
				}
				
				return $this->localImagesManager->getThumbs($sizes, $inf_sizes);
			} else {
				return [];
			}
		}
		
		/**
		 * Use for local files only
		 */
		protected function loadImageManager() {
			if (empty($this->localImagesManager)) {
				$productName = get_post_meta($this->post_id, ProductMeta::P_LOCAL_PHOTO_NAME, true);
				$folderPhotoPath = trim(get_post_meta($this->config_id, "files-local-photos-path", true), "/\\");
				
				$folderPath = $folderPhotoPath . "/" . $this->infocob_id . "/";
				$folderURL = get_home_url() . "/" . $folderPath;
				
				if(file_exists($folderPath)) {
					foreach (glob(trim($folderPath, "/\\") . "/*") as $filename) {
						if(preg_match("/^(l_)?(out|in|plan)[0-9]+\.(jpg|png)/mi", basename($filename)) === 1) {
							static::$imagesNames[] = basename($filename);
						}
					}
				}
				
				$this->localImagesManager = new LocalImagesManager($folderPath, $folderURL, static::$imagesNames, $productName);
			}
		}
	}
