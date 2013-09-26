<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerPageLoginDisplay extends KunenaControllerDisplay
{
	protected function display() {
		$layout = ($this->me->exists() ? 'Logout' : 'Login' );

		// Display layout with given parameters.
		$content = KunenaLayout::factory("Page/Login/{$layout}")
			->setProperties($this->getProperties());

		return $content;
	}

	protected function before()
	{
		$login = KunenaLogin::getInstance();
		if (!$login->enabled()) return false;

		$this->my = JFactory::getUser();
		$this->me = KunenaUserHelper::getMyself();
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
