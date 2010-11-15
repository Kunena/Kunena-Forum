<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();

class CKunenaShowcat {
	public $allow = 0;
	public $embedded = null;
	public $actionDropdown = array();

	public $category = null;
	public $subcategories = null;

	function __construct($catid, $page=0) {
		kimport('kunena.html.parser');
		kimport('kunena.forum.category.helper');
		kimport('kunena.forum.topic.helper');
		kimport('kunena.user.helper');

		$this->func = 'showcat';
		$this->catid = intval($catid);
		$this->page = intval($page);

		$this->category = KunenaForumCategoryHelper::get($this->catid);
		if (! $this->category->exists())
			return;
		if (! $this->category->authorise('read'))
			return;

		$this->allow = 1;

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->app = JFactory::getApplication ();
		$this->db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->myprofile = KunenaFactory::getUser ();
		$this->session = KunenaFactory::getSession ();
		$this->config = KunenaFactory::getConfig ();

		$this->prevCheck = $this->session->lasttime;

		$access = KunenaFactory::getAccessControl();
		$hold = $access->getAllowedHold($this->myprofile, $this->catid);

		$this->page = $this->page < 1 ? 1 : $this->page;
		$limit = $this->config->threads_per_page;
		$limitstart = ($this->page - 1) * $limit;

		$params = array(
			'orderby'=>'tt.ordering DESC, tt.last_post_time DESC',
			'hold'=>$hold);

		list ($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($this->catid, $limitstart, $limit, $params);
		$this->totalpages = ceil ( $this->total / $limit );

		$this->highlight = 0;
		if ($this->total > 0) {
			// collect user ids for avatar prefetch when integrated
			$userlist = array();
			$routerlist = array ();
			foreach ( $this->topics as $topic ) {
				$routerlist [$topic->id] = $topic->subject;
				if ($topic->ordering) $this->highlight++;
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
			}
			require_once (KUNENA_PATH . DS . 'router.php');
			KunenaRouter::loadMessages ( $routerlist );

			if ($this->config->shownew && $this->my->id) {
				// TODO: Need to convert to topics table design
				$idstr = implode ( ",", array_keys($this->topics) );
				$readlist = $this->session->readtopics;
				$this->db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread
					FROM #__kunena_messages
					WHERE hold IN ({$hold}) AND moved='0' AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>{$this->db->Quote($this->prevCheck)}
					GROUP BY thread" );
				$msgidlist = $this->db->loadObjectList ();
				KunenaError::checkDatabaseError();

				foreach ( $msgidlist as $msgid ) {
					$this->messages[$msgid->thread]->lastread = $msgid->lastread;
					$this->messages[$msgid->thread]->unread = $msgid->unread;
				}
			}
		}

		// Get list of moderators
		$this->db->setQuery ( "SELECT * FROM #__kunena_moderation AS m
			INNER JOIN #__users AS u ON u.id=m.userid
			WHERE m.catid={$this->db->Quote($this->catid)} AND u.block=0" );
		$this->modslist = $this->db->loadObjectList ();
		KunenaError::checkDatabaseError();
		foreach ($this->modslist as $mod) {
			$userlist[intval($mod->userid)] = intval($mod->userid);
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

		//meta description and keywords
		$document = JFactory::getDocument ();
		$parentCategory = $this->category->getParent();
		$metaKeys = $this->escape ( JText::_('COM_KUNENA_CATEGORIES') . ", {$parentCategory->name}, {$this->category->name}, {$this->config->board_title}, " . $this->app->getCfg ( 'sitename' ) );
		$metaDesc = $document->get ( 'description' ) . '. ' . $this->escape ( "{$parentCategory->name} ({$this->page}/{$this->totalpages}) - {$this->category->name} - {$this->config->board_title}" );
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		$this->headerdesc = $this->category->headerdesc;

		// Is user allowed to post new topic?
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || !$this->category->locked) {
			$this->forum_new = CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		// Is user allowed to mark forums as read?
		if ($this->my->id && $this->total) {
			$this->forum_markread = CKunenaLink::GetCategoryActionLink ( 'markthisread', $this->catid, CKunenaTools::showButton ( 'markread', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD_LONG') );
		}

		// Is user allowed to subscribe category?
		if ($this->my->id && $this->config->allowsubscriptions) {
			$query = "SELECT subscribed
				FROM #__kunena_user_categories
				WHERE user_id={$this->db->Quote($this->my->id)} AND category_id={$this->db->Quote($this->catid)}";
			$this->db->setQuery ( $query );
			$subscribed = $this->db->loadResult ();
			if (KunenaError::checkDatabaseError()) return;

			if (!$subscribed) {
				$this->thread_subscribecat = CKunenaLink::GetCategoryActionLink ( 'subscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG') );
			} else {
				$this->thread_subscribecat = CKunenaLink::GetCategoryActionLink ( 'unsubscribecat', $this->catid, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG') );
			}
		}

		$this->columns = CKunenaTools::isModerator ( $this->my->id, $this->catid ) ? 6 : 5;
		$this->showposts = 0;

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

	function displaySubCategories() {
		require_once (KUNENA_PATH_FUNCS . DS . 'listcat.php');
		$obj = new CKunenaListCat($this->catid);
		$obj->loadCategories();
		if (!empty($obj->categories [$this->catid])) {
			$obj->displayCategories();
			$this->subcategories = true;
		}
	}

	function displayFlat() {
		$this->header = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->escape( $this->category->name );
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDel', JText::_('COM_KUNENA_DELETE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkMove', JText::_('COM_KUNENA_MOVE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
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
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
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
