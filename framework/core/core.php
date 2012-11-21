<?php

	/**
	 * Core - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel
	 * 
	 */

	// <editor-fold defaultstate="collapsed" desc="HOUSEKEEPING">
	// Makes sure everything is safe to run
	error_reporting(E_ALL);
	ini_set('display_errors','On');
	$version = explode('.', PHP_VERSION);
	$version = ($version[0] * 10000 + $version[1] * 100 + $version[2]);
	if($version < 50302) { exit('xGlide will only work on php 5.3.2 and above.'); }
	if(get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	if(get_magic_quotes_runtime()) {	exit('xGlide requires magic_quotes to be turned off'); }
	unset($version);
	// </editor-fold>

	
	// <editor-fold defaultstate="collapsed" desc="REQUIRES">
	require(__DIR__.'/core_settings.php');
	require(__DIR__.'/core_debug.php');
	require(__DIR__.'/core_logging.php');
	require(__DIR__.'/core_requests.php');
	require(__DIR__.'/core_database.php');
	require(__DIR__.'/core_paper.php');
	require(__DIR__.'/core_xml.php');

	require(__DIR__.'/abstract_store.php');
	require(__DIR__.'/abstract_model.php');
	require(__DIR__.'/abstract_presenter.php');
	require(__DIR__.'/abstract_query.php');

	foreach (glob(__DIR__."/../libraries/*.php") as $filename) {
		require($filename);
	}
	
	foreach (glob(__DIR__.'/../../application/config/*.php') as $filename) {
		require($filename);
	}	
	
	foreach (glob(__DIR__.'/../../application/models/*.php') as $filename) {
		require($filename);
	}
	
	foreach (glob(__DIR__.'/../../application/stores/*.php') as $filename) {
		require($filename);
	}
	// </editor-fold>

	
	// <editor-fold defaultstate="collapsed" desc="AUTH + DEBUGGING">
	if(core_settings::i()->get('CONFIG_SETTINGS_AUTH') > 0) {
		require(__DIR__.'/core_auth.php');
		session_start();
		core_auth::i()->connect();
	}
	
	if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
		set_error_handler("customError");
	} else {
		set_error_handler("suppressError");
	}
	// </editor-fold>

		
	// <editor-fold defaultstate="collapsed" desc="RUN THE PRESENTER">
	
	// Get the default presenter if one isn't request
	if(isset($_GET['presenter'])) {
		core_requests::i()->add('presenter',	$_GET['presenter']);
	} else {
		core_requests::i()->add('presenter', core_settings::i()->get('CONFIG_SETTINGS_DEFAULTPRESENTER')); 
	}

	if(file_exists(__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php')) {
		// Presenter exists so run it
		require(__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php');
		$presenter_name = 'presenter_' . core_requests::i()->get('presenter');
		$presenter = new $presenter_name();
	} else {
		// Presenter doesn't exist
		if(in_array(core_requests::i()->get('presenter'),array_keys(core_settings::i()->get('CONFIG_SETTINGS_ROUTING')))) {
			// Presenter exists in routing
			$routing = core_settings::i()->get('CONFIG_SETTINGS_ROUTING');
			$fake_presenter = core_requests::i()->get('presenter');
			core_requests::i()->add('presenter', (string)substr($routing[$fake_presenter],0,strpos($routing[$fake_presenter],'/')));
			core_requests::i()->add('query', (int)substr($routing[$fake_presenter],(strpos($routing[$fake_presenter],'/') + 1),strlen($fake_presenter)));
			if(file_exists(__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php')) {
				require(__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php');
				$p = 'presenter_' . core_requests::i()->get('presenter');
				$presenter = new $p();	
			} else {
				core_debug::i()->add('404', 'File not found: ',__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php');
			}
		} else {
			// Can't find the presenter at all.
			// Show a 404
			core_debug::i()->add('404', 'File not found: ',__DIR__.'/../../application/presenters/presenter_'.core_requests::i()->get('presenter').'.php');
		}
	}
	// </editor-fold>

	
	// <editor-fold defaultstate="collapsed" desc="DISPLAY RESULTS">
	core_paper::i()->add(
		$presenter->data
	);
	core_paper::i()->template = core_requests::i()->get('presenter').'.html';
	core_paper::i()->display();
	// </editor-fold>

	
	// <editor-fold defaultstate="collapsed" desc="DEBUGGING + LOGGING">
	if(core_settings::i()->get('CONFIG_SETTINGS_DEBUG')) {
		// Only display the debug data in xml or html files
		if(core_paper::i()->mime == 'text/html' && core_paper::i()->mime == 'text/xml') {
			print("<!--[\n\n");
			if(isset($_SESSION)) {
				print(session_id()."\n\n");
				print_r($_SESSION);
				print("\n\n");				
			}
			print_r(core_debug::i()->get());
			print("\n]-->");
		}
	}

	if(core_settings::i()->get('CONFIG_SETTINGS_LOGFILE')) {
		core_logging::i()->write();
	}
	// </editor-fold>
