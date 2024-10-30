<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\CloudFichier;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\CloudFichierEntity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ProduitFiche;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\CloudFichierManager;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CloudFichierCore extends CloudFichierEntity {
		// Types
		const CLOUD_TYPE_LOCAL    = 0;
		const CLOUD_TYPE_FTP      = 1;
		const CLOUD_TYPE_S3       = 2;
		const CLOUD_TYPE_DROPBOX  = 3;
		const CLOUD_TYPE_GDRIVE   = 4;
		const CLOUD_TYPE_ONEDRIVE = 5;
		const CLOUD_TYPE_OVH      = 6;
		const CLOUD_TYPE_OWNCLOUD = 7;
		
		// State
		const CLOUD_ETAT_PRET      = 0;
		const CLOUD_ETAT_AJOUTER   = 1000;
		const CLOUD_ETAT_MODIFIER  = 2000;
		const CLOUD_ETAT_SUPPRIMER = 3000;
		
		public static $max_upload_size; // Bytes
		
		
		public function getItemId($service_id = false) {
			// FC_PARAM INI
			$config_fc_parsed = parse_ini_string($this->get(D::fc_param) ?? "", true, INI_SCANNER_RAW);
			
			$item_id = false;
			$config_name = "";
			switch ($service_id) {
				case CloudFichier::CLOUD_TYPE_GDRIVE:
					$config_name = "CLOUDFICHIER_GOOGLE.GSUITE";
					break;
				default:
					// @TODO CLOUD_TYPE
					if(isset($config_fc_parsed["CLOUDFICHIER_GOOGLE.GSUITE"])) {
						$config_fc = $config_fc_parsed["CLOUDFICHIER_GOOGLE.GSUITE"] ?? [];
						$item_id = $config_fc["ITEM_ID"] ?? false;
					}
					break;
			}
			
			if(!empty($config_name) && isset($config_fc_parsed[$config_name])) {
				$config_fc = $config_fc_parsed[$config_name] ?? [];
				$item_id = $config_fc["ITEM_ID"] ?? false;
			}
			
			return $item_id;
		}
		
		/**
		 * @param $produit
		 * @param $repository
		 * @param $filename string regex
		 * @param $extensions
		 *
		 * @return array
		 */
		public static function getFromProduit($produit, $repository = false, $filename = "", $extensions = []) {
			$p_code = $produit->getID();
			if($produit instanceof ProduitModeleFiche) {
				$table_code = ProduitModeleFiche::$tableCode;
			} else if($produit instanceof TypeInventaireProduit) {
				$table_code = TypeInventaireProduit::$tableCode;
			} else {
				$table_code = ProduitFicheCore::$tableCode;
			}
			
			$params = [
				":p_code"     => $p_code,
				":table_code" => $table_code,
			];
			
			$extensions = array_map(function($extension) {
				return trim(strtolower($extension), ".");
			}, $extensions);
			
			$repository_sql = ($repository !== false) ? " AND TRIM('/' from " . D::fc_repertoire . ") LIKE :fc_repertoire" : "";
			$extensions_sql = "";
			if(!empty($extensions)) {
				$extensions_sql = " AND LOWER(REPLACE(" . D::fc_extension . ", '.', '')) IN (";
				$sql_values = "";
				foreach ($extensions as $index => $extension) {
					$sql_values .= ":ext_" . $index . ",";
					$params[":ext_" . $index] = $extension;
					
				}
				$sql_values = trim($sql_values, ",");
				$extensions_sql .= $sql_values . ")";
			}
			
			$sql = "SELECT * FROM "
				. self::$tableName . " WHERE "
				. D::fc_index_table . " = :table_code " .
				"AND " . D::fc_code_maitre . " = :p_code " .
				"AND COALESCE(".D::fc_traite.", ".CloudFichier::CLOUD_ETAT_PRET.") = ".CloudFichier::CLOUD_ETAT_PRET." " .
				"AND " . static::GetDroitCondition() .
				$repository_sql .
				$extensions_sql .
				" ORDER BY " . D::fc_repertoire . " DESC nulls last, " . D::fc_date_upload . " DESC";
			
			if($repository !== false) {
				$params[":fc_repertoire"] = $repository . "%";
			}
			
			$results = InfocobDB::getInstance()->fetchAll($sql, $params);
			
			$cloudfichiers = [];
			foreach ($results as $r) {
				$f = new CloudFichier();
				$f->loadFromArray($r);
				if(empty($filename) || preg_match("/". $filename . "/mi", $f->get(D::fc_nom_fichier)) === 1) {
					$cloudfichiers[] = $f;
				}
			}
			
			return $cloudfichiers;
		}
		
		public static function setRepertoires($repertoires, &$liste, $value) {
			
			$count = count($repertoires);
			foreach ($repertoires as $index => $repertoire) {
				if (!isset($liste[$repertoire])) {
					$liste[$repertoire] = [];
					if (!isset($liste[$repertoire]["dossiers"])) {
						$liste[$repertoire]["dossiers"] = [];
					}
					if (!isset($liste[$repertoire]["fichiers"])) {
						$liste[$repertoire]["fichiers"] = [];
					}
				}
				
				if ($index < $count - 1) {
					array_shift($repertoires);
					self::setRepertoires($repertoires, $liste[$repertoire]["dossiers"], $value);
					break;
				} else {
					$liste[$repertoire]["fichiers"][] = $value;
				}
			}
		}
		
		public function getFromFcParam($field, $header = "CLOUDFICHIER_GOOGLE.GSUITE") {
			$config_ini = !empty($this->get(D::fc_param)) ? $this->get(D::fc_param) : false;
			
			if ($config_ini) {
				$config = parse_ini_string($config_ini, true, INI_SCANNER_RAW);
				
				if(isset($config[$header]) && isset($config[$header][$field])) {
					return $config[$header][$field];
				}
			}
			
			return false;
		}
		
		public static function uploadCloudfichier($module, $code, $file_name, $file_data = false, $repository = "/", $service_id = CloudFichier::CLOUD_TYPE_GDRIVE, $fc_code = false) {
			switch ($module) {
				case "produit":
					$entity = new ProduitFiche();
					$entity->load($code);
					break;
			}
			
			if (!isset($entity) || empty($entity->getID())) {
				return [
					"success" => false
				];
			}
			
			$cloudfichierManager = new CloudFichierManager($fc_code);
			
			$operation = false;
			$response = [
				"success" => false
			];
			
			/*
			 * Creation
			 */
			if($file_name && $file_data && $repository !== false && empty($fc_code) && $service_id) {
				$response = $cloudfichierManager->upload($file_name, $file_data, $repository, $entity, false, $service_id);
				$operation = "insert";
			}
			
			/*
			 * Update
			 */
			if($repository !== false && !empty($fc_code) && $service_id) {
				if(!empty($file_name) && !empty($file_data)) {
					$response = $cloudfichierManager->upload($file_name, $file_data, $repository, $entity, $fc_code, $service_id);
				} else {
					$cloudfichier = new CloudFichier();
					$cloudfichier->load($fc_code);
					if(!empty($cloudfichier->getID())) {
						$cloudfichier->set(D::fc_repertoire, $repository);
						if(!empty($file_name)) {
							$cloudfichier->set(D::fc_nom_fichier, $file_name);
						}
						if($cloudfichier->maj()){
							$response = [
								"success" => true,
							];
						}
					}
				}
				$operation = "update";
			}
			
			/*
			 * Delete
			 */
			if($repository === false && !$file_name && !$file_data && !empty($fc_code) && $service_id) {
				$response = $cloudfichierManager->delete($fc_code, $service_id);
				$operation = "delete";
			}
			
			return array_merge($response, [
				"operation" => $operation
			]);
		}
		
		public static function downloadCloudfichier($fc_code, $return = false) {
			if ($fc_code) {
				$cloudfichierManager = new CloudFichierManager($fc_code);
				
				$file = $cloudfichierManager->download($return);
				if($return) {
					return $file;
				}
			}
		}
		
		public static function getFile($fc_code) {
			$cloudfichierManager = new CloudFichierManager($fc_code);
			
			return $cloudfichierManager->get();
		}
		
		/**
		 * @param array $filter GET filters
		 *
		 * @return array Request response
		 */
		public static function getForAPI($filter) {
			$filter["FC_CODE"] = $filter["CODE"];
			unset($filter["CODE"]);
			$debut = (isset($filter["DEBUT"]) && $filter["DEBUT"] > 0) ? $filter["DEBUT"] - 1 : 0;                                          #ICI LA LIMIT **********************
			$fin = (isset($filter["FIN"]) && $filter["FIN"] >= $debut + 1 && $filter["FIN"] - $debut <= 50) ? $filter["FIN"] - $debut : 50; #ICI LA LIMIT *********************
			if (!isset($filter["FIN"]))
				$fin = 10;
			$fin = (isset($filter["FIN"]) && $filter["FIN"] < $debut + 1) ? 10 : $fin;
			if (is_null($filter["FC_CODE"]))
				unset($filter["FC_CODE"]);      // If filter "c_code" not set, remove it from the filter array
			$filter_keys = array_keys($filter); // get all filters name (keys)
			
			$sql_select = "SELECT FIRST " . $fin . " SKIP " . $debut . " * ";
			$sql = " FROM Cloudfichier WHERE" . static::GetDroitCondition(); // define sql base request
			
			if (($key = array_search("DEBUT", $filter_keys)) !== false)
				unset($filter_keys[$key]); // Search and remove elements with the key "DEBUT"
			if (($key = array_search("FIN", $filter_keys)) !== false)
				unset($filter_keys[$key]); // Search and remove elements with the key "FIN"
			
			
			// Check if the filters enter are correct/exists, If not, show error response
			$cloudfichier = new CloudFichier();
			
			$correct_fields = array_map([$cloudfichier, "hasField"], $filter_keys);
			if (in_array(false, $correct_fields)) {
				$response = [
					'success'        => false,
					'status_code'    => 404,
					'status_message' => 'Fields Name not found !'
				];
				return $response;
			}
			
			// Construct sql request depending on filters.
			foreach ($filter as $key => $value) {
				if (!is_null($filter[$key]) && $key != "DEBUT" && $key != "FIN") {
					if ($key == "FC_CODE") {
						$sql .= "AND UPPER(" . $key . ") = UPPER('" . $filter[$key] . "')";
					} else {
						$sql .= "AND UPPER(" . $key . ") LIKE UPPER('%" . $filter[$key] . "%')";
					}
				}
			}
			
			// Execute the sql request and show the response depending on execute result
			$result = InfocobDB::getInstance()->fetchAll($sql_select . $sql);
			$sqlCount = "SELECT count(*) " . $sql;
			if (isset($filter["FC_CODE"]))
				$sqlCount .= " AND UPPER(FC_CODE) = UPPER('" . $filter["FC_CODE"] . "')";
			$count = InfocobDB::getInstance()->fetch($sqlCount);
			if (empty($result)) {
				$response = [
					'success'        => false,
					'status_code'    => 404,
					'status_message' => 'Data not found !'
				];
			} else {
				if($count["COUNT"] === 1 && isset($result[0])) {
					$file = CloudFichier::downloadCloudfichier($result[0]["FC_CODE"], true);
					if(!empty($file["success"])) {
						$file = base64_encode($file["file"] ?? "");
						$result[0]["FICHIER"] = [
							"success" => true,
							"file" => $file,
						];
					} else {
						$result[0]["FICHIER"] = [
							"success" => false,
							"message" => $file["message"] ?? "",
							"code" => $file["code"] ?? ""
						];
					}
				}
				
				$response = [
					'success'        => true,
					'status_code'    => 200,
					'status_message' => 'Data found !',
					'nb_results'     => $count["COUNT"],
					'result'         => $result
				];
			}
			return $response;
		}
	}
