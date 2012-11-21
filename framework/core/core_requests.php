<?php

	/**
	 * Core Requests - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	class core_requests {

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
		 * Requests registry
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
			if($value != "") {
				$this->data[$name] = $value;
			} else {
				$this->data[$name] = NULL;
			}
		}

		/**
		 * Get a setting from the reqistry
		 * @param string $name
		 * @return mixed 
		 */
		public function get($name) {
			return $this->data[$name];
		}

		// </editor-fold>

	}
	
	// <editor-fold defaultstate="collapsed" desc=" DEFAULT REQUESTS ">
	// query
	if(isset($_GET['query'])) { 
		core_requests::i()->add('query', $_GET['query']);
	} else {
		core_requests::i()->add('query', NULL);
	}

	if(isset($_GET['node'])) { 
		core_requests::i()->add('node', $_GET['node']);
	} else {
		core_requests::i()->add('node', NULL);
	}
	
	// query_sort
	if(isset($_GET['sort'])) {
		core_requests::i()->add('query_sort', $_GET['sort']);
	} elseif(isset($_GET['query_sort'])) {
		core_requests::i()->add('query_sort', $_GET['query_sort']);
	} else {
		core_requests::i()->add('query_sort', NULL);
	}

	// query_dir
	if(isset($_GET['dir'])) {
		core_requests::i()->add('query_dir', $_GET['dir']);
	} elseif(isset($_GET['query_dir'])) {
		core_requests::i()->add('query_dir', $_GET['query_dir']);
	} else {
		core_requests::i()->add('query_dir', NULL);
	}

	// query_start
	if(isset($_GET['start'])) {
		core_requests::i()->add('query_start', $_GET['start']);
	} elseif(isset($_GET['query_start'])) {
		core_requests::i()->add('query_start', $_GET['query_start']);
	} else {
		core_requests::i()->add('query_start', NULL);
	}

	// query_limit
	if(isset($_GET['limit'])) {
		core_requests::i()->add('query_limit', $_GET['limit']);
	} elseif(isset($_GET['query_limit'])) {
		core_requests::i()->add('query_limit', $_GET['query_limit']);
	} else {
		core_requests::i()->add('query_limit', NULL);
	}

	// commit
	if(isset($_GET['commit'])) {
		core_requests::i()->add('query_commit', $_GET['commit']);
	} elseif(isset($_GET['query_commit'])) {
		core_requests::i()->add('query_commit', $_GET['commit']);
	} else {
		core_requests::i()->add('query_commit', NULL);
	}
	// </editor-fold>
