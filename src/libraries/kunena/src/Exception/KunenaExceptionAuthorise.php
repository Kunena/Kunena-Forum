<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Exception
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Exception;

\defined('_JEXEC') or die();

use RuntimeException;

/**
 * Class KunenaExceptionAuthorise
 *
 * @since   Kunena 6.0
 */
class KunenaExceptionAuthorise extends RuntimeException
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $responseCodes = [
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		410 => '410 Gone',
		500 => '500 Internal Server Error',
		503 => '503 Service Temporarily Unavailable',
	];

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getResponseStatus(): string
	{
		return $this->responseCodes[$this->getResponseCode()];
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	public function getResponseCode(): int
	{
		return isset($this->responseCodes[$this->code]) ? (int) $this->code : 500;
	}
}
