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

/**
 * Statistics View
 */
class KunenaViewStatistics extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig ();
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' .      $this->config->board_title);

		$this->userlist = CKunenaLink::GetUserlistLink('', intval($this->get('TotalMembers')));
		$this->lastestmemberid = $this->get('LastestMemberID');
		$this->lastestmembername = $this->get('LastUser');
		$this->totaltitles = $this->get('totaltitles');
		$this->totalmessages = $this->get('totalmsgs');
		$this->totalsections = $this->get('totalsections');
		$this->totalcats = $this->get('totalcats');

		$this->todayopen = $this->get('todayopen');
		$this->yesterdayopen = $this->get('yesterdayopen');
		$this->todayanswer = $this->get('todayanswer');
		$this->yesterdayanswer = $this->get('yesterdayanswer');

		$this->topthanks =  $this->get('topthanks');
		$this->topuserthanks =  $this->get('topuserthanks');

		$this->topposters = $this->get('topposters');
		$this->topmessage = $this->get('topmessage');

		$this->topprofiles = $this->get('topprofiles');
		$this->topprofilehits = $this->get('topprofilehits');

		$this->toptitles = $this->get('toptitles');
		$this->toptitlehits = $this->get('toptitlehits');

		$this->toppolls = $this->get('toppolls');
		$this->toppollvotes = $this->get('toppollvotes');

		parent::display ();
	}
}