<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaError {
	static $enabled = 0;

	function initialize() {

		if (!self::$enabled) {
			$debug = KunenaFactory::getConfig ()->debug;
			set_error_handler('kunenaErrorHandler');
			register_shutdown_function('kunenaShutdownHandler', $debug);
			if (!$debug) return;

			@ini_set('display_errors', 1);
			@error_reporting(E_ALL);
			JFactory::getDBO()->debug(1);

			self::$enabled++;
		}
	}

	function cleanup() {
		if (self::$enabled && (--self::$enabled) == 0) {
			restore_error_handler ();
		}
	}

	function error($msg, $where='default') {
		$config = KunenaFactory::getConfig();
		if ($config->debug) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('COM_KUNENA_ERROR_'.strtoupper($where), $msg), 'error');
		}
	}

	function warning($msg, $where='default') {
		$config = KunenaFactory::getConfig();
		if ($config->debug) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('COM_KUNENA_WARNING_'.strtoupper($where), $msg), 'notice');
		}
	}

	function checkDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			$app = JFactory::getApplication();
			$my = JFactory::getUser();
			$acl = KunenaFactory::getAccessControl();
			if ($acl->isAdmin ($my)) {
				if ($app->isAdmin())
					$app->enqueueMessage ( $db->getErrorMsg (), 'error' );
				else
					$app->enqueueMessage ( 'Kunena '.JText::sprintf ( 'COM_KUNENA_INTERNAL_ERROR_ADMIN', '<a href="http:://www.kunena.org/">www.kunena.org</a>' ), 'error' );
			} else {
				$app->enqueueMessage ( 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' ), 'error' );
			}
			return true;
		}
		return false;
	}

	function getDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			$app = JFactory::getApplication();
			$my = JFactory::getUser();
			$acl = KunenaFactory::getAccessControl();
			if ($acl->isAdmin ($my)) {
				return $db->getErrorMsg();
			} else {
				return 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' );
			}
		}
	}
}

function kunenaErrorHandler($errno, $errstr, $errfile, $errline) {
	$debug = class_exists('KunenaFactory') ? KunenaFactory::getConfig ()->debug : true;
	if (error_reporting () == 0 || !strstr($errfile, 'kunena')) {
		return false;
	}
	switch ($errno) {
		case E_NOTICE :
		case E_USER_NOTICE :
			$error = "Notice";
			break;
		case E_WARNING :
		case E_USER_WARNING :
		case E_CORE_WARNING :
		case E_COMPILE_WARNING :
			$error = "Warning";
			break;
		case E_ERROR :
		case E_USER_ERROR :
		case E_PARSE :
		case E_CORE_ERROR :
		case E_COMPILE_ERROR :
		case E_RECOVERABLE_ERROR :
			$error = "Fatal Error";
			break;
		case E_STRICT :
			return false;
		default :
			$error = "Unknown Error $errno";
			break;
	}

	$errfile_short = preg_replace('%^.*?/((administrator/)?components/)%', '\\1', $errfile);
	if ($debug) {
		printf ( "<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error, $errstr, $errfile_short, $errline );
	}
	if (ini_get ( 'log_errors' )) {
		error_log ( sprintf ( "PHP %s:  %s in %s on line %d", $error, $errstr, $errfile, $errline ) );
	}
	return true;
}

function kunenaShutdownHandler($debug) {
	static $types = array (E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);
	$error = error_get_last ();
	if ($error && in_array ( $error ['type'], $types )) {
		header('HTTP/1.1 500 Internal Server Error');
		while(@ob_end_clean());
		if ($debug) {
			printf ( "<b>Fatal Error</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error ['message'], $error ['file'], $error ['line'] );
		} else {
			echo "<b>Fatal Error</b>";
		}
	}
}