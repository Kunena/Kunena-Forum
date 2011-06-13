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

require_once (KUNENA_PATH_LIB . '/kunena.smile.class.php');

class CKunenaLatestX {
	public $allow = 0;
	public $messages = array();
	public $threads = array();
	public $lastreply = array ();
	public $customreply = array ();
	public $page = 1;
	public $totalpages = 1;
	public $embedded = null;
	public $actionDropdown = array();
	public $actionMove = false;

	function __construct($func, $page = 0) {
		$this->func = JString::strtolower ($func );
		$this->userid = JRequest::getInt ( 'userid' );
		if (!$this->userid) $this->userid = null;
		$this->catid = 0;
		$this->hasSubCats = '';
		$this->mode = 'threads';
		$this->header = '';

		$this->db = JFactory::getDBO ();
		$this->user = JFactory::getUser ( $this->userid );
		$this->my = JFactory::getUser ();
		$this->myprofile = KunenaFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = KunenaFactory::getConfig ();

		$this->latestcategory = explode ( ',', $this->config->latestcategory );
		$this->latestcategory_in = $this->config->latestcategory_in;
		$this->page = $page < 1 ? 1 : $page;
		$this->threads_per_page = $this->config->threads_per_page;
		$this->offset = ($this->page - 1) * $this->threads_per_page;

		$this->prevCheck = $this->session->lasttime;

		$this->app = & JFactory::getApplication ();
		$this->document = & JFactory::getDocument ();

		$this->show_list_time = JRequest::getInt ( 'sel', $this->config->show_list_time );

		$this->allow = 1;
		$this->highlight = 0;

		$this->tabclass = array ("row1", "row2" );

		if (! $this->my->id && $this->show_list_time == 0) {
			$this->show_list_time = $this->config->show_list_time;
		}

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;

		$access = KunenaFactory::getAccessControl();
		$this->hold = $access->getAllowedHold($this->myprofile, $this->catid);

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

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

	protected function _common() {
		$this->totalpages = ceil ( $this->total / $this->threads_per_page );

		if (!empty ( $this->threadids ) ) {
			$idstr = implode ( ",", $this->threadids );
			if (empty($this->loadids)) $loadstr = '';
			else $loadstr = 'OR a.id IN ('.implode ( ",", $this->loadids ).')';

			$query = "SELECT a.*, j.id AS userid, t.message, l.myfavorite, l.favcount, l.threadhits, l.lasttime, l.threadattachments, COUNT(aa.id) AS attachments,
				l.msgcount, l.mycount, l.lastid, l.mylastid, l.lastid AS lastread, 0 AS unread, u.avatar, j.username, j.name AS uname, c.name AS catname, c.class_sfx
			FROM (
				SELECT m.thread, MAX(m.hits) AS threadhits, MAX(f.userid IS NOT null AND f.userid={$this->db->Quote($this->my->id)}) AS myfavorite, COUNT(DISTINCT f.userid) AS favcount,
					COUNT(DISTINCT a.id) AS threadattachments, COUNT(DISTINCT m.id) AS msgcount, COUNT(DISTINCT IF(m.userid={$this->db->Quote($this->user->id)}, m.id, NULL)) AS mycount,
					MAX(m.id) AS lastid, MAX(IF(m.userid={$this->db->Quote($this->user->id)}, m.id, 0)) AS mylastid, MAX(m.time) AS lasttime
				FROM #__kunena_messages AS m";
				if ($this->config->allowfavorites) $query .= " LEFT JOIN #__kunena_favorites AS f ON f.thread = m.thread";
				else $query .= " LEFT JOIN #__kunena_favorites AS f ON f.thread = 0";
				$query .= "
				LEFT JOIN #__kunena_attachments AS a ON a.mesid = m.id
				WHERE m.hold IN ({$this->hold}) AND m.moved='0' AND m.thread IN ({$idstr})
				GROUP BY thread
			) AS l
			INNER JOIN #__kunena_messages AS a ON a.thread = l.thread
			INNER JOIN #__kunena_messages_text AS t ON a.id = t.mesid
			LEFT JOIN #__users AS j ON j.id = a.userid
			LEFT JOIN #__kunena_users AS u ON u.userid = j.id
			LEFT JOIN #__kunena_categories AS c ON c.id = a.catid
			LEFT JOIN #__kunena_attachments AS aa ON aa.mesid = a.id
			WHERE (a.parent='0' OR a.id=l.lastid $loadstr)
			GROUP BY a.id
			ORDER BY {$this->order}";

			$this->db->setQuery ( $query );
			$this->messages = $this->db->loadObjectList ('id');
			KunenaError::checkDatabaseError();
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
				$userlist[intval($message->userid)] = intval($message->userid);
				$userlist[intval($message->modified_by)] = intval($message->modified_by);
			}

			// Load threads to Kunena router to avoid extra SQL queries
			if (!empty($routerlist)) {
				include_once (KUNENA_PATH . '/router.php');
				KunenaRouter::loadMessages ( $routerlist );
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUser::loadUsers($userlist);

			if ($this->config->shownew && $this->my->id) {
				$readlist = $this->session->readtopics;
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__kunena_messages " . "WHERE hold IN ({$this->hold}) AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>{$this->db->Quote($this->prevCheck)} GROUP BY thread" ); // TODO: check input
				$msgidlist = $this->db->loadObjectList ();
				KunenaError::checkDatabaseError();

				foreach ( $msgidlist as $msgid ) {
					$this->messages[$msgid->thread]->lastread = $msgid->lastread;
					$this->messages[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}
	}

	protected function _getMyLatest($posts = true, $fav = true, $sub = false) {
		// Only available to users
		if (! $this->user->id) {
			return;
		}

		$subquery = array();
		if (!$posts && !$fav && !$sub) $subquery[] = "SELECT thread, 0 AS fav, 0 AS sub
			FROM #__kunena_messages
			WHERE userid='{$this->user->id}' AND parent='0' AND moved='0' AND hold IN ({$this->hold}) AND catid IN ({$this->session->allowed})";
		if ($posts) $subquery[] = "SELECT thread, 0 AS fav, 0 AS sub
			FROM #__kunena_messages
			WHERE userid={$this->db->Quote($this->user->id)} AND moved='0' AND hold IN ({$this->hold}) AND catid IN ({$this->session->allowed})
			GROUP BY thread";
		if ($fav) $subquery[] = "SELECT thread, 1 AS fav, 0 AS sub FROM #__kunena_favorites WHERE userid={$this->db->Quote($this->user->id)}";
		if ($sub) $subquery[] = "SELECT thread, 0 AS fav, 1 AS sub FROM #__kunena_subscriptions WHERE userid={$this->db->Quote($this->user->id)}";
		if (empty($subquery)) return;
		$subqueries = implode("\n	UNION ALL \n", $subquery);

		$query = "SELECT COUNT(DISTINCT m.thread) FROM ({$subqueries}) AS t
		INNER JOIN #__kunena_messages AS m ON m.thread=t.thread
		WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		if ($this->func == 'mylatest') $this->order = "myfavorite DESC, lastid DESC";
		else if ($this->func == 'usertopics') $this->order = "mylastid DESC";
		else $this->order = "lastid DESC";

		$query = "SELECT m.thread, MAX(m.id) AS lastid, MAX(IF(m.userid={$this->db->Quote($this->user->id)}, m.id, 0)) AS mylastid, MAX(t.fav) AS myfavorite, MAX(t.sub) AS mysubscribe
		FROM ({$subqueries}) AS t
		INNER JOIN #__kunena_messages AS m ON m.thread=t.thread
		WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
		GROUP BY m.thread
		ORDER BY {$this->order}";

		$this->db->setQuery ( $query, $this->offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		if (KunenaError::checkDatabaseError()) return;

		$this->_common();
	}

	protected function _getPosts($type = 'user') {
		if (isset($this->total)) return;
		$this->mode = 'posts';

		$myhold = explode(',', $this->hold);
		$hold = $this->hold;
		$latestcats = '';
		$wheretime = '';
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
				// Only available to users
				if (! $this->user->id) {
					return;
				}

				$user = 1;
				break;
			default:
				$latestcats = $this->_getCategoriesWhere();
				$this->mode = 'latestposts';
				$wheretime = ($this->_getShowListTime() ? " AND m.time>{$this->db->Quote($this->_getShowListTime())}" : '');
				break;
		}
		if (isset($user)) $where[] = "m.userid='{$this->user->id}'";
		$where[] = "mm.moved='0'";
		$where[] = "m.moved='0'";
		$where[] = "mm.hold IN ({$this->hold})";
		$where[] = "m.hold IN ({$hold})";
		$where[] = "mm.catid IN ({$this->session->allowed})";
		$where = implode(' AND ', $where);

		$query = "SELECT COUNT(*) FROM #__kunena_messages AS m
		INNER JOIN #__kunena_messages AS mm ON m.thread = mm.id
		WHERE {$where} {$latestcats} {$wheretime}";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$query = "SELECT m.thread, m.id
		FROM #__kunena_messages AS m
		INNER JOIN #__kunena_messages AS mm ON m.thread = mm.id
		WHERE {$where} {$latestcats} {$wheretime}
		ORDER BY m.time DESC";

		$this->db->setQuery ( $query, $this->offset, $this->threads_per_page );
		$idlist = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$this->threadids = array();
		$this->loadids = array();
		foreach ( $idlist as $item ) {
			$this->threadids[$item->thread] = $item->thread;
			$this->loadids[$item->id] = $item->id;
		}

		$this->order = 'field(a.id,'.implode ( ",", $this->loadids ).')';
		$this->_common();
	}

	function _getThankYouPosts($saidgot){
		if($saidgot == 'got') {
			$this->header = $this->title = JText::_('COM_KUNENA_THANKYOU_GOT');
		} else {
			$this->header = $this->title = JText::_('COM_KUNENA_THANKYOU_SAID');
		}

		// Only available to users
		if (! $this->user->id) {
			return;
		}

		kimport('thankyou');

		$this->total = 10;//$limit default is on 10 TODO make adjustable
		if (empty($this->threads_per_page)) $this->threads_per_page = 10;

		$idlist = KunenaThankYou::getThankYouPosts($this->user->id, $saidgot, $this->threads_per_page);

		$this->threadids = array();
		$this->loadids = array();
		foreach( $idlist as $item){
			$this->threadids[$item->thread] = $item->thread;
			$this->loadids[$item->id] = $item->id;
		}

		$this->order = 'field(a.id,'.implode ( ",", $this->loadids ).')';
		$this->_common();
	}

	function _getSubCategories() {
		// Only available to users
		if (! $this->user->id) {
			return;
		}

		$this->categories = array();
		if (isset($this->total)) return;

		$uname = $this->config->username ? 'name' : 'username';

		$query = "SELECT COUNT(DISTINCT c.id) FROM #__kunena_subscriptions_categories AS t
		INNER JOIN #__kunena_categories AS c ON c.id=t.catid WHERE t.userid={$this->db->Quote($this->user->id)}";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$this->totalpages = ceil ( $this->total / $this->threads_per_page );

		$query = "SELECT j.id AS userid, j.{$uname} AS uname, cat.*, cat.id AS catid, cat.name AS catname,
			0 AS fav, 1 AS sub, msg.thread, msg.id AS msgid, msg.subject,msg.time, COUNT(mmm.id) AS msgcount
		FROM #__kunena_subscriptions_categories AS t
		INNER JOIN #__kunena_categories AS cat ON cat.id=t.catid
		LEFT JOIN #__kunena_messages AS msg ON cat.id_last_msg=msg.id
		LEFT JOIN #__kunena_messages AS mmm ON msg.thread=mmm.thread AND mmm.hold=0
		LEFT JOIN #__users AS j ON j.id = t.userid
		WHERE t.userid={$this->db->Quote($this->user->id)}
		GROUP BY t.catid
		ORDER BY ordering";
		$this->db->setQuery ( $query, $this->offset, $this->threads_per_page );
		$this->categories = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;
	}

	function getUserPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_USERPOSTS');
		$this->_getPosts('user');
	}

	function getLatestPosts() {
		if (isset($this->total)) return;
		$this->header = JText::_('COM_KUNENA_LATESTPOSTS');
		$this->title = JText::_('COM_KUNENA_MY_DISCUSSIONS');
		$this->_getPosts('latest');
	}

	function getGotThankYouPosts() {
		$this->_getThankYouPosts('got');
	}

	function getSaidThankYouPosts(){
		$this->_getThankYouPosts('said');
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
		$this->actionDropdown[] = JHTML::_('select.option', 'bulkFavorite', JText::_('COM_KUNENA_DELETE_FAVORITE'));
		$this->_getMyLatest(false, true, false);
	}

	function getSubscriptions() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_SUBSCRIPTIONS');
		$this->actionDropdown[] = JHTML::_('select.option', 'bulkSub', JText::_('COM_KUNENA_DELETE_SUBSCRIPTION'));
		$this->_getMyLatest(false, false, true);
	}

	function getCategoriesSubscriptions() {
		if (isset($this->total)) return;
		$this->columns--;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');
		$this->_getSubCategories();
	}

	function getmyLatest() {
		if (isset($this->total)) return;
		$this->header = JText::_('COM_KUNENA_MENU_MYLATEST_DESC');
		$this->title = JText::_('COM_KUNENA_MY_DISCUSSIONS');
		$this->_getMyLatest();
	}

	protected function _getCategoriesWhere() {
		$catlist = array ();
		// need this becuse with kunena latest module when there is only one item selected in the list, it isn't saved in array
		if(!is_array($this->latestcategory)) $this->latestcategory = array($this->latestcategory);
		foreach ( $this->latestcategory as $catnum ) {
			$catlist [] = ( int ) $catnum;
		}
		$latestcats = '';
		if ( !empty($catlist) && !in_array(0, $catlist)) {
			$catlist = implode ( ',', $catlist );
			if ( $this->latestcategory_in == '1' ) {
				$latestcats = ' AND m.catid IN ('.$catlist.') ';
			} else {
				$latestcats = ' AND m.catid NOT IN ('.$catlist.') ';
			}
		}
		return $latestcats;
	}
	function getLatestTopics() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');

		$latestcats = $this->_getCategoriesWhere();
		$wheretime = ($this->_getShowListTime() ? " AND m.time>{$this->db->Quote($this->_getShowListTime())}" : '');

		$query = "SELECT COUNT(m.thread) FROM #__kunena_messages AS m
			WHERE m.hold IN ({$this->hold}) AND m.moved=0 AND m.catid IN ({$this->session->allowed}) {$latestcats} {$wheretime}";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;
		$offset = ($this->page - 1) * $this->threads_per_page;

		$this->order = "time DESC";
		$query = "SELECT id FROM #__kunena_messages AS m
			WHERE m.hold IN ({$this->hold}) AND m.moved='0' AND m.catid IN ({$this->session->allowed}) {$latestcats} {$wheretime}
			ORDER BY {$this->order}";
		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		if (KunenaError::checkDatabaseError()) return;

		$this->_common();
	}

	function getLatest() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');

		$latestcats = $this->_getCategoriesWhere();
		$wheretime = ($this->_getShowListTime() ? " AND t.time>{$this->db->Quote($this->_getShowListTime())}" : '');

		$query = "Select COUNT(DISTINCT t.thread) FROM #__kunena_messages AS t
			INNER JOIN #__kunena_messages AS m ON m.id=t.thread
			WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
			AND t.hold IN ({$this->hold}) AND t.moved=0 AND t.catid IN ({$this->session->allowed}) {$latestcats} {$wheretime}";


		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;
		$offset = ($this->page - 1) * $this->threads_per_page;

		$this->order = "lastid DESC";
		$query = "SELECT m.id, MAX(t.id) AS lastid FROM #__kunena_messages AS t
			INNER JOIN #__kunena_messages AS m ON m.id=t.thread
			WHERE m.moved='0' AND m.hold IN ({$this->hold}) AND m.catid IN ({$this->session->allowed})
			AND t.hold IN ({$this->hold}) AND t.moved='0' AND t.catid IN ({$this->session->allowed}) {$latestcats} {$wheretime}
			GROUP BY t.thread
			ORDER BY {$this->order}";
		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		if (KunenaError::checkDatabaseError()) return;

		$this->_common();
	}

