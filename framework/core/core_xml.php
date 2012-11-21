<?php

	/**
	 * Core XML - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	class core_xml {
		
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
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : xml2array create_node generate_xml create_xml create_html xml_success">
		final public function xml2array($xml) { 
		
			// So simple :D
			return json_decode(json_encode((array)simplexml_load_string($xml)),1);;
		
		}
		
		final public function create_node($object_node) {

			$node = $this->doc->createElement('node');
			$class_name = get_class($object_node);
			
			for($i=0;$i<count($object_node->_properties);$i++) {
				
				try {
					$column = $object_node->_properties[$i];
					if(in_array($column, $class_name::$xml_children) && !empty($object_node->$column['nodes'])) {
						$field 	= $this->doc->createElement($column);
						for($i=0;$i<count($object_node->$column['nodes']);$i++) {
							$f = $this->create_node($object_node->$column['nodes'][$i]);
							$field->appendChild($f);
						}
						$node->appendChild($field);
					} elseif(in_array($column, $class_name::$xml_html_elements) && $object_node->$column != '') {
						//use html_elements if were transforming with XSL
						$field = $this->doc->createElement($column);
						$f = $this->doc->createDocumentFragment();
						$f->appendXML($object_node->$column);
						$field->appendChild($f);
						$node->appendChild($field);
					} elseif(((is_string($object_node->$column) && $object_node->$column != '') || is_int($object_node->$column) || is_float($object_node->$column)) && !is_array($object_node->$column)) {
						$field 	= $this->doc->createElement($column, $object_node->$column);
						$node->appendChild($field);
					}
				} catch (Exception $e) {
					throw $e;
				}				
			}

			return $node;
			
		}
		
		final public function generate_xml($array) {

			$this->doc = new DOMDocument('1.0', 'utf-8');
			
			$name_root = 'nodes';
			$root = $this->doc->createElement($name_root);
			
			if(isset($array['results'])) {
				$results = $this->doc->createElement('results', $array['results']);
				$root->appendChild($results);
			}
			
			if(isset($array['elapsed'])) {
				$elapsed = $this->doc->createElement('elapsed', $array['elapsed']);
				$root->appendChild($elapsed);
			}
			
			if(isset($array['success'])) {
				$success = $this->doc->createElement('success', $array['success']);
				$root->appendChild($success);
			}
			
			if(isset($array['message'])) {
				$message = $this->doc->createElement('message', $array['message']);
				$root->appendChild($message);
			}

			if(!isset($array['nodes'])) {
				$this->doc->appendChild($root);
				return $this->doc;
			}

			if(count($array['nodes']) < 1) {
				// Just return the root element if the array is empty
				$this->doc->appendChild($root);
				return $this->doc;
			}

			if(is_array($array['nodes'])) {
				for($i=0;$i<count($array['nodes']);$i++) {
					$node = $this->create_node($array['nodes'][$i]);
					$root->appendChild($node);
				}
			}

			$this->doc->appendChild($root);
			
			if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
				$endtime = lib_datetime_microtime();
				//core_debug::i()->add('xml_output',$this->doc->SaveXML(), ''); 
			}

			return $this->doc;
				
		}
		
		final public function create_xml($array) {
			$obj_xml = $this->generate_xml($array);
			return $obj_xml->SaveXML();
		}
		
		final public function create_html($array, $stylesheet) {
			$obj_xml = $this->generate_xml($array);
			$obj_stylesheet = new DOMDocument();
			$obj_stylesheet->load(core_settings::i()->get('CONFIG_PAPER_XSLT') . $stylesheet);
			$obj_xsl = new XSLTProcessor();
			$obj_xsl->registerPHPFunctions();
			$obj_xsl->importStyleSheet($obj_stylesheet);
			$str_xhtml = $obj_xsl->transformToXML($obj_xml);
			if(is_null($str_xhtml)) {
				$str_xhtml = "";
			}
			return $str_xhtml;
		}

		final public function xml_success($bool, $message = "") {

			if($bool) {
				return '<?xml version="1.0" encoding="utf-8"?>'."\n".'<result><status>true</status><message>'.$message.'</message></result>';
			} else {
				return '<?xml version="1.0" encoding="utf-8"?>'."\n".'<result><status>false</status><message>'.$message.'</message></result>';
			}
		}
		// </editor-fold>
		
	}
