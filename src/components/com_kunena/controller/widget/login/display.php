<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetLoginDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetLoginDisplay extends KunenaControllerDisplay
{
	protected $name = 'Widget/Login';

	public $me;

	public $my;

	public $registrationUrl;

	public $resetPasswordUrl;

	public $remindUsernameUrl;

	public $rememberMe;

	public $lastvisitDate;

	public $announcementsUrl;

	public $pm_link;

	public $inboxCount;

	public $inboxCountValue;

	public $profile_edit_url;

	/**
	 * Prepare login display.
	 *
	 * @return boolean
	 */
	protected function before()
	{
		parent::before();

		$login = KunenaLogin::getInstance();

		if (!$login->enabled())
		{
			return false;
		}

		$this->me = KunenaUserHelper::getMyself();
		$this->name = ($this->me->exists() ? 'Widget/Login/Logout' : 'Widget/Login/Login');

		$this->my = JFactory::getUser();

		if ($this->my->guest)
		{
			$this->registrationUrl = $login->getRegistrationUrl();
			$this->resetPasswordUrl = $login->getResetUrl();
			$this->remindUsernameUrl = $login->getRemindUrl();
			$this->rememberMe = $login->getRememberMe();
		}
		else
		{
			$this->lastvisitDate = KunenaDate::getInstance($this->my->lastvisitDate);

			$private = KunenaFactory::getPrivateMessaging();

			if ($private)
			{
				$this->inboxCountValue = $private->getUnreadCount($this->me->userid);
				$this->inboxCount = $this->inboxCountValue ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->inboxCountValue) : JText::_('COM_KUNENA_PMS_INBOX');
				$this->pm_link = $private->getInboxURL();
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
