<?php
	
	
	namespace Infocob\CRM\Products\Admin\Classes;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Polylang {
		protected static $translations = [];
		
		public static function addTranslations($translations) {
			if(empty(static::$translations)) {
				static::$translations = $translations;
			} else {
				foreach($translations as $k => $s) {
					if(isset(static::$translations[ $k ]) && static::$translations[ $k ] !== $s) {
						$i = 0;
						do {
							$kn = $k . "-" . $i;
							$i ++;
						} while(isset(static::$translations[ $kn ]));
						$k = $kn;
					}
					static::$translations[ $k ] = $s;
				}
			}
		}
		
		public static function registerStrings() {
			foreach(static::$translations as $name => $string) {
				pll_register_string($name, $string, 'infocob-crm-products', true);
			}
		}
		
		public static function register_string() {
			Polylang::addTranslations([]);
			
			if(function_exists('pll_register_string')) {
				Polylang::registerStrings();
			}
		}
		
		public static function isPostTypeMultilanguages($post_type) {
			if(function_exists("pll_is_translated_post_type") && pll_is_translated_post_type($post_type)) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function isTaxonomyMultilanguages($taxonomy) {
			if(function_exists("pll_is_translated_taxonomy") && pll_is_translated_taxonomy($taxonomy)) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function getCurrentLanguage($post_type = false) {
			$language = substr(get_locale(), 0, 2);
			if (function_exists("pll_languages_list") && (($post_type === false) || ($post_type !== false && Polylang::isPostTypeMultilanguages($post_type)))) {
				$language = pll_current_language();
			}
			
			return $language;
		}
	}
