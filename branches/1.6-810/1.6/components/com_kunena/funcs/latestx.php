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
 **/
defined ( '_JEXEC' ) or die ();

require_once (KUNENA_PATH_LIB . DS . 'kunena.smile.class.php');

class CKunenaLatestX {
	public $allow = 0;

	function __construct($func, $page = 0) {
		$this->func = JString::strtolower ($func );
		$this->catid = 0;
		$this->hasSubCats = '';
		$this->mode = 'threads';
		$this->header = '';

		$this->db = JFactory::getDBO ();
		$this->user = $this->my = JFactory::getUser ();
		$this->myprofile = KunenaFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = CKunenaConfig::getInstance ();

		$this->page = $page < 1 ? 1 : $page;
		$this->threads_per_page = $this->config->threads_per_page;
		$this->offset = ($this->page - 1) * $this->threads_per_page;

		$this->prevCheck = $this->session->lasttime;

		$this->app = & JFactory::getApplication ();
		$this->document = & JFactory::getDocument ();

		$this->show_list_time = JRequest::getInt ( 'sel', 720 );

		// My latest is only available to users
		if (! $this->user->id && $func == "mylatest") {
			return;
		}

		$this->allow = 1;
		$this->highlight = 0;

		$this->tabclass = array ("sectiontableentry1", "sectiontableentry2" );

		if (! $this->my->id && $this->show_list_time == 0) {
			$this->show_list_time = "720";
		}

		//start the latest x
		if ($this->show_list_time == 0) {
			$this->querytime = ($this->prevCheck - $this->config->fbsessiontimeout); //move 30 minutes back to compensate for expired sessions
		} else {
			//Time translation
			$back_time = $this->show_list_time * 3600; //hours*(mins*secs)
			$this->querytime = time () - $back_time;
		}

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;

		$access = KunenaFactory::getAccessControl();
		$this->hold = $access->getAllowedHold($this->myprofile, $this->catid);
	}

