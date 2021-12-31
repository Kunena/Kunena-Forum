<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easysocial
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Component\ComponentHelper;

/**
 * @package     Kunena
 *
 * @since       Kunena 5.0
 */
class KunenaLoginEasySocial
{
	protected $params = null;

	/**
	 * KunenaLoginEasySocial constructor.
	 *
	 * @param $params
	 *
	 * @since       Kunena 5.0
	 *
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return mixed
	 * @since       Kunena 5.0
	 *
	 */
	public function getLoginURL()
	{
		return FRoute::dashboard();
	}

	/**
	 * @return mixed
	 * @since       Kunena 5.0
	 *
	 */
	public function getLogoutURL()
	{
		return FRoute::dashboard();
	}

	/**
	 * @return null
	 * @since       Kunena 5.0
	 */
	public function getRegistrationURL()
	{
		$usersConfig = ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			return FRoute::registration();
		}

		return null;
	}
}
