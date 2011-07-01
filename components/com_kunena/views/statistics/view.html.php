<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );
kimport ( 'kunena.forum.statistics' );

/**
 * Statistics View
 */
class KunenaViewStatistics extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig ();
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' .      $this->config->board_title);

		require_once(KPATH_SITE.'/lib/kunena.link.class.php');
		$kunena_stats = KunenaForumStatistics::getInstance ( );
		$kunena_stats->loadAll();

		$this->assign($kunena_stats);
		$this->latestMemberLink = CKunenaLink::GetProfileLink($this->lastUserId);
		$this->userlist = CKunenaLink::GetUserlistLink('', intval($this->get('memberCount')));
		$this->statisticsURL = KunenaRoute::_('index.php?option=com_kunena&view=statistics');

		parent::display ();
	}
}