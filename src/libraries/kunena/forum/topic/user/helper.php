<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Kunena Forum Topic User Helper Class
 * @since Kunena
 */
abstract class KunenaForumTopicUserHelper
{
	/**
	 * @var array|KunenaForumTopicUser[]
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @var array|KunenaForumTopicUser[]
	 * @since Kunena
	 */
	protected static $_topics = array();

	/**
	 * Returns KunenaForumTopicUser object.
	 *
	 * @param   KunenaForumTopic|int|null $topic  topic
	 * @param   mixed                     $user   user
	 * @param   bool                      $reload reload
	 *
	 * @return KunenaForumTopicUser
	 * @throws Exception
	 * @since Kunena
	 */
	public static function get($topic = null, $user = null, $reload = false)
	{
		if ($topic instanceof KunenaForumTopic)
		{
			$topic = $topic->id;
		}

		$topic = intval($topic);
		$user  = KunenaUserHelper::get($user);

		if ($topic < 1)
		{
			return new KunenaForumTopicUser(null, $user);
		}

		if ($reload || empty(self::$_instances [$user->userid][$topic]))
		{
			$topics                                   = self::getTopics($topic, $user);
			self::$_instances [$user->userid][$topic] = self::$_topics [$topic][$user->userid] = array_pop($topics);
		}

		return self::$_instances [$user->userid][$topic];
	}

	/**
	 * @param   bool|array $ids  ids
	 * @param   mixed      $user user
	 *
	 * @return KunenaForumTopicUser[]
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getTopics($ids = false, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($ids === false)
		{
			return isset(self::$_instances[$user->userid]) ? self::$_instances[$user->userid] : array();
		}
		elseif (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Convert topic objects into ids
		foreach ($ids as $i => $id)
		{
			if ($id instanceof KunenaForumTopic)
			{
				$ids[$i] = $id->id;
			}
		}

		$ids = array_unique($ids);
		self::loadTopics($ids, $user);

		$list = array();

		foreach ($ids as $id)
		{
			if (!empty(self::$_instances [$user->userid][$id]))
			{
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	/**
	 * @param   array      $ids  ids
	 * @param   KunenaUser $user user
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	protected static function loadTopics(array $ids, KunenaUser $user)
	{
		foreach ($ids as $i => $id)
		{
			$id = intval($id);

			if (!$id || isset(self::$_instances [$user->userid][$id]))
			{
				unset($ids[$i]);
			}
		}

		if (empty($ids))
		{
			return;
		}

		$idlist = implode(',', $ids);
		$db     = Factory::getDBO();
		$query  = "SELECT * FROM #__kunena_user_topics WHERE user_id={$db->quote($user->userid)} AND topic_id IN ({$idlist})";
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('topic_id');
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaForumTopicUser;
				$instance->bind($results[$id]);
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = $instance;
			}
			else
			{
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = new KunenaForumTopicUser($id, $user->userid);
			}
		}

		unset($results);
	}

	/**
	 * Get all user ids who have participated to the given topics.
	 *
	 * @param   array|KunenaForumTopic[] $topics topics
	 * @param   string                   $value  Row to pick up as value.
	 *
	 * @return array List of [topic][userid] = value.
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getUserIds(array $topics, $value = 'user_id')
	{
		// Convert topic objects into ids
		$ids = array();

		foreach ($topics as $id)
		{
			if ($id instanceof KunenaForumTopic)
			{
				$ids[(int) $id->id] = (int) $id->id;
			}
			else
			{
				$ids[(int) $id] = (int) $id;
			}
		}

		$idlist = implode(',', $ids);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('topic_id, user_id')
			->from($db->quoteName('#__kunena_user_topics'))
			->where("topic_id IN ({$idlist})")
			->where('posts>0');

		$query->select($db->quoteName($value));

		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadRowList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$list = array();

		foreach ($results as $result)
		{
			$list[$result->topic_id][$result->user_id] = $result->{$value};
		}

		return $list;
	}

	/**
	 * @param   KunenaForumTopic $old old
	 * @param   KunenaForumTopic $new new
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public static function move($old, $new)
	{
		// Update database
		$db    = Factory::getDBO();
		$query = "UPDATE #__kunena_user_topics SET topic_id={$db->quote($new->id)}, category_id={$db->quote($new->category_id)} WHERE topic_id={$db->quote($old->id)}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		// Update internal state
		if (isset(self::$_topics [$old->id]))
		{
			if ($new->id != $old->id)
			{
				self::$_topics [$new->id] = self::$_topics [$old->id];
				unset(self::$_topics [$old->id]);
			}

			foreach (self::$_topics [$new->id] as &$instance)
			{
				$instance->topic_id    = $new->id;
				$instance->category_id = $new->category_id;
			}
		}

		return true;
	}

	/**
	 * @param   KunenaForumTopic $old old
	 * @param   KunenaForumTopic $new new
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public static function merge($old, $new)
	{
		$db = Factory::getDBO();

		// Move all user topics which do not exist in new topic
		$queries[] = "UPDATE #__kunena_user_topics AS ut
			INNER JOIN #__kunena_user_topics AS o ON o.user_id = ut.user_id
			SET ut.topic_id={$db->quote($new->id)}, ut.category_id={$db->quote($new->category_id)}
			WHERE o.topic_id={$db->quote($old->id)} AND ut.topic_id IS NULL";

		// Merge user topics information that exists in both topics
		$queries[] = "UPDATE #__kunena_user_topics AS ut
			INNER JOIN #__kunena_user_topics AS o ON o.user_id = ut.user_id
			SET ut.posts = o.posts + ut.posts,
				ut.last_post_id = GREATEST( o.last_post_id, ut.last_post_id ),
				ut.owner = GREATEST( o.owner, ut.owner ),
				ut.favorite = GREATEST( o.favorite, ut.favorite ),
				ut.subscribed = GREATEST( o.subscribed, ut.subscribed )
				WHERE ut.topic_id = {$db->quote($new->id)}
				AND o.topic_id = {$db->quote($old->id)}";

		// Delete all user topics from the shadow topic
		$queries[] = "DELETE FROM #__kunena_user_topics WHERE topic_id={$db->quote($old->id)}";

		foreach ($queries as $query)
		{
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		// Update internal state
		self::reloadTopic($old->id);
		self::reloadTopic($new->id);

		return true;
	}

	/**
	 * @param   int $id id
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	protected static function reloadTopic($id)
	{
		if (empty(self::$_topics [$id]))
		{
			return;
		}

		$idlist = implode(',', array_keys(self::$_topics [$id]));
		$db     = Factory::getDBO();
		$query  = "SELECT * FROM #__kunena_user_topics WHERE user_id IN ({$idlist}) AND topic_id={$id}";
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('user_id');
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// TODO: Is there a bug?
		foreach (self::$_topics[$id] as $instance)
		{
			if (isset($results[$instance->user_id]))
			{
				$instance->bind($results[$instance->user_id]);
				$instance->exists(true);
			}
			else
			{
				$instance->reset();
			}
		}

		unset($results);
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 * @since Kunena
	 * @return void
	 */
	public static function cleanup()
	{
		self::$_instances = array();
		self::$_topics    = array();
	}

