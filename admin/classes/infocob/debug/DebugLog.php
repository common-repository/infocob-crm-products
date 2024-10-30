<?php
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Debug;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class DebugLog {
		
		protected $fp = null;
		protected $fpPath = null;
		
		public function __construct($file) {
			$this->fpPath = $file;
		}
		
		public function addNotice($message) {
			$this->addMessage($message, "notice");
		}
		
		protected function addMessage($message, $type) {
			if(!is_resource($this->fp)) {
				$this->open();
			}
			
			if(is_resource($this->fp)) {
				$logTxt = "[" . date("Y-m-d H:i:s") . "]\t[" . esc_html($type) . "]\t" . str_replace(array(
						"\t",
						"\n",
						"\r"
					), "", esc_html($message)) . "\r\n";
				
				fwrite($this->fp, $logTxt);
			}
		}
		
		protected function open() {
			if(!empty($this->fpPath)) {
				$this->fp = fopen($this->fpPath, 'a');
			}
		}
		
		public function addWarning($message) {
			$this->addMessage($message, "warning");
		}
		
		public function addError($message) {
			$this->addMessage($message, "error");
		}
		
		public function close() {
			if(is_resource($this->fp)) {
				fclose($this->fp);
			}
		}
	}

?>
