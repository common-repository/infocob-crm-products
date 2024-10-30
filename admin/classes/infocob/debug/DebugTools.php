<?php
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Debug;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class DebugTools {
		
		protected static $_debugMode = false;
		
		
		public function __construct() {
		}
		
		/**
		 * Display errors
		 */
		public static function DebugMode($isDebugMode) {
			if($isDebugMode == true) {
				ini_set("display_errors", "on");
				error_reporting(E_ALL);
				self::$_debugMode = true;
			} else {
				ini_set("display_errors", "off");
				self::$_debugMode = false;
			}
		}
		
		/**
		 * Return true if debug mode enabled
		 */
		public static function IsDebugMode() {
			return self::$_debugMode;
		}
		
		/**
		 * Display an error
		 */
		public static function SendError($error, $die = false) {
			if(self::$_debugMode) {
				if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow") && DebugWindow::IsDebugDisplay()) {
					DebugWindow::AddDumpMgs("<p style='color:red'>" . esc_html($error) . "</p>");
				} else {
					echo "<p style='color:red'>" . esc_html($error) . "</p>";
				}
			}
			if($die) {
				die();
			}
		}
		
		
		/**
		 * Display all var content + state
		 */
		public static function Dump($var, $color = "black") {
			if(self::$_debugMode) {
				if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow") && DebugWindow::IsDebugDisplay()) {
					DebugWindow::AddDumpMgs("<pre style='color:" . esc_attr($color) . "'>" . var_export($var, true) . "</pre>");
				} else {
					echo "<pre style='color:" . esc_attr($color) . "'>";
					var_dump($var);
					echo "</pre>";
				}
			}
		}
		
		/**
		 * Display all var content + state
		 */
		public static function TableDump($var, $color = "black") {
			if(self::$_debugMode) {
				if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow") && DebugWindow::IsDebugDisplay()) {
					DebugWindow::AddDumpMgs("<pre style='color:" . esc_attr($color) . "'>" . print_r($var, true) . "</pre>");
				} else {
					echo "<pre style='color:" . esc_attr($color) . "'>";
					print_r($var);
					echo "</pre>";
				}
			}
		}
	}

?>
