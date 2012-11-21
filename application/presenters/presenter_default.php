<?php

	/**
	 * Default Presenter
	 *
	 * @author Oliver Ridgway
	 * @copyright Kinetic Faction
	 * @package xGlide
	 * @version 3.0
	 */
	class presenter_default extends presenter {

		public function __construct() {
			try {
				parent::__construct();
				$this->model();
				$this->view();
			} catch(Exception $e) {
				exit($e->getMessage());
			}
			
		}

		/**
		 * Set up the model
		 */
		private function model() {
			
			$store_default = new store_default();
			$store_default->read(
				1
			);
			
		}

		/**
		 * Set up the view
		 */
		private function view() {
			
			$this->data['header'] = strtoupper('xGlide Framework');
			$this->data['content'] = core_xml::i()->create_html(
				$this->store['defaultdata'],
				"default.xsl"
			);

		}

	}

?>
