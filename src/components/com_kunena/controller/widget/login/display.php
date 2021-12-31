<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerWidgetLoginDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetLoginDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Widget/Login';

	/**
	 * @var
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var
	 * @since Kunena
	 */
	public $my;

	/**
	 * @var
	 * @since Kunena
	 */
	public $registrationUrl;

	/**
	 * @var
	 * @since Kunena
	 */
	public $resetPasswordUrl;

	/**
	 * @var
	 * @since Kunena
	 */
	public $remindUsernameUrl;

	/**
	 * @var
	 * @since Kunena
	 */
	public $rememberMe;

	/**
	 * @var
	 * @since Kunena
	 */
	public $lastvisitDate;

	/**
	 * @var
	 * @since Kunena
	 */
	public $announcementsUrl;

	/**
	 * @var
	 * @since Kunena
	 */
	public $pm_link;

	/**
	 * @var
	 * @since Kunena
	 */
	public $inboxCount;

	/**
	 * @var
	 * @since Kunena
	 */
	public $inboxCountValue;

	/**
	 * @var
	 * @since Kunena
	 */
	public $profile_edit_url;

	/**
	 * @var
	 * @since Kunena 5.1
	 */
	public $plglogin;

	/**
	 * Prepare login display.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$login = KunenaLogin::getInstance();

		$params = new JRegistry($login->getParams());
		$this->plglogin = $params->get('login', '1');

		if (!$login->enabled())
		{
			return false;
		}

		$this->me   = KunenaUserHelper::getMyself();
		$this->name = ($this->me->exists() ? 'Widget/Login/Logout' : 'Widget/Login/Login');

		$this->my = Factory::getUser();

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
