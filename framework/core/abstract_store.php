<?php

	/**
	 * Root store class - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 */
	abstract class store {

		// <editor-fold defaultstate="collapsed" desc=" STATIC PROPERTIES : $db_dirs $db_types $db_columns ">
		
		/**
		 * Database table name
		 * 
		 * @var type
		 * @static 
		 */
		static $db_table		= '';
		
		/**
		 * Column Names and types
		 * 
		 * @var array
		 * @static
		 */
		static $db_columns		= array();
		
		/**
		 * List of enum or set values
		 */
		static $db_types		= array();
		
		/**
		 * List of sort directions
		 */
		static $db_dirs			= array('DESC','ASC');

		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $array_results ">
		
		/**
		 * Database query results
		 * 
		 * @var array
		 *
		 */
		public $array_result			= array();


		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : create read update delete">
		
		/**
		 * Create record prototype
		 */
		public function create(model $obj_data) {
			
			// <editor-fold defaultstate="collapsed" desc="BUILD QUERY">
			$pad = '                       ';
			$str_ident = '##' . get_called_class() . ' ' . __FUNCTION__ . '()' . " \n";


			$obj_data->sanitize();
			
			if(core_settings::i()->get('CONFIG_SETTINGS_DEVMODE')) {
				$array_columns = core_database::i()->columns(static::$db_table);
			}

			$array_fields = array();
			foreach(static::$db_columns as $str_fieldname => $str_fieldtype) {
				if(core_settings::i()->get('CONFIG_SETTINGS_DEVMODE')) {
					// check column really exists in dev mode
					if(!in_array($str_fieldname, $array_columns)) {
						throw new Exception('Column "' . $str_fieldname . '" not allowed in "'.get_class($this).'"');
					}
				}
				if(!empty($obj_data->{$str_fieldname})) {
					$array_fields[] =  '`' . $str_fieldname . '`';
				}
			}
			$str_insert	= 'INSERT INTO ' . static::$db_table . ' (' . implode(", ", $array_fields) . ") \n";


			$array_values = array();
			foreach(static::$db_columns as $str_fieldname => $str_fieldtype) {
				if(!empty($obj_data->{$str_fieldname})) {
					$array_values[] = $obj_data->{$str_fieldname};
				}
			}
			$str_values = 'VALUES (' . implode(", ", $array_values) . ')';		
			// </editor-fold>


			// <editor-fold defaultstate="collapsed" desc="QUERY DATABASE">
			try {
				$this->array_result = core_database::i()->debug_query(
						$str_insert . 
					$pad .  $str_values
				);
			} catch(Exception $e) {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
			// </editor-fold>
			
			
		}

		/**
		 * Read record prototype
		 */
		public function read($query, $query_column, $query_order, $query_dir, $query_start, $query_limit) {
			
			// <editor-fold defaultstate="collapsed" desc="BUILD QUERY">
			$pad = '                       ';
			$str_ident = '##' . get_called_class() . ' ' . __FUNCTION__ . '()' . " \n";
				
			
			$array_fields = array();
			foreach(static::$db_columns as $str_fieldname => $str_fieldtype) {
				if($str_fieldtype == 'datetime') {
					$array_fields[] =  'UNIX_TIMESTAMP(`' . $str_fieldname . '`) AS `'.$str_fieldname.'`';
				} else {
					$array_fields[] =  '`' . $str_fieldname . '`';
				}
			}
			$str_select	= 'SELECT ' . implode(", ", $array_fields) . " \n";
			
			
			$str_from	= 'FROM ' . static::$db_table . " \n";
			
			
			if($query !== false && $query_column !== false) {
				if (array_key_exists($query_column, static::$db_columns)) {
					$str_where	= 'WHERE `' . core_database::i()->real_escape_string($query_column) .  '` = "' . core_database::i()->real_escape_string($query) . '"' . " \n";
				} else {
					$str_where	= '';
				}
			} else {
				$str_where	= '';
			}
			
			
			if($query_order !== false && $query_dir !== false) {
				if (array_key_exists($query_column, static::$db_columns)) {
					$str_order	= 'ORDER BY `' . $query_order . '` ' . $query_dir . " \n";
				} else {
					$str_order	= '';
				}
			} else {
				$str_order	= '';
			}
			
			
			if($query_start !== false) {
				if ($query_limit !== false) {
					$str_limit	= 'LIMIT ' . $query_start . ', ' . $query_limit . " \n";
				} else {
					$str_limit	= 'LIMIT ' . $query_start . " \n";
				}
			} else {
				$str_limit	= '';
			}		
			// </editor-fold>
			
			
			// <editor-fold defaultstate="collapsed" desc="QUERY DATABASE">
			try {
				$this->array_result = core_database::i()->debug_query(
						$str_ident . 
					$pad .  $str_select . 
					$pad .  $str_from . 
					$pad .  $str_where . 
					$pad .  $str_order . 
					$pad .  $str_limit
				);
			} catch(Exception $e) {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
			// </editor-fold>
			
		}

		/**
		 * Update record prototype
		 */
		public function update($query, $query_column, model $obj_data) {
			
			// <editor-fold defaultstate="collapsed" desc="BUILD QUERY">
			$pad = '                       ';
			$str_ident = '##' . get_called_class() . ' ' . __FUNCTION__ . '()' . " \n";


			$obj_data->sanitize();

			
			$str_update	= 'UPDATE ' . static::$db_table . " \n";

			
			$array_set = array();
			foreach(static::$db_columns as $str_fieldname => $str_fieldtype) {
				if(!empty($obj_data->{$str_fieldname})) {
					$array_set[] = '`' . $str_fieldname . '` = ' .  $obj_data->{$str_fieldname} . '';
				}
			}
			$str_set = 'SET ' . implode(", \n", $array_set) . " \n";
			
			
			
			if (array_key_exists($query_column, static::$db_columns)) {
				$str_where	= 'WHERE `' . core_database::i()->real_escape_string($query_column) .  '` = "' . core_database::i()->real_escape_string($query) . '"' . " \n";
			} else {
				core_debug::i()->add('500', 'Unknown db_column', '"' . $query_column . '"');
			}
			// </editor-fold>
			
			
			// <editor-fold defaultstate="collapsed" desc="QUERY DATABASE">
			try {
				$this->array_result = core_database::i()->debug_query(
						$str_update . 
					$pad .  $str_set . 
					$pad .  $str_where
				);
			} catch(Exception $e) {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
			// </editor-fold>
			
			
		}

		/**
		 * Delete record prototype
		 */
		public function delete() {
			
			// <editor-fold defaultstate="collapsed" desc="BUILD QUERY">
			$pad = '                       ';
			$str_ident = '##' . get_called_class() . ' ' . __FUNCTION__ . '()' . " \n";


			$obj_data->sanitize();

			
			$str_update	= 'DELETE FROM ' . static::$db_table . " \n";

			
			if (array_key_exists($query_column, static::$db_columns)) {
				$str_where	= 'WHERE `' . core_database::i()->real_escape_string($query_column) .  '` = "' . core_database::i()->real_escape_string($query) . '"' . " \n";
			} else {
				core_debug::i()->add('500', 'Unknown db_column', '"' . $query_column . '"');
			}
			// </editor-fold>
			
			
			// <editor-fold defaultstate="collapsed" desc="QUERY DATABASE">
			try {
				$this->array_result = core_database::i()->debug_query(
						$str_update . 
					$pad .  $str_set . 
					$pad .  $str_where
				);
			} catch(Exception $e) {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
			// </editor-fold>
			
		}
		
		// </editor-fold>
		
	}
