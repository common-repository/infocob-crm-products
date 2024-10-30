<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Upgrades\Versions;
	
	use Infocob\CRM\Products\Admin\Classes\Logger;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use Infocob\CRM\Products\Admin\Classes\Upgrades\Upgrade_version;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Upgrade_version_2 extends Upgrade_version {
		
		/*
		 * Move old logs folder to the new location
		 */
		public function upgrade() {
			$base_path = trim(ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH, "/") . "/logs";
			if(file_exists($base_path)) {
				$new_base_path = Logger::getLogsFolder();
				if(file_exists($new_base_path)) {
					Tools::copyDirectory($base_path, $new_base_path."/logs");
					Tools::deleteDirectory($base_path);
				}
			}
		}
	}
