<?php
/**
 * Datetime Library
 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.8 Blueberry Blend 
 * 
 */
	
	function lib_datetime_microtime() {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
