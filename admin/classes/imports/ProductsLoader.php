<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Imports;
	
	use Google\Exception;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\FamilleTypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\ChampLibre;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use PDO;
	use WP_Post;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsLoader {
		
		private ?\WP_Post  $post;
		private ?InfocobDB $db;
		
		/**
		 * @param WP_Post $post
		 */
		public function __construct(\WP_Post $post) {
			$this->post = $post;
			$this->db = InfocobDB::getInstance();
		}
		
		/**
		 * @return array|int
		 */
		public function get($count = false) {
			if($count) {
				$products = 0;
			} else {
				$products = [];
			}
			
			$infocob_type_produit = get_post_meta($this->post->ID, "infocob-type-produit", true);
			$infocob_filters_base64 = get_post_meta($this->post->ID, "infocob-filters", true);
			$infocob_filters = Tools::decodeConfig($infocob_filters_base64);
			
			$infocob_groupe_droit = get_post_meta($this->post->ID, "infocob-groupe-droit", true);
			if($infocob_groupe_droit === "") {
				$infocob_groupe_droit = 2;
			} else {
				$infocob_groupe_droit = (int)$infocob_groupe_droit;
			}
			
			if($infocob_type_produit === TypeInventaireProduit::$tableName) {
				$primaryKey = "TIP_CODE";
				$sql_droits = ($infocob_groupe_droit !== -1) ? TypeInventaireProduit::GetDroitCondition($infocob_groupe_droit) : "";
				$left_join = "LEFT JOIN " . FamilleTypeInventaireProduit::$tableName . " ON FTI_CODE = TIP_CODE ";
			} else {
				$primaryKey = "P_CODE";
				$sql_droits = ($infocob_groupe_droit !== -1) ? ProduitFiche::GetDroitCondition($infocob_groupe_droit) : "";
				$left_join = "";
			}
			
			if($count) {
				$sql_select = "SELECT count(" . $primaryKey . ") as NB_PRODUCTS " .
					"FROM " . $infocob_type_produit . " " . $left_join;
			} else {
				$sql_select = "SELECT * " .
					"FROM " . $infocob_type_produit . " " . $left_join;
			}
			
			$args = [];
			
			if (!empty($infocob_filters)) {
				$sql_select .= "WHERE ";
				if($sql_droits !== "") {
					$sql_select .= $sql_droits . " AND ";
				}
				
				$next_condition = "";
				foreach ($infocob_filters as $index => $filter) {
					$type = $filter["type"] ?? "row";
					
					if (strcasecmp($type, "row") === 0) {
						$field_name = $filter["field_name"] ?? "";
						$operator = $filter["operator"] ?? "";
						$value = $filter["value"] ?? "";
						
						if (ChampLibre::isChampLibre($field_name)) {
							if(in_array($operator, ["is_null", "is_not_null"])) {
								if($operator === "is_null") {
									$operator = "IS NULL";
								} else if($operator === "is_not_null") {
									$operator = "IS NOT NULL";
								}
								$sql_select .= $next_condition . " (SELECT FIRST 1 LV_VALEUR FROM LISTETYPE_VALEUR LV WHERE LV.LV_CODEMAITRE = " . $primaryKey . " AND LV.LV_CODETYPE = :ChampLibre" . strtolower($field_name) . "_" . $index . ") " . $operator . " ";
								$args[":ChampLibre" . strtolower($field_name) . "_" . $index] = ChampLibre::getCodeFromFieldName($field_name);
							} else {
								$sql_select .= $next_condition . " (SELECT FIRST 1 LV_VALEUR FROM LISTETYPE_VALEUR LV WHERE LV.LV_CODEMAITRE = " . $primaryKey . " AND LV.LV_CODETYPE = :ChampLibre" . strtolower($field_name) . "_" . $index . ") " . $operator . " :ChampLibreValue" . strtolower($field_name) . "_" . $index . " ";
								$args[":ChampLibre" . strtolower($field_name) . "_" . $index] = ChampLibre::getCodeFromFieldName($field_name);
								$args[":ChampLibreValue" . strtolower($field_name) . "_" . $index] = $value;
							}
						} else {
							if(in_array($operator, ["is_null", "is_not_null"])) {
								if($operator === "is_null") {
									$operator = "IS NULL";
								} else if($operator === "is_not_null") {
									$operator = "IS NOT NULL";
								}
								$sql_select .= $next_condition . " " . $field_name . " " . $operator . " ";
							} else {
								$sql_select .= $next_condition . " " . $field_name . " " . $operator . " :" . strtolower($field_name) . "_" . $index . " ";
								$args[":" . strtolower($field_name) . "_" . $index] = $value;
							}
						}
						
						$next_condition = $filter["next_condition"] ?? "";
						
					} else if (strcasecmp($type, "group") === 0) {
						$values = $filter["value"] ?? [];
						
						if (!empty($values)) {
							$sql_select .= $next_condition . " ( ";
							
							$next_condition_group = "";
							foreach ($values as $sub_index => $value_filter) {
								$sub_type = $value_filter["type"] ?? "row";
								
								if (strcasecmp($sub_type, "row") === 0) {
									$sub_field_name = $value_filter["field_name"] ?? "";
									$sub_operator = $value_filter["operator"] ?? "";
									$sub_value = $value_filter["value"] ?? "";
									
									if (ChampLibre::isChampLibre($sub_field_name)) {
										if(in_array($sub_operator, ["is_null", "is_not_null"])) {
											if($sub_operator === "is_null") {
												$sub_operator = "IS NULL";
											} else if($sub_operator === "is_not_null") {
												$sub_operator = "IS NOT NULL";
											}
											$sql_select .= $next_condition_group . " (SELECT FIRST 1 LV_VALEUR FROM LISTETYPE_VALEUR LV WHERE LV.LV_CODEMAITRE = " . $primaryKey . " AND LV.LV_CODETYPE = :ChampLibre" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index . ") " . $sub_operator . " ";
											$args[":ChampLibre" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index] = ChampLibre::getCodeFromFieldName($sub_field_name);
										} else {
											$sql_select .= $next_condition_group . " (SELECT FIRST 1 LV_VALEUR FROM LISTETYPE_VALEUR LV WHERE LV.LV_CODEMAITRE = " . $primaryKey . " AND LV.LV_CODETYPE = :ChampLibre" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index . ") " . $sub_operator . " :ChampLibreValue" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index . " ";
											$args[":ChampLibre" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index] = ChampLibre::getCodeFromFieldName($sub_field_name);
											$args[":ChampLibreValue" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index] = $sub_value;
										}
									} else {
										if(in_array($sub_operator, ["is_null", "is_not_null"])) {
											if($sub_operator === "is_null") {
												$sub_operator = "IS NULL";
											} else if($sub_operator === "is_not_null") {
												$sub_operator = "IS NOT NULL";
											}
											$sql_select .= $next_condition_group . " " . $sub_field_name . " " . $sub_operator . " ";
										} else {
											$sql_select .= $next_condition_group . " " . $sub_field_name . " " . $sub_operator . " :" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index . " ";
											$args[":" . strtolower($sub_field_name) . "_" . $index . "_" . $sub_index] = $sub_value;
										}
									}
									
									$next_condition_group = $value_filter["next_condition"] ?? "";
								}
							}
							
							$sql_select .= ") ";
							$next_condition = $filter["next_condition"] ?? "";
						}
					}
				}
			}
			
			try {
				if ($count) {
					$results = $this->db->fetch($sql_select, $args);
				} else {
					$results = $this->db->fetchAll($sql_select, $args);
				}
			} catch (\Exception $exception){
				// Do nothing
			}
			
			if (!empty($results)) {
				if($count) {
					$products = $results["NB_PRODUCTS"] ?? 0;
				} else {
					foreach ($results as $result) {
						if (strcasecmp($infocob_type_produit, "produitfiche") === 0) {
							$product = new ProduitFiche();
							$product->loadFromArrayComplete($result);
							$product->setWpTable($infocob_type_produit);
							$products[$product->getID()] = $product;
							
						} else if (strcasecmp($infocob_type_produit, "produitmodelefiche") === 0) {
							$product = new ProduitModeleFiche();
							$product->loadFromArrayComplete($result);
							$product->setWpTable($infocob_type_produit);
							$products[$product->getID()] = $product;
							
						} else if (strcasecmp($infocob_type_produit, "typeinventaireproduit") === 0) {
							$product = new TypeInventaireProduit();
							$product->loadFromArrayComplete($result);
							$product->setWpTable($infocob_type_produit);
							$products[$product->getID()] = $product;
						}
					}
					
					apply_filters('icp_import_products_loaded', $products, $this->post->ID ?? false);
				}
			}
			
			return $products;
		}
		
		/**
		 * @return WP_Post|null
		 */
		public function getPost(): ?WP_Post {
			return $this->post;
		}
		
	}
