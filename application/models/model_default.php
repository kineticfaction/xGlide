<?php
	
	/**
	 * Default Model
	 *
	 * @author Oliver Ridgway
	 * @copyright Kinetic Faction
	 * @package xGlide
	 * @version 3.0
	 */
	class model_alert extends model {
	
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
		static $db_columns		= array(
			'default_id'	=>	'int',
			'default_name'	=>	'string',
			'default_data'	=>	'string'
		);

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" MAGIC METHODS : __construct __call">
		
		final public function __construct($array_data) {

			parent::__construct($array_data);
			
		}
		
		// </editor-fold>
		
	}

?>