	/**
	 * @param   bool|array|int $topicids topicids
	 * @param   int            $start    start
	 * @param   int            $end      end
	 *
	 * @return boolean|integer
	 * @throws Exception
	 * @since Kunena
	 */
	public static function recount($topicids = false, $start = 0, $end = 0)
	{
		$db = Factory::getDBO();

		if (is_array($topicids))
		{
			$where  = 'AND m.thread IN (' . implode(',', $topicids) . ')';
			$where2 = 'AND ut.topic_id IN (' . implode(',', $topicids) . ')';
		}
		elseif ((int) $topicids)
		{
			$where  = 'AND m.thread=' . (int) $topicids;
			$where2 = 'AND ut.topic_id=' . (int) $topicids;
		}
		else
		{
			$where  = '';
			$where2 = '';
		}

		if ($end)
		{
			$where  .= " AND (m.thread BETWEEN {$start} AND {$end})";
			$where2 .= " AND (ut.topic_id BETWEEN {$start} AND {$end})";
		}

		// Create missing user topics and update post count and last post if there are posts by that user
		$query = "INSERT INTO `#__kunena_user_topics` (`user_id`, `topic_id`, `category_id`, `posts`, `last_post_id`, `owner`)
					SELECT m.userid AS `user_id`, m.thread AS `topic_id`, m.catid AS `category_id`, SUM(m.hold=0) AS `posts`, MAX(IF(m.hold=0,m.id,0)) AS `last_post_id`, MAX(IF(m.parent=0,1,0)) AS `owner`
					FROM `#__kunena_messages` AS m
					WHERE m.userid>0 AND m.moved=0 {$where}
					GROUP BY m.userid, m.thread
				ON DUPLICATE KEY UPDATE `category_id`=VALUES(`category_id`), `posts`=VALUES(`posts`), `last_post_id`=VALUES(`last_post_id`)";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		// Find user topics where last post doesn't exist and reset values in it
		$query = "UPDATE #__kunena_user_topics AS ut
			LEFT JOIN #__kunena_messages AS m ON ut.last_post_id=m.id AND m.hold=0
			SET posts=0, last_post_id=0
			WHERE m.id IS NULL {$where2}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows += $db->getAffectedRows();

		// Delete entries that have default values
		$query = "DELETE ut FROM #__kunena_user_topics AS ut WHERE ut.posts=0 AND ut.owner=0 AND ut.favorite=0 AND ut.subscribed=0 AND ut.params='' {$where2}";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows += $db->getAffectedRows();

		return $rows;
	}
}
