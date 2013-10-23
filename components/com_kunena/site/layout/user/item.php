<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutUserItem extends KunenaLayout
{
	public function getTabs()
	{
		$banInfo = KunenaUserBan::getInstanceByUserid($this->user->id, true);
		$myProfile = $this->profile->isMyself();
		$moderator = $this->me->isModerator();

		// Decide which tabs to display.
		$showPosts = true;
		$showSubscriptions = $this->config->allowsubscriptions && $myProfile;
		$showFavorites = $this->config->allowfavorites && $myProfile;
		$showThankYou = $this->config->showthankyou && $this->me->exists();
		$showUnapproved = $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus();
		$showAttachments = $this->config->show_imgfiles_manage_profile && ($moderator || $myProfile);
		$showBanManager = $moderator && $myProfile;
		$showBanHistory = $moderator && !$myProfile;
		$showBanUser = $banInfo->canBan();

		// Define all tabs.
		$tabs = array();
		if ($showPosts) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_USERPOSTS');
			$tab->content = 'FIXME';
			$tab->active = true;
			$tabs['posts'] = $tab;
		}
		if ($showSubscriptions) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_SUBSCRIPTIONS');
			$tab->content = $this->subRequest('Category/Subscriptions').'FIXME';
			$tab->active = false;
			$tabs['subscriptions'] = $tab;
		}
		if ($showFavorites) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_FAVORITES');
			$tab->content = 'FIXME';
			$tab->active = false;
			$tabs['favorites'] = $tab;
		}
		if ($showThankYou) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_THANK_YOU');
			$tab->content = 'FIXME';
			$tab->active = false;
			$tabs['thankyou'] = $tab;
		}
		if ($showUnapproved) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION');
			$tab->content = 'FIXME';
			$tab->active = false;
			$tabs['unapproved'] = $tab;
		}
		if ($showAttachments) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_MANAGE_ATTACHMENTS');
			$tab->content = $this->subRequest('User/Attachments');
			$tab->active = false;
			$tabs['attachments'] = $tab;
		}
		if ($showBanManager) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_BAN_BANMANAGER');
			$tab->content = $this->subRequest('User/Ban/Manager');
			$tab->active = false;
			$tabs['banmanager'] = $tab;
		}
		if ($showBanHistory) {
			$tab = new stdClass();
			$tab->title = JText::_('COM_KUNENA_BAN_BANHISTORY');
			$tab->content = $this->subRequest('User/Ban/History');
			$tab->active = false;
			$tabs['banhistory'] = $tab;
		}
		if ($showBanUser) {
			$tab = new stdClass();
			$tab->title = $banInfo->exists() ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW');
			$tab->content = $this->subRequest('User/Ban/Form');
			$tab->active = false;
			$tabs['banuser'] = $tab;
		}

		return $tabs;
	}

	function displayUnapprovedPosts() {
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'unapproved',
			'sel' => -1,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayUserPosts() {
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'latest',
			'sel' => 8760,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayGotThankyou() {
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'mythanks',
			'sel' => -1,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displaySaidThankyou() {
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'thankyou',
			'sel' => -1,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayFavorites() {
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'favorites',
			'sel' => -1,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'user', 'embed', $params);
	}

	function displaySubscriptions() {
		if ($this->config->topic_subscriptions == 'disabled') return;
		$params = array(
			'topics_categories' => 0,
			'topics_catselection' => 1,
			'userid' => $this->user->id,
			'mode' => 'subscriptions',
			'sel' => -1,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('topics', 'user', 'embed', $params);
	}

	function displayCategoriesSubscriptions() {
		if ($this->config->category_subscriptions == 'disabled') return;
		$params = array(
			'userid' => $this->user->id,
			'limit' => 6,
			'filter_order' => 'time',
			'limitstart' => 0,
			'filter_order_Dir' => 'desc',
		);
		KunenaForum::display('category', 'user', 'embed', $params);
	}
}
