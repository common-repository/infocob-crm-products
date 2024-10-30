<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class CryptTools {
		
		public static function encrypt($plain_text, $key, $iv_len = 16) {
			$plain_text .= "\x13";
			$n          = strlen($plain_text);
			if($n % 16) {
				$plain_text .= str_repeat("\0", 16 - ($n % 16));
				$i          = 0;
				$enc_text   = self::get_rnd_iv($iv_len);
				$iv         = substr($key ^ $enc_text, 0, 512);
				while($i < $n) {
					$block    = substr($plain_text, $i, 16) ^ pack('H*', sha1($iv));
					$enc_text .= $block;
					$iv       = substr($block . $iv, 0, 512) ^ $key;
					$i        += 16;
				}
				
				return base64_encode($enc_text);
			} else {
			}
		}
		
		private static function get_rnd_iv($iv_len) {
			$iv = '';
			while($iv_len -- > 0) {
				$iv .= chr(mt_rand() & 0xff);
			}
			
			return $iv;
		}
		
		public static function decrypt($enc_text, $key, $iv_len = 16) {
			$enc_text   = base64_decode($enc_text);
			$n          = strlen($enc_text);
			$i          = $iv_len;
			$plain_text = '';
			$iv         = substr($key ^ substr($enc_text, 0, $iv_len), 0, 512);
			while($i < $n) {
				$block      = substr($enc_text, $i, 16);
				$plain_text .= $block ^ pack('H*', sha1($iv));
				$iv         = substr($block . $iv, 0, 512) ^ $key;
				$i          += 16;
			}
			
			return stripslashes(preg_replace('/\\x13\\x00*$/', '', $plain_text));
		}
	}
