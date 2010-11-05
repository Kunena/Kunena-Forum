<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined( '_JEXEC' ) or die();


class CKunenaStats {
	protected $_db = null;
	protected $_config = null;

	public $totalmembers = null;
	public $totaltitles = null;
	public $totalmsgs = null;
	public $totalsections = null;
	public $totalcats = null;
	public $lastestmember = null;
	public $lastestmemberid = null;
	public $todayopen = null;
	public $yesterdayopen = null;
	public $todayanswer = null;
	public $yesterdayanswer = null;
	public $topposters = null;
	public $topmessage = null;
	public $topprofiles = null;
	public $topprofilehits = null;
	public $toptitles = null;
	public $toptitlehits = null;
	public $toppolls = null;

	public $showgenstats = false;
	public $showpopuserstats = false;
	public $showpopsubjectstats = false;
	public $showpoppollstats = false;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_config = KunenaFactory::getConfig ();

		$show = $this->_config->showstats;
		$this->showgenstats = $show ? $this->_config->showgenstats : 0;
		$this->showpopuserstats = $show ? $this->_config->showpopuserstats : 0;
		$this->showpopsubjectstats = $show ? $this->_config->showpopsubjectstats : 0;
		$this->showpoppollstats = $show ? $this->_config->showpoppollstats : 0;
	}

	public function loadTotalMembers() {
		if ($this->totalmembers === null) {
			$this->_db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE block=0 OR activation=''" );
			$this->totalmembers = $this->_db->loadResult ();
			KunenaError::checkDatabaseError();
		}
	}

	public function loadLastDays() {
		if ($this->todayopen === null) {
			$todaystart = strtotime ( date ( 'Y-m-d' ) );
			$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
			$this->_db->setQuery ( "SELECT SUM(time >= '{$todaystart}' AND parent='0') AS todayopen,
				SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent='0') AS yesterdayopen,
				SUM(time >= '{$todaystart}' AND parent>'0') AS todayanswer,
				SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent>'0') AS yesterdayanswer
				FROM #__kunena_messages WHERE time >= '{$yesterdaystart}' AND hold='0'" );

			$totaltmp = $this->_db->loadObject ();
			KunenaError::checkDatabaseError();
			$ret['todayopen'] = $this->todayopen = ! empty ( $totaltmp->todayopen ) ? $totaltmp->todayopen : 0;
			$ret['yesterdayopen'] = $this->yesterdayopen = ! empty ( $totaltmp->yesterdayopen ) ? $totaltmp->yesterdayopen : 0;
			$ret['todayanswer'] = $this->todayanswer = ! empty ( $totaltmp->todayanswer ) ? $totaltmp->todayanswer : 0;
			$ret['yesterdayanswer'] = $this->yesterdayanswer = ! empty ( $totaltmp->yesterdayanswer ) ? $totaltmp->yesterdayanswer : 0;
		}
	}

	public function loadTotalTopics() {
		if ($this->totaltitles === null) {
			$this->_db->setQuery ( "SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__kunena_categories WHERE parent='0' AND published=1" );
			$totaltmp = $this->_db->loadObject ();
			KunenaError::checkDatabaseError();
			$this->totaltitles = ! empty ( $totaltmp->titles ) ? $totaltmp->titles : 0;
			$this->totalmsgs = ! empty ( $totaltmp->msgs ) ? $totaltmp->msgs + $this->totaltitles : $this->totaltitles;
		}
	}

	public function loadTotalCategories() {
		if ($this->totalsections === null) {
			$this->_db->setQuery ( "SELECT SUM(parent='0') AS totalcats, SUM(parent>'0') AS totalsections FROM #__kunena_categories WHERE published=1" );
			$totaltmp = $this->_db->loadObject ();
			KunenaError::checkDatabaseError();
			$this->totalsections = ! empty ( $totaltmp->totalsections ) ? $totaltmp->totalsections : 0;
			$this->totalcats = ! empty ( $totaltmp->totalcats ) ? $totaltmp->totalcats : 0;
		}
	}

	public function loadLastUser() {
		if ($this->lastestmember === null) {
			$queryName = $this->_config->username ? "username" : "name";
			$this->_db->setQuery ( "SELECT id, {$queryName} AS username FROM #__users WHERE block='0' OR activation='' ORDER BY id DESC", 0, 1 );
			$_lastestmember = $this->_db->loadObject ();
			KunenaError::checkDatabaseError();
			$this->lastestmember = $_lastestmember->username;
			$this->lastestmemberid = $_lastestmember->id;
		}
	}

	function loadTopPosters($PopUserCount=0) {
		if (!$PopUserCount)
			$PopUserCount = $this->_config->popusercount;
		if (count($this->topposters) < $PopUserCount) {
			$queryName = $this->_config->username ? "username" : "name";
			$this->_db->setQuery ( "SELECT p.userid, p.posts, u.id, u.{$queryName} AS username FROM #__kunena_users AS p
				INNER JOIN #__users AS u ON u.id = p.userid WHERE p.posts > '0' AND u.block=0 ORDER BY p.posts DESC", 0, $PopUserCount );

			$this->topposters = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$this->topmessage = ! empty ( $this->topposters [0]->posts ) ? $this->topposters [0]->posts : 0;
		}
	}

	function loadTopProfiles($PopUserCount=0) {
		if (!$PopUserCount)
			$PopUserCount = $this->_config->popusercount;
		if (count($this->topprofiles) < $PopUserCount) {
			$queryName = $this->_config->username ? "username" : "name";
			if ($this->_config->integration_profile == "jomsocial") {
				$this->_db->setQuery ( "SELECT u.id AS user_id, c.view AS hits, u.{$queryName} AS user FROM #__community_users as c
					LEFT JOIN #__users as u on u.id=c.userid
					WHERE c.view>'0' ORDER BY c.view DESC", 0, $PopUserCount );
			} elseif ($this->_config->integration_profile == "cb") {
				$this->_db->setQuery ( "SELECT c.hits AS hits, u.id AS user_id, u.{$queryName} AS user FROM #__comprofiler AS c
					INNER JOIN #__users AS u ON u.id = c.user_id
					WHERE c.hits>'0' ORDER BY c.hits DESC", 0, $PopUserCount );
			} elseif ($this->_config->integration_profile == "aup") {
				$this->_db->setQuery ( "SELECT a.profileviews AS hits, u.id AS user_id, u.{$queryName} AS user FROM #__alpha_userpoints AS a
					INNER JOIN #__users AS u ON u.id = a.userid
					WHERE u.profileviews>'0' ORDER BY u.profileviews DESC", 0, $PopUserCount );
			} else {
				$this->_db->setQuery ( "SELECT u.uhits AS hits, u.userid AS user_id, j.id, j.{$queryName} AS user FROM #__kunena_users AS u
					INNER JOIN #__users AS j ON j.id = u.userid
					WHERE u.uhits>'0' AND j.block=0 ORDER BY u.uhits DESC", 0, $PopUserCount );
			}

			$this->topprofiles = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$this->topprofilehits = ! empty ( $this->topprofiles [0]->hits ) ? $this->topprofiles [0]->hits : 0;
		}
	}

	function loadGenStats($override=false) {
		if (! $this->showgenstats && ! $override)
			return;

		$this->loadTotalMembers();
		$this->loadLastDays();
		$this->loadTotalTopics();
		$this->loadTotalCategories();
		$this->loadLastUser();
	}

	function loadUserStats($override=false) {
		if (! $this->showpopuserstats && ! $override)
			return;

		$this->loadTopPosters();
		$this->loadTopProfiles();
	}

	function loadTopicStats($override=false) {
		if (! $this->showpopsubjectstats && ! $override)
			return;

		if (!$override) $PopSubjectCount = $this->_config->popsubjectcount;
		else $PopSubjectCount = $override;

		if (count($this->toptitles) < $PopSubjectCount) {
			$kunena_session = & KunenaFactory::getSession ();
			$this->_db->setQuery ( "SELECT * FROM #__kunena_messages WHERE moved='0' AND hold='0' AND parent='0' AND catid IN ($kunena_session->allowed)
				ORDER BY hits DESC", 0, $PopSubjectCount );

			$this->toptitles = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$this->toptitlehits = ! empty ( $this->toptitles [0]->hits ) ? $this->toptitles [0]->hits : 0;
		}
	}

	function loadPollStats($override=false) {
		if (! $this->showpoppollstats && ! $override)
			return;

		if (!$override) $PopPollsCount = $this->_config->poppollscount;
		else $PopPollsCount = $override;

		require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
  		$kunena_polls =& CKunenaPolls::getInstance();

		if (count($this->toppolls) < $PopPollsCount) {
			$this->toppolls = $kunena_polls->get_top_five_polls ( $PopPollsCount );
			$this->toppollvotes = $kunena_polls->get_top_five_votes ( $PopPollsCount );
		}
	}

	public function showStats() {
		CKunenaTools::loadTemplate('/plugin/stats/stats.php');
	}

	public function showFrontStats() {
		CKunenaTools::loadTemplate('/plugin/stats/frontstats.php');
	}
}
