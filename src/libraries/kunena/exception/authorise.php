<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Exception
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaExceptionAuthorise
 * @since Kunena
 */
class KunenaExceptionAuthorise extends RuntimeException
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected $responseCodes = array(
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		410 => '410 Gone',
		500 => '500 Internal Server Error',
		503 => '503 Service Temporarily Unavailable',
	);

	/**
	 * @return mixed
	 * @since Kunena
	 */
	public function getResponseStatus()
	{
		return $this->responseCodes[$this->getResponseCode()];
	}

	/**
	 * @return integer
	 * @since Kunena
	 */
	public function getResponseCode()
	{
		return isset($this->responseCodes[$this->code]) ? (int) $this->code : 500;
	}
}
