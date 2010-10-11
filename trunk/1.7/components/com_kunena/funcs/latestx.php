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

class CKunenaLatestX {
	public $allow = 0;
	public $topics = array();
	public $page = 1;
	public $totalpages = 1;
	public $embedded = null;
	public $actionDropdown = array();
	public $actionMove = false;

	public $category = null;
	public $subcategories = null;

	function __construct($func, $page = 0) {
		kimport ('category');

		$this->func = JString::strtolower ($func );
		$this->catid = 0;
		$this->mode = 'threads';
		$this->header = '';

		$this->app = JFactory::getApplication ();
		$this->db = JFactory::getDBO ();
		$this->document = JFactory::getDocument ();
		$this->user = $this->my = JFactory::getUser ();
		$this->myprofile = KunenaFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = KunenaFactory::getConfig ();

		$this->latestcategory = explode ( ',', $this->config->latestcategory );
		$this->latestcategory_in = $this->config->latestcategory_in;

		$this->page = $page < 1 ? 1 : $page;
		$this->limit = $this->config->threads_per_page;
		$this->limitstart = ($this->page - 1) * $this->limit;

		$this->prevCheck = $this->session->lasttime;

		$this->show_list_time = JRequest::getInt ( 'sel', 720 );

		// My latest is only available to users
		if (! $this->user->id && $func == "mylatest") {
			return;
		}

		$this->allow = 1;
		$this->highlight = 0;

		if (! $this->my->id && $this->show_list_time == 0) {
			$this->show_list_time = "720";
		}

		//start the latest x
		if ($this->show_list_time == 0) {
			$this->querytime = $this->prevCheck - $this->config->fbsessiontimeout;
		} else {
			//Time translation
			$back_time = $this->show_list_time * 3600; //hours*(mins*secs)
			$this->querytime = time () - $back_time;
		}

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;

		$access = KunenaFactory::getAccessControl();
		$this->hold = $access->getAllowedHold($this->myprofile, $this->catid);

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->actionDropdown[] = JHTML::_('select.option', '', '&nbsp;');
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
		$this->totalpages = ceil ( $this->total / $this->limit );

		if (!empty ( $this->threadids ) ) {
			$idstr = implode ( ",", $this->threadids );

			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
			FROM #__kunena_topics AS tt
			LEFT JOIN #__kunena_user_topics AS ut ON ut.topic_id=tt.id AND ut.user_id={$this->my->id}
			WHERE tt.id IN ({$idstr})
			ORDER BY field(id, {$idstr})";

			$this->db->setQuery ( $query );
			$this->topics = $this->db->loadObjectList ('id');
			KunenaError::checkDatabaseError();
		}
		if (!empty($this->topics)) {
			// Collect user ids for avatar prefetch, topics for the router
			$routerlist = array();
			$userlist = array();
			foreach ( $this->topics as $topic ) {
				$routerlist [$topic->id] = $topic->subject;
				if ($this->func == 'mylatest' && $topic->favorite) $this->highlight++;
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
			}

			// Load threads to Kunena router to avoid extra SQL queries
			if (!empty($routerlist)) {
				include_once (KUNENA_PATH . DS . 'router.php');
				KunenaRouter::loadMessages ( $routerlist );
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUser::loadUsers($userlist);

			// Get unread messages from all topics
			if ($this->config->shownew && $this->my->id) {
				$readlist = $this->session->readtopics;
				$idstr = implode ( ",", array_keys($this->topics) );
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__kunena_messages
				WHERE hold IN ({$this->hold}) AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>{$this->db->Quote($this->prevCheck)}
				GROUP BY thread" );
				// TODO: check input
				$msgidlist = $this->db->loadObjectList ();
				KunenaError::checkDatabaseError();

				foreach ( $msgidlist as $msgid ) {
					$this->topics[$msgid->thread]->lastread = $msgid->lastread;
					$this->topics[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}
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
				$latestcats = ' AND tt.category_id IN ('.$catlist.') ';
			} else {
				$latestcats = ' AND tt.category_id NOT IN ('.$catlist.') ';
			}
		}
		return $latestcats;
	}

	protected function _getLatestTopics($lastpost = true, $where = '') {
		if (!$lastpost) {
			$xpost = 'tt.first_post';
		} else {
			$xpost = 'tt.last_post';
		}
		$latestcats = $this->_getCategoriesWhere();
		$wheretime = ($this->querytime ? " AND {$xpost}_time>{$this->db->Quote($this->querytime)}" : '');
		$where = "tt.hold IN ({$this->hold}) AND tt.moved_id=0 AND tt.category_id IN ({$this->session->allowed}) {$latestcats} {$wheretime} {$where}";
		$this->order = "{$xpost}_time DESC";

		// Get total count
		$query = "SELECT COUNT(*) FROM #__kunena_topics AS tt WHERE {$where}";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
			FROM #__kunena_topics AS tt
			LEFT JOIN #__kunena_user_topics AS ut ON tt.id=ut.topic_id AND ut.user_id={$this->db->Quote($this->my->id)}
			WHERE {$where} ORDER BY {$this->order}";
		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
		$this->topics = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$this->_common();
	}

	protected function _getMyLatestTopics($posts = true, $fav = true, $sub = false) {
		// Set where
		if (!$posts && !$fav && !$sub) {
			$where = 'ut.owner=1';
		} else {
			// Find user topics
			if ($posts) $where[] = 'ut.posts>0';
			if ($fav) $where[] = 'ut.favorite=1';
			if ($sub) $where[] = 'ut.subscribed=1';
			$where = implode(' OR ',$where);
		}
		$where = "ut.user_id={$this->db->Quote($this->user->id)} AND ({$where}) AND tt.moved_id='0'
			AND tt.hold IN ({$this->hold}) AND tt.category_id IN ({$this->session->allowed})";

		// Set order by
		switch ($this->func) {
			case 'mylatest':
				$this->order = "ut.favorite DESC, tt.last_post_time DESC";
				break;
			case 'usertopics':
				$this->order = "ut.last_post_time DESC";
				break;
			default:
				$this->order = "tt.last_post_time DESC";
		}

		// Get total count
		$query = "SELECT COUNT(*) FROM #__kunena_user_topics AS ut INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id WHERE {$where}";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		// Get topics
		$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
			FROM #__kunena_user_topics AS ut
			INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id
			WHERE {$where} ORDER BY {$this->order}";
		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
		$this->topics = $this->db->loadObjectList ();
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
				$user = 1;
				break;
			default:
				$latestcats = $this->_getCategoriesWhere();
				$wheretime = ($this->querytime ? " AND m.time>{$this->db->Quote($this->querytime)}" : '');
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

		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
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
		kimport('thankyou');

		$this->total = 10;//$limit default is on 10 TODO make adjustable
		$this->limit = 10;

		$idlist = KunenaThankYou::getThankYouPosts($this->user->id, $saidgot);

		$this->threadids = array();
		$this->loadids = array();
		foreach( $idlist as $item){
			$this->threadids[$item->thread] = $item->thread;
			$this->loadids[$item->id] = $item->id;
		}

		if($saidgot == 'got') {
			$this->header = $this->title = JText::_('COM_KUNENA_THANKYOU_GOT');
		} else {
			$this->header = $this->title = JText::_('COM_KUNENA_THANKYOU_SAID');
		}

		$this->order = 'field(a.id,'.implode ( ",", $this->loadids ).')';
		$this->_common();
	}

	function _getCategorySubscriptions() {
		$this->categories = array();
		if (isset($this->total)) return;

		$uname = $this->config->username ? 'name' : 'username';

		$query = "SELECT COUNT(*) FROM #__kunena_user_categories WHERE user_id={$this->db->Quote($this->user->id)} AND subscribed=1";
		$this->db->setQuery ( $query );
		$this->total = ( int ) $this->db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$query = "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$this->db->Quote($this->user->id)} AND subscribed=1";
		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
		$this->categories = KunenaCategory::getCategories($this->db->loadResultArray ());
		if (KunenaError::checkDatabaseError()) return;
	}

	function getUserPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_USERPOSTS');
		$this->_getPosts('user');
	}

	function getLatestPosts() {
		if (isset($this->total)) return;
		$this->header = $this->title = JText::_('COM_KUNENA_LATESTPOSTS');
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
		$this->_getMyLatestTopics(false, false, false);
	}

	function getUserTopics() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_USERTOPICS');
		$this->_getMyLatestTopics(true, false, false);
	}

