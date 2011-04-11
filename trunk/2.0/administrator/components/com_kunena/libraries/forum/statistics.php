<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Statistics Class
 */
class KunenaForumStatistics {
	protected static $_instance = null;
	protected $_db = null;
	protected $_config = null;

	public $lastUserId = null;
	public $memberCount = null;
	public $sectionCount = null;
	public $categoryCount = null;
	public $topicCount = null;
	public $messageCount = null;
	public $todayTopicCount = null;
	public $yesterdayTopicCount = null;
	public $todayReplyCount = null;
	public $yesterdayReplyCount = null;

	public $topTopics = null;
	public $topPosters = null;
	public $topProfiles = null;
	public $topPolls = null;
	public $topThanks = null;
	public $top = array();

	public $showgenstats = false;
	public $showpopuserstats = false;
	public $showpopsubjectstats = false;
	public $showpoppollstats = false;
	public $showpopthankyoustats = false;

	public function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_config = KunenaFactory::getConfig ();

		$show = $this->_config->showstats;
		if ($show) {
			$this->showgenstats = (bool) $this->_config->showgenstats;
			$this->showpopuserstats = (bool) $this->_config->showpopuserstats;
			$this->showpopsubjectstats = (bool) $this->_config->showpopsubjectstats;
			$this->showpoppollstats = (bool) $this->_config->showpoppollstats;
			$this->showpopthankyoustats = (bool) $this->_config->showpopthankyoustats;
		}
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new KunenaForumStatistics();
		}
		return self::$_instance;
	}

	public function loadAll($force=false) {
		$this->top = array();
		$this->loadGeneral();
		$this->loadTopicStats();
		$this->loadUserStats();
	}

	public function loadGeneral($force=false) {
		if (! $this->showgenstats && ! $force)
			return;

		$this->loadMemberCount();
		$this->loadLastUserId();
		$this->loadCategoryCount();
		$this->loadLastDays();
	}

	public function loadUserStats($override=false) {
		if ($this->showpopuserstats || $override) {
			$this->top[] = $this->loadTopPosters();
			if (!end($this->top)) array_pop($this->top);
			$this->top[] = $this->loadTopProfiles();
			if (!end($this->top)) array_pop($this->top);
		}
		if ($this->showpopthankyoustats || $override) {
			$this->top[] = $this->loadTopThankyous();
			if (!end($this->top)) array_pop($this->top);
		}
	}

	public function loadTopicStats($override=false) {
		if ($this->showpopsubjectstats || $override) {
			$this->top[] = $this->loadTopTopics();
			if (!end($this->top)) array_pop($this->top);
		}
		if ($this->showpoppollstats || $override) {
			$this->top[] = $this->loadTopPolls();
			if (!end($this->top)) array_pop($this->top);
		}
	}

	public function loadLastUserId() {
		if ($this->lastUserId === null) {
			$this->lastUserId = KunenaUserHelper::getLastId();
		}
	}

	public function loadMemberCount() {
		if ($this->memberCount === null) {
			$this->memberCount = KunenaUserHelper::getTotalCount();
		}
	}

	public function loadCategoryCount() {
		if ($this->sectionCount === null) {
			$this->sectionCount = $this->categoryCount = 0;
			kimport('kunena.forum.category.helper');
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
			foreach ($categories as $category) {
				if (!$category->published) continue;
				if ($category->isSection())
					$this->sectionCount ++;
				else {
					$this->categoryCount ++;
					$this->topicCount += $category->numTopics;
					$this->messageCount += $category->numPosts;
				}
			}
		}
	}

	public function loadLastDays() {
		if ($this->todayTopicCount === null) {
			$todaystart = strtotime ( date ( 'Y-m-d' ) );
			$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
			$this->_db->setQuery ( "SELECT
				SUM(time>={$todaystart} AND parent=0) AS todayTopicCount,
				SUM(time>={$todaystart} AND parent>0) AS todayReplyCount,
				SUM(time>={$yesterdaystart} AND time<{$todaystart} AND parent=0) AS yesterdayTopicCount,
				SUM(time>={$yesterdaystart} AND time<{$todaystart} AND parent>0) AS yesterdayReplyCount
				FROM #__kunena_messages WHERE time>={$yesterdaystart} AND hold=0" );

			$counts = $this->_db->loadObject ();
			KunenaError::checkDatabaseError();
			if ($counts) {
				$this->todayTopicCount = (int) $counts->todayTopicCount;
				$this->todayReplyCount = (int) $counts->todayReplyCount;
				$this->yesterdayTopicCount = (int) $counts->yesterdayTopicCount;
				$this->yesterdayReplyCount = (int) $counts->yesterdayReplyCount;
			} else {
				$this->todayTopicCount = $this->todayReplyCount = $this->yesterdayTopicCount = $this->yesterdayReplyCount = 0;
			}
		}
	}

	public function loadTopTopics($limit=0) {
		$limit = $limit ? $limit : $this->_config->popsubjectcount;
		if (count($this->topTopics) < $limit) {
			$categories = implode(',', array_keys(KunenaForumCategoryHelper::getCategories()));
			$query = "SELECT id, category_id AS catid, subject, posts AS count
				FROM #__kunena_topics
				WHERE moved_id=0 AND hold=0 AND category_id IN ({$categories})
				ORDER BY count DESC";
			$this->_db->setQuery ( $query, 0, $limit );
			$this->topTopics = (array) $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$top = reset($this->topTopics);
			if (empty($top->count)) {
				$this->topTopics = array();
				return;
			}
			foreach ($this->topTopics as $item) {
				$item->link = CKunenaLink::GetThreadLink( 'view', $item->catid, $item->id, KunenaHtmlParser::parseText ($item->subject), '' );
				$item->percent = round(100 * $item->count / $top->count);
			}
			$top->title = JText::_('COM_KUNENA_STAT_TOP') .' '. $limit .' '. JText::_('COM_KUNENA_STAT_POPULAR') .' '. JText::_('COM_KUNENA_STAT_POPULAR_USER_KGSG');
			$top->titleName = JText::_('COM_KUNENA_GEN_SUBJECT');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_HITS');
		}
		return array_slice($this->topTopics, 0, $limit);
	}

	public function loadTopPosters($limit=0) {
		$limit = $limit ? $limit : $this->_config->popusercount;
		if (count($this->topPosters) < $limit) {
			$this->topPosters = KunenaUserHelper::getTopPosters($limit);
			$top = reset($this->topPosters);
			if (empty($top->count)) {
				$this->topPosters = array();
				return;
			}
			foreach ($this->topPosters as $item) {
				$item->link = CKunenaLink::GetProfileLink($item->id);
				$item->percent = round(100 * $item->count / $top->count);
			}
			$top->title = JText::_('COM_KUNENA_STAT_TOP') .' '. $limit .' '. JText::_('COM_KUNENA_STAT_POPULAR') .' '. JText::_('COM_KUNENA_STAT_POPULAR_USER_TMSG');
			$top->titleName = JText::_('COM_KUNENA_USRL_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_POSTS');
		}
		return array_slice($this->topPosters, 0, $limit);
	}

	public function loadTopProfiles($limit=0) {
		$limit = $limit ? $limit : $this->_config->popusercount;
		if (count($this->topProfiles) < $limit) {
			$this->topProfiles = KunenaFactory::getProfile()->getTopHits($limit);
			$top = reset($this->topProfiles);
			if (empty($top->count)) {
				$this->topProfiles = array();
				return;
			}
			foreach ($this->topProfiles as $item) {
				$item->link = CKunenaLink::GetProfileLink($item->id);
				$item->percent = round(100 * $item->count / $top->count);
			}
			$top->title = JText::_('COM_KUNENA_STAT_TOP') .' '. $limit .' '. JText::_('COM_KUNENA_STAT_POPULAR') .' '. JText::_('COM_KUNENA_STAT_POPULAR_USER_GSG');
			$top->titleName = JText::_('COM_KUNENA_USRL_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_HITS');
		}
		return array_slice($this->topProfiles, 0, $limit);
	}

	public function loadTopPolls($limit=0) {
		$limit = $limit ? $limit : $this->_config->poppollscount;
		if (count($this->topPolls) < $limit) {
			$query = "SELECT m.*, poll.*, SUM(opt.votes) AS count
					FROM #__kunena_polls_options AS opt
					INNER JOIN #__kunena_polls AS poll ON poll.id=opt.pollid
					LEFT JOIN #__kunena_messages AS m ON poll.threadid=m.id
					GROUP BY pollid
					ORDER BY count DESC";
			$this->_db->setQuery($query, 0, $limit);
			$this->topPolls = $this->_db->loadObjectList();
			KunenaError::checkDatabaseError();
			$top = reset($this->topPolls);
			if (empty($top->count)){
				$this->topPolls = array();
				return;
			}
			foreach ($this->topPolls as $item) {
				$item->link = CKunenaLink::GetThreadLink( 'view', $item->catid, $item->id, KunenaHtmlParser::parseText ($item->subject), '' );
				$item->percent = round(100 * $item->count / $top->count);
			}
			$top->title = JText::_('COM_KUNENA_STAT_TOP') .' '. $limit .' '. JText::_('COM_KUNENA_STAT_POPULAR') .' '. JText::_('COM_KUNENA_STAT_POPULAR_POLLS_KGSG');
			$top->titleName = JText::_('COM_KUNENA_POLL_STATS_NAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_VOTES');
		}
		return array_slice($this->topPolls, 0, $limit);
	}

	public function loadTopThankyous($limit=0) {
		$limit = $limit ? $limit : $this->_config->popthankscount;
		if (count($this->topThanks) < $limit) {
			$query = "SELECT targetuserid AS id, COUNT(targetuserid) AS count FROM `#__kunena_thankyou` GROUP BY targetuserid ORDER BY count DESC";
			$this->_db->setQuery ( $query, 0, $limit );
			$this->topThanks = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$top = reset($this->topThanks);
			if (empty($top->count)) {
				$this->topThanks = array();
				return;
			}
			foreach ($this->topThanks as $item) {
				$item->link = CKunenaLink::GetProfileLink($item->id);
				$item->percent = round(100 * $item->count / $top->count);
			}
			$top->title = JText::_('COM_KUNENA_STAT_TOP') .' '. $limit .' '. JText::_('COM_KUNENA_STAT_POPULAR') .' '. JText::_('COM_KUNENA_STAT_POPULAR_USER_THANKS_YOU');
			$top->titleName = JText::_('COM_KUNENA_USRL_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_STAT_THANKS_YOU_RECEIVED');
		}
		return array_slice($this->topThanks, 0, $limit);
	}
}