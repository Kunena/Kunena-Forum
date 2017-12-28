<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Community
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaLoginCommunity
{
	protected $params = null;

	/**
	 * KunenaLoginCommunity constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return string
	 */
	public function getLoginURL()
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	/**
	 * @return string
	 */
	public function getLogoutURL()
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	/**
	 * @return null|string
	 */
	public function getRegistrationURL()
	{
		$usersConfig = JComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			return CRoute::_('index.php?option=com_community&view=register');
		}

		return null;
	}
}
