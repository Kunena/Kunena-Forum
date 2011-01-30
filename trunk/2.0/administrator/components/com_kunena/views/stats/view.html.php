<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * About view for Kunena stats backend
 */
class KunenaAdminViewStats extends KunenaView {
	function display() {
		kimport ( 'kunena.forum.message.thankyou' );

		jimport ( 'joomla.utilities.date' );
		$this->datem = new JDate(date("Y-m-d 00:00:01"));
		$this->datee = new JDate(date("Y-m-d 23:59:59"));

		// FIXME: move into model -- there's a Joomla function to get results into $this
		$data = $this->get('StatsDatas');
		$data->loadTotalMembers();
		$this->totalmembers =  $data->totalmembers;
		$data->loadTotalTopics();
		$this->totaltitles = $data->totaltitles;
		$this->totalmessages = $data->totalmsgs;
		$data->loadTotalCategories();
		$this->totalsections = $data->totalsections;
		$this->totalcats = $data->totalcats;
		$data->loadLastDays();
		$this->todayopen = $data->todayopen;
		$this->yesterdayopen = $data->yesterdayopen;
		$this->todayanswer = $data->todayanswer;
		$this->yesterdayanswer = $data->yesterdayanswer;
		$data->loadLastUser();
		$this->lastestmembername = $data->lastestmember;
		$data->loadThanksStats('10');
		$this->topthanks = $data->topthanks;
		$data->loadTopPosters('10');
		$this->topposters = $data->topposters;
		$data->loadTopProfiles('10');
		$this->topprofiles = $data->topprofiles;
		$data->loadThanksStats('10');
		$this->topuserthanks = $data->topuserthanks;
		$data->loadTopicStats('10');
		$this->toptitles = $data->toptitles;

		parent::display ();
	}
}