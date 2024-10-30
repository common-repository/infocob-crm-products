<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\ProduitFicheCore;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\DirectoryExplorer;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\ProduitInfocob;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProduitFiche extends ProduitFicheCore {
		
		use DirectoryExplorer;
		use ProduitInfocob;
	
	}
