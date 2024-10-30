<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\CloudFichier;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\ChampLibre;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\CloudFichierManager;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use PDO;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsFilesManager {
		
		private ?\WP_Post  $post;
		private ?InfocobDB $db;
		private ?PDO       $wp_db;
		
		private array $config_post = [];
		private array $products;
		
		private string $post_author = "";
		private string $post_author_update = "";
		
		private string $mode                     = "";
		private string $path                     = "";
		private string $filename                 = "";
		private string $order_field              = "";
		private array  $extensions               = [];
		private array  $meta_name                = [];
		private string $media_name_update        = "";
		private array  $meta_alt_text            = [];
		private string $media_alt_text_update    = "";
		private array  $media_legend             = [];
		private string $media_legend_update      = "";
		private array  $media_description        = [];
		private string $media_description_update = "";
		
		private array $fc_codes_used = [];
		
		/**
		 * @param \WP_Post $post
		 * @param array    $products
		 */
		public function __construct(\WP_Post $post, array $products = []) {
			$this->post = $post;
			$this->db = InfocobDB::getInstance();
			$this->wp_db = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASSWORD);
			
			$this->products = $products;
			
			$post_meta_base64 = get_post_meta($this->post->ID, "files-cloud-meta", true);
			$post_meta = Tools::decodeConfig($post_meta_base64);
			
			$this->config_post = $post_meta["post_meta"] ?? [];
			
			$files_use_cloud = get_post_meta($this->post->ID, "files-use-cloud", true);
			if ($files_use_cloud) {
				$this->mode = "cloud";
			}
			
			$post_author = get_post_meta($this->post->ID, "post-author", true);
			$this->post_author = $post_author;
			
			$post_author_update = get_post_meta($this->post->ID, "post-author-update", true);
			$this->post_author_update = $post_author_update;
		}
		
		public function update() {
			$this->loadFiles("photos");
			$this->loadFiles("documents");
			
			$this->cleanupFiles();
		}
		
		protected function loadFiles(string $type) {
			$this->path = "";
			$this->filename = "";
			if ($type === "photos") {
				$this->path = get_post_meta($this->post->ID, "files-cloud-photos-folder", true);
				$this->filename = get_post_meta($this->post->ID, "files-cloud-photos-filename", true);
				$this->order_field = get_post_meta($this->post->ID, "files-cloud-photos-order", true);
				$this->extensions = get_post_meta($this->post->ID, "files-cloud-photos-ext", true);
				$this->meta_name = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-photos-name", true));
				if ($this->meta_name === null) $this->meta_name = [];
				$this->media_name_update = get_post_meta($this->post->ID, "files-cloud-photos-name-update", true);
				$this->meta_alt_text = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-photos-alt-text", true));
				if ($this->meta_alt_text === null) $this->meta_alt_text = [];
				$this->media_alt_text_update = get_post_meta($this->post->ID, "files-cloud-photos-alt-text-update", true);
				$this->media_legend = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-photos-legend", true));
				if ($this->media_legend === null) $this->media_legend = [];
				$this->media_legend_update = get_post_meta($this->post->ID, "files-cloud-photos-legend-update", true);
				$this->media_description = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-photos-description", true));
				if ($this->media_description === null) $this->media_description = [];
				$this->media_description_update = get_post_meta($this->post->ID, "files-cloud-photos-description-update", true);
				
			} else if ($type === "documents") {
				$this->path = get_post_meta($this->post->ID, "files-cloud-files-folder", true);
				$this->filename = get_post_meta($this->post->ID, "files-cloud-files-filename", true);
				$this->order_field = get_post_meta($this->post->ID, "files-cloud-files-order", true);
				$this->extensions = get_post_meta($this->post->ID, "files-cloud-files-ext", true);
				$this->meta_name = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-files-name", true));
				if ($this->meta_name === null) $this->meta_name = [];
				$this->media_name_update = get_post_meta($this->post->ID, "files-cloud-files-name-update", true);
				$this->meta_alt_text = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-files-alt-text", true));
				if ($this->meta_alt_text === null) $this->meta_alt_text = [];
				$this->media_alt_text_update = get_post_meta($this->post->ID, "files-cloud-files-alt-text-update", true);
				$this->media_legend = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-files-legend", true));
				if ($this->media_legend === null) $this->media_legend = [];
				$this->media_legend_update = get_post_meta($this->post->ID, "files-cloud-files-legend-update", true);
				$this->media_description = Tools::decodeConfig(get_post_meta($this->post->ID, "files-cloud-files-description", true));
				if ($this->media_description === null) $this->media_description = [];
				$this->media_description_update = get_post_meta($this->post->ID, "files-cloud-files-description-update", true);
			}
			
			if ($this->mode === "cloud" && $this->path !== "") {
				$attached_media_type = "";
				if ($type === "photos") {
					$attached_media_type = "image";
				}
				
				if (CloudFichierManager::hasCloudService()) {
					foreach ($this->products as $post_type => $wp_product) {
						$post_types[] = $post_type;
						
						$fc_code_files = [];
						
						$posts_translations = [];
						foreach ($wp_product as $lang => $products) {
							foreach ($products as $code => $product) {
								if ($product instanceof ProduitFiche || $product instanceof ProduitModeleFiche || $product instanceof TypeInventaireProduit) {
									$wp_posts_attachment = get_attached_media("", $product->getWpId());
									$parent_post_id = $product->getWpId(); // The ID of the post this attachment is for.
									$wp_posts_attachment_used = [];
									
									$files = CloudFichier::getFromProduit($product, $this->path, $this->filename, $this->extensions);
									
									foreach ($files as $file) {
										$fc_code = $file->getID();
										$fc_date_upload = $file->get(D::fc_date_upload);
										$filename = $file->get(D::fc_nom_fichier);
										$extension = trim($file->get(D::fc_extension), ".");
										$filename = trim($filename, "." . $extension) . "." . $extension;
										
										$full_filename = "icp-" . $fc_code . "__" . $filename;
										$this->fc_codes_used[] = $fc_code;
										
										$need_to_download = empty($fc_code_files[$fc_code]);
										
										$existing = false;
										foreach ($wp_posts_attachment as $wp_post_attachment) {
											if ($wp_post_attachment instanceof \WP_Post) {
												$attachment_meta_fc_code = get_post_meta($wp_post_attachment->ID, ProductMeta::FC_CODE_META_KEY, true);
												$attachment_meta_fc_date_upload = get_post_meta($wp_post_attachment->ID, ProductMeta::FC_DATE_UPLOAD_META_KEY, true);
												$download_success = false;
												
												// Update
												if (!empty($attachment_meta_fc_code) && $attachment_meta_fc_code === $fc_code) {
													$uploaded_file = false;
													$existing = true;
													$wp_posts_attachment_used[$wp_post_attachment->ID] = $wp_post_attachment;
													
													if ($wp_post_attachment instanceof \WP_Post) {
														$attach_id = $wp_post_attachment->ID;
														$wp_file_path = get_attached_file($attach_id);
														
														// Update files
														if ($need_to_download) {
															if ($attachment_meta_fc_date_upload !== $fc_date_upload) {
																if ($wp_file_path !== false) {
																	$response = CloudFichier::downloadCloudfichier($fc_code, true);
																	
																	$download_success = $response["success"] ?? false;
																	
																	if ($download_success) {
																		$blob = $response["file"] ?? false;
																		
																		if ($blob) {
																			$old_md5_file = md5_file($wp_file_path); // get the current md5
																			$uploaded_file = wp_upload_bits($filename, null, $blob);
																			
																			if (!($uploaded_file["error"] ?? false)) {
																				$fc_code_files[$fc_code] = $uploaded_file;
																			}
																		}
																	}
																}
															}
														} else {
															$uploaded_file = $fc_code_files[$fc_code] ?? false;
														}
														
														if ($uploaded_file !== false) {
															$download_success = true;
															$path_filename = $uploaded_file["file"] ?? "";
															$file_url = $uploaded_file["url"] ?? "";
															$file_type = $uploaded_file["type"] ?? "";
															chmod($path_filename, 0644); // To be sure to have the permission for md5 function
															
															// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
															require_once(ABSPATH . 'wp-admin/includes/image.php');
															
															// If file has changed, delete old files
															// Fixed problem with sizes not all updated
															$attach_data = wp_get_attachment_metadata($attach_id);
															
															$original_file = $attach_data["file"] ?? false;
															if ($original_file !== false) {
																$dir_original_file = pathinfo($original_file, PATHINFO_DIRNAME); // Get the dir where the file is saved
																$upload_file_dir = WP_CONTENT_DIR . "/uploads/" . $dir_original_file;
																
																$sizes = $attach_data["sizes"] ?? [];
																foreach ($sizes as $size => $values) {
																	$file = $values["file"] ?? false;
																	$full_path_file = $upload_file_dir . "/" . $file;
																	
																	if (file_exists($full_path_file)) {
																		unlink($full_path_file);
																	}
																}
																
																unlink($upload_file_dir = WP_CONTENT_DIR . "/uploads/" . $original_file);
															}
															
															// Generate the metadata for the attachment, and update the database record.
															$attach_data = wp_generate_attachment_metadata($attach_id, $path_filename);
															wp_update_attachment_metadata($attach_id, $attach_data);
														}
														
														if ($this->post_author_update) {
															wp_update_post([
																"ID" => $attach_id,
																"post_author" => $this->post_author
															]);
														}
														
														update_post_meta($attach_id, ProductMeta::FC_CODE_META_KEY, $fc_code);
														update_post_meta($attach_id, ProductMeta::FC_TYPE_META_KEY, $type);
														
														// Only update the date upload if download succeed (to retry later)
														if ($download_success) {
															update_post_meta($attach_id, ProductMeta::FC_DATE_UPLOAD_META_KEY, $fc_date_upload);
														}
														
														$cloudfichier = new CloudFichier();
														$cloudfichier->load($fc_code);
														$cloudfichier->setLang($lang);
														
														if (!empty($this->order_field)) {
															if (ChampLibre::isChampLibre($this->order_field)) {
																$champLibre = new ChampLibre($cloudfichier, $this->order_field);
																update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, $champLibre->getValue());
															} else {
																update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, $cloudfichier->getAuto($this->order_field));
															}
														} else {
															update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, 0);
														}
														
														foreach ($this->getConfigPostMeta($product, $post_type, true) as $key => $value) {
															$value = Tools::setFieldInfocobFromString($value, $cloudfichier);
															update_post_meta($attach_id, $key, $value);
														}
														
														
														$update_post = [
															"ID" => $attach_id
														];
														
														if ($this->media_name_update) {
															$nice_filename = Tools::setFieldInfocobFromString($this->meta_name[$lang] ?? "", $product);
															$nice_filename = Tools::setFieldInfocobFromString($nice_filename, $cloudfichier);
															$update_post["post_title"] = $nice_filename;
														}
														
														if ($this->media_alt_text_update) {
															$media_alt_text = Tools::setFieldInfocobFromString($this->meta_alt_text[$lang] ?? "", $product);
															$media_alt_text = Tools::setFieldInfocobFromString($media_alt_text, $cloudfichier);
															update_post_meta($attach_id, "_wp_attachment_image_alt", $media_alt_text);
														}
														
														if ($this->media_legend_update) {
															$photos_legend = Tools::setFieldInfocobFromString($this->media_legend[$lang] ?? "", $product);
															$photos_legend = Tools::setFieldInfocobFromString($photos_legend, $cloudfichier);
															$update_post["post_excerpt"] = $photos_legend;
														}
														
														if ($this->media_description_update) {
															$photos_description = Tools::setFieldInfocobFromString($this->media_description[$lang] ?? "", $product);
															$photos_description = Tools::setFieldInfocobFromString($photos_description, $cloudfichier);
															$update_post["post_content"] = $photos_description;
														}
														
														// Update post informations
														wp_update_post($update_post);
													}
												}
											}
										}
										
										// Creation
										if (!$existing) {
											$uploaded_file = false;
											
											if ($need_to_download) {
												$response = CloudFichier::downloadCloudfichier($fc_code, true);
												
												if ($response["success"] ?? false) {
													$blob = $response["file"] ?? false;
													
													if ($blob) {
														$wp_upload_dir = wp_upload_dir();
														$wp_upload_path = $wp_upload_dir["path"] ?? false;
														
														$path_filename = "";
														$file_url = "";
														$file_type = "";
														if ($wp_upload_path) {
															$uploaded_file = wp_upload_bits($filename, null, $blob);
															if (!($uploaded_file["error"] ?? false)) {
																$fc_code_files[$fc_code] = $uploaded_file;
															}
														}
													}
												}
											} else {
												$uploaded_file = $fc_code_files[$fc_code] ?? false;
											}
											
											if ($uploaded_file !== false) {
												
												$path_filename = $uploaded_file["file"] ?? "";
												$file_url = $uploaded_file["url"] ?? "";
												$file_type = $uploaded_file["type"] ?? "";
												chmod($path_filename, 0644); // To be sure to have the permission for md5 function
												
												// The ID of the post this attachment is for.
												$parent_post_id = $product->getWpId();
												
												// Prepare an array of post data for the attachment.
												$attachment = [
													'guid'           => $file_url,
													'post_mime_type' => $file_type,
													'post_status'    => 'inherit'
												];
												
												$cloudfichier = new CloudFichier();
												$cloudfichier->load($fc_code);
												$cloudfichier->setLang($lang);
												
												$nice_filename = Tools::setFieldInfocobFromString($this->meta_name[$lang] ?? "", $product);
												$nice_filename = Tools::setFieldInfocobFromString($nice_filename, $cloudfichier);
												$attachment["post_title"] = $nice_filename;
												
												$media_legend = Tools::setFieldInfocobFromString($this->media_legend[$lang] ?? "", $product);
												$media_legend = Tools::setFieldInfocobFromString($media_legend, $cloudfichier);
												$attachment["post_excerpt"] = $media_legend;
												
												$media_description = Tools::setFieldInfocobFromString($this->media_description[$lang] ?? "", $product);
												$media_description = Tools::setFieldInfocobFromString($media_description, $cloudfichier);
												$attachment["post_content"] = $media_description;
												
												// Insert the attachment.
												$attach_id = wp_insert_attachment($attachment, $path_filename, $parent_post_id);
												
												// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
												require_once(ABSPATH . 'wp-admin/includes/image.php');
												
												// Generate the metadata for the attachment, and update the database record.
												$attach_data = wp_generate_attachment_metadata($attach_id, $path_filename);
												wp_update_attachment_metadata($attach_id, $attach_data);
												
												update_post_meta($attach_id, ProductMeta::FC_CODE_META_KEY, $fc_code);
												update_post_meta($attach_id, ProductMeta::FC_DATE_UPLOAD_META_KEY, $fc_date_upload);
												update_post_meta($attach_id, ProductMeta::FC_TYPE_META_KEY, $type);
												wp_update_post([
													"ID" => $attach_id,
													"post_author" => $this->post_author
												]);
												
												$media_alt_text = Tools::setFieldInfocobFromString($this->meta_alt_text[$lang] ?? "", $product);
												$media_alt_text = Tools::setFieldInfocobFromString($media_alt_text, $cloudfichier);
												update_post_meta($attach_id, "_wp_attachment_image_alt", $media_alt_text);
												
												if (!empty($this->order_field)) {
													if (ChampLibre::isChampLibre($this->order_field)) {
														$champLibre = new ChampLibre($cloudfichier, $this->order_field);
														update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, $champLibre->getValue());
													} else {
														update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, $cloudfichier->getAuto($this->order_field));
													}
												} else {
													update_post_meta($attach_id, ProductMeta::FC_ORDER_META_KEY, 0);
												}
												
												foreach ($this->getConfigPostMeta($product, $post_type, true) as $key => $value) {
													$value = Tools::setFieldInfocobFromString($value, $cloudfichier);
													update_post_meta($attach_id, $key, $value);
												}
											}
										}
									}
									
									$posts_translations[$product->getID()][$lang] = $product->getWpId();
								}
							}
						}
						
						if (function_exists("pll_save_post_translations")) {
							foreach ($posts_translations as $code => $translation) {
								pll_save_post_translations($translation); // Define translation relation between posts
							}
						}
					}
				} else {
					Logger::errorImport("Cloud files Infocob not configured");
				}
			}
		}
		
		protected function cleanupFiles() {
			$wp_posts_attachment = $this->getWpPostsAttachmentUnused();
			
			foreach ($wp_posts_attachment as $wp_post_id) {
				wp_delete_attachment($wp_post_id);
			}
		}
		
		/**
		 * Get unused post attachments id
		 * @return array
		 */
		protected function getWpPostsAttachmentUnused() {
			global $wpdb;
			
			$this->fc_codes_used = array_unique($this->fc_codes_used);
			$args = [];
			
			$sql_not_in = "";
			foreach ($this->fc_codes_used as $fc_code) {
				$sql_not_in .= ":cloudfichier_" . $fc_code . ",";
				$args[":cloudfichier_" . $fc_code] = $fc_code;
			}
			$sql_not_in = trim($sql_not_in, ",");
			
			$sql_fc_codes = "";
			if (!empty($sql_not_in)) {
				$sql_fc_codes = " OR (PM.`meta_value` NOT IN (" . $sql_not_in . ") ) ";
			}
			
			$posts_id = [];
			$sql = "SELECT PJ.`ID`
					FROM `" . $wpdb->prefix . "postmeta` PM
					JOIN `" . $wpdb->prefix . "posts` PJ ON PJ.`ID` = PM.`post_id`
					WHERE PJ.`post_type` = 'attachment'
					AND PM.`meta_key` = :fc_code_meta_key
					AND (
					    PJ.`post_parent` = 0
					    " . $sql_fc_codes . "
					)";
			
			$args[":fc_code_meta_key"] = ProductMeta::P_CODE_META_KEY;
			$req = $this->wp_db->prepare($sql);
			
			$req->execute($args);
			while ($result = $req->fetch(\PDO::FETCH_ASSOC)) {
				if (!empty($result["ID"])) {
					$posts_id[] = $result["ID"];
				}
			}
			
			return $posts_id;
		}
		
		/**
		 * @param ProduitFiche|ProduitModeleFiche|TypeInventaireProduit|CloudFichier $product
		 *
		 * @return array
		 */
		public function getConfigPostMeta($product, $post_type, $update = null) {
			$lang = $product->getLang();
			
			$meta_inputs = [];
			foreach ($this->config_post as $config) {
				$config_post_type = $config["post_type"] ?? "";
				$config_update = $config["update"] ?? false;
				$meta_key = $config["meta_key"] ?? "";
				$meta_value = $config["meta_value"] ?? "";
				$langs = !empty($config["langs"]) ? $config["langs"] : [substr(get_locale(), 0, 2)];
				
				if (!str_starts_with($meta_key, ".") && $post_type === $config_post_type && ($update === null || $config_update === $update) && in_array($lang, $langs)) {
					// Security check for reserved keywords
					if (!in_array(strtolower($meta_key), [
						ProductMeta::P_CODE_META_KEY,
						ProductMeta::P_SUPP_META_KEY,
						ProductMeta::P_LANG_META_KEY
					])) {
						$meta_value = Tools::setFieldInfocobFromString($meta_value, $product);
						$meta_inputs[$meta_key] = $meta_value;
					}
				}
			}
			
			return $meta_inputs;
		}
		
		/**
		 * @return InfocobDB|null
		 */
		public function getDb(): ?InfocobDB {
			return $this->db;
		}
		
		/**
		 * @return PDO|null
		 */
		public function getWpDb(): ?PDO {
			return $this->wp_db;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getPath() {
			return $this->path;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getFilesPath() {
			return $this->files_path;
		}
		
		
	}
