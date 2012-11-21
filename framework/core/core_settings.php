<?php

	/**
	 * Core Settings - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */	
	class core_settings {
		
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
		
				
		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $data ">
		
		/**
		 * Settings registry
		 * 
		 * @var array $data 
		 */
		private $data = array();
		
		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : add get ">
		
		/**
		 * Add a setting to the registry
		 * @param string $name
		 * @param mixed $value 
		 */
		public function add($name, $value) {
			$this->data[$name] = $value;
		}
		
		/**
		 * Append existing setting to the registry
		 * @param string $name
		 * @param mixed $value 
		 */
		public function append($name, $value) {
			if(isset($this->data[$name])) {
				$this->data[$name] .= $value;
			} else {
				$this->add($name, $value);
			}
		}
		
		/**
		 * Get a setting from the registry
		 * @param string $name
		 * @return mixed 
		 */
		public function get($name) {
			return $this->data[$name];
		}

		// </editor-fold>
		
	}
