<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Traits;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	trait DirectoryExplorer {
		
		protected $finfo = false;
		protected $fichiers = null;
		
		
		public function exploreDirectory($basePath, $baseUri, $folder, $regexp = "", $subnom = "", $dateformat = "d/m/Y H:i", $mime_content_types = null) {
			if(!$this->finfo) {
				$this->finfo = finfo_open(FILEINFO_MIME_TYPE);
			}
			
			$path = $basePath . $folder;
			
			$fichiers = array();
			
			if(is_dir($path)) {
				if($dh = opendir($path)) {
					while(($file = readdir($dh)) !== false) {
						if($file != "." && $file != "..") {
							if(!is_dir($path . "/" . $file) && is_file($path . "/" . $file)) {
								if(!empty($regexp)) {
									if(preg_match($regexp, $file)) {
										$contentType = finfo_file($this->finfo, $basePath . $folder . "/" . $file);
										if(empty($mime_content_types) || in_array($contentType, $mime_content_types)) {
											$filetime   = filemtime($basePath . $folder . "/" . $file);
											$fichiers[] = array(
												"url"       => $baseUri . $folder . "/" . $file,
												"path"      => $basePath . $folder . "/" . $file,
												"name"       => $file,
												"fullname"  => $subnom . $file,
												"date"      => date($dateformat, $filetime),
												"timestamp" => $filetime,
												"folder"    => $subnom,
											);
										}
									}
								} else {
									$contentType = finfo_file($this->finfo, $basePath . $folder . "/" . $file);
									if(empty($mime_content_types) || in_array($contentType, $mime_content_types)) {
										$filetime   = filemtime($basePath . $folder . "/" . $file);
										$fichiers[] = array(
											"url"       => $baseUri . $folder . "/" . $file,
											"path"      => $basePath . $folder . "/" . $file,
											"name"       => $file,
											"fullname"  => $subnom . $file,
											"date"      => date($dateformat, $filetime),
											"timestamp" => $filetime,
											"folder"    => $subnom,
										);
									}
								}
							} elseif(is_dir($path . "/" . $file)) {
								$fichiers = array_merge($fichiers, $this->exploreDirectory($basePath, $baseUri, $folder . "/" . $file, $regexp, $subnom . $file . "/", $dateformat, $mime_content_types));
							}
						}
					}
					closedir($dh);
				}
			}
			
			return $fichiers;
		}
	}
