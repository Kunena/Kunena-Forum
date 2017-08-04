<?php
/**
 * @package        EasySocial
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Unauthorized Access');

/**
 * Class KunenaLoginEasySocial
 * @since Kunena
 */
class KunenaLoginEasySocial
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaLoginEasySocial constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return mixed
	 * @since Kunena
	 */
	public function getLoginURL()
	{
		return FRoute::dashboard();
	}

	/**
	 * @return mixed
	 * @since Kunena
	 */
	public function getLogoutURL()
	{
		return FRoute::dashboard();
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getRegistrationURL()
	{
		$usersConfig = \Joomla\CMS\Component\ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			return FRoute::registration();
		}

		return null;
	}
}
