<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Community
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;

/**
 * Class KunenaLoginCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaLoginCommunity
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaLoginCommunity constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLoginURL(): string
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLogoutURL(): string
	{
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	/**
	 * @return string
	 * @since   Kunena 6.0
	 */
	public function getRegistrationURL(): string
	{
		$usersConfig = ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			return CRoute::_('index.php?option=com_community&view=register');
		}

		return;
	}
}
