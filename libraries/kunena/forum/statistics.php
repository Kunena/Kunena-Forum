<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumStatistics
 */
class KunenaForumStatistics
{
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

	/**
	 * @var array|KunenaForumTopic[]
	 */
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

	/**
	 *
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO ();
		$this->_config = KunenaFactory::getConfig ();

		$show = $this->_config->showstats;
		$show = ($this->_config->statslink_allowed) ? $show : (KunenaUserHelper::get()->exists() ? $show : false);
		if ($show)
		{
			$this->showgenstats = (bool) $this->_config->showgenstats;
			$this->showpopuserstats = (bool) $this->_config->showpopuserstats;
			$this->showpopsubjectstats = (bool) $this->_config->showpopsubjectstats;
			$this->showpoppollstats = (bool) $this->_config->showpoppollstats;
			$this->showpopthankyoustats = (bool) $this->_config->showpopthankyoustats;
		}
	}

	/**
	 * @return KunenaForumStatistics
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
		{
			self::$_instance = new KunenaForumStatistics();
		}

		return self::$_instance;
	}

	/**
	 * @param bool $force
	 */
	public function loadAll($force = false)
	{
		$this->top = array();
		$this->loadGeneral($force);
		$this->loadTopicStats($force);
		$this->loadUserStats($force);
	}

	/**
	 * @param bool $force
	 */
	public function loadGeneral($force = false)
	{
		if (! $this->showgenstats && ! $force)
		{
			return;
		}

		$this->loadMemberCount();
		$this->loadLastUserId();
		$this->loadCategoryCount();
		$this->loadLastDays();
	}

	/**
	 * @param bool $override
	 */
	public function loadUserStats($override = false)
	{
		if ($this->showpopuserstats || $override)
		{
			$this->top[] = $this->loadTopPosters();

			if (!end($this->top))
			{
				array_pop($this->top);
			}

			$this->top[] = $this->loadTopProfiles();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}

		if ($this->showpopthankyoustats || $override) {
			$this->top[] = $this->loadTopThankyous();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}

	}

	/**
	 * @param bool $override
	 */
	public function loadTopicStats($override = false)
	{
		if ($this->showpopsubjectstats || $override)
		{
			$this->top[] = $this->loadTopTopics();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}

		if ($this->showpoppollstats || $override)
		{
			$this->top[] = $this->loadTopPolls();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}

	}

	public function loadLastUserId()
	{
		if ($this->lastUserId === null)
		{
			$this->lastUserId = KunenaUserHelper::getLastId();
		}
	}

	public function loadMemberCount()
	{
		if ($this->memberCount === null)
		{
			$this->memberCount = KunenaUserHelper::getTotalCount();
		}
	}

	public function loadCategoryCount()
	{
		if ($this->sectionCount === null)
		{
			$this->sectionCount = $this->categoryCount = 0;
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');

			foreach ($categories as $category)
			{
				if ($category->published != 1)
				{
					continue;
				}

				if ($category->isSection())
				{
					$this->sectionCount ++;
				}
				else {
					$this->categoryCount ++;
					$this->topicCount += $category->numTopics;
					$this->messageCount += $category->numPosts;
				}
			}
		}
	}

	public function loadLastDays()
	{
		if ($this->todayTopicCount === null)
		{
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

			if ($counts)
			{
				$this->todayTopicCount = (int) $counts->todayTopicCount;
				$this->todayReplyCount = (int) $counts->todayReplyCount;
				$this->yesterdayTopicCount = (int) $counts->yesterdayTopicCount;
				$this->yesterdayReplyCount = (int) $counts->yesterdayReplyCount;
			}
			else
			{
				$this->todayTopicCount = $this->todayReplyCount = $this->yesterdayTopicCount = $this->yesterdayReplyCount = 0;
			}
		}
	}

