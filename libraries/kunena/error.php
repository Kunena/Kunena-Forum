<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaError
 */
abstract class KunenaError {
	static $enabled = 0;
	static $handler = false;
	static $debug = false;
	static $admin = false;

	public static function initialize() {
		if (!self::$enabled) {
			self::$debug = JDEBUG || KunenaFactory::getConfig ()->debug;
			self::$admin = JFactory::getApplication()->isAdmin() || KunenaUserHelper::getMyself()->isAdmin();
			register_shutdown_function(array('KunenaError', 'shutdownHandler'), self::$debug || self::$admin || KUNENA_PROFILER);
			if (!self::$debug) return;

			@ini_set('display_errors', 1);
			self::$handler = true;
			@error_reporting(E_ALL | E_STRICT);
			JFactory::getDbo()->setDebug(true);
			set_error_handler(array('KunenaError', 'errorHandler'));

			self::$enabled++;
		}
	}

	public static function cleanup() {
		if (self::$enabled && (--self::$enabled) == 0) {
			if (self::$handler) {
				restore_error_handler ();
				self::$handler = false;
			}
		}
	}

	public static function error($msg, $where='default') {
		if (self::$debug) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('COM_KUNENA_ERROR_'.strtoupper($where), $msg), 'error');
		}
	}

	public static function warning($msg, $where='default') {
		if (self::$debug) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('COM_KUNENA_WARNING_'.strtoupper($where), $msg), 'notice');
		}
	}

	public static function checkDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			$app = JFactory::getApplication();
			if (JFactory::getApplication()->isAdmin()) {
				$app->enqueueMessage ($db->getErrorMsg(), 'error' );
			} elseif (self::$debug || self::$admin) {
				$app->enqueueMessage ( 'Kunena '.JText::sprintf ( 'COM_KUNENA_INTERNAL_ERROR_ADMIN', '<a href="http:://www.kunena.org/">www.kunena.org</a>' ), 'error' );
			} else {
				$app->enqueueMessage ( 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' ), 'error' );
			}
			return true;
		}
		return false;
	}

	public static function getDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			if (self::$debug || self::$admin) {
				return $db->getErrorMsg();
			} else {
				return 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' );
			}
		}
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline) {
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

		// Clean up file path (take also care of some symbolic links)
		$errfile_short = strtr($errfile, '\\', '/');
		$errfile_short = preg_replace('%'.strtr(JPATH_ROOT, '\\', '/').'/%', '\\1', $errfile_short);
		$errfile_short = preg_replace('%^.*?/((administrator/)?(components|modules|plugins|templates)/)%', '\\1', $errfile_short);
		if (self::$debug || self::$admin) {
			printf ( "<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error, $errstr, $errfile_short, $errline );
		}
		if (ini_get ( 'log_errors' )) {
			error_log ( sprintf ( "PHP %s:  %s in %s on line %d", $error, $errstr, $errfile, $errline ) );
		}
		return true;
	}

	public static function shutdownHandler($debug) {
		static $types = array (E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);
		$error = error_get_last ();
		if ($error && in_array ( $error ['type'], $types )) {
			header('HTTP/1.1 500 Internal Server Error');
			while(@ob_end_clean());
			ob_start();
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>500 Internal Server Error</title>
	</head>
	<body>
	';
			echo '<div style="width:760px;margin:0 auto;text-align:center;background:#d9e2ea top left repeat-x;-webkit-box-shadow: 3px 3px 5px #777;-moz-box-shadow: 3px 3px 5px #777;box-shadow: 3px 3px 5px #777;-webkit-border-radius:20px; -moz-border-radius:20px;border-radius:20px;">';
			echo '<h1 style="background-color:#5388b4;color:white;text-shadow: 1px 1px 1px #000000;filter: dropshadow(color=#000000, offx=1, offy=1);padding-left:20px;-webkit-box-shadow: 1px 1px 2px #444;-moz-box-shadow: 1px 1px 2px #444;box-shadow: 1px 1px 2px #444;-webkit-border-radius: 20px; -moz-border-radius:20px;border-radius:20px;">500 Internal Server Error</h1>';
			echo '<h2>Fatal Error was detected!</h2>';
			if ($debug) {
				// Clean up file path (take also care of some symbolic links)
				$errfile_short = strtr($error ['file'], '\\', '/');
				$errfile_short = preg_replace('%'.strtr(JPATH_ROOT, '\\', '/').'/%', '\\1', $errfile_short);
				$errfile_short = preg_replace('%^.*?/((administrator/)?(components|modules|plugins|templates)/)%', '\\1', $errfile_short);
				printf ("<p><b>Fatal Error</b>: %s in <b>%s</b> on line <b>%d</b></p>", $error ['message'], $errfile_short, $error ['line'] );
				$parts = explode('/', $errfile_short);
				$dir = (string) array_shift($parts);
				if ($dir == 'administrator') $dir = (string) array_shift($parts);
				$extension = strtr((string) array_shift($parts), '_', ' ');
				switch ($dir) {
					case 'components';
						$extension = ucwords(substr($extension,4)).' Component';
						break;
					case 'modules';
						$extension = ucwords(substr($extension,4)).' Module';
						break;
					case 'plugins';
						$plugin = preg_replace('/\.php/', '', strtr((string) array_shift($parts), '_', ' '));
						$extension = ucwords($extension).' - '.ucwords($plugin).' Plugin';
						break;
					case 'templates';
						$extension = ucwords($extension).' Template';
						break;
					default:
						$extension = ucwords($dir);
				}
				echo "<p>The error was detected in the <b>{$extension}</b>.</p>";
				if (strpos($errfile_short, 'kunena') !== false) {
					echo '<p>For support click here: <a href="http://www.kunena.org/forum">Kunena Support</a></p>';
				}
			} else {
					echo '<p>Please contact the site owner.</p>';
			}
			echo '<hr /><p><a href="javascript:history.go(-1)">Go back</a></p><br />';
			echo '</div>';
			echo '
	</body>
	</html>';
		}
	}
}
