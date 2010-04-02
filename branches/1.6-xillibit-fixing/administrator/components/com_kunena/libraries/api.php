<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once (KPATH_SITE . DS . 'lib' . DS . 'kunena.defines.php');

class Kunena implements iKunena {
	protected static $version = false;
	protected static $version_date = false;
	protected static $version_name = false;
	protected static $version_build = false;

	public static function version() {
		if (self::$version === false) {
			if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
				$changelog = file_get_contents ( KPATH_SITE . '/CHANGELOG.php', NULL, NULL, 0, 1000 );
				preg_match ( '|\$Id\: CHANGELOG.php (\d+) (\S+) (\S+) (\S+) \$|', $changelog, $svn );
				preg_match ( '|~~\s+Kunena\s(\d+\.\d+.\d+\S*)|', $changelog, $version );
			}
			self::$version = ('@kunenaversion@' == '@' . 'kunenaversion' . '@') ? strtoupper ( $version [1] . '-SVN' ) : strtoupper ( '@kunenaversion@' );
			self::$version_date = ('@kunenaversiondate@' == '@' . 'kunenaversiondate' . '@') ? $svn [2] : '@kunenaversiondate@';
			self::$version_name = ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'SVN Revision' : '@kunenaversionname@';
			self::$version_build = ('@kunenaversionbuild@' == '@' . 'kunenaversionbuild' . '@') ? $svn [1] : '@kunenaversionbuild@';
		}
		return self::$version;
	}

	public static function getVersionInfo() {
		$version = new stdClass();
		$version->version = self::version();
		$version->date = self::$version_date;
		$version->name = self::$version_name;
		$version->build = self::$version_build;
		return $version;
	}

	public static function getConfig() {
		require_once (JPATH_COMPONENT . DS . 'lib' . DS . "kunena.config.class.php");
		return CKunenaConfig::getInstance();
	}

	public static function getUserAPI() {
		return new KunenaUserAPI();
	}

	public static function getStatsAPI() {
		return new KunenaStatsAPI();
	}

/*
	public static function getForumAPI() {
		return new KunenaForumAPI();
	}

	public static function getPostAPI() {
		return new KunenaPostAPI();
	}
*/
}


class KunenaUserAPI implements iKunenaUserAPI {
	protected $_db = null;
	protected $_my = null;
	protected $_session = null;

	public function __construct() {
		$this->_db = JFactory::getDBO ();
		$this->_my = JFactory::getUser ();
		$this->_session = KunenaFactory::getSession( true );
	}

	public static function version() {
		return 0;
	}

