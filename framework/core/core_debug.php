<?php

	/**
	 * Core Debug - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */	
	class core_debug {

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


		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $data">
		
		/**
		 * Debugger Reqistry
		 * @var array $data
		 */
		private $data = array();
		
		// </editor-fold>

		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : add get ">
		
		/**
		 * Add an error to the debugger registry
		 * @param string $type
		 * @param string $value
		 * @param mixed $data 
		 */
		public function add($type, $value, $data) {
			$this->data[] = array(
				'type'		=>	$type,
				'value'		=>	$value,
				'data'		=>	$data
			);
			switch ($type) {
				case '403':
					// Use this to stop accessing core.php etc
					header('HTTP/1.1 403 Forbidden');
					require(__DIR__.'/../errors/error403.html');
					exit();
				break;
				case '404':
					// Use this for missing presenters
					header('HTTP/1.1 404 Not Found');
					require(__DIR__.'/../errors/error404.html');
					exit();
				break;
				case '500':
					// Use this for fails.
					if(!core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
						header('HTTP/1.1 500 Internal Server Error');
						require(__DIR__.'/../errors/error500.html');
						exit();
					}
				break;              
				case 'xml':
					// Use this for parcial fails like xml errors et al.
					if(!core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
						require(__DIR__.'/../errors/errorxml.html');	
						exit();
					}
				break;
			}
		}
		
		
		/**
		 * Return debugger registry
		 * @return array 
		 */
		public function get() {
			return $this->data;
		}
		
		// </editor-fold>

	}
