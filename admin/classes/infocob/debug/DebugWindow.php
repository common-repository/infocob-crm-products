<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Debug;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class DebugWindow {
		
		protected static $_debugDisplay;
		protected static $_errors = array();
		protected static $_debugMgs = array();
		protected static $_timeMgs = array();
		
		/**
		 * Affiche les erreurs ou non dans une nouvelle fenêtre
		 *
		 * @param $isDebugMode True or False
		 */
		public static function DebugDisplay($isDebugMode) {
			if($isDebugMode == true) {
				self::$_debugDisplay = true;
				set_error_handler('\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow::ErrorHandler');
			} else {
				self::$_debugDisplay = false;
			}
		}
		
		/**
		 * Retourne true si le debug mode est activé
		 */
		public static function IsDebugDisplay() {
			return self::$_debugDisplay;
		}
		
		public static function ErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
			
			self::$_errors[] = self::ExportError(debug_backtrace());
			
		}
		
		protected static function ExportError($tracesback) {
			$error = array();
			foreach($tracesback as $traceback) {
				if(!isset($error["type"]) && isset($traceback["args"]) && isset($traceback["args"][0])) {
					$error["type"] = self::ErrorType($traceback["args"][0]);
				}
				if(!isset($error["name"]) && isset($traceback["args"]) && isset($traceback["args"][1])) {
					$error["name"] = $traceback["args"][1];
				}
				$error["trace"][] = array(
					"file"     => isset($traceback["file"]) ? $traceback["file"] : "",
					"function" => isset($traceback["function"]) ? $traceback["function"] : "",
					"line"     => isset($traceback["line"]) ? $traceback["line"] : "",
				);
				
			}
			
			return $error;
		}
		
		public static function ErrorType($type) {
			switch($type) {
				case E_ERROR: // 1 //
					return 'E_ERROR';
				case E_WARNING: // 2 //
					return 'E_WARNING';
				case E_PARSE: // 4 //
					return 'E_PARSE';
				case E_NOTICE: // 8 //
					return 'E_NOTICE';
				case E_CORE_ERROR: // 16 //
					return 'E_CORE_ERROR';
				case E_CORE_WARNING: // 32 //
					return 'E_CORE_WARNING';
				case E_COMPILE_ERROR: // 64 //
					return 'E_COMPILE_ERROR';
				case E_COMPILE_WARNING: // 128 //
					return 'E_COMPILE_WARNING';
				case E_USER_ERROR: // 256 //
					return 'E_USER_ERROR';
				case E_USER_WARNING: // 512 //
					return 'E_USER_WARNING';
				case E_USER_NOTICE: // 1024 //
					return 'E_USER_NOTICE';
				case E_STRICT: // 2048 //
					return 'E_STRICT';
				case E_RECOVERABLE_ERROR: // 4096 //
					return 'E_RECOVERABLE_ERROR';
				case E_DEPRECATED: // 8192 //
					return 'E_DEPRECATED';
				case E_USER_DEPRECATED: // 16384 //
					return 'E_USER_DEPRECATED';
			}
			
			return "";
		}
		
		public static function AddDumpMgs($mgs) {
			
			self::$_debugMgs[] = $mgs;
			
		}
		
		public static function AddTimeMgs($mgs) {
			
			self::$_timeMgs[] = $mgs;
			
		}
	}
