<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitModeleFiche;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Dictionnaire {
		
		protected static $dictionnaire           = [];
		protected static $tables_read_only       = [];
		protected static $tables_visible         = [];
		protected static $tables_adding_disabled = [];
		protected static $prefix_tables          = [
			"PRODUITFICHE"          => "P_",
			"PRODUITMODELEFICHE"    => "P_",
			"FAMILLETYPEINVENTAIRE" => "FTI_",
			"INVENTAIREPRODUIT"     => "IP_",
			"TYPEINVENTAIREPRODUIT" => "TIP_",
			"CLOUDFICHIER"          => "FC_",
		];
		
		public static function getTableCodeFromEntity($entity) {
			if ($entity instanceof ProduitFiche) {
				return ProduitFiche::$tableCode;
			} else if ($entity instanceof ProduitModeleFiche) {
				return ProduitModeleFiche::$tableCode;
			} else if ($entity instanceof InventaireProduit) {
				return InventaireProduit::$tableCode;
			} else if ($entity instanceof TypeInventaireProduit) {
				return TypeInventaireProduit::$tableCode;
			} else if ($entity instanceof FamilleTypeInventaireProduit) {
				return FamilleTypeInventaireProduit::$tableCode;
			}
		}
		
		public static function getEntityFromTableCode($table_code) {
			if ($table_code === ProduitFiche::$tableCode) {
				return new ProduitFiche();
			} else if ($table_code === ProduitModeleFiche::$tableCode) {
				return new ProduitModeleFiche();
			} else if ($table_code === InventaireProduit::$tableCode) {
				return new InventaireProduit();
			}
		}
		
		public static function getEntityFromTypeParent($type_parent) {
			if ($type_parent === 0) {
				return Dictionnaire::getEntityFromTableCode(9); // Produitfiche
			} else if ($type_parent === 16) {
				return Dictionnaire::getEntityFromTableCode(1); // Produitmodelefiche
			} else if ($type_parent === 1) {
				return Dictionnaire::getEntityFromTableCode(2); // Inventaireproduit
			} else {
				return Dictionnaire::getEntityFromTableCode($type_parent - 100); // toutes les autres tables
			}
		}
		
		public static function getTableChampPrincipalKeyFromEntity($entity) {
			if ($entity instanceof ProduitFiche) {
				return ProduitFiche::$champPrincipalKey;
			} else if ($entity instanceof ProduitModeleFiche) {
				return ProduitModeleFiche::$champPrincipalKey;
			} else if ($entity instanceof InventaireProduit) {
				return InventaireProduit::$champPrincipalKey;
			}
		}
		
		public static function isTableExists($table_name) {
			$sql = 'SELECT 1 FROM RDB$RELATIONS WHERE RDB$RELATION_NAME = :TABLE_NAME';
			$args = [
				":TABLE_NAME" => $table_name
			];
			$response = InfocobDB::getInstance()->fetch($sql, $args);
			return !empty($response);
		}
		
		public static function getChamps($table_name, $champs_libres = true, $champs_perso = false) {
			$sql = "SELECT * FROM DICTIONNAIRE R WHERE DI_TABLE = (
    					SELECT R.DI_TABLE-100
                        FROM DICTIONNAIRE R
                        WHERE R.DI_CODEGROUPE = (
                            SELECT PR_CODE
                            FROM PROFIL
                            WHERE PR_DEFAUT = 'T'
                        ) AND UPPER(R.DI_CHAMP) = :TABLE_NAME)
                        ORDER BY R.DI_CHAMP ASC";
			
			$args = [
				":TABLE_NAME" => $table_name
			];
			
			$arrayResponse = InfocobDB::getInstance()->fetchAll($sql, $args);
			
			$results = [];
			foreach ($arrayResponse as $field) {
				if (preg_match('/^' . (static::$prefix_tables[$table_name] ?? "") . '.+$/i', $field["DI_CHAMP"]) === 1) {
					if((preg_match("/^[a-zA-Z]+_CHAMPPERSO.*/i", $field["DI_CHAMP"]) === 1 && $champs_perso) || preg_match("/^[a-zA-Z]+_CHAMPPERSO.*/i", $field["DI_CHAMP"]) !== 1) {
						$results[$field["DI_CHAMP"]] = $field["DI_DISPLAYLABEL"];
					}
				}
			}
			
			
			$lt_typeparent = false;
			if (strtoupper($table_name) === "PRODUITFICHE") {
				$lt_typeparent = 9;
			} else if (strtoupper($table_name) === "PRODUITMODELEFICHE") {
				$lt_typeparent = 16;
			} else if (strtoupper($table_name) === "CLOUDFICHIER") {
				$lt_typeparent = 39;
			} else if (strtoupper($table_name) === "INVENTAIREPRODUIT") {
				$lt_typeparent = 7;
			} else if (strtoupper($table_name) === "TYPEINVENTAIREPRODUIT") {
				$lt_typeparent = 12;
			} else if (strtoupper($table_name) === "FAMILLETYPEINVENTAIRE") {
				$lt_typeparent = 10;
			}
			
			if ($lt_typeparent !== false && $champs_libres) {
				$sql = "SELECT LT.LT_CODE, LT.LT_NOM
					FROM LISTETYPE_TYPE LT
					WHERE LT.LT_TYPEPARENT = :LT_TYPEPARENT
					OR LT.LT_TYPEPARENT = -1 ";
				
				if($lt_typeparent === 9) {
					$sql .= "OR LT.LT_TYPEPARENT = -2 ";
				}
				
				$arrayResponse = InfocobDB::getInstance()->fetchAll($sql, [
					":LT_TYPEPARENT" => $lt_typeparent
				]);
				
				foreach ($arrayResponse as $field) {
					$results[(static::$prefix_tables[$table_name] ?? "") . "CHAMPLIBRE" . $field["LT_CODE"]] = $field["LT_NOM"];
				}
			}
			
			ksort($results);
			
			return $results;
		}
		
		public static function getTableLibelle($table_code) {
			$sql = "SELECT DI_TABLE, DI_DISPLAYLABEL FROM DICTIONNAIRE "
				. " WHERE DI_TABLE = :TABLE_CODE AND DI_CODEGROUPE = (
	                        SELECT PR_CODE
	                        FROM PROFIL
	                        WHERE PR_DEFAUT = 'T'
	                    )";
			
			$args = [
				":TABLE_CODE" => $table_code
			];
			
			$result = InfocobDB::getInstance()->fetch($sql, $args);
			
			return $result["DI_DISPLAYLABEL"] ?? "";
		}
		
		public static function libelle($tableCode, $nomChamp) {
			return self::getPropriete($tableCode, $nomChamp, "libelle", $nomChamp);
		}
		
		protected static function getPropriete($tableCode, $nomChamp, $propertyName, $default) {
			$tableCode = (int)$tableCode;
			$nomChamp = (string)$nomChamp;
			
			if (!isset(self::$dictionnaire[$tableCode])) {
				self::loadTableDefinition($tableCode);
			}
			
			return (isset(self::$dictionnaire[$tableCode][$nomChamp]) && isset(self::$dictionnaire[$tableCode][$nomChamp][$propertyName]))
				? self::$dictionnaire[$tableCode][$nomChamp][$propertyName]
				: $default;
		}
		
		protected static function loadTableDefinition($tableCode) {
			$tableCode = (int)$tableCode;
			
			$sql = "SELECT * FROM DICTIONNAIRE WHERE DI_TABLE = :TABLECODE";
			$args = [
				":tableCode" => $tableCode,
			];
			
			$resDico = InfocobDB::getInstance()->fetchAll($sql, $args);
			
			foreach ($resDico as $rDico) {
				self::$dictionnaire[$tableCode][$rDico["DI_CHAMP"]] = [
					"libelle"  => !empty($rDico["DI_DISPLAYLABEL"]) ? $rDico["DI_DISPLAYLABEL"] : "",
					"readonly" => (!empty($rDico["DI_READONLY"]) && $rDico["DI_READONLY"] == "T") ? true : false,
					"required" => (!empty($rDico["DI_REQUIRED"]) && $rDico["DI_REQUIRED"] == "T") ? true : false,
					"visible"  => (!empty($rDico["DI_VISIBLE"]) && $rDico["DI_VISIBLE"] == "T") ? true : false,
				];
			}
			
			static::loadTableDefaultDefinition($tableCode);
		}
		
		protected static function loadTableDefaultDefinition($tableCode) {
			$tableCode = (int)$tableCode;
			
			$sql = "SELECT R.DI_CHAMP, R.DI_CHARCASE
               FROM DICTIONNAIRE R
               LEFT JOIN PROFIL P ON P.PR_CODE = R.DI_CODEGROUPE
               WHERE P.PR_DEFAUT = 'T'
                     AND R.DI_TABLE = :TABLECODE ";
			
			$args = [
				":tableCode" => $tableCode,
			];
			
			
			$resDico = InfocobDB::getInstance()->fetchAll($sql, $args);
			
			foreach ($resDico as $rDico) {
				$charcase = "normal";
				if (!empty($rDico["DI_CHARCASE"])) {
					$di_charcase = intval($rDico["DI_CHARCASE"]);
					
					if ($di_charcase === 0) {
						$charcase = "lower";
					} elseif ($di_charcase === 2) {
						$charcase = "upper";
					}
				}
				
				self::$dictionnaire[$tableCode][$rDico["DI_CHAMP"]]["charcase"] = $charcase;
			}
		}
		
		public static function readonly($tableCode, $nomChamp) {
			return self::getPropriete($tableCode, $nomChamp, "readonly", false);
		}
		
		public static function required($tableCode, $nomChamp) {
			return self::getPropriete($tableCode, $nomChamp, "required", false);
		}
		
		public static function visible($tableCode, $nomChamp) {
			return self::getPropriete($tableCode, $nomChamp, "visible", false);
		}
		
		public static function charcase($tableCode, $nomChamp) {
			return self::getPropriete($tableCode, $nomChamp, "charcase", "normal");
		}
		
		public static function getGroupesDeDroit() {
			$sql = "select GD_CODE, GD_NOM from GROUPEDROIT";
			$results = InfocobDB::getInstance()->fetchAll($sql);
			
			$groupes = [];
			foreach ($results as $result) {
				$groupes[$result["GD_CODE"]] = $result["GD_NOM"];
			}
			
			return $groupes;
		}
	}


