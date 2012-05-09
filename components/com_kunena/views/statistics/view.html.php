<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Statistics View
 */
class KunenaViewStatistics extends KunenaView {
	function displayDefault($tpl = null) {
		require_once(KPATH_SITE.'/lib/kunena.link.class.php');
		$kunena_stats = KunenaForumStatistics::getInstance ( );
		$kunena_stats->loadAll();

		$this->assign($kunena_stats);
		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink();
		$this->userlist = CKunenaLink::GetUserlistLink('', intval($this->get('memberCount')));

		$this->_prepareDocument();
		parent::display ();
	}

	protected function _prepareDocument(){
		$this->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS'));

		// TODO: set keywords and description
	}
}