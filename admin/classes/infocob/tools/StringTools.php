<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Infocob\Tools;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class StringTools {
		
		public function __construct() {
		
		}
		
		/**
		 * Valide la string pour une base SQL
		 *
		 * @return String Une chaine de caractères
		 */
		public static function ValidateToSql($var, $length = false, $encoding = "UTF-8") {
			$return = "";
			if(is_null($var)) $var = "";
			if($length === false || mb_strlen($var, $encoding) <= $length) {
				$return = $var;
			} elseif(mb_strlen($var, $encoding) > $length) {
				$return = mb_substr($var, 0, $length, $encoding);
			}
			
			return $return;
		}
		
		/**
		 * Netoye et traite la variable comme une String
		 *
		 * @return String Une chaine de caractères échapée
		 */
		public static function Clean($string, $length = false, $filter = FILTER_SANITIZE_SPECIAL_CHARS) {
			if($length === false || strlen($string) <= $length) {
				return (string) filter_var($string, $filter);
			} else {
				return (string) filter_var(substr($string, 0, $length), $filter);
			}
		}
		
		public static function formatFrDateInfocob($duree_jours) {
			if(!$duree_jours) {
				return "";
			}
			
			$duree_heure = round($duree_jours * 24, 4);
			
			if($duree_heure < 1) {
				$minutes      = round($duree_heure * 60, 4);
				$duree_format = floor($minutes) . " min" . (floor($minutes) > 1 ? "s" : "");
			} else {
				$duree_format = floor($duree_heure) . "H";
				
				$whole        = floor($duree_heure);
				$decimalHours = $duree_heure - $whole;
				
				if($decimalHours) {
					$minutes      = round($decimalHours * 60, 4);
					$duree_format .= number_format($minutes, 0, ".", " ");
				}
			}
			
			return $duree_format;
		}
		
		/**
		 * Netoye la variable d'eventuelle tentiive d'injection Firebird
		 *
		 * @return String Une chaine de caractères échapée
		 */
		public static function CleanInjectionFirebird($string, $quote = "'", $replace = "''") {
			$workString = $string;
			
			$workString = (preg_match("#\\\#", $workString)) ? preg_replace("#\\\#", '', $workString) : $workString;
			
			$workString = (preg_match("#[$quote]#", $workString)) ? preg_replace("#[$quote]#", "{%MOTIFTEMPORAIREINJECTION%}", $workString) : $workString;
			
			return (preg_match("#\{\%MOTIFTEMPORAIREINJECTION\%\}#", $workString)) ? preg_replace("#\{\%MOTIFTEMPORAIREINJECTION\%\}#", $replace, $workString) : $workString;
		}
		
		/**
		 * Supprime les accents & trnsforme les minuscules en majuscules
		 *
		 * @return String Une chaine de caractères échapée
		 */
		public static function Upper($string) {
			$string = self::RemoveAccents($string);
			
			return strtoupper($string);
		}
		
		/**
		 * Supprime les accents d'une chaine de caractères
		 *
		 * @return String Une chaint de caractères sans accents
		 */
		public static function RemoveAccents($string) {
			if(!preg_match('/[\x80-\xff]/', $string)) {
				return $string;
			}
			
			if(self::seems_utf8($string)) {
				$chars = array(
					// Decompositions for Latin-1 Supplement
					chr(195) . chr(128)            => 'A',
					chr(195) . chr(129)            => 'A',
					chr(195) . chr(130)            => 'A',
					chr(195) . chr(131)            => 'A',
					chr(195) . chr(132)            => 'A',
					chr(195) . chr(133)            => 'A',
					chr(195) . chr(135)            => 'C',
					chr(195) . chr(136)            => 'E',
					chr(195) . chr(137)            => 'E',
					chr(195) . chr(138)            => 'E',
					chr(195) . chr(139)            => 'E',
					chr(195) . chr(140)            => 'I',
					chr(195) . chr(141)            => 'I',
					chr(195) . chr(142)            => 'I',
					chr(195) . chr(143)            => 'I',
					chr(195) . chr(145)            => 'N',
					chr(195) . chr(146)            => 'O',
					chr(195) . chr(147)            => 'O',
					chr(195) . chr(148)            => 'O',
					chr(195) . chr(149)            => 'O',
					chr(195) . chr(150)            => 'O',
					chr(195) . chr(153)            => 'U',
					chr(195) . chr(154)            => 'U',
					chr(195) . chr(155)            => 'U',
					chr(195) . chr(156)            => 'U',
					chr(195) . chr(157)            => 'Y',
					chr(195) . chr(159)            => 's',
					chr(195) . chr(160)            => 'a',
					chr(195) . chr(161)            => 'a',
					chr(195) . chr(162)            => 'a',
					chr(195) . chr(163)            => 'a',
					chr(195) . chr(164)            => 'a',
					chr(195) . chr(165)            => 'a',
					chr(195) . chr(167)            => 'c',
					chr(195) . chr(168)            => 'e',
					chr(195) . chr(169)            => 'e',
					chr(195) . chr(170)            => 'e',
					chr(195) . chr(171)            => 'e',
					chr(195) . chr(172)            => 'i',
					chr(195) . chr(173)            => 'i',
					chr(195) . chr(174)            => 'i',
					chr(195) . chr(175)            => 'i',
					chr(195) . chr(177)            => 'n',
					chr(195) . chr(178)            => 'o',
					chr(195) . chr(179)            => 'o',
					chr(195) . chr(180)            => 'o',
					chr(195) . chr(181)            => 'o',
					chr(195) . chr(182)            => 'o',
					chr(195) . chr(182)            => 'o',
					chr(195) . chr(185)            => 'u',
					chr(195) . chr(186)            => 'u',
					chr(195) . chr(187)            => 'u',
					chr(195) . chr(188)            => 'u',
					chr(195) . chr(189)            => 'y',
					chr(195) . chr(191)            => 'y',
					// Decompositions for Latin Extended-A
					chr(196) . chr(128)            => 'A',
					chr(196) . chr(129)            => 'a',
					chr(196) . chr(130)            => 'A',
					chr(196) . chr(131)            => 'a',
					chr(196) . chr(132)            => 'A',
					chr(196) . chr(133)            => 'a',
					chr(196) . chr(134)            => 'C',
					chr(196) . chr(135)            => 'c',
					chr(196) . chr(136)            => 'C',
					chr(196) . chr(137)            => 'c',
					chr(196) . chr(138)            => 'C',
					chr(196) . chr(139)            => 'c',
					chr(196) . chr(140)            => 'C',
					chr(196) . chr(141)            => 'c',
					chr(196) . chr(142)            => 'D',
					chr(196) . chr(143)            => 'd',
					chr(196) . chr(144)            => 'D',
					chr(196) . chr(145)            => 'd',
					chr(196) . chr(146)            => 'E',
					chr(196) . chr(147)            => 'e',
					chr(196) . chr(148)            => 'E',
					chr(196) . chr(149)            => 'e',
					chr(196) . chr(150)            => 'E',
					chr(196) . chr(151)            => 'e',
					chr(196) . chr(152)            => 'E',
					chr(196) . chr(153)            => 'e',
					chr(196) . chr(154)            => 'E',
					chr(196) . chr(155)            => 'e',
					chr(196) . chr(156)            => 'G',
					chr(196) . chr(157)            => 'g',
					chr(196) . chr(158)            => 'G',
					chr(196) . chr(159)            => 'g',
					chr(196) . chr(160)            => 'G',
					chr(196) . chr(161)            => 'g',
					chr(196) . chr(162)            => 'G',
					chr(196) . chr(163)            => 'g',
					chr(196) . chr(164)            => 'H',
					chr(196) . chr(165)            => 'h',
					chr(196) . chr(166)            => 'H',
					chr(196) . chr(167)            => 'h',
					chr(196) . chr(168)            => 'I',
					chr(196) . chr(169)            => 'i',
					chr(196) . chr(170)            => 'I',
					chr(196) . chr(171)            => 'i',
					chr(196) . chr(172)            => 'I',
					chr(196) . chr(173)            => 'i',
					chr(196) . chr(174)            => 'I',
					chr(196) . chr(175)            => 'i',
					chr(196) . chr(176)            => 'I',
					chr(196) . chr(177)            => 'i',
					chr(196) . chr(178)            => 'IJ',
					chr(196) . chr(179)            => 'ij',
					chr(196) . chr(180)            => 'J',
					chr(196) . chr(181)            => 'j',
					chr(196) . chr(182)            => 'K',
					chr(196) . chr(183)            => 'k',
					chr(196) . chr(184)            => 'k',
					chr(196) . chr(185)            => 'L',
					chr(196) . chr(186)            => 'l',
					chr(196) . chr(187)            => 'L',
					chr(196) . chr(188)            => 'l',
					chr(196) . chr(189)            => 'L',
					chr(196) . chr(190)            => 'l',
					chr(196) . chr(191)            => 'L',
					chr(197) . chr(128)            => 'l',
					chr(197) . chr(129)            => 'L',
					chr(197) . chr(130)            => 'l',
					chr(197) . chr(131)            => 'N',
					chr(197) . chr(132)            => 'n',
					chr(197) . chr(133)            => 'N',
					chr(197) . chr(134)            => 'n',
					chr(197) . chr(135)            => 'N',
					chr(197) . chr(136)            => 'n',
					chr(197) . chr(137)            => 'N',
					chr(197) . chr(138)            => 'n',
					chr(197) . chr(139)            => 'N',
					chr(197) . chr(140)            => 'O',
					chr(197) . chr(141)            => 'o',
					chr(197) . chr(142)            => 'O',
					chr(197) . chr(143)            => 'o',
					chr(197) . chr(144)            => 'O',
					chr(197) . chr(145)            => 'o',
					chr(197) . chr(146)            => 'OE',
					chr(197) . chr(147)            => 'oe',
					chr(197) . chr(148)            => 'R',
					chr(197) . chr(149)            => 'r',
					chr(197) . chr(150)            => 'R',
					chr(197) . chr(151)            => 'r',
					chr(197) . chr(152)            => 'R',
					chr(197) . chr(153)            => 'r',
					chr(197) . chr(154)            => 'S',
					chr(197) . chr(155)            => 's',
					chr(197) . chr(156)            => 'S',
					chr(197) . chr(157)            => 's',
					chr(197) . chr(158)            => 'S',
					chr(197) . chr(159)            => 's',
					chr(197) . chr(160)            => 'S',
					chr(197) . chr(161)            => 's',
					chr(197) . chr(162)            => 'T',
					chr(197) . chr(163)            => 't',
					chr(197) . chr(164)            => 'T',
					chr(197) . chr(165)            => 't',
					chr(197) . chr(166)            => 'T',
					chr(197) . chr(167)            => 't',
					chr(197) . chr(168)            => 'U',
					chr(197) . chr(169)            => 'u',
					chr(197) . chr(170)            => 'U',
					chr(197) . chr(171)            => 'u',
					chr(197) . chr(172)            => 'U',
					chr(197) . chr(173)            => 'u',
					chr(197) . chr(174)            => 'U',
					chr(197) . chr(175)            => 'u',
					chr(197) . chr(176)            => 'U',
					chr(197) . chr(177)            => 'u',
					chr(197) . chr(178)            => 'U',
					chr(197) . chr(179)            => 'u',
					chr(197) . chr(180)            => 'W',
					chr(197) . chr(181)            => 'w',
					chr(197) . chr(182)            => 'Y',
					chr(197) . chr(183)            => 'y',
					chr(197) . chr(184)            => 'Y',
					chr(197) . chr(185)            => 'Z',
					chr(197) . chr(186)            => 'z',
					chr(197) . chr(187)            => 'Z',
					chr(197) . chr(188)            => 'z',
					chr(197) . chr(189)            => 'Z',
					chr(197) . chr(190)            => 'z',
					chr(197) . chr(191)            => 's',
					// Euro Sign
					chr(226) . chr(130) . chr(172) => 'E',
					// GBP (Pound) Sign
					chr(194) . chr(163)            => ''
				);
				
				$string = strtr($string, $chars);
			} else {
				// Assume ISO-8859-1 if not UTF-8
				$chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
				               . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
				               . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
				               . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
				               . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
				               . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
				               . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
				               . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
				               . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
				               . chr(252) . chr(253) . chr(255);
				
				$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
				
				$string              = strtr($string, $chars['in'], $chars['out']);
				$double_chars['in']  = array(
					chr(140),
					chr(156),
					chr(198),
					chr(208),
					chr(222),
					chr(223),
					chr(230),
					chr(240),
					chr(254)
				);
				$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
				$string              = str_replace($double_chars['in'], $double_chars['out'], $string);
			}
			
			return $string;
		}
		
		public static function CleanUrl($chaine) {
			$chaine = self::RemoveAccents($chaine);
			//$caracteresSpeciaux = array("#"=>"-", "("=>"-", ")"=>"-", "["=>"-", "]"=>"-", "'"=>"-", "~"=>"-", "$"=>"-", "&"=>"-", "%"=>"-", "*"=>"-", "@"=>"-", "ç"=>"-", "!"=>"-", "?"=>"-", ";"=>"-", ","=>"-", ":"=>"-", "/"=>"-", "\\"=>"-", "^"=>"-", "¨"=>"-", "€"=>"-", "{"=>"-", "}"=>"-", "|"=>"-", "+"=>"-", "-"=>"-", "."=>"-", "\"_"=>"-");
			//$chaine = strtr($chaine, $caracteresSpeciaux);
			
			$chaine = preg_replace("#[^A-Za-z0-9\-]#", "-", $chaine);
			
			$chaine = preg_replace("#\"#", "-", $chaine);
			$chaine = preg_replace("#\r#", "", $chaine);
			$chaine = preg_replace("#\n#", "", $chaine);
			$chaine = preg_replace("#[ ]{1,}#", "-", $chaine);
			
			$chaine = preg_replace("#[\-]{2,}#", "-", $chaine);
			$chaine = preg_replace("#[\-]{1}$#", "", $chaine);
			
			$chaine = strtolower($chaine);
			return $chaine;
		}
		
		protected static function seems_utf8($str) {
			$length = strlen($str);
			for($i = 0; $i < $length; $i ++) {
				$c = ord($str[ $i ]);
				if($c < 0x80) {
					$n = 0;
				}# 0bbbbbbb
				elseif(($c & 0xE0) == 0xC0) {
					$n = 1;
				}# 110bbbbb
				elseif(($c & 0xF0) == 0xE0) {
					$n = 2;
				}# 1110bbbb
				elseif(($c & 0xF8) == 0xF0) {
					$n = 3;
				}# 11110bbb
				elseif(($c & 0xFC) == 0xF8) {
					$n = 4;
				}# 111110bb
				elseif(($c & 0xFE) == 0xFC) {
					$n = 5;
				}# 1111110b
				else {
					return false;
				}# Does not match any model
				for($j = 0; $j < $n; $j ++) { # n bytes matching 10bbbbbb follow ?
					if((++ $i == $length) || ((ord($str[ $i ]) & 0xC0) != 0x80)) {
						return false;
					}
				}
			}
			
			return true;
		}
		
		public static function Tel($string) {
			return preg_replace("#[^0-9\+]#", "", $string);
		}
		
		public static function Web($string) {
			return preg_match("#^http[s]{0,1}\/\/\/#", $string)
				? $string
				: (!empty($string)
					? "http://" . $string
					: ""
				);
		}
		
		/**
		 * Renvoi un extrait du texte en paramètre
		 *
		 * @return String Un extrait de la chaine de caractère
		 */
		public static function Extract($string, $maxLength) {
			if(strlen($string) > $maxLength) {
				$string       = substr($string, 0, ($maxLength - 3));
				$posLastSpace = strrpos($string, " ");
				if($posLastSpace !== false && $posLastSpace > 0) {
					$string = substr($string, 0, $posLastSpace);
					
					return $string . "...";
				} else {
					return $string . "...";
				}
			} else {
				return $string;
			}
		}
		
		protected static function rtf_isPlainText($s) {
			$arrfailAt = array("*", "fonttbl", "colortbl", "datastore", "themedata");
			for($i = 0; $i < count($arrfailAt); $i ++) {
				if(!empty($s[ $arrfailAt[ $i ] ])) {
					return false;
				}
			}
			
			return true;
		}
		
		//--- Private function
		
		protected static function ProcessTags($tags, $line, $fcolor) {
			$html = "";
			global $color;
			global $size;
			global $bullets;
			
			// Supprime les espaces
			$tags = trim($tags);
			
			// trouve le debut de la ligne pointée
			if(preg_match("\\\pnindent", $tags)) {
				$html .= "<ul>";
				$html .= "<li>";
				
				$bullets += $line;
				$tags    = preg_replace("/\\\par/", "", $tags);
				$tags    = preg_replace("/\\\(tab)/", "", $tags);
			}
			if($line - $bullets == 0) {
				$tags = preg_replace("/\\\par/", "", $tags);
			} elseif($line - $bullets == 1) {
				if(preg_match("\\\pntext", $tags)) {
					
					$html .= "<li>";
					
					$tags = preg_replace("/\\\par/", "", $tags);
					$tags = preg_replace("/\\\(tab)/", "", $tags);
					$bullets ++;
				} else {
					//$html .= "</ul>";
					$bullets = 0;
				}
			}
			
			// convertit en italic
			if(preg_match("\\\i0", $tags)) {
				$html .= "</i>";
			} elseif(preg_match("\\\i", $tags)) {
				$html .= "<i>";
			}
			
			if(preg_match("\\\pard\\\qc", $tags)) {
				$html .= "";
			} elseif(preg_match("\\\pard\\\qr", $tags)) {
				$html .= "";
			} elseif(preg_match("\\\pard", $tags)) {
				$html .= "";
			}
			
			
			// supprime les \pard du tag pour ne pas qu'il y ai des confusions avec les \par.
			$tags = preg_replace("/\\\pard/", "", $tags);
			
			// converti les retours à la ligne.
			if(preg_match("/\\\par/", $tags)) {
				$html .= "<br>";
			}
			
			
			// convertit les cotes
			if(preg_match("/rquote/", $tags)) {
				$html .= "&acute;";
			}
			
			// Utilise le tableau de couleur en fonction de celle spécifiée.
			if(preg_match("\\\cf[0-9]", $tags)) {
				$numcolors = count($fcolor);
				for($i = 0; $i <= $numcolors; $i ++) {
					$test = "\\\cf" . ($i);
					if(preg_match($test, $tags)) {
						$color = $fcolor[ $i - 1 ];
					}
				}
			}
			
			// on remplace les \tab par des non break spacings
			if(preg_match("\\\(tab)", $tags)) {
				$html .= "&nbsp; &nbsp; &nbsp; &nbsp; ";
			}
			
			return $html;
		}
		
		//------------------ An Other RTF Function --------------------------------
		
		public static function Rtf3($text) {
			// Read the data from the input file.
			if(!strlen($text)) {
				return "";
			}
			
			$text = preg_replace("#\n#", "", $text);
			// Create empty stack array.
			$document = "";
			$stack    = array();
			$j        = - 1;
			// Read the data character-by- character…
			for($i = 0, $len = strlen($text); $i < $len; $i ++) {
				$c = $text[ $i ];
				
				// Depending on current character select the further actions.
				switch($c) {
					// the most important key word backslash
					case "\\":
						// read next character
						$nc = $text[ $i + 1 ];
						
						// If it is another backslash or nonbreaking space or hyphen,
						// then the character is plain text and add it to the output stream.
						if($nc == '\\' && self::rtf_isPlainText($stack[ $j ])) {
							$document .= '\\';
						} elseif($nc == '~' && self::rtf_isPlainText($stack[ $j ])) {
							$document .= ' ';
						} elseif($nc == '_' && self::rtf_isPlainText($stack[ $j ])) {
							$document .= '-';
						} // If it is an asterisk mark, add it to the stack.
						elseif($nc == '*') {
							$stack[ $j ]["*"] = true;
						}
						// If it is a single quote, read next two characters that are the hexadecimal notation
						// of a character we should add to the output stream.
						elseif($nc == "'") {
							$hex = substr($text, $i + 2, 2);
							if(self::rtf_isPlainText($stack[ $j ])) {
								$document .= html_entity_decode("&#" . hexdec($hex) . ";", ENT_QUOTES | ENT_XML1, "UTF-8");
							}
							//Shift the pointer.
							$i += 2;
							// Since, we’ve found the alphabetic character, the next characters are control word
							// and, possibly, some digit parameter.
						} elseif($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
							$word  = "";
							$param = null;
							
							// Start reading characters after the backslash.
							for($k = $i + 1, $m = 0; $k < strlen($text); $k ++, $m ++) {
								$nc = $text[ $k ];
								// If the current character is a letter and there were no digits before it,
								// then we’re still reading the control word. If there were digits, we should stop
								// since we reach the end of the control word.
								if($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
									if(empty($param)) {
										$word .= $nc;
									} else {
										break;
									}
									// If it is a digit, store the parameter.
								} elseif($nc >= '0' && $nc <= '9') {
									$param .= $nc;
								}
								// Since minus sign may occur only before a digit parameter, check whether
								// $param is empty. Otherwise, we reach the end of the control word.
								elseif($nc == '-') {
									if(empty($param)) {
										$param .= $nc;
									} else {
										break;
									}
								} else {
									break;
								}
							}
							// Shift the pointer on the number of read characters.
							$i += $m - 1;
							
							// Start analyzing what we’ve read. We are interested mostly in control words.
							$toText = "";
							switch(strtolower($word)) {
								// If the control word is "u", then its parameter is the decimal notation of the
								// Unicode character that should be added to the output stream.
								// We need to check whether the stack contains \ucN control word. If it does,
								// we should remove the N characters from the output stream.
								case "u":
									$toText  .= html_entity_decode("&#x" . dechex($param) . ";", ENT_QUOTES | ENT_XML1, "UTF-8");
									$ucDelta = @$stack[ $j ]["uc"];
									if($ucDelta > 0) {
										$i += $ucDelta;
									}
									break;
								// Select line feeds, spaces and tabs.
								case "par":
								case "page":
								case "column":
								case "line":
								case "lbr":
									$toText .= "\n";
									break;
								case "emspace":
								case "enspace":
								case "qmspace":
									$toText .= " ";
									break;
								case "tab":
									$toText .= "\t";
									break;
								// Add current date and time instead of corresponding labels.
								case "chdate":
									$toText .= date("m.d.Y");
									break;
								case "chdpl":
									$toText .= date("l, j F Y");
									break;
								case "chdpa":
									$toText .= date("D, j M Y");
									break;
								case "chtime":
									$toText .= date("H:i:s");
									break;
								// Replace some reserved characters to their html analogs.
								case "emdash":
									$toText .= html_entity_decode("&mdash;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "endash":
									$toText .= html_entity_decode("&ndash;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "bullet":
									$toText .= html_entity_decode("&#149;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "lquote":
									$toText .= html_entity_decode("&lsquo;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "rquote":
									$toText .= html_entity_decode("&rsquo;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "ldblquote":
									$toText .= html_entity_decode("&laquo;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								case "rdblquote":
									$toText .= html_entity_decode("&raquo;", ENT_QUOTES | ENT_XML1, "UTF-8");
									break;
								// Add all other to the control words stack. If a control word
								// does not include parameters, set &param to true.
								default:
									$stack[ $j ][ strtolower($word) ] = empty($param) ? true : $param;
									break;
							}
							// Add data to the output stream if required.
							if(self::rtf_isPlainText($stack[ $j ])) {
								$document .= $toText;
							}
						}
						
						$i ++;
						break;
					// If we read the opening brace {, then new subgroup starts and we add
					// new array stack element and write the data from previous stack element to it.
					case "{":
						array_push($stack, $stack[ $j ++ ]);
						break;
					// If we read the closing brace }, then we reach the end of subgroup and should remove
					// the last stack element.
					case "}":
						array_pop($stack);
						$j --;
						break;
					// Skip “trash”.
					case '\0':
					case '\r':
					case '\f':
					case '\n':
						break;
					// Add other data to the output stream if required.
					default:
						if(self::rtf_isPlainText($stack[ $j ])) {
							$document .= $c;
						}
						break;
				}
			}
			
			// Return result.
			return $document;
		}
		
		////////////////////////////////  RTF  /////////////////////////////////////
		
	}

?>
