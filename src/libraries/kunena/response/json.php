<?php
/**
 * @version   $Id$
 * @author	RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Kunena JSON Response, extends JResponseJson.
 */
class KunenaResponseJson extends KunenaCompatResponseJson
{
	public $code = 200;
	public $location;

	/**
	 * Constructor
	 *
	 * @param   mixed    $response        The Response data
	 * @param   string   $message         The main response message
	 * @param   boolean  $error           True, if the success flag shall be set to false, defaults to false
	 * @param   boolean  $ignoreMessages  True, if the message queue shouldn't be included, defaults to false
	 */
	public function __construct($response = null, $message = null, $error = false, $ignoreMessages = false)
	{
		parent::__construct($response, $message, $error, $ignoreMessages);

		if ($response instanceof Exception)
		{
			$this->code = $response->getCode();

			// Build data from exceptions.
			$exceptions = array();
			$e = $response;

			do
			{
				$exception = array(
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				);

				if (JDEBUG)
				{
					$exception += array(
						'type' => get_class($e),
						'file' => $e->getFile(),
						'line' => $e->getLine()
					);
				}

				$exceptions[] = $exception;
				$e = $e->getPrevious();
			}
			while (JDEBUG && $e);

			// Create response data on exceptions.
			$this->data = array('exceptions' => $exceptions);
		}

		// Empty output buffer to make sure that the response is clean and valid.
		while (($output = ob_get_clean()) !== false)
		{
			// In debug mode send also output buffers (debug dumps, PHP notices and warnings).
			if ($output && defined(JDEBUG))
			{
				$response->messages['php'][] = $output;
			}
		}
	}
}
