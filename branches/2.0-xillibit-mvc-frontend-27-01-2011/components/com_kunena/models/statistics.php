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

kimport ( 'kunena.model' );

/**
 * Statistics Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelStatistics extends KunenaModel {
	protected function _getStatsClass() {
		require_once (KUNENA_PATH_LIB . '/kunena.stats.class.php');
		$kunena_stats = CKunenaStats::getInstance ();

		return $kunena_stats;
	}

	public function getTotalMembers() {
		$stats = $this->_getStatsClass ();
		$stats->loadTotalMembers ();

		return $stats->totalmembers;
	}

	public function getLastestMemberID() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastUser ();

		return $stats->lastestmemberid;
	}

	public function gettotaltitles() {
		$stats = $this->_getStatsClass ();
		$stats->loadTotalTopics ();
		return $stats->totaltitles;
	}

	public function gettotalmsgs() {
		$stats = $this->_getStatsClass ();
		$stats->loadTotalTopics ();
		return $stats->totalmsgs;
	}

	public function gettotalsections() {
		$stats = $this->_getStatsClass ();
		$stats->loadTotalCategories ();
		return $stats->totalsections;
	}

	public function gettotalcats() {
		$stats = $this->_getStatsClass ();
		$stats->loadTotalCategories ();
		return $stats->totalcats;
	}

	public function gettodayopen() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastDays ();
		return $stats->todayopen;
	}

	public function getyesterdayopen() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastDays ();
		return $stats->yesterdayopen;
	}

	public function gettodayanswer() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastDays ();
		return $stats->todayanswer;
	}

	public function getyesterdayanswer() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastDays ();
		return $stats->yesterdayanswer;
	}

	public function getLastUser() {
		$stats = $this->_getStatsClass ();
		$stats->loadLastUser ();
		return $stats->lastestmember;
	}

	public function gettopthanks() {
		$stats = $this->_getStatsClass ();
		$stats->loadThanksStats ();
		return $stats->topthanks;
	}

	public function gettopuserthanks() {
		$stats = $this->_getStatsClass ();
		$stats->loadThanksStats ();
		return $stats->topuserthanks;
	}

	public function gettopposters() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopPosters ();
		return $stats->topposters;
	}

	public function gettopmessage() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopPosters ();
		return $stats->topmessage;
	}

	public function gettopprofiles() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopProfiles ();
		return $stats->topprofiles;
	}

	public function gettopprofilehits() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopProfiles ();
		return $stats->topprofilehits;
	}

	public function gettoptitles() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopicStats ();
		return $stats->toptitles;
	}

	public function gettoptitlehits() {
		$stats = $this->_getStatsClass ();
		$stats->loadTopicStats ();
		return $stats->toptitlehits;
	}

	public function gettoppolls() {
		$stats = $this->_getStatsClass ();
		$stats->loadPollStats ();
		return $stats->toppolls;
	}

	public function gettoppollvotes() {
		$stats = $this->_getStatsClass ();
		$stats->loadPollStats ();
		return $stats->toppollvotes;
	}
}