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

defined( '_JEXEC' ) or die();

class KunenaError {
	function initialize() {
		@ini_set('display_errors', 0);
		@error_reporting(E_ALL);
		$db = JFactory::getDBO();
		$db->debug(1);

		set_exception_handler('kunenaExceptionHandler');
		set_error_handler('kunenaErrorHandler');
		register_shutdown_function('kunenaShutdownHandler');
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

function kunenaExceptionHandler($exception) {
	echo "Uncaught Exception: {$exception->getMessage()}";
	return false;
}

function kunenaErrorHandler($errno, $errstr, $errfile, $errline) {
	if (error_reporting () == 0 || !strstr($errfile, 'com_kunena')) {
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
	printf ( "<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error, $errstr, $errfile_short, $errline );
	if (ini_get ( 'log_errors' ))
		error_log ( sprintf ( "PHP %s:  %s in %s on line %d", $error, $errstr, $errfile, $errline ) );
	return true;
}

function kunenaShutdownHandler() {
	static $types = array (E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);
	$error = error_get_last ();
	if ($error && in_array ( $error ['type'], $types )) {
		kunenaErrorHandler ( $error ['type'], $error ['message'], $error ['file'], $error ['line'] );
	}
}