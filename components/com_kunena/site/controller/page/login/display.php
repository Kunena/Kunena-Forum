<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Page
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerPageLoginDisplay
 */
class ComponentKunenaControllerPageLoginDisplay extends KunenaControllerDisplay
{
	protected $name = 'Credits';

	public $me;
	public $my;
	public $registrationUrl;
	public $resetPasswordUrl;
	public $remindUsernameUrl;
	public $rememberMe;
	public $lastvisitDate;
	public $announcementsUrl;

	protected function before()
	{
		parent::before();

		$login = KunenaLogin::getInstance();
		if (!$login->enabled()) return false;

		$this->me = KunenaUserHelper::getMyself();
		$this->name = ($this->me->exists() ? 'Page/Login/Logout' : 'Page/Login/Login');

		$this->my = JFactory::getUser();
		if ($this->my->guest) {
			$this->registrationUrl = $login->getRegistrationUrl();
			$this->resetPasswordUrl = $login->getResetUrl();
			$this->remindUsernameUrl = $login->getRemindUrl();
			$this->rememberMe = $login->getRememberMe();
		} else {
			$this->lastvisitDate = KunenaDate::getInstance($this->my->lastvisitDate);

			// TODO: Private messages
			//$this->getPrivateMessageLink();

			// Announcements
			if ($this->me->isModerator()) {
				$this->announcementsUrl = KunenaForumAnnouncementHelper::getUrl('list');
			}

		}

		return true;
	}
}