	protected function _common() {
		$this->totalpages = ceil ( $this->total / $this->threads_per_page );

		$this->messages = array ();
		$this->threads = array ();
		$this->lastreply = array ();
		$this->customreply = array ();

		if (!empty ( $this->threadids ) ) {
			$idstr = implode ( ",", $this->threadids );
			if (empty($this->loadids)) $loadstr = '';
			else $loadstr = 'OR a.id IN ('.implode ( ",", $this->loadids ).')';

			$query = "SELECT a.*, j.id AS userid, t.message, l.myfavorite, l.favcount, l.threadhits, l.threadattachments, COUNT(aa.id) AS attachments,
				l.msgcount, l.mycount, l.lastid, l.mylastid, l.lastid AS lastread, 0 AS unread, u.avatar, c.name AS catname, c.class_sfx
			FROM (
				SELECT m.thread, MAX(m.hits) AS threadhits, MAX(f.userid IS NOT null AND f.userid='{$this->my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount,
					COUNT(DISTINCT a.id) AS threadattachments, COUNT(DISTINCT m.id) AS msgcount, COUNT(DISTINCT IF(m.userid={$this->user->id}, m.id, NULL)) AS mycount,
					MAX(m.id) AS lastid, MAX(IF(m.userid={$this->user->id}, m.id, 0)) AS mylastid, MAX(m.time) AS lasttime
				FROM #__fb_messages AS m";
				if ($this->config->allowfavorites) $query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread";
				else $query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = 0";
				$query .= "
				LEFT JOIN #__kunena_attachments AS a ON a.mesid = m.id
				WHERE m.hold IN ({$this->hold}) AND m.moved='0' AND m.thread IN ({$idstr})
				GROUP BY thread
			) AS l
			INNER JOIN #__fb_messages AS a ON a.thread = l.thread
			INNER JOIN #__fb_messages_text AS t ON a.id = t.mesid
			LEFT JOIN #__users AS j ON j.id = a.userid
			LEFT JOIN #__fb_users AS u ON u.userid = j.id
			LEFT JOIN #__fb_categories AS c ON c.id = a.catid
			LEFT JOIN #__kunena_attachments AS aa ON aa.mesid = a.id
			WHERE (a.parent='0' OR a.id=l.lastid {$loadstr})
			GROUP BY a.id
			ORDER BY {$this->order}";

			$this->db->setQuery ( $query );
			$this->messages = $this->db->loadObjectList ('id');
			check_dberror ( "Unable to load messages." );
			// collect user ids for avatar prefetch when integrated
			$userlist = array();
			foreach ( $this->messages as $message ) {
				if ($message->parent == 0) {
					$this->threads [$message->thread] = $message;
					$routerlist [$message->id] = $message->subject;
					if ($this->func == 'mylatest' && $message->myfavorite) $this->highlight++;
				}
				if ($message->id == $message->lastid) {
					$this->lastreply [$message->thread] = $message;
				}
				if (isset($this->loadids) && in_array($message->id, $this->loadids)) {
					$this->customreply [$message->id] = $message;
				}
				$userlist[$message->userid] = $message->userid;
			}

			// Load threads to Kunena router to avoid extra SQL queries
			include_once (KUNENA_PATH . DS . 'router.php');
			KunenaRouter::loadMessages ( $routerlist );

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			$avatars = KunenaFactory::getAvatarIntegration();
			$avatars->load($userlist);

			if ($this->config->shownew && $this->my->id) {
				$readlist = $this->session->readtopics;
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages " . "WHERE hold IN ({$this->hold}) AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>'{$this->prevCheck}' GROUP BY thread" );
				$msgidlist = $this->db->loadObjectList ();
				check_dberror ( "Unable to get unread messages count and first id." );

				foreach ( $msgidlist as $msgid ) {
					$this->messages[$msgid->thread]->lastread = $msgid->lastread;
					$this->messages[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}
	}

	protected function _getMyLatest($posts = true, $fav = true, $sub = false) {
		$subquery = array();
		if (!$posts && !$fav && !$sub) $subquery[] = "SELECT thread, 0 AS fav, 0 AS sub
			FROM #__fb_messages
			WHERE userid='{$this->user->id}' AND parent='0' AND moved='0' AND hold IN ({$this->hold}) AND catid IN ({$this->session->allowed})";
		if ($posts) $subquery[] = "SELECT thread, 0 AS fav, 0 AS sub
			FROM #__fb_messages
			WHERE userid='{$this->user->id}' AND moved='0' AND hold IN ({$this->hold}) AND catid IN ({$this->session->allowed})
			GROUP BY thread";
		if ($fav) $subquery[] = "SELECT thread, 1 AS fav, 0 AS sub FROM #__fb_favorites WHERE userid='{$this->user->id}'";
		if ($sub)  $subquery[] = "SELECT thread, 0 AS fav, 1 AS sub FROM #__fb_subscriptions WHERE userid='{$this->user->id}'";
		if (empty($subquery)) return;
		$subqueries = implode("\n	UNION ALL \n", $subquery);

		$query = "SELECT COUNT(DISTINCT m.thread) FROM ({$subqueries}) AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to count total threads' );

		if ($this->func == 'mylatest') $this->order = "myfavorite DESC, lastid DESC";
		else if ($this->func == 'usertopics') $this->order = "mylastid DESC";
		else $this->order = "lastid DESC";

		$query = "SELECT m.thread, MAX(m.id) AS lastid, MAX(IF(m.userid={$this->user->id}, m.id, 0)) AS mylastid, MAX(t.fav) AS myfavorite, MAX(t.sub) AS mysubscribe FROM ({$subqueries}) AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
		GROUP BY thread
		ORDER BY {$this->order}";

		$this->db->setQuery ( $query, $this->offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		check_dberror ( "Unable to load thread list." );

		$this->_common();
	}

	protected function _getPosts($type = 'user') {
		if (isset($this->total)) return;
		$this->mode = 'posts';

		$this->threads_per_page = 10;
		$myhold = explode(',', $this->hold);
		$hold = $this->hold;
		switch ($type) {
			case 'unapproved':
				if (!in_array(1, $myhold)) {
					return $this->allow = false;
				}
				$hold = '1';
				break;
			case 'deleted':
				if (!in_array(2, $myhold)) {
					return $this->allow = false;
				}
				$hold = '2,3';
				break;
			case 'user':
				$user = 1;
				break;
		}
		if (isset($user)) $where[] = "m.userid='{$this->user->id}'";
		$where[] = "mm.moved='0'";
		$where[] = "m.moved='0'";
		$where[] = "mm.hold IN ({$this->hold})";
		$where[] = "m.hold IN ({$hold})";
		$where[] = "mm.catid IN ({$this->session->allowed})";
		$where = implode(' AND ', $where);

		$query = "SELECT COUNT(*) FROM #__fb_messages AS m
		INNER JOIN #__fb_messages AS mm ON m.thread = mm.id
		WHERE {$where}";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to count total threads' );

		$query = "SELECT m.thread, m.id
		FROM #__fb_messages AS m
		INNER JOIN #__fb_messages AS mm ON m.thread = mm.id
		WHERE {$where}
		ORDER BY m.time DESC";

		$this->db->setQuery ( $query, $this->offset, $this->threads_per_page );
		$idlist = $this->db->loadObjectList ();
		check_dberror ( "Unable to load post list." );

		$this->threadids = array();
		$this->loadids = array();
		foreach ( $idlist as $item ) {
			$this->threadids[$item->thread] = $item->thread;
			$this->loadids[$item->id] = $item->id;
		}

		$this->order = 'field(a.id,'.implode ( ",", $this->loadids ).')';
		$this->_common();
	}

	function getUserPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_USERPOSTS');
		$this->_getPosts('user');
	}

	function getUnapprovedPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_UNAPPROVEDPOSTS');
		$this->_getPosts('unapproved');
	}

	function getDeletedPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_DELETEDPOSTS');
		$this->_getPosts('deleted');
	}

	function getOwnTopics() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_OWNTOPICS');
		$this->_getMyLatest(false, false, false);
	}

	function getUserTopics() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_USERTOPICS');
		$this->_getMyLatest(true, false, false);
	}

