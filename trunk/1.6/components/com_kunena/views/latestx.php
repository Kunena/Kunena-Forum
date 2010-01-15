<?php
/**
 * @version $Id: view.php 1666 2010-01-15 10:49:12Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined ( '_JEXEC' ) or die ();

class CKunenaLatestX {
	public $allow = 0;

	function __construct($func) {
		$this->func = $func;

		$this->allow = 1;

		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->session = CKunenaSession::getInstance ();
		$this->config = CKunenaConfig::getInstance ();

		$this->prevCheck = $this->session->lasttime;
		$this->read_topics = explode ( ',', $this->session->readtopics );

		$kunena_app = & JFactory::getApplication ();
		$document = & JFactory::getDocument ();

		$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
		$this->sel = JRequest::getVar ( 'sel', '' );

		// My latest is only available to logged-in users
		if (! $this->my->id && $func == "mylatest") {
			header ( "HTTP/1.1 307 Temporary Redirect" );
			header ( "Location: " . htmlspecialchars_decode ( CKunenaLink::GetShowLatestURL () ) );
			$kunena_app->close ();
		}

		//resetting some things:
		$lockedForum = 0;
		$lockedTopic = 0;
		$topicSticky = 0;

		if ('' == $this->sel || (! $this->my->id && $this->sel == 0)) {
			/*
    if($this->my->id != 0) { $this->sel="0"; }	// Users: show messages after last visit
    else { $this->sel="720"; }		// Others: show 1 month as default
*/
			$this->sel = "720";
		}
		$show_list_time = $this->sel;

		//start the latest x
		if ($this->sel == 0) {
			$querytime = ($this->prevCheck - $this->config->fbsessiontimeout); //move 30 minutes back to compensate for expired sessions
		} else {
			//Time translation
			$back_time = $this->sel * 3600; //hours*(mins*secs)
			$querytime = time () - $back_time;
		}

		//get the db data with allowed forums and turn it into an array
		$this->threads_per_page = $this->config->threads_per_page;
		/*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
		$this->page = JRequest::getInt ( 'page', 0 );
		$this->page = $this->page < 1 ? 1 : $this->page;
		$offset = ($this->page - 1) * $this->threads_per_page;
		$row_count = $this->page * $this->threads_per_page;

		if ($func != 'mylatest') {
			$lookcats = explode ( ',', $this->config->latestcategory );
			$catlist = array ();
			$latestcats = '';
			foreach ( $lookcats as $catnum ) {
				if (( int ) $catnum && ( int ) $catnum > 0)
					$catlist [] = ( int ) $catnum;
			}
			if (count ( $catlist ))
				$latestcats = " AND m.catid IN (" . implode ( ',', $catlist ) . ") ";
		}

		if ($func == "mylatest") {
			$document->setTitle ( _KUNENA_MY_DISCUSSIONS . ' - ' . stripslashes ( $this->config->board_title ) );
			$query = "SELECT COUNT(DISTINCT m.thread) FROM (
			SELECT thread, 0 AS fav
			FROM #__fb_messages
			WHERE userid='{$this->my->id}' AND moved='0' AND hold='0' AND catid IN ({$this->session->allowed})
			GROUP BY thread
		UNION ALL
			SELECT thread, 1 AS fav FROM #__fb_favorites WHERE userid='{$this->my->id}'
		) AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$this->session->allowed})";
		} else if ($func == "noreplies") {
			$document->setTitle ( _KUNENA_NO_REPLIES . ' - ' . stripslashes ( $this->config->board_title ) );
			$query = "SELECT COUNT(DISTINCT tmp.thread) FROM
				(SELECT m.thread, count(*) AS posts
					FROM #__fb_messages as m
					JOIN (SELECT thread
						FROM #__fb_messages
						WHERE time >'$querytime' AND parent=0 AND
							hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
					) as t ON m.thread = t.thread
					WHERE m.hold=0 AND m.moved=0
					GROUP BY 1
				) AS tmp
				WHERE tmp.posts = 1";
		} else {
			$document->setTitle ( _KUNENA_ALL_DISCUSSIONS . ' - ' . stripslashes ( $this->config->board_title ) );
			$query = "Select COUNT(DISTINCT t.thread) FROM #__fb_messages AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$this->session->allowed})
		AND t.time >'{$querytime}' AND t.hold=0 AND t.moved=0 AND t.catid IN ({$this->session->allowed})" . $latestcats; // if categories are limited apply filter
		}
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to count total threads' );
		$this->totalpages = ceil ( $this->total / $this->threads_per_page );

		//meta description and keywords
		$metaKeys = kunena_htmlspecialchars ( stripslashes ( _KUNENA_ALL_DISCUSSIONS . ", {$this->config->board_title}, " . $kunena_app->getCfg ( 'sitename' ) ) );
		$metaDesc = kunena_htmlspecialchars ( stripslashes ( _KUNENA_ALL_DISCUSSIONS . " ({$this->page}/{$this->totalpages}) - {$this->config->board_title}" ) );

		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document = & JFactory::getDocument ();
		$document->setMetadata ( 'robots', 'noindex, follow' );
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		if ($func == "mylatest") {
			$order = "myfavorite DESC, lastid DESC";
			$query = "SELECT m.thread, MAX(m.id) as lastid, MAX(t.fav) AS myfavorite FROM (
			SELECT thread, 0 AS fav
			FROM #__fb_messages
			WHERE userid='{$this->my->id}' AND moved='0' AND hold='0' AND catid IN ({$this->session->allowed})
			GROUP BY thread
		UNION ALL
			SELECT thread, 1 AS fav FROM #__fb_favorites WHERE userid='{$this->my->id}'
		) AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$this->session->allowed})
		GROUP BY thread
		ORDER BY {$order}
	";
		} else if ($func == "noreplies") {
			$order = "lastid DESC";
			$query = "SELECT thread, thread as lastid FROM
				(SELECT m.thread, count(*) AS posts
					FROM #__fb_messages as m
					JOIN (SELECT thread
						FROM #__fb_messages
						WHERE time >'{$querytime}' AND parent=0 AND
							hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
					) as t ON m.thread = t.thread
					WHERE m.hold=0 AND m.moved=0
					GROUP BY 1
				) AS tmp
				WHERE tmp.posts = 1
				GROUP BY thread
				ORDER BY {$order}";
		} else {
			$order = "lastid DESC";
			$query = "SELECT m.id, MAX(t.id) AS lastid FROM #__fb_messages AS t
		INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$this->session->allowed})
		AND t.time>'{$querytime}' AND t.hold='0' AND t.moved='0' AND t.catid IN ({$this->session->allowed}) {$latestcats}
		GROUP BY t.thread
		ORDER BY {$order}
	";
		}

		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		check_dberror ( "Unable to load thread list." );
		$idstr = @join ( ",", $this->threadids );

		$this->favthread = array ();
		$this->thread_counts = array ();
		$this->messages = array ();
		$this->messages [0] = array ();
		$routerlist = array ();

		if (count ( $this->threadids ) > 0) {
			$query = "SELECT a.*, j.id AS userid, t.message AS messagetext, l.myfavorite, l.favcount, l.attachmesid,
			l.msgcount, l.lastid, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
	FROM (
		SELECT m.thread, MAX(f.userid IS NOT null AND f.userid='{$this->my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachmesid,
			COUNT(DISTINCT m.id) AS msgcount, MAX(m.id) AS lastid, MAX(m.time) AS lasttime
		FROM #__fb_messages AS m
		LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread
		LEFT JOIN #__fb_attachments AS a ON a.mesid = m.thread
		WHERE m.hold='0' AND m.moved='0' AND m.thread IN ({$idstr})
		GROUP BY thread
	) AS l
	INNER JOIN #__fb_messages AS a ON a.thread = l.thread
	INNER JOIN #__fb_messages_text AS t ON a.thread = t.mesid
	LEFT JOIN #__users AS j ON j.id = a.userid
	LEFT JOIN #__fb_users AS u ON u.userid = j.id
	LEFT JOIN #__fb_categories AS c ON c.id = a.catid
	WHERE (a.parent='0' OR a.id=l.lastid)
	ORDER BY {$order}";

			$this->db->setQuery ( $query );
			$messagelist = $this->db->loadObjectList ();
			check_dberror ( "Unable to load messages." );
			foreach ( $messagelist as $message ) {
				$this->messages [$message->parent] [] = $message;
				$this->messagetext [$message->id] = JString::substr ( smile::purify ( $message->messagetext ), 0, 500 );
				if ($message->parent == 0) {
					$this->hits [$message->id] = $message->hits;
					$this->thread_counts [$message->id] = $message->msgcount - 1;
					$this->last_read [$message->id]->unread = 0;
					if ($message->favcount)
						$this->favthread [$message->id] = $message->favcount;
					if ($message->id == $message->lastid)
						$this->last_read [$message->id]->lastread = $this->last_reply [$message->id] = $message;
					$routerlist [$message->id] = $message->subject;
				} else {
					$this->last_read [$message->thread]->lastread = $this->last_reply [$message->thread] = $message;
				}
			}
			include_once (KUNENA_PATH . DS . 'router.php');
			KunenaRouter::loadMessages ( $routerlist );

			$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages " . "WHERE hold='0' AND moved='0' AND thread IN ({$idstr}) AND time>'{$this->prevCheck}' GROUP BY thread" );
			$msgidlist = $this->db->loadObjectList ();
			check_dberror ( "Unable to get unread messages count and first id." );

			foreach ( $msgidlist as $msgid ) {
				if (! in_array ( $msgid->thread, $this->read_topics ))
					$this->last_read [$msgid->thread] = $msgid;
			}
		}

		$this->show_list_time = JRequest::getInt ( 'sel', 720 );
	}

	function displayPathway() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'pathway.php' )) {
			require_once (KUNENA_ABSTMPLTPATH . DS . 'pathway.php');
		} else {
			require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'pathway.php');
		}
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'announcement' . DS . 'announcementbox.php' )) {
				require_once (KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'announcement' . DS . 'announcementbox.php');
			} else {
				require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin' . DS . 'announcement' . DS . 'announcementbox.php');
			}
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump)
			require_once (KUNENA_PATH_LIB . DS . 'kunena.forumjump.php');
	}

	function displayFlat() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'threads' . DS . 'flat.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'threads' . DS . 'flat.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'threads' . DS . 'flat.php');
		}
	}

	function displayStats() {
		if ($this->config->showstats > 0) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'stats' . DS . 'stats.class.php' )) {
				include_once (KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'stats' . DS . 'stats.class.php');
			} else {
				include_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin' . DS . 'stats' . DS . 'stats.class.php');
			}

			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'who' . DS . 'whoisonline.php' )) {
				include (KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'who' . DS . 'whoisonline.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin' . DS . 'who' . DS . 'whoisonline.php');
			}
		}
	}

	function getPagination($func, $sel, $page, $totalpages, $maxpages) {
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<span class="kpagination">' . _PAGE;

		if (($startpage) > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= CKunenaLink::GetLatestPageLink ( $func, 1, 'follow', '', $sel );
			if (($startpage) > 2) {
				$output .= "...";
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= "<strong>$i</strong>";
			} else {
				$output .= CKunenaLink::GetLatestPageLink ( $func, $i, 'follow', '', $sel );
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= "...";
			}

			$output .= CKunenaLink::GetLatestPageLink ( $func, $totalpages, 'follow', '', $sel );
		}

		$output .= '</span>';
		return $output;
	}

	function display() {
		if (! $this->allow) {
			echo _KUNENA_NO_ACCESS;
			return;
		}
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'threads' . DS . 'latestx.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'threads' . DS . 'latestx.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'threads' . DS . 'latestx.php');
		}
	}
}
