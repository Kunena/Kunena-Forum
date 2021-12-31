<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerStatisticsWhoisonlineDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetWhoisonlineDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Widget/WhoIsOnline';

	/**
	 * @var
	 * @since Kunena
	 */
	public $usersUrl;

	/**
	 * Prepare Who is online display.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();

		if (!$this->config->get('showwhoisonline'))
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		$me        = KunenaUserHelper::getMyself();
		$moderator = intval($me->isModerator()) + intval($me->isAdmin());

		$users = KunenaUserHelper::getOnlineUsers();
		KunenaUserHelper::loadUsers(array_keys($users));
		$onlineusers = KunenaUserHelper::getOnlineCount();

		$who = '<strong>' . $onlineusers['user'] . ' </strong>';

		if ($onlineusers['user'] == 1)
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBER') . '&nbsp;';
		}
		else
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBERS') . '&nbsp;';
		}

		$who .= Text::_('COM_KUNENA_WHO_AND');
		$who .= '<strong> ' . $onlineusers['guest'] . ' </strong>';

		if ($onlineusers['guest'] == 1)
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_GUEST') . '&nbsp;';
		}
		else
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_GUESTS') . '&nbsp;';
		}

		$who                 .= Text::_('COM_KUNENA_WHO_ONLINE_NOW');
		$this->membersOnline = $who;

		$this->onlineList = array();
		$this->hiddenList = array();

		foreach ($users as $userid => $usertime)
		{
			$user = KunenaUserHelper::get($userid);

			if (!$user->showOnline)
			{
				if ($moderator)
				{
					$this->hiddenList[$user->getName()] = $user;
				}
			}
			else
			{
				$this->onlineList[$user->getName()] = $user;
			}
		}

		ksort($this->onlineList);
		ksort($this->hiddenList);

		$profile        = KunenaFactory::getProfile();
		$this->usersUrl = $profile->getUserListURL();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$this->setTitle(Text::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'));
	}
}
