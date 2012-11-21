<?php

	/**
	 * Core Paper - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */
	class core_paper {

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
		
		
		// <editor-fold defaultstate="collapsed" desc=" PROPERTIES : $data $mime $template $encoding">
		
		/**
		 * Paper buffer data store
		 * 
		 * @var array $data 
		 */
		private      $data = array();
		function  set_data ($value) {	$this->data = $value; }  
		function  get_data () {	return $this->data; }
		
		/**
		 * Output mimetype
		 * 
		 * @var string $mime 
		 */
		private      $mime = 'text/html';
		function  set_mime ($value) {
			switch ($value) { 
				case "text/html":			$this->mime = $value; break; 
				case "text/xml":			$this->mime = $value; break; 
				case "application/json":		$this->mime = $value; break;
				case "application/octet-stream":	$this->mime = $value; break; 
				case "application/zip":			$this->mime = $value; break; 
				case "application/msword":		$this->mime = $value; break; 
				case "application/vnd.ms-excel":	$this->mime = $value; break; 
				case "image/gif":			$this->mime = $value; break; 
				case "image/png":			$this->mime = $value; break; 
				case "image/jpg":			$this->mime = $value; break; 
				default: core_debug::i()->add('500', 'Mime Error', 'Mime type ' . $value . ' is not supported.'); 
			}
		}
		function  get_mime () {	return $this->mime; }
		
		/**
		 * Template file to use for output
		 * 
		 * @var string $template 
		 */
		private      $template = NULL;
		function  set_template ($value) {
			if(is_null($this->template)) {
				if(file_exists(core_settings::i()->get('CONFIG_PAPER_ROOT').$value)) {
					$this->template = core_settings::i()->get('CONFIG_PAPER_ROOT').$value;
				} else {
					core_debug::i()->add('404', 'File not found: ',core_settings::i()->get('CONFIG_PAPER_ROOT').$value);
				}
			}
		}
		function  get_template () {	return $this->template; }
		
		/**
		 * Output encoding
		 * 
		 * @var string $mime 
		 */
		private      $encoding = 'utf-8';
		function  set_encoding ($value) {	$this->encoding = $value; }
		function  get_encoding () {	return $this->encoding; }
		
		// </editor-fold>
		
		
		// <editor-fold defaultstate="collapsed" desc=" MAGIC METHODS : __set __get">
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
		
		
		// <editor-fold defaultstate="collapsed" desc=" METHODS : add clear display">
		/**
		 * Adds an item to the buffer
		 * 
		 * @param mixed $input 
		 */
		public function add($input) {
			if(isset($input)) {
				foreach ($input as $name => $value) {
					$this->data['%'.$name.'%'] = $value;
				}
			}
		}
		
		/**
		 * Empties the Paper buffer
		 */
		public function clear() {
			unset($this->data);
		}
		
		/**
		 * Prints the Paper buffer to the screen 
		 */
		public function display() {
			try {
				header('Content-Type: '.$this->mime.'; charset=' . strtoupper($this->encoding) . '');
				header('Expires: ' . date ('D, d M Y H:i:s ', time() + core_settings::i()->get('CONFIG_PAPER_EXPIRES') ) . 'GMT');
				$handle = fopen($this->template, "r");
				$contents = fread($handle, filesize($this->template));
				fclose($handle);
				$find = array_keys($this->data);
				$replace = array_values($this->data);
				$xhtml = str_replace($find, $replace, $contents);
				print($xhtml);
			} catch(Exception $e) {
				core_debug::i()->add('500', $e->getMessage(), '');
			}
		}
		// </editor-fold>
		
	}