<?php

	/**
	 * Root Model Object - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	abstract class model {

		// <editor-fold defaultstate="collapsed" desc=" STATIC PROPERTIES : $xml_html_elements $xml_html_noshow $xml_children $db_columns ">
		
		/**
		 * Elements that may be malformed or HTML 
		 * 
		 * @var array
		 * @static
		 */
		static $xml_html_elements	= array();

		/**
		 * Elements that should not be turned into XML
		 * 
		 * @var array
		 * @static
		 */
		static $xml_html_noshow		= array();

		/**
		 * Elements that have children
		 * 
		 * @var array
		 * @static
		 */
		static $xml_children		= array();
		
		/**
		 * Column Names and types
		 * 
		 * @var array
		 * @static
		 */
		static $db_columns		= array();

		// </editor-fold>


		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $_properties">
		
		/**
		 * All the properties of the object
		 * 
		 * @var array
		 *
		 */
		public $_properties			= array();

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" MAGIC METHODS : __construct __call">
		
		/**
		 * Constructor
		 * 
		 * Takes an array and creates the models properties.
		 * 
		 * @param array $array_data
		 * @throws Exception
		 */
		public function __construct($array_data) {
			$this->_properties = array_keys(static::$db_columns);
			foreach($array_data as $str_fieldname => $mixed_fieldcontent) {
				if (array_key_exists($str_fieldname, static::$db_columns)) {
					$this->{"set_$str_fieldname"}($str_fieldname, static::$db_columns[$str_fieldname], $mixed_fieldcontent);
				} else {
					throw new Exception('Column "' . $str_fieldname . '" not allowed in "'.get_class($this).'"');
				}
			}
		}

		/**
		 * Call
		 * 
		 * Magically creates the set_variable and get_variable methods for each property
		 * 
		 * @param string $name
		 * @return bool 
		 */
		final public function __call($name, $arguments) {
			if(array_key_exists($arguments[0], static::$db_columns)) {
				$this->$arguments[0] = $arguments[2];
			}
		}
		
		
		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : sanitize">
		
		/**
		 * Cleans up the members for database insertion
		 */
		public function sanitize() {
			foreach($this as $key => $value) {
				if($key != '_properties') {
					if (array_key_exists($key, static::$db_columns)) {
						switch ( static::$db_columns[$key]) {
							case 'int':
								  $this->$key = (int)$value;
							break;
							case 'bool':
								  $this->$key = (bool)$value;
							break;
							case 'float':
								  $this->$key = (float)$value;
							break;
							case 'datetime':
								  $this->$key = 'FROM_UNIXTIME(' . (int)$value . ')';
							break;
							case 'string':
								  $this->$key = '"'.(string)core_database::i()->real_escape_string($value).'"';
							break;
							default:
								throw new Exception('Sanitize Failed - Unknown Type "' .static::$db_columns[$key] . '"');
						
						}
						
					}
				}
					
			}
		}
		
		// </editor-fold>
		
	}
