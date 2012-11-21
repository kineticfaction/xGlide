<?php
/**
 * Error Handlers
 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.8 Blueberry Blend 
 * 
 */

function customError($errno, $errstr, $errfile, $errline, $errcontext) {
	
	switch ($errno) {
		case 2:
			$error_level = ' [WARNING]';
		break;
		case 8:
			$error_level = ' [NOTICE]';
		break;
		case 256:
			$error_level = ' [USER ERROR]';
		break;
		case 512:
			$error_level = ' [WARNING]';
		break;
		case 1024:
			$error_level = ' [USER NOTICE]';
		break;
		case 4096:
			$error_level = ' [RECOVERABLE ERROR]';
		break;
		case 8191:
			$error_level = ' [ALL]';
		break;
		default:
			$error_level = '';
	}
	
	//$error_context = print_r($errcontext, true);
	$error_context = '';
	
	echo "<!-- \n\tError:$error_level $errstr\n\tFile:  $errfile\n\tLine:  $errline\n\t$error_context\n-->\n\n";
	

}

function suppressError($errno, $errstr) {
	exit(file_get_contents('framework/errors/error500.html'));
}
