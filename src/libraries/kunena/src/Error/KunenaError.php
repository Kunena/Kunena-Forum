<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Error;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Log\KunenaLog;
use StdClass;

/**
 * Class KunenaError
 *
 * @since   Kunena 6.0
 */
abstract class KunenaError
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public static $enabled = 0;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $handler = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $debug = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $admin = false;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public static $format;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function initialize(): void
	{
		if (!self::$enabled && !KunenaForum::isDev())
		{
			self::$format = Factory::getApplication()->input->getWord('format', 'html');
			self::$debug  = JDEBUG || KunenaFactory::getConfig()->debug;
			self::$admin  = Factory::getApplication()->isClient('administrator');

			// Make sure we are able to log fatal errors.
			register_shutdown_function(['Kunena\Forum\Libraries\Error\KunenaError', 'shutdownHandler'], self::$debug || self::$admin || KUNENA_PROFILER);

			if (!self::$debug)
			{
				return;
			}

			@ini_set('display_errors', 1);
			self::$handler = true;

			self::$enabled++;
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function cleanup(): void
	{
		if (self::$enabled && (--self::$enabled) == 0)
		{
			if (self::$handler)
			{
				restore_error_handler();
				self::$handler = false;
			}
		}
	}

	/**
	 * @param   string  $msg    msg
	 * @param   string  $where  where
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function error($msg, $where = 'default'): void
	{
		if (self::$debug)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_KUNENA_ERROR_' . strtoupper($where), $msg), 'error');
		}
	}

	/**
	 * @param   string  $msg    msg
	 * @param   string  $where  where
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function warning($msg, $where = 'default'): void
	{
		if (self::$debug)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_KUNENA_WARNING_' . strtoupper($where), $msg), 'notice');
		}
	}

	/**
	 * Return different error if it's an admin or a simple user
	 *
	 * @param   Exception| ExecutionFailureException  $exception  exception
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 *
	 * @throws  Exception
	 */
	public static function displayDatabaseError($exception): void
	{
		$app = Factory::getApplication();

		if (Factory::getApplication()->isClient('administrator'))
		{
			$app->enqueueMessage('Exception throw at line ' . $exception->getLine() . ' in file ' . $exception->getFile() . ' with message ' . $exception->getMessage(), 'error');
		}
		elseif (!JDEBUG && !KunenaFactory::getConfig()->debug && !self::$admin)
		{
			$app->enqueueMessage('Kunena ' . Text::sprintf(
				'COM_KUNENA_INTERNAL_ERROR_ADMIN',
				'<a href="https://www.kunena.org/">www.kunena.org</a>'
			), 'error');
		}
		elseif (KunenaFactory::getUser()->isAdmin() && Factory::getApplication()->isClient('site'))
		{
			$app->enqueueMessage('Exception throw at line ' . $exception->getLine() . ' in file ' . $exception->getFile() . ' with message ' . $exception->getMessage(), 'error');
		}
		else
		{
			$app->enqueueMessage('Kunena ' . Text::_('COM_KUNENA_INTERNAL_ERROR'), 'error');
		}

		KunenaLog::log(KunenaLog::TYPE_ERROR, KunenaLog::LOG_ERROR_FATAL, 'Exception throw at line ' . $exception->getLine() . ' in file ' . $exception->getFile() . ' with message ' . $exception->getMessage());
	}

	/**
	 * @param   string  $errno    error no
	 * @param   string  $errstr   error str
	 * @param   string  $errfile  error file
	 * @param   string  $errline  error line
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function errorHandler(string $errno, string $errstr, string $errfile, string $errline): bool
	{
		if (error_reporting() == 0 || !strstr($errfile, 'kunena'))
		{
			if (strpos($errstr, "mail()") !== false)
			{
				return KunenaEmail::on_mail_error($errno, $errstr, $errfile, $errline);
			}

			return false;
		}

		switch ($errno)
		{
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
		$errfile_short = preg_replace('%' . strtr(JPATH_ROOT, '\\', '/') . '/%', '\\1', $errfile_short);
		$errfile_short = preg_replace('%^.*?/((administrator/)?(components|modules|plugins|templates)/)%', '\\1', $errfile_short);

		if (self::$debug || self::$admin)
		{
			printf("<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error, $errstr, $errfile_short, $errline);
		}

		if (ini_get('log_errors'))
		{
			error_log(sprintf("PHP %s:  %s in %s on line %d", $error, $errstr, $errfile, $errline));
		}

		return true;
	}

	/**
	 * @param   mixed  $debug  debug
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function shutdownHandler($debug): void
	{
		static $types = [E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR];

		$error = error_get_last();

		if ($error && \in_array($error['type'], $types))
		{
			KunenaLog::log(KunenaLog::TYPE_ERROR, KunenaLog::LOG_ERROR_FATAL, $error);

			if ($debug)
			{
				// Clean up file path (take also care of some symbolic links).
				$file     = strtr($error ['file'], '\\', '/');
				$file     = preg_replace('%' . strtr(JPATH_ROOT, '\\', '/') . '/%', '\\1', $file);
				$file     = preg_replace('%^.*?/((administrator/)?(components|modules|plugins|templates)/)%', '\\1', $file);
				$errorMsg = sprintf(
					"<p><b>Fatal Error</b>: %s in <b>%s</b> on line <b>%d</b></p>",
					$error['message'],
					$file,
					$error['line']
				);
				$parts    = explode('/', $file);
				$dir      = (string) array_shift($parts);

				if ($dir == 'administrator')
				{
					$dir = (string) array_shift($parts);
				}

				$extension = strtr((string) array_shift($parts), '_', ' ');

				switch ($dir)
				{
					case 'components';
						$extension = ucwords(substr($extension, 4)) . ' Component';
						break;
					case 'modules';
						$extension = ucwords(substr($extension, 4)) . ' Module';
						break;
					case 'plugins';
						$plugin    = preg_replace('/\.php/', '', strtr((string) array_shift($parts), '_', ' '));
						$extension = ucwords($extension) . ' - ' . ucwords($plugin) . ' Plugin';
						break;
					case 'templates';
						$extension = ucwords($extension) . ' Template';
						break;
					default:
						$extension = ucwords($dir);
				}
			}
			else
			{
				$errorMsg  = 'Internal Server Error';
				$extension = $file = '';
			}

			while (@ob_end_clean())
			{
			}

			ob_start();
			header('HTTP/1.1 500 Internal Server Error');

			if (self::$format == 'json')
			{
				header('Content-type: application/json');

				// Emulate Joomla\CMS\Response\JsonResponse.
				$response           = new StdClass;
				$response->success  = false;
				$response->message  = '500 ' . $errorMsg;
				$response->messages = null;

				// Build data from exceptions.
				$exception = [
					'code'    => 500,
					'message' => $errorMsg,
				];

				if (JDEBUG)
				{
					$exception += [
						'type' => 'Fatal Error',
						'file' => $error['file'],
						'line' => $error['line'],
					];
				}

				$response->data = ['exceptions' => [$exception]];

				// Create response.
				echo json_encode($response);

				return;
			}

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>500 Internal Server Error</title>
	</head>
	<body>
	';
			echo '<div style="max-width:760px;margin:0 auto;text-align:center;background:#d9e2ea top left repeat-x;-webkit-box-shadow: 3px 3px 5px #777;-moz-box-shadow: 3px 3px 5px #777;box-shadow: 3px 3px 5px #777;-webkit-border-radius:20px; -moz-border-radius:20px;border-radius:20px;">';
			echo '<h1 style="background-color:#5388b4;color:white;text-shadow: 1px 1px 1px #000000;filter: dropshadow(color=#000000, offx=1, offy=1);padding-left:20px;-webkit-box-shadow: 1px 1px 2px #444;-moz-box-shadow: 1px 1px 2px #444;box-shadow: 1px 1px 2px #444;-webkit-border-radius: 20px; -moz-border-radius:20px;border-radius:20px;">500 Internal Server Error</h1>';
			echo '<h2>Fatal Error was detected!</h2>';

			if ($debug)
			{
				echo "<p>The error was detected in the <b>{$extension}</b>.</p>";
				echo $errorMsg;

				if (strpos($file, 'kunena') !== false)
				{
					echo '<p>For support click here: <a href="https://www.kunena.org/forum">Kunena Support</a></p>';
				}
			}
			else
			{
				echo '<p>Please contact the site owner.</p>';
			}

			echo '<hr /><p><a href="javascript:window.history.back()">Go back</a></p><br />';
			echo '</div>';
			echo '
	</body>
	</html>';
		}

		// Flush Kunena Logger if it was used.
		KunenaLog::flush();
	}
}
