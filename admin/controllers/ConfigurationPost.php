<?php
	
	
	namespace Infocob\CRM\Products\Admin\Controllers;
	
	use Infocob\CRM\Products\Admin\Classes\CRON;
	use Infocob\CRM\Products\Admin\Classes\Imports\ProductsLoader;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Dictionnaire;
	use Infocob\CRM\Products\Admin\Classes\Infocob\FamilleTypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Polylang;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ConfigurationPost extends Controller {
		
		protected static $wp_ajax_get_champs_infocob             = [];
		protected static $wp_ajax_get_champs_inventaires_infocob = [];
		
		/*
		 * Meta-boxes
		 */
		
		public function renderHelpTabInfocob() {
			Tools::include("posts/configuration/help-tabs/infocob.php");
		}
		
		public function renderHelpTabMappings() {
			Tools::include("posts/configuration/help-tabs/mappings.php");
		}
		
		public function renderHelpTabPost() {
			Tools::include("posts/configuration/help-tabs/post.php");
		}
		
		public function renderHelpTabInventory() {
			Tools::include("posts/configuration/help-tabs/inventory.php");
		}
		
		public function renderHelpTabFiles() {
			Tools::include("posts/configuration/help-tabs/files.php");
		}
		
		public function renderHelpTabApi() {
			Tools::include("posts/configuration/help-tabs/api.php");
		}
		
		public function renderHelpTabImport() {
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			$structure = get_option('permalink_structure');
			if ($structure === "/%postname%/") {
				$wp_api_url = get_site_url() . "/wp-json/";
			} else {
				$wp_api_url = get_site_url() . "/?rest_route=/";
			}
			
			Tools::include("posts/configuration/help-tabs/import.php", [
				"wp_api_url" => $wp_api_url,
				"post_id"    => $post_id
			]);
		}
		
		public function renderMetaBoxInfocob() {
			/*
			 * Getting data Infocob
			 */
			$infocob_types_produit = [
				""                      => "",
				"PRODUITFICHE"          => Dictionnaire::getTableLibelle(ProduitFiche::$tableCode + 100),
				"PRODUITMODELEFICHE"    => Dictionnaire::getTableLibelle(ProduitModeleFiche::$tableCode + 100),
				"TYPEINVENTAIREPRODUIT" => Dictionnaire::getTableLibelle(TypeInventaireProduit::$tableCode + 100),
			];
			$infocob_groupes_droit = Dictionnaire::getGroupesDeDroit();
			$infocob_groupes_droit = [-1 => esc_html_x("No rights management", "Admin edit configuration post", "infocob-crm-products")] + $infocob_groupes_droit;
			
			
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			// #########
			
			$infocob_groupe_droit = get_post_meta($post_id, "infocob-groupe-droit", true);
			if ($infocob_groupe_droit === false) {
				$infocob_groupe_droit = 2;
			} else {
				$infocob_groupe_droit = (int)$infocob_groupe_droit;
			}
			
			$infocob_type_produit = get_post_meta($post_id, "infocob-type-produit", true);
			$infocob_filters = get_post_meta($post_id, "infocob-filters", true);
			
			$infocob_count_products = 0;
			if (!empty($post_id) && !empty($infocob_type_produit)) {
				$post = get_post($post_id);
				$productsLoader = new ProductsLoader($post);
				$infocob_count_products = $productsLoader->get(true);
			}
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/infocob.php", [
					"infocob_types_produit"  => $infocob_types_produit,
					"infocob_groupes_droit"  => $infocob_groupes_droit,
					"infocob_groupe_droit"   => $infocob_groupe_droit,
					"infocob_count_products" => $infocob_count_products,
					"infocob_type_produit"   => $infocob_type_produit,
					"infocob_filters"        => $infocob_filters
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("Infocob", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxMappings() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			/*
			 * Getting saved data Wordpress
			 */
			$mappings_post_type_disponibles = get_post_types([
				"_builtin" => false,
				"public"   => true
			], 'objects');
			usort($mappings_post_type_disponibles, function ($a, $b) {
				return [Tools::removeStringAccents($a->label), $a->name]
					<=>
					[Tools::removeStringAccents($b->label), $b->name];
			});
			
			$mappings = get_post_meta($post_id, "mappings", true);
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/mappings.php", [
					"mappings_post_type_disponibles" => $mappings_post_type_disponibles,
					"mappings"                       => $mappings
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("Mappings", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxPost() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			$post_meta = get_post_meta($post_id, "post-meta", true);
			$post_yoast = get_post_meta($post_id, "post-yoast", true);
			$post_acf = get_post_meta($post_id, "post-acf", true);
			$post_woocommerce = get_post_meta($post_id, "post-woocommerce", true);
			$post_status = get_post_meta($post_id, "post-status", true);
			if (empty($post_status)) {
				$post_status = "publish";
			}
			$post_status_update = get_post_meta($post_id, "post-status-update", true);
			$post_status_update = ($post_status_update !== false) ? $post_status_update : true;
			$post_deleted_status = get_post_meta($post_id, "post-deleted-status", true);
			if (empty($post_deleted_status)) {
				$post_deleted_status = "draft";
			}
			$post_deleted_status_update = get_post_meta($post_id, "post-deleted-status-update", true);
			$post_deleted_status_update = ($post_deleted_status_update !== false) ? filter_var($post_deleted_status_update, FILTER_VALIDATE_BOOLEAN) : true;
			$post_author = (int)get_post_meta($post_id, "post-author", true);
			$post_author_update = get_post_meta($post_id, "post-author-update", true);
			$post_author_update = ($post_author_update !== false) ? filter_var($post_author_update, FILTER_VALIDATE_BOOLEAN) : true;
			
			$post_title_json = get_post_meta($post_id, "post-title", true);
			$post_title = Tools::decodeConfig($post_title_json);
			$post_title_update = get_post_meta($post_id, "post-title-update", true);
			$post_title_update = ($post_title_update !== false) ? filter_var($post_title_update, FILTER_VALIDATE_BOOLEAN) : true;
			
			/*
			 * Getting saved data Wordpress
			 */
			$post_trash_status = get_post_status_object("trash");
			$post_statuses = array_merge(get_post_statuses(), [$post_trash_status->name => $post_trash_status->label]);
			$post_authors = get_users();
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list")) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/post.php", [
					"post_meta"                  => $post_meta,
					"post_yoast"                 => $post_yoast,
					"post_acf"                   => $post_acf,
					"post_woocommerce"           => $post_woocommerce,
					"post_statuses"              => $post_statuses,
					"post_status"                => $post_status,
					"post_status_update"         => $post_status_update,
					"post_deleted_status"        => $post_deleted_status,
					"post_deleted_status_update" => $post_deleted_status_update,
					"post_authors"               => $post_authors,
					"post_author"                => $post_author,
					"post_author_update"         => $post_author_update,
					"post_title"                 => $post_title,
					"post_title_update"          => $post_title_update,
					"languages"                  => $languages,
					"b64JsonLanguages"           => $b64JsonLanguages,
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("Post", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxInventory() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			/**
			 * Getting saved data
			 */
			$infocob_type_produit = get_post_meta($post_id, "infocob-type-produit", true);
			$inventory_filters = get_post_meta($post_id, "inventory-filters", true);
			$post_inventory = get_post_meta($post_id, "post-inventory", true);
			
			/*
			 * Getting saved data Wordpress
			 */
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list")) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/inventory.php", [
					"infocob_type_produit" => $infocob_type_produit,
					"inventory_filters"    => $inventory_filters,
					"post_inventory"       => $post_inventory,
					"b64JsonLanguages"     => $b64JsonLanguages,
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("Post", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxFiles() {
			/*
			 * Getting saved data Infocob
			 */
			$files_champs_cloudfichier = Dictionnaire::getChamps("CLOUDFICHIER");
			
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			// Local
			$files_use_local = get_post_meta($post_id, "files-use-local", true);
			$files_local_photos_path = get_post_meta($post_id, "files-local-photos-path", true);
			$files_local_photos_path = !empty($files_local_photos_path) ? $files_local_photos_path : "public_html/photos";
			$files_local_photos_name = get_post_meta($post_id, "files-local-photos-name", true);
			$files_local_photos_name = ($files_local_photos_name !== false) ? $files_local_photos_name : "{{ P_NOM }}";
			
			$files_local_files_path = get_post_meta($post_id, "files-local-files-path", true);
			$files_local_files_path = !empty($files_local_files_path) ? $files_local_files_path : "public_html/fichiers/pdf";
			
			// Cloud
			$files_use_cloud = get_post_meta($post_id, "files-use-cloud", true);
			// Photos
			$files_cloud_photos_folder = get_post_meta($post_id, "files-cloud-photos-folder", true);
			$files_cloud_photos_folder = ($files_cloud_photos_folder !== false) ? $files_cloud_photos_folder : "PHOTOS";
			$files_cloud_photos_filename = get_post_meta($post_id, "files-cloud-photos-filename", true);
			$files_cloud_photos_filename = ($files_cloud_photos_filename !== false) ? $files_cloud_photos_filename : "";
			$files_cloud_photos_ext = get_post_meta($post_id, "files-cloud-photos-ext", true);
			$files_cloud_photos_ext = !empty($files_cloud_photos_ext) ? $files_cloud_photos_ext : ["png", "jpg"];
			$files_cloud_photos_order = get_post_meta($post_id, "files-cloud-photos-order", true);
			$files_cloud_photos_order = !empty($files_cloud_photos_order) ? $files_cloud_photos_order : "FC_NOMFICHIER";
			
			$files_cloud_photos_name_json = get_post_meta($post_id, "files-cloud-photos-name", true);
			$files_cloud_photos_name = Tools::decodeConfig($files_cloud_photos_name_json);
			$files_cloud_photos_name_update = get_post_meta($post_id, "files-cloud-photos-name-update", true);
			$files_cloud_photos_name_update = ($files_cloud_photos_name_update !== false) ? $files_cloud_photos_name_update : true;
			
			$files_cloud_photos_alt_text_json = get_post_meta($post_id, "files-cloud-photos-alt-text", true);
			$files_cloud_photos_alt_text = Tools::decodeConfig($files_cloud_photos_alt_text_json);
			$files_cloud_photos_alt_text_update = get_post_meta($post_id, "files-cloud-photos-alt-text-update", true);
			$files_cloud_photos_alt_text_update = ($files_cloud_photos_alt_text_update !== false) ? $files_cloud_photos_alt_text_update : true;
			
			$files_cloud_photos_legend_json = get_post_meta($post_id, "files-cloud-photos-legend", true);
			$files_cloud_photos_legend = Tools::decodeConfig($files_cloud_photos_legend_json);
			$files_cloud_photos_legend_update = get_post_meta($post_id, "files-cloud-photos-legend-update", true);
			$files_cloud_photos_legend_update = ($files_cloud_photos_legend_update !== false) ? $files_cloud_photos_legend_update : true;
			
			$files_cloud_photos_description_json = get_post_meta($post_id, "files-cloud-photos-description", true);
			$files_cloud_photos_description = Tools::decodeConfig($files_cloud_photos_description_json);
			$files_cloud_photos_description_update = get_post_meta($post_id, "files-cloud-photos-description-update", true);
			$files_cloud_photos_description_update = ($files_cloud_photos_description_update !== false) ? $files_cloud_photos_description_update : true;
			
			//Documents
			$files_cloud_files_folder = get_post_meta($post_id, "files-cloud-files-folder", true);
			$files_cloud_files_folder = ($files_cloud_files_folder !== false) ? $files_cloud_files_folder : "DOCUMENTS";
			$files_cloud_files_filename = get_post_meta($post_id, "files-cloud-files-filename", true);
			$files_cloud_files_filename = !empty($files_cloud_files_filename) ? $files_cloud_files_filename : "";
			$files_cloud_files_ext = get_post_meta($post_id, "files-cloud-files-ext", true);
			$files_cloud_files_ext = !empty($files_cloud_files_ext) ? $files_cloud_files_ext : ["pdf"];
			$files_cloud_files_order = get_post_meta($post_id, "files-cloud-files-order", true);
			$files_cloud_files_order = !empty($files_cloud_files_order) ? $files_cloud_files_order : "FC_NOMFICHIER";
			
			$files_cloud_files_name_json = get_post_meta($post_id, "files-cloud-files-name", true);
			$files_cloud_files_name = Tools::decodeConfig($files_cloud_files_name_json);
			$files_cloud_files_name_update = get_post_meta($post_id, "files-cloud-files-name-update", true);
			$files_cloud_files_name_update = ($files_cloud_files_name_update !== false) ? $files_cloud_files_name_update : true;
			
			$files_cloud_files_alt_text_json = get_post_meta($post_id, "files-cloud-files-alt-text", true);
			$files_cloud_files_alt_text = Tools::decodeConfig($files_cloud_files_alt_text_json);
			$files_cloud_files_alt_text_update = get_post_meta($post_id, "files-cloud-files-alt-text-update", true);
			$files_cloud_files_alt_text_update = ($files_cloud_files_alt_text_update !== false) ? $files_cloud_files_alt_text_update : true;
			
			$files_cloud_files_legend_json = get_post_meta($post_id, "files-cloud-files-legend", true);
			$files_cloud_files_legend = Tools::decodeConfig($files_cloud_files_legend_json);
			$files_cloud_files_legend_update = get_post_meta($post_id, "files-cloud-files-legend-update", true);
			$files_cloud_files_legend_update = ($files_cloud_files_legend_update !== false) ? $files_cloud_files_legend_update : true;
			
			$files_cloud_files_description_json = get_post_meta($post_id, "files-cloud-files-description", true);
			$files_cloud_files_description = Tools::decodeConfig($files_cloud_files_description_json);
			$files_cloud_files_description_update = get_post_meta($post_id, "files-cloud-files-description-update", true);
			$files_cloud_files_description_update = ($files_cloud_files_description_update !== false) ? $files_cloud_files_description_update : true;
			
			$files_cloud_meta = get_post_meta($post_id, "files-cloud-meta", true);
			$photos_cloud_meta = get_post_meta($post_id, "photos-cloud-meta", true);
			
			$wp_lang = substr(get_locale(), 0, 2);
			$languages = [$wp_lang];
			if (function_exists("pll_languages_list")) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			$b64JsonLanguages = base64_encode(json_encode($languages ?? []));
			
			if ($post_id === "") {
				$files_use_cloud = true; // Défaut
			}
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/files.php", [
					"files_champs_cloudfichier"             => $files_champs_cloudfichier,
					
					// Local
					"files_use_local"                       => $files_use_local,
					// Photos
					"files_local_photos_path"               => $files_local_photos_path,
					"files_local_photos_name"               => $files_local_photos_name,
					// Documents
					"files_local_files_path"                => $files_local_files_path,
					
					// Cloud
					"files_use_cloud"                       => $files_use_cloud,
					// Photos
					"files_cloud_photos_folder"             => $files_cloud_photos_folder,
					"files_cloud_photos_filename"           => $files_cloud_photos_filename,
					"files_cloud_photos_ext"                => $files_cloud_photos_ext,
					"files_cloud_photos_order"              => $files_cloud_photos_order,
					"files_cloud_photos_name"               => $files_cloud_photos_name,
					"files_cloud_photos_name_update"        => $files_cloud_photos_name_update,
					"files_cloud_photos_alt_text"           => $files_cloud_photos_alt_text,
					"files_cloud_photos_alt_text_update"    => $files_cloud_photos_alt_text_update,
					"files_cloud_photos_legend"             => $files_cloud_photos_legend,
					"files_cloud_photos_legend_update"      => $files_cloud_photos_legend_update,
					"files_cloud_photos_description"        => $files_cloud_photos_description,
					"files_cloud_photos_description_update" => $files_cloud_photos_description_update,
					// Documents
					"files_cloud_files_folder"              => $files_cloud_files_folder,
					"files_cloud_files_filename"            => $files_cloud_files_filename,
					"files_cloud_files_ext"                 => $files_cloud_files_ext,
					"files_cloud_files_order"               => $files_cloud_files_order,
					"files_cloud_files_name"                => $files_cloud_files_name,
					"files_cloud_files_name_update"         => $files_cloud_files_name_update,
					"files_cloud_files_alt_text"            => $files_cloud_files_alt_text,
					"files_cloud_files_alt_text_update"     => $files_cloud_files_alt_text_update,
					"files_cloud_files_legend"              => $files_cloud_files_legend,
					"files_cloud_files_legend_update"       => $files_cloud_files_legend_update,
					"files_cloud_files_description"         => $files_cloud_files_description,
					"files_cloud_files_description_update"  => $files_cloud_files_description_update,
					
					"files_cloud_meta"  => $files_cloud_meta,
					"photos_cloud_meta" => $photos_cloud_meta,
					"b64JsonLanguages"  => $b64JsonLanguages,
					"languages"         => $languages,
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("Files", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxApi() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? (int)filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			$api_authorize_ip = get_post_meta($post_id, "api-authorize-ip", true);
			$my_ip = Tools::getMyIp();
			
			$api_cron_enable = get_post_meta($post_id, "api-cron-enable", true);
			$api_cron_recurrence = get_post_meta($post_id, "api-cron-recurrence", true);
			if (empty($api_cron_recurrence)) {
				$api_cron_recurrence = "hourly";
			}
			
			$cron_timestamp = wp_next_scheduled("icp_cron_import_products", [$post_id]);
			if ($cron_timestamp !== false) {
				$api_cron_date = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
				$api_cron_date = $api_cron_date->setTimestamp($cron_timestamp);
				$api_cron_date = $api_cron_date->format("d-m-Y H:i");
			} else {
				$api_cron_date = false;
			}
			
			$api_cron_recurrences = wp_get_schedules();
			
			$rest_api_enable = get_post_meta($post_id, "rest-api-key-enable", true);
			$rest_api_key = get_post_meta($post_id, "rest-api-key", true);
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/api.php", [
					"api_authorize_ip"     => $api_authorize_ip,
					"my_ip"                => $my_ip,
					"api_cron_enable"      => $api_cron_enable,
					"api_cron_recurrence"  => $api_cron_recurrence,
					"api_cron_recurrences" => $api_cron_recurrences,
					"api_cron_date"        => $api_cron_date,
					"rest_api_key_enable"  => $rest_api_enable,
					"rest_api_key"         => $rest_api_key,
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("API", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		public function renderMetaBoxImport() {
			/*
			 * Getting saved data
			 */
			$post_id = (!isset($_GET["post"]) || !empty($_GET["post"])) ? (int)filter_var($_GET["post"] ?? "", FILTER_SANITIZE_NUMBER_INT) : false;
			
			/*
			 * Display view
			 */
			if ($post_id !== false) {
				Tools::include("posts/configuration/meta-boxes/import.php", [
					"post_id" => $post_id,
				]);
				
			} else {
				Tools::include("posts/configuration/meta-boxes/error.php", [
					"metabox" => esc_html_x("API", "Notice error", "infocob-crm-products")
				]);
			}
		}
		
		/**
		 * @param $post_id
		 */
		public function save($post_id) {
			/**
			 * Meta box Infocob
			 */
			update_post_meta($post_id, "infocob-groupe-droit", (int)sanitize_text_field($_POST["infocob-groupe-droit"] ?? 2));
			update_post_meta($post_id, "infocob-type-produit", sanitize_text_field($_POST["infocob-type-produit"] ?? ""));
			update_post_meta($post_id, "infocob-filters", sanitize_text_field($_POST["infocob-filters"] ?? ""));
			
			/**
			 * Meta box Mappings
			 */
			update_post_meta($post_id, "mappings", sanitize_text_field($_POST["mappings"] ?? ""));
			
			/**
			 * Meta box Post
			 */
			update_post_meta($post_id, "post-status", sanitize_text_field($_POST["post-status"] ?? "publish"));
			update_post_meta($post_id, "post-status-update", (bool)filter_var(($_POST["post-status-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "post-deleted-status", sanitize_text_field($_POST["post-deleted-status"] ?? "draft"));
			update_post_meta($post_id, "post-deleted-status-update", (bool)filter_var(($_POST["post-deleted-status-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "post-author", (int)sanitize_text_field($_POST["post-author"] ?? ""));
			update_post_meta($post_id, "post-author-update", (bool)filter_var(($_POST["post-author-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "post-title", Tools::encodeConfig($_POST["post-title"] ?? []));
			update_post_meta($post_id, "post-title-update", (bool)filter_var(($_POST["post-title-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "post-meta", sanitize_text_field($_POST["post-meta"] ?? ""));
			update_post_meta($post_id, "post-yoast", sanitize_text_field($_POST["post-yoast"] ?? ""));
			update_post_meta($post_id, "post-acf", sanitize_text_field($_POST["post-acf"] ?? ""));
			update_post_meta($post_id, "post-woocommerce", sanitize_text_field($_POST["post-woocommerce"] ?? ""));
			
			/**
			 * Meta box Inventory
			 */
			update_post_meta($post_id, "inventory-filters", sanitize_text_field($_POST["inventory-filters"] ?? ""));
			update_post_meta($post_id, "post-inventory", sanitize_text_field($_POST["post-inventory"] ?? ""));
			
			/**
			 * Meta box files
			 */
			update_post_meta($post_id, "files-use-local", (bool)filter_var(($_POST["files-use-local"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-local-photos-path", trim(sanitize_text_field($_POST["files-local-photos-path"] ?? "public_html/photos"), "/\\"));
			update_post_meta($post_id, "files-local-files-path", trim(sanitize_text_field($_POST["files-local-files-path"] ?? "public_html/fichiers/pdf"), "/\\"));
			update_post_meta($post_id, "files-local-photos-name", sanitize_text_field($_POST["files-local-photos-name"] ?? ""));
			
			update_post_meta($post_id, "files-use-cloud", (bool)filter_var(($_POST["files-use-cloud"] ?? false), FILTER_VALIDATE_BOOLEAN));
			// Documents cloud
			update_post_meta($post_id, "files-cloud-files-folder", trim(sanitize_text_field($_POST["files-cloud-files-folder"] ?? ""), "/\\"));
			update_post_meta($post_id, "files-cloud-files-filename", sanitize_text_field($_POST["files-cloud-files-filename"] ?? ""));
			update_post_meta($post_id, "files-cloud-files-ext", Tools::sanitize_recursive($_POST["files-cloud-files-ext"] ?? []));
			update_post_meta($post_id, "files-cloud-files-order", sanitize_text_field($_POST["files-cloud-files-order"] ?? ""));
			update_post_meta($post_id, "files-cloud-files-name", Tools::encodeConfig($_POST["files-cloud-files-name"] ?? []));
			update_post_meta($post_id, "files-cloud-files-name-update", (bool)filter_var(($_POST["files-cloud-files-name-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-files-alt-text", Tools::encodeConfig($_POST["files-cloud-files-alt-text"] ?? []));
			update_post_meta($post_id, "files-cloud-files-alt-text-update", (bool)filter_var(($_POST["files-cloud-files-alt-text-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-files-legend", Tools::encodeConfig($_POST["files-cloud-files-legend"] ?? []));
			update_post_meta($post_id, "files-cloud-files-legend-update", (bool)filter_var(($_POST["files-cloud-files-legend-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-files-description", Tools::encodeConfig($_POST["files-cloud-files-description"] ?? []));
			update_post_meta($post_id, "files-cloud-files-description-update", (bool)filter_var(($_POST["files-cloud-files-description-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			
			// Photos cloud
			update_post_meta($post_id, "files-cloud-photos-folder", trim(sanitize_text_field($_POST["files-cloud-photos-folder"] ?? ""), "/\\"));
			update_post_meta($post_id, "files-cloud-photos-filename", sanitize_text_field($_POST["files-cloud-photos-filename"] ?? ""));
			update_post_meta($post_id, "files-cloud-photos-ext", Tools::sanitize_recursive($_POST["files-cloud-photos-ext"] ?? []));
			update_post_meta($post_id, "files-cloud-photos-order", Tools::sanitize_recursive($_POST["files-cloud-photos-order"] ?? ""));
			update_post_meta($post_id, "files-cloud-photos-name", Tools::encodeConfig($_POST["files-cloud-photos-name"] ?? []));
			update_post_meta($post_id, "files-cloud-photos-name-update", (bool)filter_var(($_POST["files-cloud-photos-name-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-photos-alt-text", Tools::encodeConfig($_POST["files-cloud-photos-alt-text"] ?? []));
			update_post_meta($post_id, "files-cloud-photos-alt-text-update", (bool)filter_var(($_POST["files-cloud-photos-alt-text-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-photos-legend", Tools::encodeConfig($_POST["files-cloud-photos-legend"] ?? []));
			update_post_meta($post_id, "files-cloud-photos-legend-update", (bool)filter_var(($_POST["files-cloud-photos-legend-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "files-cloud-photos-description", Tools::encodeConfig($_POST["files-cloud-photos-description"] ?? []));
			update_post_meta($post_id, "files-cloud-photos-description-update", (bool)filter_var(($_POST["files-cloud-photos-description-update"] ?? false), FILTER_VALIDATE_BOOLEAN));
			
			update_post_meta($post_id, "files-cloud-meta", sanitize_text_field($_POST["files-cloud-meta"] ?? ""));
			update_post_meta($post_id, "photos-cloud-meta", sanitize_text_field($_POST["photos-cloud-meta"] ?? ""));
			
			/**
			 * Meta box api
			 */
			$api_cron_enable = (bool)filter_var(($_POST["api-cron-enable"] ?? false), FILTER_VALIDATE_BOOLEAN);
			$api_cron_recurrence = sanitize_text_field($_POST["api-cron-recurrence"] ?? "");
			
			$cron_scheduled_timestamp = wp_next_scheduled("icp_cron_import_products", [$post_id]);
			
			$current_post_status = get_post_status($post_id);
			
			// if cron enable
			if ($api_cron_enable && $current_post_status === "publish") {
				$previous_api_cron_recurrence = get_post_meta($post_id, "api-cron-recurrence", true);
				if (($api_cron_recurrence !== $previous_api_cron_recurrence) || ($cron_scheduled_timestamp === false)) {
					CRON::unschedule($post_id);
					CRON::schedule($post_id, $api_cron_recurrence);
				}
				
			} else if ($cron_scheduled_timestamp !== false) {
				CRON::unschedule($post_id);
			}
			
			update_post_meta($post_id, "api-authorize-ip", sanitize_text_field($_POST["api-authorize-ip"] ?? ""));
			update_post_meta($post_id, "api-cron-enable", ($api_cron_enable && $current_post_status === "publish"));
			update_post_meta($post_id, "api-cron-recurrence", $api_cron_recurrence);
			
			update_post_meta($post_id, "rest-api-key-enable", (bool)filter_var(($_POST["rest-api-key-enable"] ?? false), FILTER_VALIDATE_BOOLEAN));
			update_post_meta($post_id, "rest-api-key", sanitize_text_field($_POST["rest-api-key"] ?? ""));
		}
		
		/*
		 * CRON
		 */
		public function onPostDelete($post_id, $post) {
			if ($post instanceof \WP_Post) {
				if ($post->post_type === "icp-configuration") {
					CRON::unschedule($post->ID);
				}
			}
		}
		
		public function onPostTrash($post_id) {
			$post_type = get_post_type($post_id);
			if ($post_type === "icp-configuration") {
				CRON::unschedule($post_id);
			}
		}
		
		public function onPostStatusChange($new_status, $old_status, $post) {
			if ($post instanceof \WP_Post && !empty($post->ID)) {
				$post_type = get_post_type($post);
				if ($post_type === "icp-configuration") {
					
					if ($new_status !== $old_status && $new_status !== "publish" && $new_status !== "trash") {
						CRON::unschedule($post->ID);
					}
				}
			}
		}
		
		/*
		 * Ajax requests
		 */
		
		public function wp_ajax_get_champs_infocob() {
			$infocob_champs_produit = false;
			if (isset($_POST["module"])) {
				$module = sanitize_text_field($_POST["module"]);
				if (isset(static::$wp_ajax_get_champs_infocob[$module])) {
					$infocob_champs_produit = static::$wp_ajax_get_champs_infocob[$module];
				} else {
					$infocob_champs_produit = Dictionnaire::getChamps($module);
					
					static::$wp_ajax_get_champs_infocob[$module] = $infocob_champs_produit;
				}
			}
			
			if ($infocob_champs_produit !== false && check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($infocob_champs_produit);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_champs_infocob", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_champs_inventaires_infocob() {
			if (!empty(static::$wp_ajax_get_champs_inventaires_infocob)) {
				$champs = static::$wp_ajax_get_champs_inventaires_infocob;
			} else {
				$champs_inventaires = Dictionnaire::getChamps(InventaireProduit::$tableName);
				$champs_type_inventaires = Dictionnaire::getChamps(TypeInventaireProduit::$tableName);
				$champs_famille_type_inventaires = Dictionnaire::getChamps(FamilleTypeInventaireProduit::$tableName);
				
				$champs = array_merge($champs_inventaires, $champs_type_inventaires, $champs_famille_type_inventaires);
				
				static::$wp_ajax_get_champs_inventaires_infocob = $champs;
			}
			
			if ($champs !== false && check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($champs);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_champs_inventaires_infocob", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_post_types_from_taxonomy() {
			$post_types = false;
			if (isset($_POST["taxonomy"])) {
				$taxonomy_object = get_taxonomy(sanitize_text_field($_POST["taxonomy"]));
				$post_type_names = $taxonomy_object->object_type;
				$post_types = [];
				foreach ($post_type_names as $post_type_name) {
					$post_types[] = get_post_type_object($post_type_name);
				}
			}
			
			if ($post_types !== false && check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($post_types);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_post_types_from_taxonomy", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_taxonomies_from_post_type() {
			$taxonomies = false;
			if (isset($_POST["post_type"])) {
				$taxonomies = [];
				$taxonomies_object = get_object_taxonomies(sanitize_text_field($_POST["post_type"]), 'objects');
				
				foreach ($taxonomies_object as $taxonomy) {
					if ($taxonomy->public === true && $taxonomy->_builtin === false) {
						$taxonomies[] = $taxonomy;
					}
				}
			}
			
			if ($taxonomies !== false && check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($taxonomies);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_taxonomies_from_post_type", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_categories_from_taxonomy() {
			$categories = false;
			if (isset($_POST["taxonomy"])) {
				$taxonomy = sanitize_text_field($_POST["taxonomy"]);
				
				if (function_exists("pll_default_language") && function_exists("pll_languages_list") && function_exists("pll_get_term")) {
					$default_language = pll_default_language();
					
					$categories = [];
					$categories_object = Tools::getTaxonomyHierarchy($taxonomy, 0, $default_language, true);
					
					foreach ($categories_object as $category) {
						$languages = pll_languages_list([
							'hide_empty' => false,
							"fields"     => "slug"
						]);
						
						$category->term_ids = [$category->term_id];
						foreach ($languages as $language) {
							if ($language !== $default_language) {
								$pll_category_id = pll_get_term($category->term_id, $language);
								if ($pll_category_id !== false) {
									$pll_category = get_term($pll_category_id, $taxonomy);
									if($pll_category instanceof \WP_Term) {
										$category->term_ids[] = $pll_category->term_id;
									}
								}
							}
						}
						
						$categories[] = $category;
					}
				} else {
					$categories = Tools::getTaxonomyHierarchy($taxonomy, 0, "", true);
				}
				
				foreach ($categories as &$category) {
					$level = Tools::getLevelParentCategory($category, $category->taxonomy);
					$category->level = $level;
				}
			}
			
			if ($categories !== false && check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($categories);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_categories_from_taxonomy", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_post_types() {
			$post_type_disponibles = get_post_types([
				"_builtin" => false,
				"public"   => true
			], 'objects');
			usort($post_type_disponibles, function ($a, $b) {
				return [Tools::removeStringAccents($a->label), $a->name]
					<=>
					[Tools::removeStringAccents($b->label), $b->name];
			});
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($post_type_disponibles);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_post_types", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_langs() {
			$post_type = false;
			if (isset($_GET["post_type"]) && rest_sanitize_boolean($_GET["post_type"]) !== false) {
				$post_type = sanitize_text_field($_GET["post_type"]);
			}
			
			$languages = false;
			if (function_exists("pll_languages_list") && (($post_type === false) || ($post_type !== false && Polylang::isPostTypeMultilanguages($post_type)))) {
				$languages = pll_languages_list([
					'hide_empty' => false,
					"fields"     => "slug"
				]);
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($languages);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_langs", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_acf_field_groups_from_post_type() {
			$acf_field_groups = false;
			if (function_exists("get_field_objects") && isset($_POST["post_type"])) {
				$post_type = sanitize_text_field($_POST["post_type"] ?? "");
				$acf_field_groups = [];
				$acf_field_groups_object = get_posts([
					"post_type"   => "acf-field-group",
					"numberposts" => -1
				]);
				
				foreach ($acf_field_groups_object as $acf_field_group) {
					$post_content = $acf_field_group->post_content;
					$field = unserialize($post_content);
					
					if ($field !== false) {
						$locations = $field["location"] ?? [];
						
						foreach ($locations as $location) {
							foreach ($location as $condition) {
								$operator = $condition["operator"] ?? "";
								$param = $condition["param"] ?? "";
								$value = $condition["value"] ?? "";
								
								if (strcasecmp($param, "post_type") === 0) {
									if (strcasecmp($operator, "==") === 0) {
										if (strcasecmp($post_type, $value) === 0) {
											$acf_field_groups[] = $acf_field_group;
										}
									}
								}
							}
						}
					}
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($acf_field_groups);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_acf_field_groups_from_post_type", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_acf_fields_from_group() {
			$acf_fields = false;
			if (isset($_POST["post_id"]) && function_exists("get_field_objects")) {
				$acf_fields = [];
				$post_id = (int)sanitize_text_field($_POST["post_id"]);
				
				$acf_posts = get_posts([
					"post_type"   => "acf-field",
					"post_parent" => $post_id,
					"numberposts" => -1
				]);
				
				foreach ($acf_posts as $acf_post) {
					$post_content = $acf_post->post_content;
					$field = unserialize($post_content);
					
					if ($field !== false) {
						$field_title = $acf_post->post_title;
						$field_name = $acf_post->post_name;
						$type = $field["type"] ?? "";
						
						if (in_array($type, [
							"text",
							"textarea",
							"number",
							"email",
							"url",
							"password",
							"select",
							"true_false"
						])) {
							$acf_fields[] = [
								"title" => $field_title,
								"name"  => $field_name
							];
						}
					}
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($acf_fields);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_acf_fields_from_group", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_acf_repeater_fields_from_group() {
			$acf_fields = false;
			if (isset($_POST["post_id"]) && function_exists("get_field_objects")) {
				$acf_fields = [];
				$post_id = (int)sanitize_text_field($_POST["post_id"]);
				
				$acf_posts = get_posts([
					"post_type"   => "acf-field",
					"post_parent" => $post_id,
					"numberposts" => -1
				]);
				
				foreach ($acf_posts as $acf_post) {
					$post_content = $acf_post->post_content;
					$field = unserialize($post_content);
					
					if ($field !== false) {
						$field_title = $acf_post->post_title;
						$field_name = $acf_post->post_name;
						$type = $field["type"] ?? "";
						
						if (in_array($type, [
							"repeater",
						])) {
							$acf_fields[] = [
								"title" => $field_title,
								"name"  => $field_name
							];
						}
					}
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($acf_fields);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_acf_repeater_fields_from_group", "infocob-crm-products"));
			}
		}
		
		public function wp_ajax_get_acf_sub_fields_from_field() {
			$acf_fields = false;
			if (isset($_POST["acf_field"]) && isset($_POST["post_id"]) && function_exists("get_field_objects")) {
				$acf_fields = [];
				$acf_field = sanitize_text_field($_POST["acf_field"]);
				$post_id = (int)sanitize_text_field($_POST["post_id"]);
				
				$repeater_field = get_field_object($acf_field, $post_id);
				foreach (($repeater_field["sub_fields"] ?? []) as $field) {
					$sub_field_title = $field["label"] ?? "";
					$sub_field_name = $field["key"] ?? "";
					
					$acf_fields[] = [
						"title" => $sub_field_title,
						"name"  => $sub_field_name
					];
				}
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($acf_fields);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_get_acf_sub_fields_from_field", "infocob-crm-products"));
			}
		}
		
		/**
		 * Start import
		 * @return void
		 */
		public function wp_ajax_start_import() {
			$import_started = false;
			if (isset($_POST["post_id"])) {
				$post_id = (int)sanitize_text_field($_POST["post_id"]);
				
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
				
				$import_started = true;
			}
			
			if (check_ajax_referer('icp-security-nonce', 'security', false) === 1) {
				wp_send_json_success($import_started);
			} else {
				wp_send_json_error(esc_html_x("Unable to retrieve data", "wp_ajax_start_import", "infocob-crm-products"));
			}
		}
		
		/*
		 * Utilities
		 */
		
		/**
		 * @param $post_id
		 *
		 * @return array
		 */
		public static function getUsedPostTypes($post_id) {
			$post_types = [];
			$configBase64 = get_post_meta($post_id, "mappings", true);
			if (!empty($configBase64)) {
				$config = Tools::decodeConfig($configBase64);
				if (!empty($config) && is_array($config)) {
					foreach ($config["rows"] ?? [] as $row) {
						$post = $row["post"] ?? [];
						if (!empty($post["post_type"])) {
							$post_types[] = $post["post_type"];
						}
					}
				}
			}
			
			return array_unique($post_types);
		}
	}
