<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaTopicHelper
{
	/**
	 * @var     KunenaTopic[]
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Topic\Topic object.
	 *
	 * @param   int   $identifier  The topic to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  KunenaTopic
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof KunenaTopic)
		{
			return $identifier;
		}

		$id = (int) $identifier;

		if ($id < 1)
		{
			return new KunenaTopic;
		}

		if (empty(self::$_instances[$id]))
		{
			$instance = new KunenaTopic;

			// Only load topics which haven't been preloaded before (including missing ones).
			$instance->load(!\array_key_exists($id, self::$_instances) ? $id : null);
			$instance->id          = $id;
			self::$_instances[$id] = $instance;
		}
		elseif ($reload)
		{
			self::$_instances[$id]->load();
		}

		return self::$_instances[$id];
	}

	/**
	 * @param   mixed  $ids    ids
	 * @param   bool   $value  value
	 * @param   mixed  $user   user
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function subscribe($ids, $value = true, $user = null): int
	{
		// Pre-load all items
		// Codestyle fixes: use real url
		KunenaTopicUserHelper::getTopics($ids, $user);
		$count = 0;

		foreach ($ids as $id)
		{
			$usertopic = KunenaTopicUserHelper::get($id, $user);

			if ($usertopic->subscribed != (int) $value)
			{
				$count++;
			}

			$usertopic->subscribed = (int) $value;
			$usertopic->save();
		}

		return $count;
	}

	/**
	 * @param   mixed  $ids    ids
	 * @param   bool   $value  value
	 * @param   mixed  $user   user
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function favorite($ids, $value = true, $user = null): int
	{
		// Pre-load all items
		KunenaTopicUserHelper::getTopics($ids, $user);
		$count = 0;

		foreach ($ids as $id)
		{
			$usertopic = KunenaTopicUserHelper::get($id, $user);

			if ($usertopic->favorite != (int) $value)
			{
				$count++;
			}

			$usertopic->favorite = (int) $value;
			$usertopic->save();
		}

		return $count;
	}

	/**
	 * @param   mixed   $ids        ids
	 * @param   string  $authorise  authorise
	 *
	 * @return  KunenaTopic[]
	 *
	 * @since   Kunena
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getTopics($ids = false, $authorise = 'read'): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($ids === false)
		{
			return self::$_instances;
		}

		if (\is_array($ids))
		{
			$ids = array_unique($ids);
		}
		else
		{
			$ids = [$ids];
		}

		self::loadTopics($ids);

		$list = [];

		foreach ($ids as $id)
		{
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->isAuthorised($authorise, null))
			{
				$list [$id] = self::$_instances [$id];
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected static function loadTopics(array $ids): void
	{
		foreach ($ids as $i => $id)
		{
			$id = \intval($id);

			if (!$id || isset(self::$_instances [$id]))
			{
				unset($ids[$i]);
			}
		}

		if (empty($ids))
		{
			return;
		}

		$idlist = implode(',', $ids);
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_topics'))
			->where($db->quoteName('id') . ' IN (' . $idlist . ')');
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaTopic($results[$id]);
				$instance->exists(true);
				self::$_instances [$id] = $instance;
			}
			else
			{
				self::$_instances [$id] = null;
			}
		}

		unset($results);
	}

	/**
	 * @param   mixed  $ids   ids
	 * @param   mixed  $user  user
	 *
	 * @return User\KunenaTopicUser|User\KunenaTopicUser[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getUserTopics($ids = false, $user = null)
	{
		if ($ids === false)
		{
			$ids = array_keys(self::$_instances);
		}

		return KunenaTopicUserHelper::getTopics($ids, $user);
	}

	/**
	 * @param   mixed  $categories  categories
	 * @param   int    $limitstart  limitstart
	 * @param   int    $limit       limit
	 * @param   array  $params      params
	 *
	 * @return array
	 *
	 * @since   Kunena
	 *
	 * @throws \Exception
	 */
	public static function getLatestTopics($categories = false, $limitstart = 0, $limit = 0, $params = []): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$config = KunenaFactory::getConfig();

		if ($limit < 1 && empty($params['nolimit']))
		{
			$limit = $config->threadsPerPage;
		}

		$reverse   = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$exclude   = isset($params['exclude']) ? (int) $params['exclude'] : 0;
		$orderby   = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$user      = isset($params['user']) ? KunenaUserHelper::get($params['user']) : KunenaUserHelper::getMyself();
		$hold      = isset($params['hold']) ? (string) $params['hold'] : 0;
		$moved     = isset($params['moved']) ? (string) $params['moved'] : 0;
		$where     = isset($params['where']) ? (string) $params['where'] : '';

		if (strstr('ut.last_', $orderby))
		{
			$post_time_field = 'ut.last_post_time';
		}
		elseif (strstr('tt.first_', $orderby))
		{
			$post_time_field = 'tt.first_post_time';
		}
		else
		{
			$post_time_field = 'tt.last_post_time';
		}

		if (!$exclude)
		{
			$categories = KunenaCategoryHelper::getCategories($categories, $reverse);
		}
		else
		{
			$categories = KunenaCategoryHelper::getCategories($categories, 0);
		}

		$catlist = [];

		foreach ($categories as $category)
		{
			$catlist += $category->getChannels();
		}

		if (empty($catlist))
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [0, []];
		}

		$catlist = implode(',', array_keys($catlist));

		$whereuser = [];

		if (!empty($params['started']))
		{
			$whereuser[] = 'ut.owner=1';
		}

		if (!empty($params['replied']))
		{
			$whereuser[] = '(ut.owner=0 AND ut.posts>0)';
		}

		if (!empty($params['posted']))
		{
			$whereuser[] = 'ut.posts>0';
		}

		if (!empty($params['favorited']))
		{
			$whereuser[] = 'ut.favorite=1';
		}

		if (!empty($params['subscribed']))
		{
			$whereuser[] = 'ut.subscribed=1';
		}

		$wheretime = ($starttime ? " AND {$post_time_field}>{$db->quote($starttime)}" : '');
		$whereuser = ($whereuser ? " AND ut.user_id={$db->quote($user->userid)} AND (" . implode(' OR ', $whereuser) . ')' : '');

		if ($exclude)
		{
			$where = "tt.hold IN ({$hold}) AND tt.category_id NOT IN ({$catlist}) {$whereuser} {$wheretime} {$where}";
		}
		else
		{
			$where = "tt.hold IN ({$hold}) AND tt.category_id IN ({$catlist}) {$whereuser} {$wheretime} {$where}";
		}

		if (!$moved)
		{
			$where .= " AND tt.moved_id='0'";
		}

		// Get total count
		if ($whereuser)
		{
			$query = "SELECT COUNT(*) FROM #__kunena_user_topics AS ut INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id WHERE {$where}";
		}
		else
		{
			$query = "SELECT COUNT(*) FROM #__kunena_topics AS tt WHERE {$where}";
		}

		$db->setQuery($query);

		try
		{
			$total = (int) $db->loadResult();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return [0, []];
		}

		if (!$total)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [0, []];
		}

		// If out of range, use last page
		if ($limit && $total < $limitstart)
		{
			$limitstart = \intval($total / $limit) * $limit;
		}

		// Get items
		if ($whereuser)
		{
			$query = $db->getQuery(true);
			$query->select('tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread')
				->from($db->quoteName('#__kunena_user_topics', 'ut'))
				->innerJoin($db->quoteName('#__kunena_topics', 'tt') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('ut.topic_id'))
				->where($where)
				->order($orderby);
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select('tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread')
				->from($db->quoteName('#__kunena_topics', 'tt'))
				->leftJoin($db->quoteName('#__kunena_user_topics', 'ut') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('ut.topic_id') . ' AND ' . $db->quoteName('ut.user_id') . ' = ' . $db->quote($user->userid))
				->where($where)
				->order($orderby);
		}

		$query->setLimit($limit, $limitstart);
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [0, []];
		}

		$topics = [];

		foreach ($results as $id => $result)
		{
			$instance = new KunenaTopic($result);
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			$topics[$id]            = $instance;
		}

		unset($results);
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return [$total, $topics];
	}

	/**
	 * Method to delete selected topics.
	 *
	 * @param   array|int  $ids  ids
	 *
	 * @return  integer Count of deleted topics.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function delete($ids)
	{
		if (empty($ids))
		{
			return 0;
		}

		if (\is_array($ids))
		{
			$idlist = implode(',', $ids);
		}
		else
		{
			$idlist = (int) $ids;
		}

		// Delete user topics
		$queries[] = "DELETE FROM #__kunena_user_topics WHERE topic_id IN ({$idlist})";

		// Delete user read
		$queries[] = "DELETE FROM #__kunena_user_read WHERE topic_id IN ({$idlist})";

		// Delete thank yous
		$queries[] = "DELETE t FROM #__kunena_thankyou AS t INNER JOIN #__kunena_messages AS m ON m.id=t.postid WHERE m.thread IN ({$idlist})";

		// Delete poll users (if not shadow)
		$queries[] = "DELETE p FROM #__kunena_polls_users AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.pollid WHERE tt.id IN ({$idlist}) AND tt.moved_id=0";

		// Delete poll options (if not shadow)
		$queries[] = "DELETE p FROM #__kunena_polls_options AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.pollid WHERE tt.id IN ({$idlist}) AND tt.moved_id=0";

		// Delete polls (if not shadow)
		$queries[] = "DELETE p FROM #__kunena_polls AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.id WHERE tt.id IN ({$idlist}) AND tt.moved_id=0";

		// Delete messages
		$queries[] = "DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.thread IN ({$idlist})";

		// TODO: delete attachments
		// Delete topics
		$queries[] = "DELETE FROM #__kunena_topics WHERE id IN ({$idlist})";

		$db = Factory::getContainer()->get('DatabaseDriver');

		foreach ($queries as $query)
		{
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		return $db->getAffectedRows();
	}

	/**
	 * Method to trash topics. They will be marked as deleted, but still exist in database.
	 *
	 * @param   array|int  $ids  ids
	 *
	 * @return  integer  Count of trashed topics.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function trash($ids)
	{
		if (empty($ids))
		{
			return 0;
		}

		if (\is_array($ids))
		{
			$idlist = implode(',', $ids);
		}
		else
		{
			$idlist = (int) $ids;
		}

		$db        = Factory::getContainer()->get('DatabaseDriver');
		$queries[] = "UPDATE #__kunena_messages SET hold='2' WHERE thread IN ({$idlist})";
		$queries[] = "UPDATE #__kunena_topics SET hold='2' WHERE id IN ({$idlist})";

		foreach ($queries as $query)
		{
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		return $db->getAffectedRows();
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function cleanup(): void
	{
		self::$_instances = [];
	}

	/**
	 * @param   mixed  $ids    ids
	 * @param   int    $start  start
	 * @param   int    $end    end
	 *
	 * @return  boolean|integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function recount($ids = false, $start = 0, $end = 0)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (\is_array($ids))
		{
			$threads = 'AND ' . $db->quoteName('m.thread') . ' IN (' . implode(',', $ids) . ')';
		}
		elseif ((int) $ids)
		{
			$threads = 'AND ' . $db->quoteName('m.thread') . ' = ' . $db->quote((int) $ids);
		}
		else
		{
			$threads = '';
		}

		if ($end)
		{
			if ($start < 1)
			{
				$start = 1;
			}

			$topics = " AND (" . $db->quoteName('tt.id') . " BETWEEN {$start} AND {$end})";
		}
		else
		{
			$topics = '';
		}

		// Mark all empty topics as deleted
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__kunena_topics', 'tt'))
			->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.thread') . ' = ' . $db->quoteName('tt.id') . ' AND ' . $db->quoteName('tt.hold') . ' = ' . $db->quoteName('m.hold'))
			->set($db->quoteName('tt.hold') . ' = 4')
			->set($db->quoteName('tt.posts') . ' = 0')
			->set($db->quoteName('tt.attachments') . ' = 0')
			->set($db->quoteName('tt.first_post_id') . ' = 0')
			->set($db->quoteName('tt.first_post_time') . ' = 0')
			->set($db->quoteName('tt.first_post_userid') . ' = 0')
			->set($db->quoteName('tt.first_post_message') . ' =  \'\'')
			->set($db->quoteName('tt.first_post_guest_name') . ' =  \'\'')
			->set($db->quoteName('tt.last_post_id') . ' = 0')
			->set($db->quoteName('tt.last_post_time') . ' = 0')
			->set($db->quoteName('tt.last_post_userid') . ' = 0')
			->set($db->quoteName('tt.last_post_message') . ' =  \'\'')
			->set($db->quoteName('tt.last_post_guest_name') . ' =  \'\'')
			->where('tt.moved_id=0 AND tt.hold!=4 AND m.id IS NULL ' . $topics . ' ' . $threads);
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		// Find out if there are deleted topics with visible replies.
		$query = "UPDATE #__kunena_topics AS tt
			INNER JOIN (
				SELECT m.thread, MIN(m.hold) AS hold FROM #__kunena_messages AS m WHERE m.hold IN (0,1) {$threads} GROUP BY thread
			) AS c ON tt.id=c.thread
			SET tt.hold = c.hold
			WHERE tt.moved_id=0 {$topics}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows += $db->getAffectedRows();

		// Recount total posts, total attachments and update first & last post information (by time)
		$query = "UPDATE #__kunena_topics AS tt
			INNER JOIN (
				SELECT m.thread, m.hold, COUNT(DISTINCT m.id) AS posts, COUNT(a.id) as attachments, MIN(m.time) AS mintime, MAX(m.time) AS maxtime
				FROM #__kunena_messages AS m
				LEFT JOIN #__kunena_attachments AS a ON m.id=a.mesid
				WHERE m.moved=0 {$threads}
				GROUP BY m.thread, m.hold
			) AS c ON tt.id=c.thread
			INNER JOIN #__kunena_messages AS mmin ON c.thread=mmin.thread AND mmin.hold=tt.hold AND mmin.time=c.mintime
			INNER JOIN #__kunena_messages AS mmax ON c.thread=mmax.thread AND mmax.hold=tt.hold AND mmax.time=c.maxtime
			INNER JOIN #__kunena_messages_text AS tmin ON tmin.mesid=mmin.id
			INNER JOIN #__kunena_messages_text AS tmax ON tmax.mesid=mmax.id
			SET tt.posts=c.posts,
				tt.attachments=c.attachments,
				tt.first_post_id = mmin.id,
				tt.first_post_time = mmin.time,
				tt.first_post_userid = mmin.userid,
				tt.first_post_message = tmin.message,
				tt.first_post_guest_name = mmin.name,
				tt.last_post_id = mmax.id,
				tt.last_post_time = mmax.time,
				tt.last_post_userid = mmax.userid,
				tt.last_post_message = tmax.message,
				tt.last_post_guest_name = mmax.name
			WHERE moved_id=0 {$topics}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows += $db->getAffectedRows();

		return $rows;
	}

	/**
	 * @param   array  $topics  Topics
	 * @param   mixed  $user    User
	 *
	 * @return  array|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function fetchNewStatus(array $topics, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->showNew || empty($topics) || !$user->exists())
		{
			return [];
		}

		$session = KunenaFactory::getSession();

		$ids = [];

		foreach ($topics as $topic)
		{
			if ($topic->last_post_time < $session->getAllReadTime())
			{
				continue;
			}

			$allreadtime = $topic->getCategory()->getUserInfo()->allreadtime;

			if ($allreadtime && $topic->last_post_time < $allreadtime)
			{
				continue;
			}

			$ids[] = $topic->id;
		}

		if ($ids)
		{
			$idstr = implode(",", $ids);

			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select($db->quoteName('m.thread') . ' AS ' . $db->quoteName('id') . ', MIN(' . $db->quoteName('m.id') . ') AS ' . $db->quoteName('lastread') . ', SUM(1) AS ' . $db->quoteName('unread'))
				->from($db->quoteName('#__kunena_messages', 'm'))
				->leftJoin($db->quoteName('#__kunena_user_read', 'ur') . ' ON ' . $db->quoteName('ur.topic_id') . ' = ' . $db->quoteName('m.thread') . ' AND ' . $db->quoteName('user_id') . ' = ' . $db->quote($user->userid))
				->where($db->quoteName('m.hold') . ' = 0')
				->andWhere($db->quoteName('m.moved') . ' = 0')
				->andWhere($db->quoteName('m.thread') . ' IN (' . $idstr . ')')
				->andWhere($db->quoteName('m.time') . ' > ' . $db->quote($session->getAllReadTime()))
				->andWhere($db->quoteName('ur.time') . ' IS NULL')
				->orWhere($db->quoteName('m.time') . ' > ' . $db->quoteName('ur.time'))
				->group($db->quoteName('thread'));
			$db->setQuery($query);

			try
			{
				$topiclist = (array) $db->loadObjectList('id');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		$list = [];

		foreach ($topics as $topic)
		{
			if (isset($topiclist[$topic->id]))
			{
				$topic->lastread = $topiclist[$topic->id]->lastread;
				$topic->unread   = $topiclist[$topic->id]->unread;
			}
			else
			{
				$topic->lastread = $topic->last_post_id;
				$topic->unread   = 0;
			}

			$list[$topic->id] = $topic->lastread;
		}

		return $list;
	}
}