	/**
	 * @param int $limit
	 *
	 * @return array|KunenaForumTopic[]
	 */
	public function loadTopTopics($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popsubjectcount;

		if (count($this->topTopics) < $limit)
		{
			$params = array('orderby'=>'posts DESC');
			list($total, $this->topTopics) = KunenaForumTopicHelper::getLatestTopics(false, 0, $limit, $params);

			$top = reset($this->topTopics);

			if (!$top)
			{
				return array();
			}

			$top->title = JText::_('COM_KUNENA_LIB_STAT_TOP_TOPICS');
			$top->titleName = JText::_('COM_KUNENA_GEN_SUBJECT');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_POSTS');

			foreach ($this->topTopics as &$item)
			{
				$item = clone $item;
				$item->count = $item->posts;
				$item->link = JHtml::_('kunenaforum.link', $item->getUri(), KunenaHtmlParser::parseText ($item->subject));
				$item->percent = round(100 * $item->count / $top->posts);
			}
		}

		return array_slice($this->topTopics, 0, $limit);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function loadTopPosters($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popusercount;

		if (count($this->topPosters) < $limit)
		{
			$this->topPosters = KunenaUserHelper::getTopPosters($limit);

			$top = reset($this->topPosters);

			if (!$top)
			{
				return array();
			}

			$top->title = JText::_('COM_KUNENA_LIB_STAT_TOP_POSTERS');
			$top->titleName = JText::_('COM_KUNENA_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_POSTS');

			foreach ($this->topPosters as &$item)
			{
				$item = clone $item;
				$item->link = KunenaUserHelper::get($item->id)->getLink();
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topPosters, 0, $limit);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function loadTopProfiles($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popusercount;

		if (count($this->topProfiles) < $limit)
		{
			$this->topProfiles = KunenaFactory::getProfile()->getTopHits($limit);

			$top = reset($this->topProfiles);

			if (!$top)
			{
				return array();
			}

			$top->title = JText::_('COM_KUNENA_LIB_STAT_TOP_PROFILES');
			$top->titleName = JText::_('COM_KUNENA_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_HITS');

			foreach ($this->topProfiles as &$item)
			{
				$item = clone $item;
				$item->link = KunenaUserHelper::get($item->id)->getLink();
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topProfiles, 0, $limit);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function loadTopPolls($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->poppollscount;

		if (count($this->topPolls) < $limit)
		{
			$query = "SELECT poll.threadid AS id, SUM(opt.votes) AS count
					FROM #__kunena_polls_options AS opt
					INNER JOIN #__kunena_polls AS poll ON poll.id=opt.pollid
					GROUP BY pollid
					HAVING count > 0
					ORDER BY count DESC";
			$this->_db->setQuery($query, 0, $limit);
			$polls = (array) $this->_db->loadObjectList('id');
			KunenaError::checkDatabaseError();
			$this->topPolls = KunenaForumTopicHelper::getTopics(array_keys($polls));

			$top = reset($this->topPolls);

			if (!$top)
			{
				return array();
			}

			$top->title = JText::_('COM_KUNENA_LIB_STAT_TOP_POLLS');
			$top->titleName = JText::_('COM_KUNENA_POLL_STATS_NAME');
			$top->titleCount =  JText::_('COM_KUNENA_USRL_VOTES');
			$top->count = $polls[$top->id]->count;

			foreach ($this->topPolls as &$item)
			{
				$item = clone $item;
				$item->count = $polls[$item->id]->count;
				$item->link = JHtml::_('kunenaforum.link', $item->getUri(), KunenaHtmlParser::parseText ($item->subject));
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topPolls, 0, $limit);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function loadTopThankyous($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popthankscount;

		if (count($this->topThanks) < $limit)
		{
			$query = "SELECT t.targetuserid AS id, COUNT(t.targetuserid) AS count
				FROM `#__kunena_thankyou` AS t
				INNER JOIN `#__users` AS u ON u.id=t.targetuserid
				GROUP BY t.targetuserid
				ORDER BY count DESC";
			$this->_db->setQuery ( $query, 0, $limit );
			$this->topThanks = (array) $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();

			$top = reset($this->topThanks);

			if (!$top)
			{
				return array();
			}

			$top->title = JText::_('COM_KUNENA_LIB_STAT_TOP_THANKS');
			$top->titleName = JText::_('COM_KUNENA_USERNAME');
			$top->titleCount =  JText::_('COM_KUNENA_STAT_THANKS_YOU_RECEIVED');

			foreach ($this->topThanks as &$item)
			{
				$item = clone $item;
				$item->link = KunenaUserHelper::get($item->id)->getLink();
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topThanks, 0, $limit);
	}
}
