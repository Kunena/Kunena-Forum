<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetLoginDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetLoginDisplay extends KunenaControllerDisplay
{
	protected $name = 'Widget/Credits';

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

	/**
	 * Prepare login display.
	 *
	 * @return bool
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
				$count = $private->getUnreadCount($this->me->userid);
				$this->inboxCount = $count ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count) : JText::_('COM_KUNENA_PMS_INBOX');
				$this->pm_link = $private->getInboxURL();
			}

			// Display announcements.
			if ($this->me->isModerator())
			{
				$this->announcementsUrl = KunenaForumAnnouncementHelper::getUrl('list');
			}
		}

		return true;
	}
}
