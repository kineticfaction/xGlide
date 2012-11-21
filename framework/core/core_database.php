<?php

	/**
	 * Core Database - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	class core_database extends mysqli { 
		
		// <editor-fold defaultstate="collapsed" desc=" SINGLETON : $instance __construct i _clone ">
		static private $instance = null;
		static public function i() { 
			if(self::$instance === null) { 
				$c = __CLASS__;
				try {
					self::$instance = new $c(); 
				} catch(Exception $e){
					throw new Exception($e->getMessage());
				}
			} 
			return self::$instance; 
		} 
		private function __construct() {
			@parent::__construct( 
				core_settings::i()->get('CONFIG_SERVERS_DATABASE_IP'),
				core_settings::i()->get('CONFIG_SERVERS_DATABASE_USERNAME'),
				core_settings::i()->get('CONFIG_SERVERS_DATABASE_PASSWORD'),
				core_settings::i()->get('CONFIG_SERVERS_DATABASE_DATABASE'),
				core_settings::i()->get('CONFIG_SERVERS_DATABASE_PORT')
			);
			if($this->connect_errno) {
				throw new Exception("Cannot Connect to Database: " . $this->connect_error);
			}
		}
		public function __clone() {
			throw new Exception("Cannot clone ".__CLASS__." class"); 
		}
		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : debug_query destroy ">
		
		/**
		 * MySQLi query with debugging features
		 * @param string $query
		 * @return array 
		 */
		public function debug_query($query) {
			if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
				$starttime = lib_datetime_microtime();
			}
			
			$store = array();
			if($object_result = core_database::i()->query($query)) {
				if(is_object($object_result)) {
					if($object_result->num_rows > 0) {
						while($array_row = $object_result->fetch_assoc()) {
							$store[] = $array_row;
						}
						$object_result->close();
					}
				}
			} else {
				throw new Exception("Problem with SQL: " . $this->error . ' : ' . $query);
			}

			if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
				$endtime = lib_datetime_microtime();
				core_debug::i()->add('query',$query, round(($endtime - $starttime),6)); 
			}
			return $store;
		}
				
		/**
		 * Destroys the database connection
		 */
		public function destroy() { 
			self::$instance = NULL; 
		}
		
		
		public function columns($table) {
			if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
				$starttime = lib_datetime_microtime();
			}
			
			$store = array();
			$object_result = core_database::i()->query("
				select * from `".core_database::i()->real_escape_string($table)."` LIMIT 1;
			");
			$object_fields = $object_result->fetch_fields();
			for($i=0;$i<count($object_fields);$i++) {
				$store[] = $object_fields[$i]->name;
			}
			
			if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
				$endtime = lib_datetime_microtime();
				core_debug::i()->add('query',$query, round(($endtime - $starttime),6)); 
			}
			return $store;
		}
		// </editor-fold>

	}
