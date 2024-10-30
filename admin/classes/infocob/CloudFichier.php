<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Core\CloudFichierCore;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\DirectoryExplorer;
	use Infocob\CRM\Products\Admin\Classes\Infocob\Traits\ProduitInfocob;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CloudFichier extends CloudFichierCore {
		
		use DirectoryExplorer;
		use ProduitInfocob;
	
	}
