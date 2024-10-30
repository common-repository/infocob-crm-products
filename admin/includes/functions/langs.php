<?php
	if(!function_exists('icp_e')) {
		function icp_e($string) {
			if(function_exists('pll_e')) {
				pll_e($string);
			} else {
				echo esc_html($string);
			}
		}
	}
	
	if(!function_exists('icp__')) {
		function icp__($string) {
			if(function_exists('pll__')) {
				return pll__($string);
			} else {
				return $string;
			}
		}
	}
	
	if(!function_exists('icp_translate_string')) {
		function icp_translate_string($string, $lang) {
			if(function_exists('pll_translate_string')) {
				return pll_translate_string($string, $lang);
			} else {
				return $string;
			}
		}
	}
?>
