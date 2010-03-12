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
		require_once (KUNENA_PATH_LIB .DS. "kunena.session.class.php");

		$this->_db = JFactory::getDBO ();
		$this->_my = JFactory::getUser ();
		$this->_session = CKunenaSession::getInstance();
	}

	public static function version() {
		return 0;
	}

	public function getAllowedCategories($userid) {
		if ((int)$userid<1 || $userid != $this->_my->id) return;
		return $this->_session->allowed;
	}
	public function getProfile($userid) {
		require_once (KUNENA_PATH_LIB .DS. "kunena.user.class.php");

		return CKunenaUserprofile::getInstance ( $userid );
	}
	public function getRank($userid) {
		require_once (KUNENA_PATH_LIB .DS. "kunena.user.class.php");
		require_once (KPATH_SITE .DS. "class.kunena.php");

		$profile = CKunenaUserprofile::getInstance ( $userid );
		return CKunenaTools::getRank ( $profile );
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

		$this->_session->updateAllowedForums($this->_my->id);
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

		$this->_session->updateAllowedForums($this->_my->id);
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

		$this->_session->updateAllowedForums($this->_my->id);
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

