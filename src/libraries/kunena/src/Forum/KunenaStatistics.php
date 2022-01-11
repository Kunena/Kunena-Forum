<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Class KunenaForumStatistics
 *
 * @since   Kunena 6.0
 */
class KunenaStatistics
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_instance = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $lastUserId = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $memberCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $sectionCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $categoryCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topicCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $messageCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $todayTopicCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $yesterdayTopicCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $todayReplyCount = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $yesterdayReplyCount = null;

	/**
	 * @var     array|KunenaTopic[]
	 * @since   Kunena 6.0
	 */
	public $topTopics = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topPosters = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topProfiles = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topPolls = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topThanks = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $top = [];

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showGenStats = true;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showPopUserStats = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showPopSubjectStats = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showPopPollStats = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showPopThankYouStats = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $showStats = true;

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var     KunenaConfig|null
	 * @since   Kunena 6.0
	 */
	protected $_config = null;

	/**
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		$this->_db     = Factory::getContainer()->get('DatabaseDriver');
		$this->_config = KunenaConfig::getInstance();

		$this->showStats            = (bool) $this->_config->showStats;
		$this->showGenStats         = (bool) $this->_config->showGenStats;
		$this->showPopUserStats     = (bool) $this->_config->showPopUserStats;
		$this->showPopSubjectStats  = (bool) $this->_config->showPopSubjectStats;
		$this->showPopPollStats     = (bool) $this->_config->showPopPollStats;
		$this->showPopThankYouStats = (bool) $this->_config->showPopThankYouStats;
	}

	/**
	 * @return \Kunena\Forum\Libraries\Forum\KunenaStatistics|null
	 *
	 * @since   Kunena 6.0
	 *
	 */
	public static function getInstance(): ?KunenaStatistics
	{
		if (self::$_instance === null)
		{
			self::$_instance = new KunenaStatistics;
		}

		return self::$_instance;
	}

	/**
	 * @return int|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public static function getTotalEmoticons(): ?int
	{
		$smilies = null;

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query
			->select('COUNT(*)')
			->from($db->quoteName('#__kunena_smileys'));
		$db->setQuery($query);

		try
		{
			$smilies = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $smilies;
	}

	/**
	 * @param   bool  $force  force
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function loadAll($force = false): void
	{
		$this->top = [];
		$this->loadGeneral($force);
		$this->loadTopicStats($force);
		$this->loadUserStats($force);
	}

	/**
	 * @param   bool  $force  force
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadGeneral($force = false): void
	{
		if (!$this->showStats && !$force)
		{
			return;
		}

		$this->loadMemberCount();
		$this->loadLastUserId();
		$this->loadCategoryCount();
		$this->loadLastDays();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadMemberCount(): void
	{
		if ($this->memberCount === null)
		{
			$this->memberCount = KunenaUserHelper::getTotalCount();
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadLastUserId(): void
	{
		if ($this->lastUserId === null)
		{
			$this->lastUserId = KunenaUserHelper::getLastId();
		}
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadCategoryCount(): array
	{
		if ($this->sectionCount === null)
		{
			$this->sectionCount = $this->categoryCount = 0;
			$categories         = KunenaCategoryHelper::getCategories(false, false, 'none');

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

		return [
			'sections'   => $this->sectionCount,
			'categories' => $this->categoryCount,
			'topics'     => $this->topicCount,
			'messages'   => $this->messageCount,
		];
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadLastDays(): void
	{
		if ($this->todayTopicCount === null)
		{
			$todaystart     = strtotime(date('Y-m-d'));
			$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);

			$query = $this->_db->getQuery(true);
			$query->select(
				'SUM(' . $this->_db->quoteName('time') . ' >= ' .
				$this->_db->quote($todaystart) . ' AND ' . $this->_db->quoteName('parent') . ' = 0) AS ' .
				$this->_db->quote('todayTopicCount') . ',
				SUM(' . $this->_db->quoteName('time') . ' >= ' . $this->_db->quote($todaystart) . ' AND ' .
				$this->_db->quoteName('parent') . ' > 0) AS ' . $this->_db->quote('todayReplyCount') . ',
				SUM(' . $this->_db->quoteName('time') . ' >= ' . $this->_db->quote($yesterdaystart) . ' AND ' .
				$this->_db->quoteName('time') . ' < ' . $this->_db->quote($todaystart) . '  AND ' .
				$this->_db->quoteName('parent') . ' = 0 ) AS ' . $this->_db->quote('yesterdayTopicCount') . ',
				SUM(' . $this->_db->quoteName('time') . ' >= ' . $this->_db->quote($yesterdaystart) . '  AND ' .
				$this->_db->quoteName('time') . '< ' . $this->_db->quote($todaystart) . '  AND ' .
				$this->_db->quoteName('parent') . ' > 0) AS ' . $this->_db->quote('yesterdayReplyCount')
			)
				->from($this->_db->quoteName('#__kunena_messages'))
				->where(
					$this->_db->quoteName('time') . ' >= ' . $this->_db->quote($yesterdaystart) . ' AND ' .
					$this->_db->quoteName('hold') . ' = 0'
				);
			$this->_db->setQuery($query);

			try
			{
				$counts = $this->_db->loadObject();
			}
			catch (ExecutionFailureException $e)
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
	 * @param   bool  $override  override
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function loadTopicStats($override = false): void
	{
		if ($this->showPopSubjectStats || $override)
		{
			$this->top[] = $this->loadTopTopics();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}

		if ($this->showPopPollStats || $override)
		{
			$this->top[] = $this->loadTopPolls();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}
	}

	/**
* @param   int  $limit  limit
	 *
	 * @return array
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function loadTopTopics($limit = 0): array
	{
		$limit           = $limit ? $limit : $this->_config->popSubjectCount;
		$this->topTopics = [];

		if ($this->topTopics < $limit)
		{
			$params = ['orderby' => 'posts DESC'];
			list($this->topTopics) = KunenaTopicHelper::getLatestTopics(false, 0, $limit, $params);

			$top = reset($this->topTopics);

			if (!$top)
			{
				return [];
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_TOPICS');
			$top->titleName  = Text::_('COM_KUNENA_GEN_SUBJECT');
			$top->titleCount = Text::_('COM_KUNENA_USRL_POSTS');

			foreach ($this->topTopics as &$item)
			{
				$item          = clone $item;
				$item->count   = $item->posts;
				$item->link    = HTMLHelper::_('link', $item->getUri(), KunenaParser::parseText($item->subject), null, null, '');
				$item->percent = round(100 * $item->count / $top->posts);
			}
		}

		return \array_slice($this->topTopics, 0, $limit);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function loadTopPolls($limit = 0): array
	{
		$limit = $limit ? $limit : $this->_config->popPollsCount;

		if ($this->topPolls < $limit)
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query
				->select(
					$this->_db->quoteName('poll.threadid', 'id') . ', SUM(' .
					$this->_db->quoteName('opt.votes') . ') AS ' . $this->_db->quoteName('count')
				)
				->from($db->quoteName('#__kunena_polls_options', 'opt'))
				->innerJoin(
					$db->quoteName('#__kunena_polls', 'poll') . ' ON ' .
					$this->_db->quoteName('poll.id') . ' = ' . $this->_db->quoteName('opt.pollid')
				)
				->group($this->_db->quoteName('pollid'))
				->having($this->_db->quoteName('count') . ' > 0')
				->order($this->_db->quoteName('count') . ' DESC');
			$query->setLimit($limit);
			$this->_db->setQuery($query);

			try
			{
				$polls = (array) $this->_db->loadObjectList('id');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			// Codestyler fixes: use real dir
			$this->topPolls = KunenaTopicHelper::getTopics(array_keys($polls));

			$top = reset($this->topPolls);

			if (!$top)
			{
				return [];
			}

			$top->title      = Text::_('COM_KUNENA_LIB_STAT_TOP_POLLS');
			$top->titleName  = Text::_('COM_KUNENA_POLL_STATS_NAME');
			$top->titleCount = Text::_('COM_KUNENA_USRL_VOTES');
			$top->count      = $polls[$top->id]->count;

			foreach ($this->topPolls as &$item)
			{
				$item          = clone $item;
				$item->count   = $polls[$item->id]->count;
				$item->link    = HTMLHelper::_('link', $item->getUri(), KunenaParser::parseText($item->subject), null, null, '');
				$item->percent = round(100 * $item->count / $top->count);
			}
		}

		return \array_slice($this->topPolls, 0, $limit);
	}

	/**
	 * @param   bool  $override  override
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadUserStats($override = false): void
	{
		if ($this->showPopUserStats || $override)
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

		if ($this->showPopThankYouStats || $override)
		{
			$this->top[] = $this->loadTopThankyous();

			if (!end($this->top))
			{
				array_pop($this->top);
			}
		}
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadTopPosters($limit = 0): array
	{
		$limit = $limit ? $limit : $this->_config->popUserCount;

		if ($this->topPosters < $limit)
		{
			$this->topPosters = KunenaUserHelper::getTopPosters($limit);

			$top = reset($this->topPosters);

			if (!$top)
			{
				return [];
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

		return \array_slice($this->topPosters, 0, $limit);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadTopProfiles($limit = 0): array
	{
		$limit = $limit ? $limit : $this->_config->popUserCount;

		if ($this->topProfiles < $limit)
		{
			$this->topProfiles = KunenaFactory::getProfile()->getTopHits($limit);

			$top = reset($this->topProfiles);

			if (!$top)
			{
				return [];
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

		return \array_slice($this->topProfiles, 0, $limit);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadTopThankyous($limit = 0): array
	{
		$limit = $limit ? $limit : $this->_config->popThanksCount;

		if ($this->topThanks < $limit)
		{
			$query = $this->_db->getQuery(true);
			$query
				->select(
					[
						$this->_db->quoteName('t.targetuserid'),
						$this->_db->quoteName('id'),
					]
				)
				->select('COUNT(' . $this->_db->quoteName('t.targetuserid') . ') AS ' . $this->_db->quoteName('count'))
				->from($this->_db->quoteName('#__kunena_thankyou', 't'))
				->innerJoin(
					$this->_db->quoteName('#__users', 'u') . ' ON ' .
					$this->_db->quoteName('u.id') . ' = ' . $this->_db->quoteName('t.targetuserid')
				)
				->group($this->_db->quoteName('t.targetuserid'));
			$query->order($this->_db->quoteName('count') . ' DESC');

			if (KunenaFactory::getConfig()->superAdminUserlist)
			{
				$filter = Access::getUsersByGroup(8);
				$query->where($this->_db->quoteName('u.id') . ' NOT IN (' . implode(',', $filter) . ')');
			}

			$query->setLimit($limit);
			$this->_db->setQuery($query);

			try
			{
				$this->topThanks = (array) $this->_db->loadObjectList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$top = reset($this->topThanks);

			if (!$top)
			{
				return [];
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

		return \array_slice($this->topThanks, 0, $limit);
	}
}
