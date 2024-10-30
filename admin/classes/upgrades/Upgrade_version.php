<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Upgrades;
	
	abstract class Upgrade_version implements Upgrade_version_interface {
		protected $version;
		
		public function __construct() {
			$this->version = (int)substr(get_class($this), -1);
			$this->upgrade();
		}
		
	}