	function getNoReplies() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_NOREPLIES_DESC');
		$this->title = JText::_('COM_KUNENA_NO_REPLIES');
		$query = "SELECT COUNT(DISTINCT tmp.thread) FROM
			(SELECT m.thread, count(*) AS posts
				FROM #__kunena_messages as m
				JOIN (SELECT thread
					FROM #__kunena_messages
					WHERE parent=0 AND hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
				) as t ON m.thread = t.thread
				WHERE m.hold=0 AND m.moved=0
				GROUP BY 1
			) AS tmp
			WHERE tmp.posts = 1";

		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;
		$offset = ($this->page - 1) * $this->threads_per_page;

		$this->order = "lastid DESC";
		$query = "SELECT thread, thread as lastid FROM
			(SELECT m.thread, count(*) AS posts
				FROM #__kunena_messages as m
				JOIN (SELECT thread
					FROM #__kunena_messages
					WHERE parent=0 AND hold=0 AND moved=0 AND catid IN ({$this->session->allowed})
				) as t ON m.thread = t.thread
				WHERE m.hold=0 AND m.moved=0
				GROUP BY 1
			) AS tmp
			WHERE tmp.posts = 1
			GROUP BY thread
			ORDER BY {$this->order}";

		$this->db->setQuery ( $query, $offset, $this->threads_per_page );
		$this->threadids = $this->db->loadResultArray ();
		if (KunenaError::checkDatabaseError()) return;

