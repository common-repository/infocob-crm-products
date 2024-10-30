<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Core;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Entities\ProduitModeleFicheEntity;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\DirectoryExplorer;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\ProduitInfocob;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProduitModeleFiche extends ProduitModeleFicheEntity {
		
		use DirectoryExplorer;
		use ProduitInfocob;
	
	}
