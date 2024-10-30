<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Dictionnaire;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\InventaireProduitEntity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\FamilleTypeInventaireProduit;
	use Infocob\CRM\Products\Admin\Classes\Infocob\TypeInventaireProduit;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class InventaireProduitCore extends InventaireProduitEntity {
		
		public $type_inventaire = null;
		public $famille_type_inventaire = null;
		public $vendeur = null;
		protected $field_joins = [];
		
		public function get($field_name) {
			if(isset($this->$field_name)) {
				return $this->$field_name->get();
			} else if($this->type_inventaire->hasField($field_name)) {
				return $this->type_inventaire->get($field_name);
			} else if($this->famille_type_inventaire->hasField($field_name)) {
				return $this->famille_type_inventaire->get($field_name);
			} else if(isset($this->field_joins[ $field_name ])) {
				return $this->field_joins[ $field_name ];
			}
			
			return "";
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
				
			} else if(isset($this->type_inventaire->$field_name)) {
				$typeChamp = $this->type_inventaire->$field_name->getType();
				
				if($typeChamp == "datetime") {
					$this->type_inventaire->tmp_value_search = $this->type_inventaire->getTimestamp($field_name);
					
					return $this->type_inventaire->getDate($field_name, $formatDate);
				} elseif($typeChamp == "int") {
					return $this->type_inventaire->getNumber(
						$field_name, (isset($formatInt[0]) ? (int) $formatInt[0] : 0), (isset($formatInt[1]) ? $formatInt[1] : ","), (isset($formatInt[2]) ? $formatInt[2] : " ")
					);
				} elseif($typeChamp == "decimal") {
					return $this->type_inventaire->getNumber(
						$field_name, (isset($formatNumber[0]) ? (int) $formatNumber[0] : 2), (isset($formatNumber[1]) ? $formatNumber[1] : ","), (isset($formatNumber[2]) ? $formatNumber[2] : " ")
					);
				} elseif($this->type_inventaire->$field_name->isRTF()) {
					return $this->type_inventaire->getBestFormat4ver($field_name, $nl2br);
				} else {
					return $this->type_inventaire->get($field_name);
				}
				
			} else if($this->famille_type_inventaire->$field_name) {
				$typeChamp = $this->famille_type_inventaire->$field_name->getType();
				
				if($typeChamp == "datetime") {
					$this->famille_type_inventaire->tmp_value_search = $this->famille_type_inventaire->getTimestamp($field_name);
					
					return $this->famille_type_inventaire->getDate($field_name, $formatDate);
				} elseif($typeChamp == "int") {
					return $this->famille_type_inventaire->getNumber(
						$field_name, (isset($formatInt[0]) ? (int) $formatInt[0] : 0), (isset($formatInt[1]) ? $formatInt[1] : ","), (isset($formatInt[2]) ? $formatInt[2] : " ")
					);
				} elseif($typeChamp == "decimal") {
					return $this->famille_type_inventaire->getNumber(
						$field_name, (isset($formatNumber[0]) ? (int) $formatNumber[0] : 2), (isset($formatNumber[1]) ? $formatNumber[1] : ","), (isset($formatNumber[2]) ? $formatNumber[2] : " ")
					);
				} elseif($this->famille_type_inventaire->$field_name->isRTF()) {
					return $this->famille_type_inventaire->getBestFormat4ver($field_name, $nl2br);
				} else {
					return $this->famille_type_inventaire->get($field_name);
				}
			}
			
			return "";
		}
		
		public function visible($field_name) {
			if(isset($this->$field_name)) {
				return Dictionnaire::visible(static::$tableCode, $field_name);
			} else if($this->type_inventaire->hasField($field_name)) {
				return $this->type_inventaire->visible($field_name);
			} else if($this->famille_type_inventaire->hasField($field_name)) {
				return $this->famille_type_inventaire->visible($field_name);
			} else {
				return true;
			}
		}
		
		public function libelle($field_name) {
			if(isset($this->$field_name)) {
				return Dictionnaire::libelle(static::$tableCode, $field_name);
			} else if($this->type_inventaire->hasField($field_name)) {
				return $this->type_inventaire->libelle($field_name);
			} else if($this->famille_type_inventaire->hasField($field_name)) {
				return $this->famille_type_inventaire->libelle($field_name);
			} else {
				return $field_name;
			}
		}
		
		public function loadFromArray($res) {
			$this->type_inventaire         = new TypeInventaireProduit();
			$this->famille_type_inventaire = new FamilleTypeInventaireProduit();
			
			foreach($res as $key => $r) {
				if(isset($this->$key) && is_a($this->$key, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ")) {
					$this->$key->setFromLoad($r);
				} else if($this->type_inventaire->hasField($key)) {
					$this->type_inventaire->set($key, $r);
				} else if($this->famille_type_inventaire->hasField($key)) {
					$this->famille_type_inventaire->set($key, $r);
				} else {
					$this->field_joins[ $key ] = $r;
				}
			}
		}
	}
