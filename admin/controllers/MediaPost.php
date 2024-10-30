<?php
	
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class MediaPost extends Controller {
		/*
		 * Meta-boxes
		 */
		
		public function renderMetaBoxMedia() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			$media_infocob_code = get_post_meta($post_id, ProductMeta::FC_CODE_META_KEY, true);
			$media_order = get_post_meta($post_id, ProductMeta::FC_ORDER_META_KEY, true);
			$media_upload_date = get_post_meta($post_id, ProductMeta::FC_DATE_UPLOAD_META_KEY, true);
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/media/meta-boxes/media.php", [
					"media_infocob_code" => $media_infocob_code,
					"media_order"        => $media_order,
					"media_upload_date"  => $media_upload_date,
				]);
				
			} else {
				Tools::include("posts/media/meta-boxes/error.php", [
					"metabox" => esc_html_x("Media", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		/**
		 * @param $post_id
		 */
		public function save($post_id) {
			/**
			 * Meta box media
			 */
			update_post_meta($post_id, ProductMeta::FC_ORDER_META_KEY, sanitize_text_field($_POST["icp-media-fc"] ?? ""));
		}
	}
