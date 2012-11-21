<?php
/**
 * Sanitize Library
 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.8 Blueberry Blend 
 * 
 */


	// MxP Tidy Library
	
	function sanitize_cdata2xhtml($cdata) {

		//Keep iframes
		$cdata = str_replace('</iframe>', ' </iframe>', $cdata);

		$config = array(
			'bare'			=> true,
			'indent'		=> false,
			'output-xhtml'		=> true,
			'input-encoding'	=> 'utf8',
			'clean'			=> true,
			'wrap'			=> 0,
			'doctype'		=> 'strict',
			'drop-empty-paras'	=> true,
			'hide-endtags'		=> true,
			'show-body-only'	=> true,
			'quote-ampersand'	=> true,
			'quote-nbsp' 		=> true

			//'preserve-entities'	=> true,
		);	
		$tidy = new tidy;
		$tidy->parseString($cdata, $config, 'utf8');
		$tidy->CleanRepair();

		$pattern = '/(<\?xml)(.*)(\/\?>)/i';
		$replacement = '';
		$cleanxml = preg_replace($pattern, $replacement, $tidy->value);

		$pattern = '/(([ ]*)class="MsoNormal c[0-9]")/i';
		$replacement = '';
		$no_mso = preg_replace($pattern, $replacement, $cleanxml);

		return xmlEntities($no_mso);
	
	}

	function sanitize_cdata2plaintext($cdata) {
		$text = htmlentities($cdata, ENT_QUOTES, 'UTF-8', false);
		return xmlEntities($text);
	}
	
	function sanitize_striphtml($cdata) {
		$text =  trim(htmlentities(strip_tags($cdata), ENT_QUOTES, 'UTF-8', false));
		return xmlEntities($text);
	}

	function xmlEntities($str) {
		$xml = array('&#34;','&#38;','&#38;','&#60;','&#62;','&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;','&#8220;','&#8221;','&#8216;','&#8217;','-','-');
		$html = array('&quot;','&amp;','&amp;','&lt;','&gt;','&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;','&ldquo;','&rdquo;','&lsquo;','&rsquo;','&ndash;','&mdash;');
		$str = str_replace($html,$xml,$str);
		$str = str_ireplace($html,$xml,$str);
		return $str;
	}
