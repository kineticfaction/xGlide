<?php

	/**
	 * Payment
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 2.0 Coffee Creme
	 * 
	 */
	abstract class payment {

		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $_properties">
		
		/**
		 * All the properties of the object
		 * 
		 * @var array
		 *
		 */
		public $_properties			= array();

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" MAGIC METHODS : __construct __set __get __isset">
		
		public function __construct() {
			$this->_properties = array_keys(get_object_vars($this));
			if(array_pop($this->_properties) != '_properties') {
				core_debug::i()->add('500', 'Core Error', '_properties is not last item in array');	
			}
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
		
		/**
		 * Magic Isset
		 * 
		 * @param string $name
		 * @return bool 
		 */
		final public function __isset($name) {
			return isset($this->$name);
		}
		
		// </editor-fold>
		
		
	}