	function getFavorites() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_FAVORITES');
		$this->actionDropdown[] = JHTML::_('select.option', 'bulkFavorite', JText::_('COM_KUNENA_DELETE_FAVORITE'));
		$this->_getMyLatestTopics(false, true, false);
	}

	function getSubscriptions() {
		if (isset($this->total)) return;
		$this->columns++;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_SUBSCRIPTIONS');
		$this->actionDropdown[] = JHTML::_('select.option', 'bulkSub', JText::_('COM_KUNENA_DELETE_SUBSCRIPTION'));
		$this->_getMyLatestTopics(false, false, true);
	}

	function getCategoriesSubscriptions() {
		if (isset($this->total)) return;
		$this->columns--;
		$this->showposts = 1;
		$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');
		$this->_getCategorySubscriptions();
	}

	function getmyLatest() {
		if (isset($this->total)) return;
		$this->header = JText::_('COM_KUNENA_MENU_MYLATEST_DESC');
		$this->title = JText::_('COM_KUNENA_MY_DISCUSSIONS');
		$this->_getMyLatestTopics();
	}

	function getLatestTopics() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->_getLatestTopics(false);
	}

	function getLatest() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->_getLatestTopics();
	}

	function getNoReplies() {
		if (isset($this->total)) return;
		$this->header =  JText::_('COM_KUNENA_MENU_NOREPLIES_DESC');
		$this->title = JText::_('COM_KUNENA_NO_REPLIES');
		$this->querytime = 0;
		$this->latestcategory = array(0);
		$this->_getLatestTopics(true, 'AND tt.posts=1');
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.announcement.class.php');
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
		if (empty ( $this->topics )) return;
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
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE'));
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
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
			$online =& CKunenaWhoIsOnline::getInstance();
			$online->displayWhoIsOnline();
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
		else if ($this->func == 'catsubscriptions') $this->getCategoriesSubscriptions();
		else if ($this->func == 'favorites') $this->getFavorites();
		else if ($this->func == 'userposts') $this->getUserPosts();
		else if ($this->func == 'saidthankyouposts') $this->getSaidThankYouPosts();
		else if ($this->func == 'gotthankyouposts') $this->getUserPosts();
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
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document = JFactory::getDocument ();
		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );

		$this->document->setTitle ( $this->title . ' - ' . $this->config->board_title );

		CKunenaTools::loadTemplate('/threads/latestx.php');
	}
}