		$this->_common();
	}

	protected function _getShowListTime() {
		if ($this->show_list_time == 0) {
			$sincetime = ($this->prevCheck - $this->config->fbsessiontimeout); //move 30 minutes back to compensate for expired sessions
		} else {
			//Time translation
			$back_time = $this->show_list_time * 3600; //hours*(mins*secs)
			$sincetime = time () - $back_time;
		}

		return $sincetime;
  	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB .'/kunena.announcement.class.php');
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

	function displayItems() {
		if ($this->mode == 'threads') $this->displayFlat();
		else $this->displayPosts();
	}

	function displayFlat() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
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

	function displayFlatCats() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if ($this->myprofile->ordering != '0') {
			$this->topic_ordering = $this->myprofile->ordering == '1' ? 'DESC' : 'ASC';
		} else {
			$this->topic_ordering = $this->config->default_sort == 'asc' ? 'ASC' : 'DESC'; // Just to make sure only valid options make it
		}

		CKunenaTools::loadTemplate('/threads/flat_cats.php');
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
			require_once(KUNENA_PATH_LIB . '/kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once (KUNENA_PATH_LIB . '/kunena.who.class.php');
			$online =& CKunenaWhoIsOnline::getInstance();
			$online->displayWhoIsOnline();
		}
	}

	function getPagination($func, $sel, $page, $totalpages, $maxpages) {
		if ( $func != 'latest' ) $func = 'latest&do='.$func;
		if ( $this->userid ) $func .= '&userid='.$this->userid;
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
		else if ($this->func == 'latestposts') $this->getLatestPosts();
		else if ($this->func == 'latesttopics') $this->getLatestTopics();
		else if ($this->func == 'noreplies') $this->getNoReplies();
		else if ($this->func == 'subscriptions') $this->getSubscriptions();
		else if ($this->func == 'catsubscriptions') $this->getCategoriesSubscriptions();
		else if ($this->func == 'favorites') $this->getFavorites();
		else if ($this->func == 'userposts') $this->getUserPosts();
		else if ($this->func == 'owntopics') $this->getOwnTopics();
		else if ($this->func == 'saidthankyouposts') $this->getSaidThankYouPosts();
		else if ($this->func == 'gotthankyouposts') $this->getGotThankYouPosts();
		else if ($this->func == 'unapproved') $this->getUnapprovedPosts();
		else if ($this->func == 'deleted') $this->getDeletedPosts();
		else $this->getLatest();
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}

		//meta description and keywords
		$metaKeys = $this->header . kunena_htmlspecialchars ( ", {$this->config->board_title}, " ) . $this->app->getCfg ( 'sitename' );
		$metaDesc = $this->header . kunena_htmlspecialchars ( " ({$this->page}/{$this->totalpages}) - {$this->config->board_title}" );

		$cur = $this->document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$this->document = & JFactory::getDocument ();
		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );

		$this->document->setTitle ( $this->title . ' - ' . $this->config->board_title );

		if (!isset($this->total)) $this->total = 0;
		CKunenaTools::loadTemplate('/threads/latestx.php');
	}
}
