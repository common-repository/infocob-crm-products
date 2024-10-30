<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use DateTime;
	use Exception;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\StringTools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Entity {
		
		protected static $champsDefinitions = array();
		protected static $_fields_rtf_names = null;
		protected static $tableName;
		protected static $tableCode;
		protected static $champPrincipalKey;
		protected static $champDroitKey;
		public $tmp_value_search = "";
		public $serializable_fields = [];
		protected $tmp_format_date = "d/m/Y";
		protected $tmp_nl2br = false;
		protected $tmp_format_int = array();
		protected $tmp_format_nbr = array();
		
		public function __construct() {
			foreach(static::$champsDefinitions as $key => $definition) {
				$champ = new Champ();
				$champ->load($definition);
				$this->$key = $champ;
			}
		}
		
		public static function inventaireTableCode() {
			if(static::$tableCode === 9) {
				// PRODUITFICHE
				return 0;
			} else if(static::$tableCode === 16) {
				// PRODUITMODELEFICHE
				return 1;
			}
			if(static::$tableCode === 1) {
				// INVENTAIREPRODUIT
				return 2;
			} else {
				return 100 + static::$tableCode;
			}
		}
		
		protected static function extractDatasDB($res) {
			$dos = array();
			foreach($res as $r) {
				$dos[] = self::extractDataDB($r);
			}
			
			return $dos;
		}
		
		protected static function extractDataDB($r) {
			$c = new static();
			$c->loadFromArrayComplete($r);
			
			return $c;
		}
		
		public function loadFromArrayComplete($res) {
			$this->loadFromArray($res);
		}
		
		public function loadFromArray($res) {
			foreach($res as $key => $r) {
				if(isset($this->$key) && is_a($this->$key, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ")) {
					$this->$key->setFromLoad($r);
				}
			}
		}
		
		public static function GetDroitCondition($groupe = 2, $prefix = "") {
			if ($prefix) {
				$prefix = sanitize_text_field($prefix) . ".";
			}
			return " (" . $prefix . static::$champDroitKey . " is null OR " . $prefix . static::$champDroitKey . " = '' OR SUBSTRING(" . $prefix . static::$champDroitKey . " from " . filter_var($groupe, FILTER_SANITIZE_NUMBER_INT) . " for 1) != 'I') ";
		}
		
		public function maj() {
			$id = $this->ID();
			if(!empty($id)) {
				return $this->update();
			} else {
				return $this->insert();
			}
		}
		
		protected function ID($value = null) {
			if(empty($value)) {
				if(isset($this->{static::$champPrincipalKey})) {
					return $this->{static::$champPrincipalKey}->get();
				}
			} else {
				if(isset($this->{static::$champPrincipalKey})) {
					$this->{static::$champPrincipalKey}->set($value);
				}
			}
		}
		
		public function update() {
			$sql  = "UPDATE " . static::$tableName . " SET ";
			$i    = 0;
			$args = array();
			foreach(static::$champsDefinitions as $champKey => $def) {
				if(isset($this->$champKey) && $champKey !== static::$champPrincipalKey && $this->$champKey->updated) {
					if($i) {
						$sql .= ", ";
					}
					$sql .= $champKey . "=:" . $champKey;
					
					
					$args[ ":" . $champKey ] = $this->$champKey;
					
					$i ++;
				}
			}
			
			$sql                                      .= " WHERE " . static::$champPrincipalKey . "=:" . static::$champPrincipalKey;
			$args[ ":" . static::$champPrincipalKey ] = $this->{static::$champPrincipalKey};
			// si aucun champ à insérer
			if(!$i) {
				throw new Exception("Aucun champ défini pour la mise à jour");
			}
			
			return InfocobDB::getInstance()->execute($sql, $args);
		}
		
		public function insert() {
			
			$sql         = "INSERT INTO " . static::$tableName;
			$sqlChamps   = " (";
			$sqlValues   = " VALUES(";
			$i           = 0;
			$args        = array();
			$needHackPDO = false;
			foreach(static::$champsDefinitions as $champKey => $def) {
				if(isset($this->$champKey) && $champKey !== static::$champPrincipalKey && $this->$champKey->updated) {
					//-- hack lob PDO firebird - https://github.com/php/php-src/pull/2183/
					//if ($this->$champKey->getTypePDO() != \PDO::PARAM_LOB) {
					
					if($i) {
						$sqlChamps .= ", ";
					}
					$sqlChamps .= $champKey;
					
					if($i) {
						$sqlValues .= ", ";
					}
					$sqlValues .= ":" . $champKey;
					$i ++;
					
					$args[ ":" . $champKey ] = $this->$champKey;
					/*}else {
						$needHackPDO = true;
					}*/
				}
			}
			$sqlChamps .= ")";
			$sqlValues .= ") RETURNING " . static::$champPrincipalKey;
			$sql       .= $sqlChamps . $sqlValues;
			
			// si aucun champ à insérer
			if(!$i) {
				throw new Exception("Aucun champ défini pour la mise à jour");
			}
			
			$res = InfocobDB::getInstance()->fetch($sql, $args);
			
			$this->ID($res[ static::$champPrincipalKey ]);
			
			//-- hack lob PDO firebird - https://github.com/php/php-src/pull/2183/
			/*if ($needHackPDO)
				$this->update();*/
			
			return ($this->ID($res[ static::$champPrincipalKey ]) !== "");
		}
		
		public function delete() {
			$sql = "DELETE FROM " . static::$tableName . " WHERE " . static::$champPrincipalKey . "= :" . static::$champPrincipalKey;
			
			return InfocobDB::getInstance()->execute($sql, array(
				":" . static::$champPrincipalKey => $this->{static::$champPrincipalKey}
			));
		}
		
		public function load($id = null, $droitConditions = true) {
			$sql = "SELECT tb.* "
			       . " FROM " . static::$tableName . " tb "
			       . " WHERE " . static::$champPrincipalKey . "=:" . static::$champPrincipalKey;
			
			if(empty($id)) {
				$id = $this->{static::$champPrincipalKey};
			}
			
			//InfocobDB::getInstance()->dump($sql, array(":" . static::$champPrincipalKey => $id));
			$res = InfocobDB::getInstance()->fetch($sql, array(":" . static::$champPrincipalKey => $id));
			
			if(!empty($res[ static::$champPrincipalKey ])) {
				$this->loadFromArray($res);
			}
		}
		
		public function display($field_name, $nb2br = false) {
			if($nb2br) {
				echo esc_html(nl2br($this->get($field_name)));
			} else {
				echo esc_html($this->get($field_name));
			}
		}
		
		public function get($field_name) {
			if(isset($this->$field_name)) {
				return $this->$field_name->get();
			}
			
			return "";
		}
		
		public function displayLibelle($field_name, $nb2br = false) {
			if($nb2br) {
				echo esc_html(nl2br($this->libelle($field_name)));
			} else {
				echo esc_html($this->libelle($field_name));
			}
		}
		
		public function libelle($field_name) {
			return Dictionnaire::libelle(static::$tableCode, $field_name);
		}
		
		public function displayDate($field_name, $format = "d/m/Y") {
			echo esc_html($this->getDate($field_name, $format));
		}
		
		public function getDate($field_name, $format = "d/m/Y") {
			$value = "";
			if(isset($this->$field_name)) {
				$value = $this->$field_name->get();
			}
			
			return DateTimeFr::ChangeFormat($value, $format);
		}
		
		public function displayTel($field_name) {
			echo esc_html(StringTools::Tel($this->get($field_name)));
		}
		
		public function displayWeb($field_name) {
			echo esc_html(StringTools::Web($this->get($field_name)));
		}
		
		/*     * * Dico infocob */
		
		public function displayDuree($field_name) {
			echo esc_html($this->getDuree($field_name));
		}
		
		public function getDuree($field_name) {
			return StringTools::formatFrDateInfocob($this->get($field_name));
		}
		
		public function displayAutoMultiple($field_name, $formatDate = "d/m/Y", $nl2br = false, $formatInt = array(), $formatNumber = array()) {
			echo esc_html($this->getAutoMultiple($field_name, $formatDate, $nl2br, $formatInt, $formatNumber));
		}
		
		public function getAutoMultiple($field_name, $formatDate = "d/m/Y", $nl2br = false, $formatInt = array(), $formatNumber = array()) {
			$this->tmp_format_date  = $formatDate;
			$this->tmp_nl2br        = $nl2br;
			$this->tmp_format_int   = $formatInt;
			$this->tmp_format_nbr   = $formatNumber;
			$this->tmp_value_search = "";
			
			return preg_replace_callback('/[A-Z]{1,3}(\_[A-Z0-9]{1,}){1,}/', function($matches) {
				if(!empty($matches[0])) {
					if($this->get($matches[0])) {
						return $this->getAuto($matches[0], $this->tmp_format_date, $this->tmp_nl2br, $this->tmp_format_int, $this->tmp_format_nbr);
					}
				}
				
				return "";
			}, $field_name);
		}
		
		public function getAuto($field_name, $formatDate = "d/m/Y", $nl2br = false, $formatInt = array(), $formatNumber = array()) {
			if(isset($this->$field_name)) {
				$typeChamp = $this->$field_name->getType();
				
				if($typeChamp == "datetime") {
					$this->tmp_value_search = $this->getTimestamp($field_name);
					
					return $this->getDate($field_name, $formatDate);
				} elseif($typeChamp == "int") {
					return $this->getNumber(
						$field_name, (isset($formatInt[0]) ? (int) $formatInt[0] : 0), (isset($formatInt[1]) ? $formatInt[1] : ","), (isset($formatInt[2]) ? $formatInt[2] : " ")
					);
				} elseif($typeChamp == "decimal") {
					return $this->getNumber(
						$field_name, (isset($formatNumber[0]) ? (int) $formatNumber[0] : 2), (isset($formatNumber[1]) ? $formatNumber[1] : ","), (isset($formatNumber[2]) ? $formatNumber[2] : " ")
					);
				} elseif($this->$field_name->isRTF()) {
					return $this->getBestFormat4ver($field_name, $nl2br);
				} else {
					return $this->get($field_name);
				}
			}
			
			return "";
		}
		
		public function getTimestamp($field_name) {
			$timestamp = "";
			if(isset($this->$field_name)) {
				$value = $this->$field_name->get();
			}
			
			return DateTimeFr::getTimestampFromString($value ?? "");
		}
		
		public function getNumber($field_name, $decimals = 2, $dec_point = ",", $thousands_sep = " ") {
			$value = "";
			if(isset($this->$field_name)) {
				$value = $this->$field_name->get();
			}
			
			return number_format($value, $decimals, $dec_point, $thousands_sep);
		}
		
		/* END dico */
		
		public function getBestFormat4ver($field_name, $nl2br = true) {
			$val = trim(
				strip_tags(
					@StringTools::Rtf3($this->get($field_name))
				)
			);
			
			return $nl2br ? nl2br($val) : $val;
		}
		
		public function set($field_name, $value) {
			if(!is_array($value)) {
				$value = strip_tags($value);
			}
			
			if(isset($this->$field_name)) {
				return $this->$field_name->set($value);
			}
			
			return false;
		}
		
		public function setDefaultValues() {
		}
		
		public function setDatetime($field_name, $value, $format = "d/m/Y H:i:s") {
			if(!is_array($value)) {
				$value = strip_tags($value);
			}
			
			if(isset($this->$field_name)) {
				if(!empty($value)) {
					$dt = DateTime::createFromFormat($format, $value);
					
					if($dt) {
						$this->$field_name->set($dt->format('Y-m-d H:i:s'));
					} else {
						$this->$field_name->set(null);
					}
				} else {
					$this->$field_name->set(null);
				}
			}
		}
		
		/*
		 * Obsolette, utiliser displayBestFormat4ver()
		 */
		
		public function hasField($field_name) {
			return (isset($this->$field_name) && is_a($this->$field_name, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ"));
		}
		
		public function getLength($field_name) {
			if(isset($this->$field_name)) {
				return $this->$field_name->getLength();
			}
			
			return false;
		}
		
		public function disableFromMaj($field_name) {
			if(isset($this->$field_name)) {
				$this->$field_name->disableFromMaj();
			}
		}
		
		public function enableFromMaj($field_name) {
			if(isset($this->$field_name)) {
				$this->$field_name->enableFromMaj();
			}
		}
		
		public function getTable($field_name) {
			if(isset($this->$field_name)) {
				return $this->$field_name->getType();
			}
			
			return "";
		}
		
		public function visible($field_name) {
			return Dictionnaire::visible(static::$tableCode, $field_name);
		}
		
		public function readonly($field_name) {
			return Dictionnaire::readonly(static::$tableCode, $field_name);
		}
		
		public function required($field_name) {
			return Dictionnaire::required(static::$tableCode, $field_name);
		}
		
		public function setsFromArray($res) {
			foreach($res as $key => $r) {
				if(isset($this->$key) && is_a($this->$key, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ")) {
					$this->$key->set($r);
				}
			}
		}
		
		public function displayTextBestFormat4ver($field_name) {
			return $this->displayBestFormat4ver($field_name, false);
		}
		
		public function displayBestFormat4ver($field_name, $nl2br = true) {
			echo esc_html($this->getBestFormat4ver($field_name, $nl2br));
		}
		
		public function getID() {
			if(isset($this->{static::$champPrincipalKey})) {
				return $this->{static::$champPrincipalKey}->get();
			} else {
				return null;
			}
		}
		
		
	}
