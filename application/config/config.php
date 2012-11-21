<?php

	/**
	 * Config File - xGlide Framework
	 *
	 * @package xGlide
	 * @version 3.0
	 * @author Oliver Ridgway
	 * @copyright Oliver Ridgway
	 */
 
	// Debugging
	core_settings::i()->add('CONFIG_SETTINGS_DEBUG',				true);
	core_settings::i()->add('CONFIG_SETTINGS_DEVMODE',				true);

	// Logging
	core_settings::i()->add('CONFIG_SETTINGS_LOGFILE',				'/var/log/xGlide/action.log');
	core_settings::i()->add('CONFIG_SETTINGS_LOGDAYS',				NULL);

	// Presenter Routing
	core_settings::i()->add('CONFIG_SETTINGS_ROUTING',				array());

	// Page expiry in seconds
	core_settings::i()->add('CONFIG_PAPER_EXPIRES',					600); // Page expires in 5 minutes

	// Name of the default presenter
	core_settings::i()->add('CONFIG_SETTINGS_DEFAULTPRESENTER',			'default');

	// Auth level
	core_settings::i()->add('CONFIG_SETTINGS_AUTH',					0);

	// Default Database settings
	core_settings::i()->add('CONFIG_SERVERS_DATABASE_IP',			'tunnel.pagodabox.com');
	core_settings::i()->add('CONFIG_SERVERS_DATABASE_USERNAME',		'krishna');
	core_settings::i()->add('CONFIG_SERVERS_DATABASE_PASSWORD',		'uU7Hy5ib');
	core_settings::i()->add('CONFIG_SERVERS_DATABASE_DATABASE',		'xglide');
	core_settings::i()->add('CONFIG_SERVERS_DATABASE_PORT',			3306);


	// Hierarchy - You shouldn't normally need to change these
	core_settings::i()->add('CONFIG_PAPER_ROOT',				__DIR__.'/../paper/');
	core_settings::i()->add('CONFIG_PAPER_XSLT',				__DIR__.'/../paper/xsl/');
	core_settings::i()->add('CONFIG_SITE_ROOT',				'/');

?>