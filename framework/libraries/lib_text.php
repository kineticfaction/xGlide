<?php
/**
 * Text Library
 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.8 Blueberry Blend 
 * 
 */

	function strip_tabs_nl($string) {
		$string = str_replace ("\t", '', $string);
		$string = str_replace ("\r\n", '', $string);
		$string = str_replace ("\n", '', $string);
		return $string;
	}

	function string_keywords($string) {
		// Returns csv string of most used words in a string.
		$string = strip_tags($string);
		$words = str_word_count($string, 1);
		$frequency = array_count_values($words);
		arsort($frequency);
		$array = array();
		$i = 0;
		foreach ($frequency as $key => $value) {
			if($i == core_settings::i()->get('LANG_KEYWORDS_COUNT')) { break; }
			if(!in_array(strtolower($key), core_settings::i()->get('LANG_KEYWORDS_EXCLUDE'))) {
				$array[] = $key;
				$i++;
			}
		}
		return implode(", ", $array);
	}

	function string_description($string) {
		// Returns a sentence within 100 to 150 Chars.
		// If it can't will pick a nearest word to 140 and add hellip
		if(strlen($string) > 100) {
			$string = strip_tags($string);
			$string = substr($string,0,150);
			$pos = strpos($string, ".", (100));
			if($pos > 100 && $pos < 150) {
				$string = substr($string, 0, ($pos + 1));
			} else {
				$pos = strpos($string, " ", (140 - 4));
				// Check to see if the last character is a comma if so delete it.
				$poscomma = strpos($string, ",", $pos - 1);
				if(($poscomma + 1) != $pos) {
					$string = substr($string, 0, $pos) . "&#8230;";
				} else {
					$string = substr($string, 0, $pos - 1) . "&#8230;";
				}
			}
		}
		return $string;
	}
