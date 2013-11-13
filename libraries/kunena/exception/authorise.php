<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Exception
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaExceptionAuthorise
 */
class KunenaExceptionAuthorise extends RuntimeException
{
	public function getResponseStatus() {
		$code = $this->getCode();
		switch ($code) {
			case 400:
				return '400 Bad Request';
			case 401:
				return '401 Unauthorized';
			case 403:
				return '403 Forbidden';
			case 404:
				return '404 Not Found';
			case 410:
				return '410 Gone';
			case 500:
				return '500 Internal Server Error';
			case 503:
				return '503 Service Temporarily Unavailable';
			default:
				return "{$code} Error";
		}
	}
}
