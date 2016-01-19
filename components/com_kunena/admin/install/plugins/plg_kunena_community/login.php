<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    Community
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaLoginCommunity
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function getLoginURL()
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	public function getLogoutURL()
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

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