	public function getAllowedCategories($userid) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		return $this->_session->allowed;
	}
	public function getProfile($userid) {
		return KunenaFactory::getUser ( $userid );
	}
	public function getRank($userid) {
		require_once (KPATH_SITE .DS. "class.kunena.php");

		$profile = KunenaFactory::getUser ( $userid );
		return $profile->getRank ();
	}

	public function getTopicsTotal($userid) {
		$result = $this->getTopics($userid);
		return $result->total;
	}
	public function getTopics($userid, $start = 0, $limit = 10, $search=false) {
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('usertopics', 0);
		$obj->user = JUser::getInstance ( $userid );
		$obj->offset = $start;
		$obj->threads_per_page = $limit;
		$obj->getUserTopics();
		$result = new stdClass();
		$result->total = $obj->total;
		$result->messages = $obj->threads;
		return $result;
	}
	public function getPostsTotal($userid) {
		$result = $this->getPosts($userid);
		return $result->total;
	}
	public function getPosts($userid, $start = 0, $limit = 10, $search=false) {
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('ownposts', 0);
		$obj->user = JUser::getInstance ( $userid );
		$obj->offset = $start;
		$obj->threads_per_page = $limit;
		$obj->getUserPosts();
		$result = new stdClass();
		$result->total = $obj->total;
		$result->messages = $obj->customreply;
		return $result;
	}
	public function getFavoritesTotal($userid) {
		$result = $this->getFavorites($userid);
		return $result->total;
	}
	public function getFavorites($userid, $start = 0, $limit = 10, $search=false) {
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('favorites', 0);
		$obj->user = JUser::getInstance ( $userid );
		$obj->offset = $start;
		$obj->threads_per_page = $limit;
		$obj->getFavorites();
		$result = new stdClass();
		$result->total = $obj->total;
		$result->messages = $obj->threads;
		return $result;
	}
	public function getSubscriptionsTotal($userid) {
		$result = $this->getSubscriptions($userid);
		return $result->total;
	}
	public function getSubscriptions($userid, $start = 0, $limit = 10, $search=false) {
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('subscriptions', 0);
		$obj->user = JUser::getInstance ( $userid );
		$obj->offset = $start;
		$obj->threads_per_page = $limit;
		$obj->getSubscriptions();
		$result = new stdClass();
		$result->total = $obj->total;
		$result->messages = $obj->threads;
		return $result;
	}

	public function subscribeThreads($userid, $threads) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		$threadlist = $this->parseParam($threads);
		if (!is_array($threadlist) || empty($threadlist)) return;
		$threads = implode(',', $threadlist);

		$this->_session->updateAllowedForums();
		$allowed = $this->_session->allowed;

		// Only subscribe if allowed and not already subscribed
		$query = "SELECT id FROM #__fb_messages AS m LEFT JOIN #__fb_subscriptions AS s ON m.thread=s.thread
			WHERE m.id IN ($threads) AND m.parent=0 AND m.catid IN ($allowed) AND m.hold=0 AND m.moved=0 AND s.thread IS NULL";
		$this->_db->setQuery ($query);
		$threads = $this->_db->loadResultArray();
		check_dberror("Unable to load threads to be subscribed.");
		if (empty($threads)) return;

		foreach ($threads as $thread) {
			$subquery[] = "(".(int)$thread.",".(int)$userid.")";
		}
		$query = "INSERT INTO #__fb_subscriptions (thread,userid) VALUES " . implode(',', $subquery);
		$this->_db->setQuery ($query);
		$this->_db->query ();
		check_dberror('Unable to subscribe '.implode(',', $threads).'.');
		return $this->_db->getAffectedRows ();
	}
	public function unsubscribeThreads($userid, $threads = false) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		if ($threads === true) {
			$where = '';
		} else {
			$threadlist = $this->parseParam($threads);
			if (!is_array($threadlist) || empty($threadlist)) return;
			$threads = implode(',', $threadlist);
			$where = ' AND thread IN('.$threads.')';
		}

		$query = "DELETE FROM #__fb_subscriptions WHERE userid=".(int)$userid . $where;
		$this->_db->setQuery ($query);
		$this->_db->query ();
		check_dberror("Unable to unsubscribe $threads.");
		return $this->_db->getAffectedRows ();
	}
	public function subscribeCategories($userid, $catids) {
		// TODO: NOT TESTED!!
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		$catlist = $this->parseParam($catids);
		if (!is_array($catlist) || empty($catlist)) return;
		$catids = implode(',', $catlist);

		$this->_session->updateAllowedForums();
		$allowed = $this->_session->allowed;

		// Only subscribe if allowed and not already subscribed
		$query = "SELECT id FROM #__fb_categories AS c LEFT JOIN #__fb_subscriptions_categories AS s ON c.id=s.catid
			WHERE c.id IN ($catids) AND c.id IN ($allowed) AND s.catid IS NULL";
		$this->_db->setQuery ($query);
		echo $query;
		$catids = $this->_db->loadResultArray();
		check_dberror("Unable to load threads to be subscribed.");
		if (empty($catids)) return;

		foreach ($catids as $thread) {
			$subquery[] = "(".(int)$thread.",".(int)$userid.")";
		}
		$query = "INSERT INTO #__fb_subscriptions_categories (catid,userid) VALUES " . implode(',', $subquery);
		$this->_db->setQuery ($query);
		$this->_db->query ();
		echo $query;
		check_dberror('Unable to subscribe '.implode(',', $catids).'.');
		return $this->_db->getAffectedRows ();
	}
	public function unsubscribeCategories($userid, $catids = false) {
		// TODO: NOT TESTED!!
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		if ($catids === true) {
			$where = '';
		} else {
			$catlist = $this->parseParam($catids);
			if (!is_array($catlist) || empty($catlist)) return;
			$catids = implode(',', $catlist);
			$where = ' AND catid IN('.$catids.')';
		}

		$query = "DELETE FROM #__fb_subscriptions_categories WHERE userid=".(int)$userid . $where;
		$this->_db->setQuery ($query);
		echo $query;
		$this->_db->query ();
		check_dberror("Unable to unsubscribe $catids.");
		return $this->_db->getAffectedRows ();
	}
	public function favoriteThreads($userid, $threads) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		$threadlist = $this->parseParam($threads);
		if (!is_array($threadlist) || empty($threadlist)) return;
		$threads = implode(',', $threadlist);

		$this->_session->updateAllowedForums();
		$allowed = $this->_session->allowed;

		// Only favorite if allowed and not already favorited
		$query = "SELECT id FROM #__fb_messages AS m LEFT JOIN #__fb_favorites AS f ON m.thread=f.thread
			WHERE m.id IN ($threads) AND m.parent=0 AND m.catid IN ($allowed) AND m.hold=0 AND m.moved=0 AND f.thread IS NULL";
		$this->_db->setQuery ($query);
		$threads = $this->_db->loadResultArray();
		check_dberror("Unable to load threads to be favorited.");
		if (empty($threads)) return;

		foreach ($threads as $thread) {
			$subquery[] = "(".(int)$thread.",".(int)$userid.")";
		}
		$query = "INSERT INTO #__fb_favorites (thread,userid) VALUES " . implode(',', $subquery);
		$this->_db->setQuery ($query);
		$this->_db->query ();
		check_dberror('Unable to favorite '.implode(',', $threads).'.');
		return $this->_db->getAffectedRows ();

	}
	public function unfavoriteThreads($userid, $threads = false) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		if ($threads === true) {
			$where = '';
		} else {
			$threadlist = $this->parseParam($threads);
			if (!is_array($threadlist) || empty($threadlist)) return;
			$threads = implode(',', $threadlist);
			$where = ' AND thread IN('.$threads.')';
		}

		$query = "DELETE FROM #__fb_favorites WHERE userid=".(int)$userid . $where;
		$this->_db->setQuery ($query);
		$this->_db->query ();
		check_dberror("Unable to unfavorite $threads.");
		return $this->_db->getAffectedRows ();
	}

	private function parseParam($param) {
		if (is_bool($param)) return $param;
		$parsed = array();
		if (is_array($param)) {
			foreach ($param as $id) if ((int)$id > 0) $parsed[] = (int)$id;
		} else {
			if ((int)$param > 0) $parsed[] = (int)$param;
		}
		return $parsed;
	}
}

