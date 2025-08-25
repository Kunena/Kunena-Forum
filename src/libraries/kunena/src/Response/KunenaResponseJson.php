<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Lib.Response
 * @subpackage    Json
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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

        if ($response instanceof \Throwable) {
            $this->code = $response->getCode();
            $exceptions = [];
            $e          = $response;

            do {
                $exception = [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                ];

                if (\defined('JDEBUG') && JDEBUG) {
                    $exception += [
                        'type' => \get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ];
                }

                $exceptions[] = $exception;
                $e            = $e->getPrevious();
            } while (\defined('JDEBUG') && JDEBUG && $e);

            $this->data = ['exceptions' => $exceptions];
        }

        // Empty output buffer with safety limit
        $maxIterations = 10;
        $iteration     = 0;
        while ($iteration++ < $maxIterations && ($output = ob_get_clean()) !== false) {
            if ($output && \defined('JDEBUG') && JDEBUG) {
                $this->messages['php']   = $this->messages['php'] ?? [];
                $this->messages['php'][] = $output;
            }
        }
    }
}
