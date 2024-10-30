<?php
	
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	
	if(!function_exists('icp_get_ID')) {
		function icp_get_ID($post_id = "") {
			$ID = false;
			$post_id = empty($post_id) ? get_the_ID() : $post_id;
			if (!empty($post_id)) {
				$ID = get_post_meta($post_id, ProductMeta::P_CODE_META_KEY, true);
			}
			return $ID;
		}
	}
	
	if(!function_exists('icp_is_del')) {
		function icp_is_del($post_id = "") {
			$is_del = false;
			$post_id = empty($post_id) ? get_the_ID() : $post_id;
			if (!empty($post_id)) {
				$is_del = (bool)get_post_meta($post_id, ProductMeta::P_SUPP_META_KEY, true);
			}
			return $is_del;
		}
	}
	
	if(!function_exists('icp_get_images')) {
		function icp_get_images($post_id = "", $sizes = [], $inf_sizes = []) {
			$images = [];
			$post_id = empty($post_id) ? get_the_ID() : $post_id;
			if (!empty($post_id)) {
				$frontImagesManager = new \Infocob\CRM\Products\Admin\Classes\Infocob\FrontImagesManager($post_id);
				$images = $frontImagesManager->getImages($sizes, -1, $inf_sizes);
			}
			return $images;
		}
	}
	
	if(!function_exists('icp_get_files')) {
		function icp_get_files($post_id = "") {
			$files = [];
			$post_id = empty($post_id) ? get_the_ID() : $post_id;
			if (!empty($post_id)) {
				$frontImagesManager = new \Infocob\CRM\Products\Admin\Classes\Infocob\FrontFilesManager($post_id);
				$files = $frontImagesManager->getFiles();
			}
			return $files;
		}
	}
?>
