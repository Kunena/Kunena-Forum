<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Class KunenaForumStatistics
 * @since Kunena
 */
class KunenaForumStatistics
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected static $_instance = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $lastUserId = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $memberCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $sectionCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $categoryCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topicCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $messageCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $todayTopicCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $yesterdayTopicCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $todayReplyCount = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $yesterdayReplyCount = null;

	/**
	 * @var array|KunenaForumTopic[]
	 * @since Kunena
	 */
	public $topTopics = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topPosters = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topProfiles = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topPolls = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topThanks = null;

	/**
	 * @var array
	 * @since Kunena
	 */
	public $top = array();

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $showgenstats = true;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $showpopuserstats = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $showpopsubjectstats = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $showpoppollstats = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $showpopthankyoustats = false;

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * @var KunenaConfig|null
	 * @since Kunena
	 */
	protected $_config = null;

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->_db     = Factory::getDBO();
		$this->_config = KunenaFactory::getConfig();

		$this->showstats            = (bool) $this->_config->showstats;
		$this->showgenstats         = (bool) $this->_config->showgenstats;
		$this->showpopuserstats     = (bool) $this->_config->showpopuserstats;
		$this->showpopsubjectstats  = (bool) $this->_config->showpopsubjectstats;
		$this->showpoppollstats     = (bool) $this->_config->showpoppollstats;
		$this->showpopthankyoustats = (bool) $this->_config->showpopthankyoustats;

	}

	/**
	 * @return KunenaForumStatistics
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
		{
			self::$_instance = new KunenaForumStatistics;
		}

		return self::$_instance;
	}

	/**
	 * @param   bool $force force
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 * @return void
	 */
	public function loadAll($force = false)
	{
		$this->top = array();
		$this->loadGeneral($force);
		$this->loadTopicStats($force);
		$this->loadUserStats($force);
	}

	/**
	 * @param   bool $force force
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function loadGeneral($force = false)
	{
		if (!$this->showstats && !$force)
		{
			return;
		}

		$this->loadMemberCount();
		$this->loadLastUserId();
		$this->loadCategoryCount();
		$this->loadLastDays();
	}

	/**
	 * @return void
	 *
	 * @since version
	 * @throws Exception
	 */
	public function loadMemberCount()
	{
		if ($this->memberCount === null)
		{
			$this->memberCount = KunenaUserHelper::getTotalCount();
		}
	}

	/**
	 * @return void
	 *
	 * @since version
	 * @throws Exception
	 */
	public function loadLastUserId()
	{
		if ($this->lastUserId === null)
		{
			$this->lastUserId = KunenaUserHelper::getLastId();
		}
	}

	/**
	 * @return void
	 *
	 * @since version
	 * @throws Exception
	 */
	public function loadCategoryCount()
	{
		if ($this->sectionCount === null)
		{
			$this->sectionCount = $this->categoryCount = 0;
			$categories         = KunenaForumCategoryHelper::getCategories(false, false, 'none');

			foreach ($categories as $category)
			{
				if ($category->published != 1)
				{
					continue;
				}

				if ($category->isSection())
				{
					$this->sectionCount++;
				}
				else
				{
					$this->categoryCount++;
					$this->topicCount   += $category->numTopics;
					$this->messageCount += $category->numPosts;
				}
			}
		}
	}

	/**
	 * @return void
	 *
	 * @since version
	 * @throws Exception
	 */
	public function loadLastDays()
	{
		if ($this->todayTopicCount === null)
		{
			$todaystart     = strtotime(date('Y-m-d'));
			$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
			$this->_db->setQuery("SELECT
				SUM(time>={$todaystart} AND parent=0) AS todayTopicCount,
				SUM(time>={$todaystart} AND parent>0) AS todayReplyCount,
				SUM(time>={$yesterdaystart} AND time<{$todaystart} AND parent=0) AS yesterdayTopicCount,
				SUM(time>={$yesterdaystart} AND time<{$todaystart} AND parent>0) AS yesterdayReplyCount
				FROM #__kunena_messages WHERE time>={$yesterdaystart} AND hold=0"
			);

			try
			{
				$counts = $this->_db->loadObject();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			if ($counts)
			{
				$this->todayTopicCount     = (int) $counts->todayTopicCount;
				$this->todayReplyCount     = (int) $counts->todayReplyCount;
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
	 * @param   bool $override override
	 *
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 * @return void
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

	/**
	 * @param   int $limit limit
	 *
	 * @return array|KunenaForumTopic[]
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function loadTopTopics($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popsubjectcount;

		if ($this->topTopics < $limit)
		{
			$params = array('orderby' => 'posts DESC');
			list($total, $this->topTopics) = KunenaForumTopicHelper::getLatestTopics(false, 0, $limit, $params);

			$top = reset($this->topTopics);

			if (!$top)
			{
				return array();
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_TOPICS');
			$top->titleName  = Text::_('COM_KUNENA_GEN_SUBJECT');
			$top->titleCount = Text::_('COM_KUNENA_USRL_POSTS');

			foreach ($this->topTopics as &$item)
			{
				$item          = clone $item;
				$item->count   = $item->posts;
				$item->link    = HTMLHelper::_('kunenaforum.link', $item->getUri(), KunenaHtmlParser::parseText($item->subject), null, null, '');
				$item->percent = round(100 * $item->count / $top->posts);
			}
		}

		return array_slice($this->topTopics, 0, $limit);
	}

	/**
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function loadTopPolls($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->poppollscount;

		if ($this->topPolls < $limit)
		{
			$query = "SELECT poll.threadid AS id, SUM(opt.votes) AS count
					FROM #__kunena_polls_options AS opt
					INNER JOIN #__kunena_polls AS poll ON poll.id=opt.pollid
					GROUP BY pollid
					HAVING count > 0
					ORDER BY count DESC";
			$this->_db->setQuery($query, 0, $limit);

			try
			{
				$polls = (array) $this->_db->loadObjectList('id');
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$this->topPolls = KunenaForumTopicHelper::getTopics(array_keys($polls));

			$top = reset($this->topPolls);

			if (!$top)
			{
				return array();
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_POLLS');
			$top->titleName  = Text::_('COM_KUNENA_POLL_STATS_NAME');
			$top->titleCount = Text::_('COM_KUNENA_USRL_VOTES');
			$top->count      = $polls[$top->id]->count;

			foreach ($this->topPolls as &$item)
			{
				$item          = clone $item;
				$item->count   = $polls[$item->id]->count;
				$item->link    = HTMLHelper::_('kunenaforum.link', $item->getUri(), KunenaHtmlParser::parseText($item->subject), null, null, '');
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topPolls, 0, $limit);
	}

	/**
	 * @param   bool $override override
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
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

		if ($this->showpopthankyoustats || $override)
		{
			$this->top[] = $this->loadTopThankyous();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}
	}

	/**
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function loadTopPosters($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popusercount;

		if ($this->topPosters < $limit)
		{
			$this->topPosters = KunenaUserHelper::getTopPosters($limit);

			$top = reset($this->topPosters);

			if (!$top)
			{
				return array();
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_POSTERS');
			$top->titleName  = Text::_('COM_KUNENA_USERNAME');
			$top->titleCount = Text::_('COM_KUNENA_USRL_POSTS');

			foreach ($this->topPosters as &$item)
			{
				$item          = clone $item;
				$item->link    = KunenaUserHelper::get($item->id)->getLink(null, null, '');
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topPosters, 0, $limit);
	}

	/**
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function loadTopProfiles($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popusercount;

		if ($this->topProfiles < $limit)
		{
			$this->topProfiles = KunenaFactory::getProfile()->getTopHits($limit);

			$top = reset($this->topProfiles);

			if (!$top)
			{
				return array();
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_PROFILES');
			$top->titleName  = Text::_('COM_KUNENA_USERNAME');
			$top->titleCount = Text::_('COM_KUNENA_USRL_HITS');

			foreach ($this->topProfiles as &$item)
			{
				$item          = clone $item;
				$item->link    = KunenaUserHelper::get($item->id)->getLink(null, null, '');
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topProfiles, 0, $limit);
	}

	/**
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function loadTopThankyous($limit = 0)
	{
		$limit = $limit ? $limit : $this->_config->popthankscount;

		if ($this->topThanks < $limit)
		{
			$query = $this->_db->getQuery(true);
			$query->select($this->_db->quoteName(array('t.targetuserid'), array('id')));
			$query->select('COUNT(t.targetuserid) AS count');
			$query->from($this->_db->quoteName(array('#__kunena_thankyou'), array('t')));
			$query->innerJoin($this->_db->quoteName('#__users', 'u') . ' ON ' . $this->_db->quoteName('u.id') . ' = ' . $this->_db->quoteName('t.targetuserid'));
			$query->group($this->_db->quoteName('t.targetuserid'));
			$query->order($this->_db->quoteName('count') . ' DESC');

			if (KunenaFactory::getConfig()->superadmin_userlist)
			{
				$filter = \Joomla\CMS\Access\Access::getUsersByGroup(8);
				$query->where('u.id NOT IN (' . implode(',', $filter) . ')');
			}

			$this->_db->setQuery($query, 0, $limit);

			try
			{
				$this->topThanks = (array) $this->_db->loadObjectList();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$top = reset($this->topThanks);

			if (!$top)
			{
				return array();
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_THANKS');
			$top->titleName  = Text::_('COM_KUNENA_USERNAME');
			$top->titleCount = Text::_('COM_KUNENA_STAT_THANKS_YOU_RECEIVED');

			foreach ($this->topThanks as &$item)
			{
				$item          = clone $item;
				$item->link    = KunenaUserHelper::get($item->id)->getLink(null, null, '');
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return array_slice($this->topThanks, 0, $limit);
	}
}
