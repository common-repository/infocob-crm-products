<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Encoding {
		
		public static function IsUTF8($var) {
			if(is_string($var)) {
				return (strcasecmp(mb_detect_encoding($var, 'utf-8', true), "utf-8") === 0);
			} elseif(is_array($var)) {
				foreach($var as $v) {
					return self::IsUTF8($v);
				}
			}
			
			return - 1;
		}
		
		public static function CaracteresToChange($string) {
			$string = str_replace(chr(128), "euros", $string);
			$string = str_replace(chr(146), "'", $string);
			
			return str_replace(chr(146), "'", $string);
		}
		
		public static function UTF8($var){
			if($var === null) $var = "";
			if(is_string($var)){
				if(!mb_detect_encoding($var, 'utf-8', true)){
					return mb_convert_encoding(self::CaracteresToChange($var), 'utf-8', 'ISO-8859-1');
				}
			}elseif(is_array($var)){
				array_walk_recursive($var, function(&$item, $key){
					if($item === null) $item = "";
					if(!mb_detect_encoding($item, 'utf-8', true)){
						$item = mb_convert_encoding(Encoding::CaracteresToChange($item), 'utf-8', 'ISO-8859-1');
					}
				});
				return $var;
			}
			return $var;
		}
		
		public static function ISO($var){
			if($var === null) $var = "";
			if(is_string($var)){
				if(mb_detect_encoding($var, 'utf-8', true)){
					return mb_convert_encoding(self::CaracteresToChange($var), 'ISO-8859-1', 'utf-8');
				}
			}elseif(is_array($var)){
				array_walk_recursive($var, function(&$item, $key){
					if($item === null) $item = "";
					if(mb_detect_encoding($item, 'utf-8', true)){
						$item = mb_convert_encoding(Encoding::CaracteresToChange($item), 'ISO-8859-1', 'utf-8');
					}
				});
				return $var;
			}
			return $var;
		}
		
	}
