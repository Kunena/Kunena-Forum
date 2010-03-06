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

class CKunenaShowcat {
	public $allow = 0;

	function __construct($catid, $page=0) {
		$this->func = 'showcat';
		$this->catid = $catid;
		$this->page = $page;

		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->session = CKunenaSession::getInstance ();
		$this->config = CKunenaConfig::getInstance ();

		//get the allowed forums and turn it into an array
		$allow_forum = ($this->session->allowed != '') ? explode ( ',', $this->session->allowed ) : array ();

		if (! $this->catid)
			return;
		if (! in_array ( $this->catid, $allow_forum ))
			return;

		$this->allow = 1;

		$this->tabclass = array ("sectiontableentry1", "sectiontableentry2" );
		$this->prevCheck = $this->session->lasttime;

		$this->app = & JFactory::getApplication ();

		//Get the category information
		$query = "SELECT c.*, s.catid AS subscribeid
				FROM #__fb_categories AS c
				LEFT JOIN #__fb_subscriptions_categories AS s ON c.id = s.catid
				AND s.userid = '{$this->my->id}'
				WHERE c.id='{$this->catid}'";

		$this->db->setQuery ( $query );
		$this->objCatInfo = $this->db->loadObject ();
		check_dberror ( 'Unable to get categories.' );
		//Get the Category's parent category name for breadcrumb
		$this->db->setQuery ( "SELECT name, id FROM #__fb_categories WHERE id='{$this->objCatInfo->parent}'" );
		$objCatParentInfo = $this->db->loadObject ();
		check_dberror ( 'Unable to get parent category.' );

		//check if this forum is locked
		$this->kunena_forum_locked = $this->objCatInfo->locked;
		//check if this forum is subject to review
		$this->kunena_forum_reviewed = $this->objCatInfo->review;

		$threads_per_page = $this->config->threads_per_page;

		/*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
		$this->page = $this->page < 1 ? 1 : $this->page;
		$offset = ($this->page - 1) * $threads_per_page;
		$row_count = $this->page * $threads_per_page;
		$this->db->setQuery ( "SELECT COUNT(*) FROM #__fb_messages WHERE parent='0' AND catid='{$this->catid}' AND hold='0'" );
		$this->total = ( int ) $this->db->loadResult ();
		check_dberror ( 'Unable to get message count.' );
		$this->totalpages = ceil ( $this->total / $threads_per_page );

		$query = "SELECT t.id, MAX(m.id) AS lastid FROM #__fb_messages AS t
	INNER JOIN #__fb_messages AS m ON t.id = m.thread
	WHERE t.parent='0' AND t.hold='0' AND t.catid='{$this->catid}' AND m.hold='0' AND m.catid='{$this->catid}'
	GROUP BY m.thread ORDER BY t.ordering DESC, lastid DESC";
		$this->db->setQuery ( $query, $offset, $threads_per_page );
		$threadids = $this->db->loadResultArray ();
		check_dberror ( "Unable to load thread list." );
		$idstr = @join ( ",", $threadids );

		$this->messages = array ();
		$this->threads = array ();
		$this->highlight = 0;
		$routerlist = array ();
		if (count ( $threadids ) > 0) {
			$query = "SELECT a.*, j.id AS userid, t.message AS message, l.myfavorite, l.favcount, l.attachments,
							l.msgcount, l.lastid, l.lastid AS lastread, 0 AS unread, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
	FROM (
		SELECT m.thread, MAX(f.userid='{$this->my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachments,
			COUNT(DISTINCT m.id) AS msgcount, MAX(m.id) AS lastid, MAX(m.time) AS lasttime
		FROM #__fb_messages AS m";
			if ($this->config->allowfavorites) $query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread";
			else $query .= " LEFT JOIN (SELECT 0 AS userid, 0 AS myfavorite) AS f ON 1";
			$query .= "
		LEFT JOIN #__kunena_attachments AS a ON a.mesid = m.thread
		WHERE m.hold='0' AND m.thread IN ({$idstr})
		GROUP BY thread
	) AS l
	INNER JOIN #__fb_messages AS a ON a.thread = l.thread
	INNER JOIN #__fb_messages_text AS t ON a.thread = t.mesid
	LEFT JOIN #__users AS j ON j.id = a.userid
	LEFT JOIN #__fb_users AS u ON u.userid = j.id
	LEFT JOIN #__fb_categories AS c ON c.id = a.catid
	WHERE (a.parent='0' OR a.id=l.lastid)
	ORDER BY ordering DESC, lastid DESC";

			$this->db->setQuery ( $query );
			$this->messages = $this->db->loadObjectList ();
			check_dberror ( "Unable to load messages." );

			// collect user ids for avatar prefetch when integrated
			$userlist = array();

			foreach ( $this->messages as $message ) {
				if ($message->parent == 0) {
					$this->threads [$message->thread] = $message;
					$routerlist [$message->id] = $message->subject;
					if ($message->ordering) $this->highlight++;
				}
				if ($message->id == $message->lastid) {
					$this->lastreply [$message->thread] = $message;
				}
				$userlist[$message->userid] = $message->userid;
			}
			require_once (KUNENA_PATH . DS . 'router.php');
			KunenaRouter::loadMessages ( $routerlist );

			// If jomSocial integration for the avatar is turned on, prefetch all users
			// to avoid user by user queries during template iterations
			if ($this->config->avatar_src == "jomsocial") {
				CFactory::loadUsers($userlist);
			}

			if ($this->config->shownew && $this->my->id) {
				$readlist = '0' . $this->session->readtopics;
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages " . "WHERE hold='0' AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>'{$this->prevCheck}' GROUP BY thread" );
				$msgidlist = $this->db->loadObjectList ();
				check_dberror ( "Unable to get unread messages count and first id." );

				foreach ( $msgidlist as $msgid ) {
					$this->messages[$msgid->thread]->lastread = $msgid->lastread;
					$this->messages[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}

		//Perform subscriptions check
		$kunena_cansubscribecat = 0;
		if ($this->config->allowsubscriptions && ("" != $this->my->id || 0 != $this->my->id)) {
			if ($this->objCatInfo->subscribeid == '') {
				$kunena_cansubscribecat = 1;
			}
		}

		//meta description and keywords
		$metaKeys = kunena_htmlspecialchars ( stripslashes ( JText::_('COM_KUNENA_CATEGORIES') . ", {$objCatParentInfo->name}, {$this->objCatInfo->name}, {$this->config->board_title}, " . $this->app->getCfg ( 'sitename' ) ) );
		$metaDesc = kunena_htmlspecialchars ( stripslashes ( "{$objCatParentInfo->name} ({$this->page}/{$this->totalpages}) - {$this->objCatInfo->name} - {$this->config->board_title}" ) );

		$document = & JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document = & JFactory::getDocument ();
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		$this->headerdesc = CKunenaTools::parseBBCode ( $this->objCatInfo->headerdesc );

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || !$this->kunena_forum_locked) {
			//this user is allowed to post a new topic:
			$this->forum_new = CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}
		if ($this->my->id != 0) {
			$this->forum_markread = CKunenaLink::GetCategoryLink ( 'markThisRead', $this->catid, CKunenaTools::showButton ( 'markread', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD_LONG') );
		}

		// Thread Subscription
		if ($kunena_cansubscribecat == 1) {
			// this user is allowed to subscribe - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_subscribecat = CKunenaLink::GetCategoryLink ( 'subscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG') );
		}

		if ($this->my->id != 0 && $this->config->allowsubscriptions && $kunena_cansubscribecat == 0) {
			// this user is allowed to unsubscribe
			$this->thread_subscribecat = CKunenaLink::GetCategoryLink ( 'unsubscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG') );
		}
		//get the Moderator list for display
		$this->db->setQuery ( "SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$this->catid}'" );
		$this->modslist = $this->db->loadObjectList ();
		check_dberror ( "Unable to load moderators." );

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;
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

	function displaySubCategories() {
		require_once (KUNENA_PATH_FUNCS . DS . 'listcat.php');
		$obj = new CKunenaListCat($this->catid);
		$obj->loadCategories();
		if (!empty($obj->childforums)) $obj->displayCategories();
	}

	function displayFlat() {
		$this->header = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '.kunena_htmlspecialchars ( stripslashes ( $this->objCatInfo->name ) );
		CKunenaTools::loadTemplate('/threads/flat.php');
	}

	function displayStats() {
		if ($this->config->showstats > 0) {
			CKunenaTools::loadTemplate('/plugin/stats/stats.class.php');

			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			CKunenaTools::loadTemplate('/plugin/who/whoisonline.php');
		}
	}

	function getPagination($catid, $page, $totalpages, $maxpages) {
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
			$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, 1, 1, $rel = 'follow' ) . '</li>';
			if (($startpage) > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $i, $i, $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $totalpages, $totalpages, $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	function display() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		CKunenaTools::loadTemplate('/threads/showcat.php');
	}
}
