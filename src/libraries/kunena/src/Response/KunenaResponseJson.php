<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Lib.Response
 * @subpackage    Json
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Response;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Response\JsonResponse;

/**
 * Kunena JSON Response, extends Joomla\CMS\Response\JsonResponse.
 *
 * @since   Kunena 6.0
 */
class KunenaResponseJson extends JsonResponse
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $code = 200;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $location;

	/**
	 * Constructor
	 *
	 * @param   mixed    $response        The Response data
	 * @param   string   $message         The main response message
	 * @param   boolean  $error           True, if the success flag shall be set to false, defaults to false
	 * @param   boolean  $ignoreMessages  True, if the message queue shouldn't be included, defaults to false
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($response = null, $message = null, $error = false, $ignoreMessages = false)
	{
		parent::__construct($response, $message, $error, $ignoreMessages);

		if ($response instanceof Exception)
		{
			$this->code = $response->getCode();

			// Build data from exceptions.
			$exceptions = [];
			$e          = $response;

			do
			{
				$exception = [
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				];

				if (JDEBUG)
				{
					$exception += [
						'type' => \get_class($e),
						'file' => $e->getFile(),
						'line' => $e->getLine(),
					];
				}

				$exceptions[] = $exception;
				$e            = $e->getPrevious();
			}
			while (JDEBUG && $e);

			// Create response data on exceptions.
			$this->data = ['exceptions' => $exceptions];
		}

		// Empty output buffer to make sure that the response is clean and valid.
		while (($output = ob_get_clean()) !== false)
		{
			// In debug mode send also output buffers (debug dumps, PHP notices and warnings).
			if ($output && \defined(JDEBUG))
			{
				$response->messages['php'][] = $output;
			}
		}
	}
}
