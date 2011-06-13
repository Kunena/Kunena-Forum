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
 **/
defined ( '_JEXEC' ) or die ();

class CKunenaShowcat {
	public $allow = 0;
	public $embedded = null;
	public $actionDropdown = array();

	function __construct($catid, $page=0) {
		kimport('html.parser');
		$this->func = 'showcat';
		$this->catid = $catid;
		$this->page = $page;
		$this->hasSubCats = '';

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->myprofile = KunenaFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = KunenaFactory::getConfig ();

		if (! $this->catid)
			return;
		if (! $this->session->canRead ( $this->catid ))
			return;

		$this->allow = 1;

		$this->tabclass = array ("row1", "row2" );
		$this->prevCheck = $this->session->lasttime;

		$this->app = & JFactory::getApplication ();

		//Get the category information
		$query = "SELECT c.*, s.catid AS subscribeid
				FROM #__kunena_categories AS c
				LEFT JOIN #__kunena_subscriptions_categories AS s ON c.id = s.catid
				AND s.userid = '{$this->my->id}'
				WHERE c.id={$this->db->Quote($this->catid)}";

		$this->db->setQuery ( $query );
		$this->objCatInfo = $this->db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;
		//Get the Category's parent category name for breadcrumb
		$this->db->setQuery ( "SELECT name, id FROM #__kunena_categories WHERE id={$this->db->Quote($this->objCatInfo->parent)}" );
		$objCatParentInfo = $this->db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;

		//check if this forum is locked
		$this->kunena_forum_locked = $this->objCatInfo->locked;
		//check if this forum is subject to review
		$this->kunena_forum_reviewed = $this->objCatInfo->review;

		$threads_per_page = $this->config->threads_per_page;

		$access = KunenaFactory::getAccessControl();
		$hold = $access->getAllowedHold($this->myprofile, $this->catid);

		/*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
		$this->page = $this->page < 1 ? 1 : $this->page;
		$offset = ($this->page - 1) * $threads_per_page;
		$row_count = $this->page * $threads_per_page;
		$this->db->setQuery ( "SELECT COUNT(*) FROM #__kunena_messages WHERE parent='0' AND catid={$this->db->Quote($this->catid)} AND hold IN ({$hold})" );
		$this->total = ( int ) $this->db->loadResult ();
		KunenaError::checkDatabaseError();
		$this->totalpages = ceil ( $this->total / $threads_per_page );

		$query = "SELECT t.id, MAX(m.id) AS lastid FROM #__kunena_messages AS t
	INNER JOIN #__kunena_messages AS m ON t.id = m.thread
	WHERE t.parent='0' AND t.hold IN ({$hold}) AND t.catid={$this->db->Quote($this->catid)} AND m.hold IN ({$hold}) AND m.catid={$this->db->Quote($this->catid)}
	GROUP BY m.thread ORDER BY t.ordering DESC, lastid DESC";
		$this->db->setQuery ( $query, $offset, $threads_per_page );
		$threadids = $this->db->loadResultArray ();
		KunenaError::checkDatabaseError();
		$idstr = implode ( ",", $threadids );

		$this->messages = array ();
		$this->threads = array ();
		$this->highlight = 0;
		$routerlist = array ();
		if (count ( $threadids ) > 0) {
			$query = "SELECT a.*, j.id AS userid, t.message AS message, l.myfavorite, l.favcount, l.attachments,
							l.msgcount, l.lastid, l.lastid AS lastread, 0 AS unread, j.username, j.name AS uname, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
	FROM (
		SELECT m.thread, MAX(f.userid='{$this->my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachments,
			COUNT(DISTINCT m.id) AS msgcount, MAX(m.id) AS lastid, MAX(m.time) AS lasttime
		FROM #__kunena_messages AS m";
			if ($this->config->allowfavorites) $query .= " LEFT JOIN #__kunena_favorites AS f ON f.thread = m.thread";
			else $query .= " LEFT JOIN #__kunena_favorites AS f ON f.thread = 0";
			$query .= "
		LEFT JOIN #__kunena_attachments AS a ON a.mesid = m.thread
		WHERE m.hold IN ({$hold}) AND m.thread IN ({$idstr})
		GROUP BY thread
	) AS l
	INNER JOIN #__kunena_messages AS a ON a.thread = l.thread
	INNER JOIN #__kunena_messages_text AS t ON a.thread = t.mesid
	LEFT JOIN #__users AS j ON j.id = a.userid
	LEFT JOIN #__kunena_users AS u ON u.userid = j.id
	LEFT JOIN #__kunena_categories AS c ON c.id = a.catid
	WHERE (a.parent='0' OR a.id=l.lastid)
	ORDER BY ordering DESC, lastid DESC";

			$this->db->setQuery ( $query );
			$this->messages = $this->db->loadObjectList ('id');
			KunenaError::checkDatabaseError();

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
				$userlist[intval($message->userid)] = intval($message->userid);
				$userlist[intval($message->modified_by)] = intval($message->modified_by);
			}
			require_once (KUNENA_PATH . '/router.php');
			KunenaRouter::loadMessages ( $routerlist );

			if ($this->config->shownew && $this->my->id) {
				$readlist = $this->session->readtopics;
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__kunena_messages " . "WHERE hold IN ({$hold}) AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>{$this->db->Quote($this->prevCheck)} GROUP BY thread" );
				$msgidlist = $this->db->loadObjectList ();
				KunenaError::checkDatabaseError();

				foreach ( $msgidlist as $msgid ) {
					$this->messages[$msgid->thread]->lastread = $msgid->lastread;
					$this->messages[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}

		//Perform subscriptions check
		$kunena_cansubscribecat = 0;
		if ($this->config->allowsubscriptions && $this->config->category_subscriptions != 'disabled' && ("" != $this->my->id || 0 != $this->my->id)) {
			if ($this->objCatInfo->subscribeid == '') {
				$kunena_cansubscribecat = 1;
			}
		}

		//meta description and keywords
		$metaKeys = kunena_htmlspecialchars ( JText::_('COM_KUNENA_CATEGORIES') . ", {$objCatParentInfo->name}, {$this->objCatInfo->name}, {$this->config->board_title}, " . $this->app->getCfg ( 'sitename' ) );
		$metaDesc = kunena_htmlspecialchars ( "{$objCatParentInfo->name} ({$this->page}/{$this->totalpages}) - {$this->objCatInfo->name} - {$this->config->board_title}" );

		$document = & JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document = & JFactory::getDocument ();
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		$this->headerdesc = $this->objCatInfo->headerdesc;

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || !$this->kunena_forum_locked) {
			//this user is allowed to post a new topic:
			$this->forum_new = CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}
		if ($this->my->id != 0 && $this->total) {
			$this->forum_markread = CKunenaLink::GetCategoryActionLink ( 'markthisread', $this->catid, CKunenaTools::showButton ( 'markread', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD_LONG') );
		}

		// Thread Subscription
		if ($kunena_cansubscribecat == 1) {
			// this user is allowed to subscribe - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_subscribecat = CKunenaLink::GetCategoryActionLink ( 'subscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG') );
		}

		if ($this->my->id != 0 && $this->config->allowsubscriptions && $this->config->category_subscriptions != 'disabled' && $kunena_cansubscribecat == 0) {
			// this user is allowed to unsubscribe
			$this->thread_subscribecat = CKunenaLink::GetCategoryActionLink ( 'unsubscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG') );
		}
		//get the Moderator list for display
		$this->db->setQuery ( "SELECT * FROM #__kunena_moderation AS m INNER JOIN #__users AS u ON u.id=m.userid WHERE m.catid={$this->db->Quote($this->catid)} AND u.block=0" );
		$this->modslist = $this->db->loadObjectList ();
		KunenaError::checkDatabaseError();
		foreach ($this->modslist as $mod) {
			$userlist[intval($mod->userid)] = intval($mod->userid);
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		if ( !empty($userlist) ) KunenaUser::loadUsers($userlist);

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;

		$this->actionDropdown[] = JHTML::_('select.option', '', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
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

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB . '/kunena.announcement.class.php');
			$ann = new CKunenaAnnouncement();
			$ann->getAnnouncement();
			$ann->displayBox();
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displaySubCategories() {
		require_once (KUNENA_PATH_FUNCS . '/listcat.php');
		$obj = new CKunenaListCat($this->catid);
		$obj->loadCategories();
		if (!empty($obj->categories [$this->catid])) {
			$obj->displayCategories();
			$this->hasSubCats = '1';
		}
	}

	function displayFlat() {
		$this->header = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->escape( $this->objCatInfo->name );
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDel', JText::_('COM_KUNENA_DELETE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkMove', JText::_('COM_KUNENA_MOVE_SELECTED'));
			if (($this->config->mod_see_deleted == '1' && CKunenaTools::isModerator()) || ($this->config->mod_see_deleted == '0' && CKunenaTools::isAdmin())) {
				$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
				$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
			}
		}
		if ($this->myprofile->ordering != '0') {
			$this->topic_ordering = $this->myprofile->ordering == '1' ? 'DESC' : 'ASC';
		} else {
			$this->topic_ordering = $this->config->default_sort == 'asc' ? 'ASC' : 'DESC'; // Just to make sure only valid options make it
		}

		CKunenaTools::loadTemplate('/threads/flat.php');
	}

	function displayStats() {
		if ($this->config->showstats > 0) {
			require_once(KUNENA_PATH_LIB . '/kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
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
