<?php

	/**
	 * Core Logging - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	class core_logging {

		// <editor-fold defaultstate="collapsed" desc=" SINGLETON : $instance __construct i _clone ">
		static private  $instance = null; 
		static public function i() { 
			if(self::$instance === null) { 
				$c = __CLASS__; 
				self::$instance = new $c(); 
			} 
			return self::$instance; 
		} 
		private function __construct() { }
		public function __clone() {
			throw new Exception("Cannot clone ".__CLASS__." class"); 
		} 
		// </editor-fold>
		
		private $data = array();	

		public function add($name, $value) {
			if($value != "") {
				$this->data[$name] = $value;
			} else {
				$this->data[$name] = NULL;
			}
		}

		public function write() {
			$log = fopen(core_settings::i()->get('CONFIG_SETTINGS_LOGFILE'), "a");
			if(count($this->data) > 0)  {
				foreach($this->data as $name => $value) {
					if(isset($_SESSION['user']->str_username)) {
						fwrite($log, '['.date('Y-m-d H:i:s').']:'.$_SESSION['user']->str_username.': ('.$name.')' .  $this->prepare($value) . "\n");
					} else {
						fwrite($log, '['.date('Y-m-d H:i:s').']:default: ('.$name.')' .  $this->prepare($value) . "\n");
					}
				}
			}
		}
		
		private function prepare($string) { 
			$string = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $string); 
			return $string;
		}
		
		private function archive() {
						
		}

	}
