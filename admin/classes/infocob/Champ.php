<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\StringTools;
	use PDO;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Champ {
		
		public $updated = false;
		protected $value = "";
		protected $type = "string";
		protected $typePDO = PDO::PARAM_STR;
		protected $length = false;
		protected $rtf_mon_amour = false;
		
		public function set($val = null) {
			$this->setFromLoad($val);
			$this->updated = true;
		}
		
		public function setFromLoad($val = null) {
			$value = null;
			switch($this->type) {
				default :
				case "lob" :
				case "string" :
				case "datetime" :
					$value = StringTools::ValidateToSql($val, $this->length);
					break;
				
				case "int" :
					$value = (int) $val;
					break;
				
				case "decimal" :
					$value = (float) $val;
					break;
				
				case "boolean" :
					$value = (
					(isset($val) && $val == 'T')
						? "T"
						: "F"
					);
					break;
				
			}
			
			
			$this->value = $value;
		}
		
		public function disableFromMaj() {
			$this->updated = false;
		}
		
		public function enableFromMaj() {
			$this->updated = true;
		}
		
		public function get() {
			return $this->value;
		}
		
		public function getLength() {
			return $this->length;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getTypePDO() {
			return $this->typePDO;
		}
		
		public function isRTF() {
			return $this->rtf_mon_amour;
		}
		
		public function load($definition) {
			$this->type = (isset($definition["type"]) ? $definition["type"] : $this->type);
			
			//$this->rtf_mon_amour = !empty($definition["rtf"]);
			
			switch($this->type) {
				default :
				case "decimal" :
				case "string" :
					$this->typePDO = PDO::PARAM_STR;
					break;
				
				case "lob" :
					$this->typePDO = PDO::PARAM_LOB;
					$this->rtf_mon_amour = true;
					break;
				
				/*case "bool" :
					$this->typePDO = \PDO::PARAM_BOOL;
				break;*/
				
				case "int" :
					$this->typePDO = PDO::PARAM_INT;
					break;
			}
			
			
			$this->length = (isset($definition["length"]) ? $definition["length"] : $this->length);
		}
	}
