<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\TypeInventaireProduitCore;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\DirectoryExplorer;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\ProduitInfocob;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class TypeInventaireProduit extends TypeInventaireProduitCore {
		use DirectoryExplorer;
		use ProduitInfocob;
	
	}
