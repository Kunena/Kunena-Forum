<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
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
	public $topthanks = null;
	public $topuserthanks = null;

	public $showgenstats = false;
	public $showpopuserstats = false;
	public $showpopsubjectstats = false;
	public $showpoppollstats = false;
	public $showpopthankyoustats = false;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_config = KunenaFactory::getConfig ();

		$show = $this->_config->showstats;
		$this->showgenstats = $show ? $this->_config->showgenstats : 0;
		$this->showpopuserstats = $show ? $this->_config->showpopuserstats : 0;
		$this->showpopsubjectstats = $show ? $this->_config->showpopsubjectstats : 0;
		$this->showpoppollstats = $show ? $this->_config->showpoppollstats : 0;
		$this->showpopthankyoustats = $show ? $this->_config->showpopthankyoustats : 0;
	}

	public function &getInstance()
	{
		static $instance = NULL;

		if (!isset($instance)) {
			$instance = new CKunenaStats();
		}

		return $instance;
	}

	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public function loadTotalMembers() {
		if ($this->totalmembers === null) {
			$this->_db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE block=0 AND activation=''" );
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
			$this->totaltitles = 0;
			$this->totalmsgs = 0;
			kimport('kunena.forum.category.helper');
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
			foreach ($categories as $category) {
				if (!$category->published) continue;
				$this->totaltitles += $category->numTopics;
				$this->totalmsgs += $category->numPosts;
			}
		}
	}

	public function loadTotalCategories() {
		if ($this->totalsections === null) {
			$this->totalsections = 0;
			$this->totalcats = 0;
			kimport('kunena.forum.category.helper');
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
			foreach ($categories as $category) {
				if (!$category->published) continue;
				if ($category->parent_id == 0)
					$this->totalsections ++;
				else
					$this->totalcats ++;
			}
		}
	}

	public function loadLastUser() {
		if ($this->lastestmember === null) {
			$queryName = $this->_config->username ? "username" : "name";
			if ($this->_config->userlist_count_users == '0' ) $where = '1';
			elseif ($this->_config->userlist_count_users == '1' ) $where = 'block=0 OR activation=""';
			elseif ($this->_config->userlist_count_users == '2' ) $where = 'block=0 AND activation=""';
			$this->_db->setQuery ( "SELECT id, {$queryName} AS username FROM #__users WHERE {$where} ORDER BY id DESC", 0, 1 );
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
			$profile = KunenaFactory::getProfile();
			$this->topprofiles = $profile->getProfileView($PopUserCount);
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

		$categories = KunenaForumCategoryHelper::getCategories();
		$allowed = implode(',', array_keys($categories));
		if (count($this->toptitles) < $PopSubjectCount) {
			$query = "SELECT id, category_id AS catid, subject, posts AS hits
				FROM #__kunena_topics
				WHERE moved_id=0 AND hold=0 AND category_id IN ({$allowed})
				ORDER BY hits DESC";
			$this->_db->setQuery ( $query, 0, $PopSubjectCount );

			$this->toptitles = (array) $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$this->toptitlehits = ! empty ( $this->toptitles [0]->hits ) ? $this->toptitles [0]->hits : 0;
		}
	}

 	/**
	* Get the better votes in polls
	* @return int
	*/
	protected function getTopVotes($PopPollsCount) {
		$query = "SELECT SUM(o.votes) AS total
				FROM jos_kunena_polls_options AS o
				INNER JOIN jos_kunena_polls AS p
				GROUP BY p.threadid
				ORDER BY total DESC";
		$this->_db->setQuery($query,0,$PopPollsCount);
		$votecount = $this->_db->loadResult();
		KunenaError::checkDatabaseError();

		return $votecount;
	}
	/**
	* Get the better polls
	* @return Array
	*/
	protected function getTopPolls($PopPollsCount)
	{
		$query = "SELECT m.*, poll.*, SUM(opt.votes) AS total
				FROM jos_kunena_polls_options AS opt
				INNER JOIN jos_kunena_polls AS poll ON poll.id=opt.pollid
				LEFT JOIN jos_kunena_messages AS m ON poll.threadid=m.id
				GROUP BY pollid
				ORDER BY total DESC";
		$this->_db->setQuery($query,0,$PopPollsCount);
	 	$toppolls = $this->_db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $toppolls;
	}

	public function loadPollStats($override=false) {
		if (! $this->showpoppollstats && ! $override)
			return;

		if (!$override) $PopPollsCount = $this->_config->poppollscount;
		else $PopPollsCount = $override;

		if (count($this->toppolls) < $PopPollsCount) {
			$this->toppolls = $this->getTopPolls ( $PopPollsCount );
			$this->toppollvotes = $this->getTopVotes ( $PopPollsCount );
		}
	}

	public function loadThanksStats($override=false) {
		if (! $this->showpopthankyoustats && ! $override)
			return;

		if (!$override) $thanksCount = $this->_config->popthankscount;
		else $thanksCount = $override;

		if (count($this->topthanks) < $thanksCount) {
			$queryName = $this->_config->username ? "username" : "name";
			$this->_db->setQuery ( "SELECT a.*,b.id,b.{$queryName} AS username,COUNT(a.targetuserid) AS receivedthanks FROM `#__kunena_thankyou` AS a INNER JOIN #__users AS b ON a.targetuserid=b.id GROUP BY a.targetuserid ORDER BY COUNT(a.targetuserid) DESC", 0, $thanksCount );
			$this->topuserthanks = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$this->topthanks = ! empty ( $this->topuserthanks [0]->receivedthanks ) ? $this->topuserthanks [0]->receivedthanks : 0;
		}
	}
}
