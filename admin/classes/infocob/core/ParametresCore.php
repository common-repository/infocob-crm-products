<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	// don't load directly
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\ParametresEntity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ParametresCore extends ParametresEntity {
		
		public static function getCorporama() {
			$sql    = "SELECT PAR_NOM, PAR_PARAMETRE FROM PARAMETRES WHERE PAR_NOM = 'CORPORAMA'";
			$result = InfocobDB::getInstance()->fetch($sql);
			
			if(!empty($result["PAR_PARAMETRE"])) {
				$ini = parse_ini_string($result["PAR_PARAMETRE"]);
				if(!empty($ini["E_SIRET"])) {
					return $ini;
				}
			}
			
			return false;
		}
		
	}
