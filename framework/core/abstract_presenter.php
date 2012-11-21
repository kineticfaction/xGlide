<?php

	/**
	 * Root presenter class - xGlide Framework
	 * 
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	abstract class presenter {
		
		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $request_method $data $store">
		
		/**
		 * The HTTP request method, GET, POST, PUT or DELETE
		 * 
		 * @var string 
		 */
		protected $request_method		= NULL;
		
		/**
		 * View Data
		 * 
		 * @var string 
		 */
		protected    $data = NULL;
		final function  set_data ($value) {	$this->data = $value; }
		final function  get_data () {	return $this->data; }
		
		/**
		 * Model Store
		 * 
		 * @var type 
		 */
		protected    $store = NULL;
		final function  set_store ($value) {	$this->store = $value; }
		final function  get_store () {	return $this->store; }

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" MAGIC METHODS : __construct __set __get ">
		
		public function __construct() {
			$this->set_request_method($_SERVER['REQUEST_METHOD']);
		}

		/**
		 * Magic Setter
		 * 
		 * @param string $name
		 * @param mixed $value 
		 */
		final public function __set($name, $value) {
			if(method_exists($this,"set_$name")) {
				$this->{"set_$name"}($value);
			} else {
				throw new Exception('Property does not exist or no setter for ' .$name);
			}
		}
		
		/**
		 * Magic Getter
		 * 
		 * @param string $name
		 * @return mixed 
		 */
		final public function __get($name) {
			if(method_exists($this,"get_$name")){
				return $this->{"get_$name"}();
			} else {
				throw new Exception('Property does not exist or no getter for ' .$name);
			}
		}

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : set_request_method">
		
		/**
		 * Sets $request_method
		 * Throws an error if not GET, POST, PUT or DELETE
		 * 
		 * @param string $method 
		 */
		private function set_request_method($method) {
		
			if($method == "GET" || $method == "POST" || $method == "PUT" || $method == "DELETE") {
				$this->request_method = $method;
			} else {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
		
		}
		
		// </editor-fold>
		
	}
