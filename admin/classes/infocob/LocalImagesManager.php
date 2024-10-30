<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\StringTools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class LocalImagesManager {
		
		protected $folderPath = "";
		protected $folderURL = "";
		protected $imagesNames = "";
		
		protected $productName = "";
		
		protected $images = array();
		
		public function __construct($folderPath = "", $folderURL = "", $imagesNames = array(), $productName = "") {
			$this->folderPath  = $folderPath;
			$this->folderURL   = $folderURL;
			$this->imagesNames = $imagesNames;
			$this->productName = $productName;
		}
		
		public function getImages() {
			if(empty($this->images)) {
				if(is_dir($this->folderPath)) {
					foreach($this->imagesNames as $photoName) {
						if(is_file($this->folderPath . $photoName)) {
							
							$this->images[] = array(
								"path" => $this->folderPath . $photoName,
								"url"  => $this->folderURL . $photoName,
							);
							
						} elseif(is_file($this->folderPath . "l_" . $photoName)) {
							
							$this->images[] = array(
								"path" => $this->folderPath . "l_" . $photoName,
								"url"  => $this->folderURL . "l_" . $photoName,
							);
							
						}
					}
				}
			}
			
			return $this->images;
		}
		
		public function getThumbs($sizes = ['full'], $inf_sizes = []) {
			$imgs = $this->getImages();
			
			if(empty($sizes)) {
				$sizes = ["full"];
			} elseif(is_string($sizes)) {
				$sizes = [$sizes];
			}
			
			foreach ($imgs as $index => $img) {
				$match = empty($inf_sizes);
				foreach ($inf_sizes as $inf_size) {
					if(preg_match("/^" . preg_quote($inf_size, "/") . "[0-9]+\.(jpg|png)/mi", basename($img["path"])) === 1) {
						$match = true;
					}
				}
				
				if(!$match) {
					unset($imgs[$index]);
				}
			}
			
			foreach($imgs as &$img) {
				$img["thumbURL"] = $img["url"];
				$img["thumbPATH"] = $img["path"];
				
				foreach ($sizes as $size) {
					$imageDest = $this->getThumb($img, $size);
					
					if ($this->resizeWP($img, $imageDest, $size)) {
						$img["thumbURL-" . $size] = $imageDest["url"];
						$img["thumbPATH-" . $size] = $imageDest["path"];
					}
				}
			}
			
			return $imgs;
		}
		
		public function getImageUne($size = "full") {
			
			$imgs     = $this->getImages();
			$imageUne = false;
			if(!empty($imgs) && $imgs[0] && $imgs[0]["path"]) {
				$imageUne = $imgs[0];
			}
			
			
			$imageDest = $this->getThumb($imageUne, $size);
			
			if($this->resizeWP($imageUne, $imageDest, $size)) {
				$imageUne = $imageDest;
			}
			
			
			return $imageUne;
		}
		
		public function getImageUneURL($size = "full") {
			$imageUne = $this->getImageUne($size);
			
			return $imageUne ? $imageUne["url"] : false;
		}
		
		public function getImageUnePath($size = "full") {
			$imageUne = $this->getImageUne($size);
			
			return $imageUne ? $imageUne["path"] : false;
		}
		
		protected function resizeWP($imageSrc, $imageDest, $size) {
			$sizesWP = $this->getWPImagesSizes($size);
			
			if(($size === "full" || $sizesWP)
			   && is_file($imageSrc["path"])
			   && $this->needToRefresh($imageSrc["path"], $imageDest["path"])
			) {
				if(file_exists($imageDest["path"])) {
					unlink($imageDest["path"]);
				}
				
				$imgEditorWP = wp_get_image_editor($imageSrc["path"]);
				if(empty($imgEditorWP->errors)) {
					if ($size !== "full") {
						$imgEditorWP->resize($sizesWP["width"], $sizesWP["height"], $sizesWP["crop"]);
					}
					
					$imgEditorWP->save($imageDest["path"]);
				}
			}
			
			return is_file($imageDest["path"]);
		}
		
		protected function cleanUpUnusedFiles($path_file = false) {
			if(file_exists($this->folderPath)) {
				foreach (glob(trim($this->folderPath, "/\\") . "/*") as $filename) {
					if(!in_array(basename($filename), $this->imagesNames) || ($path_file !== false && $path_file === $filename)) {
						unlink($filename);
					}
				}
			}
		}
		
		protected function getThumb($image, $size) {
			$replacement = $this->productName
				? StringTools::CleanUrl($this->productName) . '-' . $size . '-$1$2'
				: '$1-' . $size . '$2';
			
			$thumb = array(
				"path" => preg_replace(
					'#([^\.\/\\\]{0,})(\.[^\.\/\\\]{0,})$#',
					$replacement,
					$image["path"]
				),
				"url"  => preg_replace(
					'#([^\.\/\\\]{0,})(\.[^\.\/\\\]{0,})$#',
					$replacement,
					$image["url"]
				),
			);
			
			return $thumb;
		}
		
		protected function needToRefresh($sourc, $dest) {
			if(file_exists($sourc)) {
				clearstatcache(true);
				$sourcedate1 = filemtime($sourc);// - (7*24*60*60);
				$sourcedate2 = filectime($sourc);
				
				$sourcedate = ($sourcedate2 > $sourcedate1) ? $sourcedate2 : $sourcedate1;
				
				$destdate1 = file_exists($dest) ? filemtime($dest) : 0;
				$destdate2 = file_exists($dest) ? filectime($dest) : 0;
				$destdate  = ($destdate1 > $destdate2) ? $destdate2 : $destdate1;
				
			}
			
			return (!file_exists($dest) || ($sourcedate > $destdate));
		}
		
		protected function getWPImagesSizes($size = "full") {
			$allSizes = get_intermediate_image_sizes();
			
			$sizesWP = false;
			foreach($allSizes as $sizeWP) {
				if($sizeWP === $size) {
					$sizesWP = array(
						"width"  => get_option($sizeWP . "_size_w"),
						"height" => get_option($sizeWP . "_size_h"),
						"crop"   => get_option($sizeWP . "_size_crop"),
					);
					break;
				}
			}
			
			return $sizesWP;
		}
		
	}
