<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Dictionnaire;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\FamilleTypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\ListeTypeType;
	use Infocob\CRM\Products\Admin\Classes\Infocob\DicoInfocob as D;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	
	class ChampLibre {
		private $entity;
		private $name;
		private $prefix;
		private $lt_code;
		private $listeTypeType;
		
		public function __construct($entity, $name) {
			$this->entity = $entity;
			$this->name = $name;
			
			$this->load();
		}
		
		private function load() {
			$this->loadPrefix();
			$this->loadCodeType();
			
			if ($this->lt_code !== false) {
				$sql = "SELECT FIRST 1 LT_CODE
					FROM LISTETYPE_TYPE ltt
					WHERE UPPER(ltt.LT_CODE) = UPPER(:lt_code)
				";
				
				$args = [
					":lt_code" => $this->lt_code
				];
				
				$res = InfocobDB::getInstance()->fetch($sql, $args);
				
				if ($res["LT_CODE"] ?? false) {
					$listeTypeType = new ListeTypeType();
					$listeTypeType->load($res["LT_CODE"]);
					
					$this->listeTypeType = $listeTypeType;
				}
			}
		}
		
		private function loadPrefix() {
			preg_match("/^([a-zA-Z]+_)/i", $this->name, $matches);
			$this->prefix = $matches[1] ?? false;
		}
		
		private function loadCodeType() {
			preg_match("/^" . $this->prefix . "ChampLibre(.+)$/i", $this->name, $matches);
			$this->lt_code = $matches[1] ?? false;
		}
		
		public function toArray() {
			$result = false;
			if ($this->listeTypeType instanceof ListeTypeType) {
				$visible = $this->listeTypeType->get(D::lt_visible) === "T";
				$required = $this->listeTypeType->get(D::lt_required) === "T";
				$readonly = $this->listeTypeType->get(D::lt_readonly) === "T";
				$values = $this->getValue();
				$libelle = $this->listeTypeType->get(D::lt_nom);
				
				$result = [
					"visible"  => $visible,
					"required" => $required,
					"readonly" => $readonly,
					"libelle"  => $libelle,
				];
				
				$result = array_merge($result, $values);
			}
			
			return $result;
		}
		
		public function getValue() {
			if ($this->listeTypeType instanceof ListeTypeType && $this->entity instanceof Entity) {
				if ($this->isChampString()) {
					return $this->getValueChampString();
				} else if ($this->isChampListeDeroulante()) {
					return $this->getValueChampListeDeroulante();
				} else if ($this->isChampListeRadio()) {
					return $this->getValueChampListeRadio();
				}
			}
			
			return false;
		}
		
		public function isChampString() {
			if ($this->listeTypeType instanceof ListeTypeType) {
				$lt_type = $this->listeTypeType->get(D::lt_type);
				return in_array($lt_type, [
					0,   // String
					1,   // Numeric
					3,   // Checkbox
					12,  // Hyperlink
					114, // URL
					102, // URL Youtube
					115, // Web 2
					116, // Web 3
					117, // Web 4
					118, // Web 5
				]);
			}
			
			return false;
		}
		
		public function isChampListeDeroulante() {
			if ($this->listeTypeType instanceof ListeTypeType) {
				$lt_type = $this->listeTypeType->get(D::lt_type);
				return ($lt_type >= 1001 && $lt_type <= 1050); // List 1 to 50
			}
			
			return false;
		}
		
		public function isChampListeRadio() {
			if ($this->listeTypeType instanceof ListeTypeType) {
				$lt_type = $this->listeTypeType->get(D::lt_type);
				return ($lt_type >= 1200 && $lt_type <= 1209); // List 1 to 10
			}
			
			return false;
		}
		
		private function getValueChampString() {
			if ($this->listeTypeType instanceof ListeTypeType && $this->entity instanceof Entity) {
				$code_type = $this->listeTypeType->getID();
				
				$sql = "SELECT FIRST 1 ltv.LV_VALEUR as valeur, ltt.LT_NOM as libelle
					FROM LISTETYPE_TYPE ltt
					LEFT JOIN LISTETYPE_VALEUR ltv
					ON ltv.LV_CODETYPE = ltt.LT_CODE
					AND ltv.LV_CODETYPE = :code_type_1
					AND ((ltv.LV_TYPEPARENT = :type_parent)  OR (ltv.LV_TYPEPARENT = -1))
					AND ltv.LV_CODEMAITRE = :code_maitre
					WHERE ltt.LT_CODE = :code_type_2
				";
				
				$code_maitre = $this->entity->getID();
				$type_parent = Dictionnaire::getTableCodeFromEntity($this->entity);
				
				if($this->entity instanceof InventaireProduit) {
					if ($this->prefix === "TIP_") {
						$code_maitre = $this->entity->get(D::ip_code_type);
						$type_parent = TypeInventaireProduit::$tableCode;
					} else if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
					
				} else if($this->entity instanceof TypeInventaireProduit) {
					if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
				}
				
				$args = [
					":code_maitre" => $code_maitre,
					":code_type_1" => $code_type,
					":code_type_2" => $code_type,
					":type_parent" => $type_parent,
				];
				
				$res = InfocobDB::getInstance()->fetch($sql, $args);
				
				return [
					"value" => ($res["VALEUR"] !== null) ? $res["VALEUR"] : ""
				];
			}
			
			return false;
		}
		
		private function getValueChampListeDeroulante() {
			if ($this->listeTypeType instanceof ListeTypeType && $this->entity instanceof Entity) {
				$lt_type = $this->listeTypeType->get(D::lt_type);
				$lt_code = $this->listeTypeType->getID();
				$numero_liste = $lt_type - 1000;
				
				$sql = "SELECT FIRST 1 lll.LLL_CODE as code, ltv.LV_VALEUR as valeur, ltt.LT_NOM as libelle, lll.LLL_NOM_LONG as nom
						FROM LISTETYPE_TYPE ltt
						LEFT JOIN LISTETYPE_VALEUR ltv
						ON ltv.LV_CODETYPE = ltt.LT_CODE
						AND ltv.LV_CODETYPE = :code_type_1
						AND ((ltv.LV_TYPEPARENT = :type_parent)  OR (ltv.LV_TYPEPARENT = -1))
						AND ltv.LV_CODEMAITRE = :code_maitre
						LEFT JOIN L_LISTETYPE_LISTE lll ON lll.LLL_INDEX = :numero_liste
						AND lll.LLL_CODE = ltv.LV_VALEUR
						WHERE ltt.LT_CODE = :code_type_2
				";
				
				$code_maitre = $this->entity->getID();
				$type_parent = Dictionnaire::getTableCodeFromEntity($this->entity);
				
				if($this->entity instanceof InventaireProduit) {
					if ($this->prefix === "TIP_") {
						$code_maitre = $this->entity->get(D::ip_code_type);
						$type_parent = TypeInventaireProduit::$tableCode;
					} else if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
					
				} else if($this->entity instanceof TypeInventaireProduit) {
					if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
				}
				
				$args = [
					":code_maitre"  => $code_maitre,
					":code_type_1"  => $lt_code,
					":code_type_2"  => $lt_code,
					":numero_liste" => $numero_liste,
					":type_parent"  => $type_parent,
				];
				
				$res = InfocobDB::getInstance()->fetch($sql, $args);
				
				return [
					"code" => $res["CODE"],
					"value" => $res["VALEUR"],
					"name" => $res["NOM"]
				];
			}
			
			return false;
		}
		
		private function getValueChampListeRadio() {
			if ($this->listeTypeType instanceof ListeTypeType && $this->entity instanceof Entity) {
				$lt_type = $this->listeTypeType->get(D::lt_type);
				$lt_code = $this->listeTypeType->getID();
				$numero_liste = $lt_type - 1199; // L_RADIO1 commence à l'index 1200 (index 1200 = table L_RADIO1) ==> 1200-1199 = 1
				
				$table = "L_RADIO".$numero_liste;
				$prefix = "LR".$numero_liste."_";
				
				$sql = "SELECT FIRST 1 lr.".$prefix."CODE as code, ltv.LV_VALEUR as valeur, ltt.LT_NOM as libelle, lr.".$prefix."NOM as nom
						FROM LISTETYPE_TYPE ltt
						LEFT JOIN LISTETYPE_VALEUR ltv
						ON ltv.LV_CODETYPE = ltt.LT_CODE
						AND ltv.LV_CODETYPE = :code_type_1
						AND ((ltv.LV_TYPEPARENT = :type_parent)  OR (ltv.LV_TYPEPARENT = -1))
						AND ltv.LV_CODEMAITRE = :code_maitre
						LEFT JOIN ".$table." lr ON cast(coalesce(lr.".$prefix."VALEUR, 0) as float) = cast(coalesce(ltv.LV_VALEUR, 0) as float)
						WHERE ltt.LT_CODE = :code_type_2
				";
				
				$code_maitre = $this->entity->getID();
				$type_parent = Dictionnaire::getTableCodeFromEntity($this->entity);
				
				if($this->entity instanceof InventaireProduit) {
					if ($this->prefix === "TIP_") {
						$code_maitre = $this->entity->get(D::ip_code_type);
						$type_parent = TypeInventaireProduit::$tableCode;
					} else if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
					
				} else if($this->entity instanceof TypeInventaireProduit) {
					if ($this->prefix === "FTI_") {
						$code_maitre = $this->entity->get(D::tip_codefamille);
						$type_parent = FamilleTypeInventaireProduit::$tableCode;
					}
				}
				
				$args = [
					":code_maitre"  => $code_maitre,
					":code_type_1"  => $lt_code,
					":code_type_2"  => $lt_code,
					":type_parent"  => $type_parent,
				];
				
				$res = InfocobDB::getInstance()->fetch($sql, $args);
				
				return [
					"code" => $res["CODE"],
					"value" => $res["VALEUR"],
					"name" => $res["NOM"]
				];
			}
			
			return false;
		}
		
		public function isLoaded() {
			if($this->listeTypeType instanceof ListeTypeType) {
				if(!empty($this->listeTypeType->getID())) {
					return true;
				}
			}
			
			return false;
		}
		
		public static function isChampLibre($string) {
			return preg_match("/^\w+_champlibre[0-9]+\w+$/mi", $string) === 1;
		}
		
		public static function getCodeFromFieldName($string) {
			preg_match("/^[A-Za-z]+_ChampLibre(.+)$/i", $string, $matches);
			return $matches[1] ?? "";
		}
		
	}