	function getFavorites() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_FAVORITES');
		$this->_getMyLatest(false, true, false);
	}

	function getSubscriptions() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_SUBSCRIPTIONS');
		$this->_getMyLatest(false, false, true);
	}

	function getmyLatest() {
		if (isset($this->total)) return;
		$this->header = JText::_('COM_KUNENA_MENU_MYLATEST_DESC');
		$this->title = JText::_('COM_KUNENA_MY_DISCUSSIONS');
		$this->_getMyLatest();
	}

	function getLatest() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$lookcats = explode ( ',', $this->config->latestcategory );
		$catlist = array ();
		foreach ( $lookcats as $catnum ) {
			$catlist [] = ( int ) $catnum;
		}
		$latestcats = '';
		if ( !empty($catlist) && !in_array(0, $catlist)) {
			$catlist = implode ( ',', $catlist );
			if ( $this->config->latestcategory_in == '1' ) {
				$latestcats = ' AND m.catid IN ('.$catlist.') ';
			} else {
				$latestcats = ' AND m.catid NOT IN ('.$catlist.') ';
			}
		}

		$query = "Select COUNT(DISTINCT t.thread) FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
		AND t.time >'{$this->querytime}' AND t.hold IN ({$this->hold}) AND t.moved=0 AND t.catid IN ({$this->session->allowed})" . $latestcats; // if categories are limited apply filter


		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to count total threads' );
		$offset = ($this->page - 1) * $this->threads_per_page;

		$this->order = "lastid DESC";
		$query = "SELECT m.id, MAX(t.id) AS lastid FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
			WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
			AND t.time>'{$this->querytime}' AND t.hold IN ({$this->hold}) AND t.moved='0' AND t.catid IN ({$this->session->allowed}) {$latestcats}
			GROUP BY t.thread
			ORDER BY {$this->order}
		";

		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		check_dberror ( "Unable to load thread list." );
		$this->_common();
	}

	function getNoReplies() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_NOREPLIES_DESC');
		$this->title = JText::_('COM_KUNENA_NO_REPLIES');
		$query = "SELECT COUNT(DISTINCT tmp.thread) FROM
			(SELECT m.thread, count(*) AS posts
				FROM #__fb_messages as m
				JOIN (SELECT thread
					FROM #__fb_messages
					WHERE time >'$this->querytime' AND parent=0 AND
						hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
				) as t ON m.thread = t.thread
				WHERE m.hold=0 AND m.moved=0
				GROUP BY 1
			) AS tmp
			WHERE tmp.posts = 1";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to count total threads' );
		$offset = ($this->page - 1) * $this->threads_per_page;

		$this->order = "lastid DESC";
		$query = "SELECT thread, thread as lastid FROM
			(SELECT m.thread, count(*) AS posts
				FROM #__fb_messages as m
				JOIN (SELECT thread
					FROM #__fb_messages
					WHERE time >'{$this->querytime}' AND parent=0 AND
						hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
				) as t ON m.thread = t.thread
				WHERE m.hold=0 AND m.moved=0
				GROUP BY 1
			) AS tmp
			WHERE tmp.posts = 1
			GROUP BY thread
			ORDER BY {$this->order}";

		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		check_dberror ( "Unable to load thread list." );
		$this->_common();
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			CKunenaTools::loadTemplate('/plugin/announcement/announcementbox.php');
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displayItems() {
		if ($this->mode == 'threads') $this->displayFlat();
		else $this->displayPosts();
	}

	function displayFlat() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		CKunenaTools::loadTemplate('/threads/flat.php');
	}

	function displayPosts() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		CKunenaTools::loadTemplate('/threads/posts.php');
	}

	function displayStats() {
		if ($this->config->showstats > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			CKunenaTools::loadTemplate('/plugin/who/whoisonline.php');
		}
	}

	function getPagination($func, $sel, $page, $totalpages, $maxpages) {
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if (($startpage) > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, 1, 'follow', '', $sel ) . '</li>';
			if (($startpage) > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, $i, 'follow', '', $sel ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, $totalpages, 'follow', '', $sel ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	function display() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if ($this->func == 'mylatest') $this->getMyLatest();
		else if ($this->func == 'noreplies') $this->getNoReplies();
		else if ($this->func == 'subscriptions') $this->getSubscriptions();
		else if ($this->func == 'favorites') $this->getFavorites();
		else if ($this->func == 'userposts') $this->getUserPosts();
		else if ($this->func == 'unapproved') $this->getUnapprovedPosts();
		else if ($this->func == 'deleted') $this->getDeletedPosts();
		else $this->getLatest();
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}

		//meta description and keywords
		$metaKeys = $this->header . kunena_htmlspecialchars ( stripslashes ( ", {$this->config->board_title}, " ) ) . $this->app->getCfg ( 'sitename' );
		$metaDesc = $this->header . kunena_htmlspecialchars ( stripslashes ( " ({$this->page}/{$this->totalpages}) - {$this->config->board_title}" ) );

		$cur = $this->document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$this->document = & JFactory::getDocument ();
		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );

		$this->document->setTitle ( $this->title . ' - ' . stripslashes ( $this->config->board_title ) );

		CKunenaTools::loadTemplate('/threads/latestx.php');
	}
}