class KunenaStatsAPI {
	protected $_db = null;
	protected $_session = null;
	protected $_config = null;

	public function __construct() {
		$this->_db = JFactory::getDBO ();
		$this->_session = KunenaFactory::getSession();
		$this->_config = CKunenaConfig::getInstance();
	}

	public function getToTalMembers() {
		$this->_db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE block=0" );
		$totalmembers = $this->_db->loadResult ();
		check_dberror ( "Unable to load total users." );

		return $totalmembers;
	}

	private function _queryTotalPosts() {
		$todaystart = strtotime ( date ( 'Y-m-d' ) );
		$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
		$this->_db->setQuery ( "SELECT SUM(time >= '{$todaystart}' AND parent='0') AS todayopen,
			SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent='0') AS yesterdayopen,
			SUM(time >= '{$todaystart}' AND parent>'0') AS todayanswer,
			SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent>'0') AS yesterdayanswer
			FROM #__fb_messages WHERE time >= '{$yesterdaystart}' AND hold='0'" );

		$totaltmp = $this->_db->loadObject ();
		check_dberror ( "Unable to load total posts today/yesterday." );

		return $totaltmp;
	}

	public function getTodayOpen() {
		$totaltmp = $this->_queryTotalPosts();

		$todayopen = ! empty ( $totaltmp->todayopen ) ? $totaltmp->todayopen : 0;

		return $todayopen;
	}

	public function getYesterdayOpen(){
		$totaltmp = $this->_queryTotalPosts();

		$yesterdayopen = ! empty ( $totaltmp->yesterdayopen ) ? $totaltmp->yesterdayopen : 0;

		return $yesterdayopen;
	}

	public function getTodayAnswer(){
		$totaltmp = $this->_queryTotalPosts();

		$todayanswer = ! empty ( $totaltmp->todayanswer ) ? $totaltmp->todayanswer : 0;

		return $todayanswer;
	}

	public function getYesterdayAnswer(){
		$totaltmp = $this->_queryTotalPosts();

		$yesterdayanswer = ! empty ( $totaltmp->yesterdayanswer ) ? $totaltmp->yesterdayanswer : 0;

		return $yesterdayanswer;
	}

	private function _getTotalMessagesQuery(){
		$this->_db->setQuery ( "SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__fb_categories WHERE parent='0'" );
		$totaltmp = $this->_db->loadObject ();
		check_dberror ( "Unable to load total total messages." );

		return $totaltmp;
	}

	public function getTotalTitles(){
	 	$totaltmp =	$this->_getTotalMessagesQuery();

	 	$totaltitles = ! empty ( $totaltmp->titles ) ? $totaltmp->titles : 0;
	 	return $totaltitles;
	}

	public function getTotalMessages(){
		$totaltmp =	$this->_getTotalMessagesQuery();
		$totaltitles = $this->getTotalTitles();

		$totalmsgs = ! empty ( $totaltmp->msgs ) ? $totaltmp->msgs + $totaltitles : $totaltitles;
		return $totalmsgs;
	}

	private function _getTotalCategoriesQuery(){
		$this->_db->setQuery ( "SELECT SUM(parent='0') AS totalcats, SUM(parent>'0') AS totalsections FROM #__fb_categories" );
		$totaltmp = $this->_db->loadObject ();
		check_dberror ( "Unable to load total categories." );

		return $totaltmp;
	}

	public function getTotalSections(){
		$totaltmp = $this->_getTotalCategoriesQuery();

		$totalsections = ! empty ( $totaltmp->totalsections ) ? $totaltmp->totalsections : 0;

		return $totalsections;
	}

	public function getTotalCats(){
		$totaltmp = $this->_getTotalCategoriesQuery();

		$totalcats = ! empty ( $totaltmp->totalcats ) ? $totaltmp->totalcats : 0;

		return $totalcats;
	}

	private function _getLastUserQuery() {
		$queryName = $this->_config->username ? "username" : "name";
		$this->_db->setQuery ( "SELECT id, {$queryName} AS username FROM #__users WHERE block='0' AND activation='' ORDER BY id DESC", 0, 1 );
		$lastestmember = $this->_db->loadObject ();
		check_dberror ( "Unable to load last user." );

		return $lastestmember;
	}

	public function getLastestMember(){
		$tmpmember = $this->_getLastUserQuery();

		$lastestmember = $tmpmember->username;

		return $lastestmember;
	}

	public function getLastestMemberid(){
		$tmpmemberid = $this->_getLastUserQuery();

		$lastestmemberid = $tmpmemberid->id;

		return $lastestmemberid;
	}

	public function getPostersStats($PosterCount) {
		if ((int)$PosterCount<0) return;

		$queryName = $this->_config->username ? "username" : "name";

		$this->_db->setQuery ( "SELECT p.userid, p.posts, u.id, u.{$queryName} AS username FROM #__fb_users AS p
			INNER JOIN #__users AS u ON u.id = p.userid WHERE p.posts > '0' AND u.block=0 ORDER BY p.posts DESC", 0, $PosterCount );

		$topposters = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load top messages." );

		if (is_array($topposters)) {
			return $topposters;
		} else {
			return 0;
		}

	}

	public function getTopMessage($PosterCount){
		if ((int)$PosterCount<0) return;

		$topposters = $this->getPostersStats($PosterCount);

		$topmessage = ! empty ( $topposters [0]->posts ) ? $topposters [0]->posts : 0;

		return $topmessage;
	}

	public function getProfileStats($ProfileCount) {
		if ((int)$ProfileCount<0) return;

		$queryName = $this->_config->username ? "username" : "name";

		if ($this->_config->fb_profile == "jomsocial") {
			$this->_db->setQuery ( "SELECT u.id AS user_id, c.view AS hits, u.{$queryName} AS user FROM #__community_users as c
				LEFT JOIN #__users as u on u.id=c.userid
				WHERE c.view>'0' ORDER BY c.view DESC", 0, $ProfileCount );
		} elseif ($this->_config->fb_profile == "cb") {
			$this->_db->setQuery ( "SELECT c.hits AS hits, u.id AS user_id, u.{$queryName} AS user FROM #__comprofiler AS c
				INNER JOIN #__users AS u ON u.id = c.user_id
				WHERE c.hits>'0' ORDER BY c.hits DESC", 0, $ProfileCount );
		} elseif ($this->_config->fb_profile == "aup") {
			$this->_db->setQuery ( "SELECT a.profileviews AS hits, u.id AS user_id, u.{$queryName} AS user FROM #__alpha_userpoints AS a
				INNER JOIN #__users AS u ON u.id = a.userid
				WHERE u.profileviews>'0' ORDER BY u.profileviews DESC", 0, $ProfileCount );
		} else {
			$this->_db->setQuery ( "SELECT u.uhits AS hits, u.userid AS user_id, j.id, j.{$queryName} AS user FROM #__fb_users AS u
				INNER JOIN #__users AS j ON j.id = u.userid
				WHERE u.uhits>'0' AND j.block=0 ORDER BY u.uhits DESC", 0, $ProfileCount );
		}

		$topprofiles = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load top profiles." );

		if (is_array($topprofiles)) {
			return $topprofiles;
		} else {
			return 0;
		}
	}

	public function getTopicsStats($TopicCount) {
		if ((int)$TopicCount<0) return;

		$allowed = $this->_session->allowed;
		$this->_db->setQuery ( "SELECT * FROM #__fb_messages WHERE moved='0' AND hold='0' AND parent='0' AND catid IN ($allowed)
				ORDER BY hits DESC", 0, $TopicCount );

		$toptopics = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load top topics." );

		if (is_array($toptopics)) {
			return $toptopics;
		} else {
			return 0;
		}
	}

	public function getTopTitlesHits($TopicCount) {
		if ((int)$TopicCount<0) return;

		$toptitlehitslist = $this->getTopicsStats($TopicCount);

		$toptitlehits = ! empty ( $toptitlehitslist [0]->hits ) ? $toptitlehitslist [0]->hits : 0;

		return $toptitlehits;
	}

	public function getTopPollStats($PollCount) {
		if ((int)$PollCount<0) return;

		$toppolls = CKunenaPolls::get_top_five_polls ( $PollCount );

		return $toppolls;
	}

	public function getTopPollVotesStats($PollCount) {
		if ((int)$PollCount<0) return;


		$toppollvotes = CKunenaPolls::get_top_five_votes ( $PollCount );

		return $toppollvotes;
	}
}
