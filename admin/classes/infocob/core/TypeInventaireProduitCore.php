<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\TypeInventaireProduitEntity;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class TypeInventaireProduitCore extends TypeInventaireProduitEntity {
		
		protected $field_joins = array();
		
		public function loadFromArray($res) {
			foreach($res as $key => $r) {
				if(isset($this->$key) && is_a($this->$key, "\Infocob\CRM\Products\Admin\Classes\Infocob\Champ")) {
					$this->$key->setFromLoad($r);
				} else {
					$this->field_joins[ $key ] = $r;
				}
			}
		}
		
		public function get($field_name) {
			if(isset($this->$field_name)) {
				return $this->$field_name->get();
			} else if(isset($this->field_joins[ $field_name ])) {
				return $this->field_joins[ $field_name ];
			}
			
			return "";
		}
	}
