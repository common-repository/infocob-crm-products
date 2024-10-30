<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\Parametres20Entity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\InfocobDB;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Parametres20Core extends Parametres20Entity {
		
		public static function getConfigCloudfichier() {
			$sql = "SELECT * "
			       . "FROM PARAMETRES20 "
			       . "WHERE PAR_PROFILE = 'CONFIGURATION' "
			       . "AND PAR_CREATEUR = 'Admin' "
			       . "AND PAR_NOM = 'CLOUD_FICHIERS.INI' ";
			
			$result = InfocobDB::getInstance()->fetch($sql);
			
			return $result;
		}
		
		public static function getFcConfigForUpload() {
			$param20 = static::getConfigCloudfichier();
			
			if($param20) {
				$config_ini = $param20["PAR_PARAMETRE"] ?? false;
				
				if($config_ini) {
					$config_to_use  = [];
					$config_ini_arr = explode('[CLOUD_FICHIER_CONFIG]', $config_ini);
					for($i = 1; $i < count($config_ini_arr); $i ++) {
						$config_ini = $config_ini_arr[ $i ];
						$config     = parse_ini_string($config_ini);
						
						if(isset($config["USERS"])) {
							$users    = $config["USERS"];
							$vendeurs = explode(",", $users);
							$config_to_use = $config;
							break;
							/*if(in_array(AuthentificationInfocob::InfocobCodeVendeur(), $vendeurs)) {
								$config_to_use = $config;
								break;
							}*/
						} else {
							$config_to_use = $config;
							break;
						}
					}
					
					return $config_to_use;
				}
			}
		}
		
	}
