<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Class ComponentKunenaControllerWidgetLoginDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerWidgetLoginDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Widget/Login';

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $my;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $registrationUrl;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $resetPasswordUrl;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $remindUsernameUrl;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $rememberMe;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $lastvisitDate;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $announcementsUrl;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $pm_link;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $inboxCount;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $inboxCountValue;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $profile_edit_url;

	/**
	 * @var     object
	 * @since   Kunena 5.1
	 */
	public $plglogin;

	/**
	 * Prepare login display.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$login = KunenaLogin::getInstance();

		$params         = new Registry($login->getParams());
		$this->plglogin = $params->get('login', '1');

		if (!$login->enabled())
		{
			return false;
		}

		$this->me   = KunenaUserHelper::getMyself();
		$this->name = ($this->me->exists() ? 'Widget/Login/Logout' : 'Widget/Login/Login');

		$this->my = Factory::getApplication()->getIdentity();

		if ($this->my->guest)
		{
			$this->registrationUrl   = $login->getRegistrationUrl();
			$this->resetPasswordUrl  = $login->getResetUrl();
			$this->remindUsernameUrl = $login->getRemindUrl();
			$this->rememberMe        = $login->getRememberMe();
		}
		else
		{
			$this->lastvisitDate = KunenaDate::getInstance($this->my->lastvisitDate);

			$private = KunenaFactory::getPrivateMessaging();

			if ($private)
			{
				$this->inboxCountValue = $private->getUnreadCount($this->me->userid);
				$this->inboxCount      = $this->inboxCountValue ? Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->inboxCountValue) : Text::_('COM_KUNENA_PMS_INBOX');
				$this->pm_link         = $private->getInboxURL();
			}

			$profile = KunenaFactory::getProfile();

			$this->profile_edit_url = $profile->getEditProfileURL($this->me->userid);

			// Display announcements.
			if ($this->me->isModerator())
			{
				$this->announcementsUrl = KunenaForumAnnouncementHelper::getUrl('list');
			}
		}

		return true;
	}
}
