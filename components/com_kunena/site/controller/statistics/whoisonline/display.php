<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controller.Statistics.Whoisonline
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerStatisticsWhoisonlineDisplay extends KunenaControllerDisplay
{
	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Page/WhoIsOnline')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		parent::before();

		$me = KunenaUserHelper::getMyself();
		$moderator = intval($me->isModerator())+intval($me->isAdmin());

		$users = KunenaUserHelper::getOnlineUsers();
		KunenaUserHelper::loadUsers(array_keys($users));
		$onlineusers = KunenaUserHelper::getOnlineCount();

		$who = '<strong>'.$onlineusers['user'].' </strong>';
		if($onlineusers['user']==1) {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;';
		} else {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;';
		}
		$who .= JText::_('COM_KUNENA_WHO_AND');
		$who .= '<strong> '. $onlineusers['guest'].' </strong>';
		if($onlineusers['guest']==1) {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;';
		} else {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;';
		}
		$who .= JText::_('COM_KUNENA_WHO_ONLINE_NOW');
		$this->membersOnline = $who;

		$this->onlineList = array();
		$this->hiddenList = array();
		foreach ($users as $userid=>$usertime) {
			$user = KunenaUserHelper::get($userid);
			if ( !$user->showOnline ) {
				if ($moderator) $this->hiddenList[$user->getName()] = $user;
			} else {
				$this->onlineList[$user->getName()] = $user;
			}
		}
		ksort($this->onlineList);
		ksort($this->hiddenList);

		$profile = KunenaFactory::getProfile();
		$this->usersUrl = $profile->getUserListURL('');
	}
}
