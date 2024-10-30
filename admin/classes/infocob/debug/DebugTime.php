<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Debug;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class DebugTime {
		
		protected static $time_start;
		protected static $time_stop;
		protected static $time_step;
		protected static $time_step_0;
		
		public function __construct() {
		}
		
		public static function Start() {
			self::$time_start  = microtime(true);
			self::$time_step_0 = self::$time_start;
		}
		
		public static function Step($stepname = "Step") {
			self::$time_step = microtime(true);
			$execution_time  = (self::$time_step - self::$time_step_0);
			
			
			if(DebugTools::IsDebugMode()) {
				if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow") && DebugWindow::IsDebugDisplay()) {
					DebugWindow::AddTimeMgs('Étape "' . esc_html($stepname) . '\" executée en ' . esc_html($execution_time) . 's');
				} else {
					echo '<hr />';
					echo "Étape \"" . esc_html($stepname) . "\" executée en " . esc_html($execution_time) . "s";
					echo '<hr />';
				}
			}
			
			self::$time_step_0 = self::$time_step;
		}
		
		public static function Stop() {
			self::$time_step = microtime(true);
			$execution_time  = (self::$time_step - self::$time_start);
			
			
			if(DebugTools::IsDebugMode()) {
				if(class_exists("\Infocob\CRM\Products\Admin\Classes\Infocob\Debug\DebugWindow") && DebugWindow::IsDebugDisplay()) {
					DebugWindow::AddTimeMgs('Fin du déboggage executé en ' . esc_html($execution_time) . 's');
				} else {
					echo '<hr />';
					echo "Fin du déboggage executé en  " . esc_html($execution_time) . "s";
					echo '<hr />';
				}
			}
			
			self::$time_stop = self::$time_step;
		}
		
	}
