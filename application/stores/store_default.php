<?php

	/**
	 * Default Store
	 *
	 * @author Oliver Ridgway
	 * @copyright Kinetic Faction
	 * @package xGlide
	 * @version 3.0
	 */
	class store_default extends store {

		// <editor-fold defaultstate="collapsed" desc=" STATIC PROPERTIES : $db_types $db_columns ">
		
		/**
		 * Database table name
		 * 
		 * @var type
		 * @static 
		 */
		static $db_table		= 'alerts';
		
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
		
		/**
		 * List of enum or set values
		 */
		static $db_types		= array();		

		// </editor-fold>
		
		
		function create(
			model_alert $obj_data
		) {
			parent::create($obj_data);
		}
		
		function read(
			$query = false, 
			$query_column = false, 
			$query_order = false,
			$query_dir = false, 
			$query_start = false, 
			$query_limit = false
		) {
			
			$int_time_start = lib_datetime_microtime();	
			
			parent::read(
				$query, 
				$query_column, 
				$query_order,
				$query_dir, 
				$query_start, 
				$query_limit
			);
			
			$array_data['results'] = count($this->array_result);
			if(count($this->array_result) > 0) {
				for($i=0;$i<count($this->array_result);$i++) {
					try {
						$array_data['nodes'][] = new model_forum_categories(
							$this->array_result[$i]
						);
					} catch(Exception $e) {
						core_debug::i()->add('500', $e->getMessage(), '');
					}
				}
			}
			
			$array_data['elapsed'] = lib_datetime_microtime() - $int_time_start;
			return $array_data;

		}
		
		function update(
			$query, 
			$query_column, 
			model_default $obj_data
		) {
			parent::update($query, $query_column, $obj_data);
		}
		
		function delete(
			$query, 
			$query_column
		) {
			parent::delete(
				$query,
				$query_column
			);
		}
		
	}

?>
