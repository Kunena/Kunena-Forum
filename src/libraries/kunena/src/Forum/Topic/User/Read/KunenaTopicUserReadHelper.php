<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.User.Read
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\User\Read;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\User\Read\ReadHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaTopicUserReadHelper
{
	/**
	 * @var     array| KunenaRead[]
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * @var     array|KunenaRead[]
	 * @since   Kunena 6.0
	 */
	protected static $_topics = [];

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Topic\User\Read\Read object.
	 *
	 * @param   mixed  $topic   User topic to load.
	 * @param   mixed  $user    user
	 * @param   bool   $reload  reload
	 *
	 * @return  KunenaRead
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function get($topic = null, $user = null, $reload = false): KunenaRead
	{
		if ($topic instanceof KunenaTopic)
		{
			$topic = $topic->id;
		}

		$topic = \intval($topic);
		$user  = KunenaUserHelper::get($user);

		if ($topic < 1)
		{
			return new KunenaRead(null, $user);
		}

		if (!$user->userid)
		{
			return new KunenaRead($topic, 0);
		}

		if ($reload || empty(self::$_instances [$user->userid][$topic]))
		{
			$topics                                   = self::getTopics($topic, $user);
			self::$_instances [$user->userid][$topic] = self::$_topics [$topic][$user->userid] = array_pop($topics);
		}

		return self::$_instances [$user->userid][$topic];
	}

	/**
	 * @param   bool|array  $ids   ids
	 * @param   mixed       $user  user
	 *
	 * @return  KunenaRead[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getTopics($ids = false, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($ids === false)
		{
			return isset(self::$_instances[$user->userid]) ? self::$_instances[$user->userid] : [];
		}

		if (!\is_array($ids))
		{
			$ids = [$ids];
		}

		// Convert topic objects into ids
		foreach ($ids as $i => $id)
		{
			if ($id instanceof KunenaTopic)
			{
				$ids[$i] = $id->id;
			}
		}

		$ids = array_unique($ids);
		self::loadTopics($ids, $user);

		$list = [];

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
	 * @param   array       $ids   ids
	 * @param   KunenaUser  $user  user
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected static function loadTopics(array $ids, KunenaUser $user): void
	{
		foreach ($ids as $i => $id)
		{
			$id = \intval($id);

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
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_user_read'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($user->userid))
			->andWhere($db->quoteName('topic_id') . ' IN (' . $idlist . ')');
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('topic_id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaRead;
				$instance->bind($results[$id]);
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = $instance;
			}
			else
			{
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = new KunenaRead($id, $user->userid);
			}
		}

		unset($results);
	}

	/**
	 * @param   KunenaTopic  $old  old
	 * @param   KunenaTopic  $new  new
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function move(KunenaTopic $old, KunenaTopic $new): bool
	{
		// Update database
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__kunena_user_read'))
			->set($db->quoteName('topic_id') . ' = ' . $db->quote($new->id))
			->set($db->quoteName('category_id') . ' = ' . $db->quote($new->category_id))
			->where($db->quoteName('topic_id') . ' = ' . $db->quote($old->id));
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

		// Update internal state
		if (isset(self::$_topics [$old->id]))
		{
			if ($new->id != $old->id)
			{
				self::$_topics [$new->id] = self::$_topics [$old->id];
				unset(self::$_topics [$old->id]);
			}

			foreach (self::$_topics [$new->id] as $instance)
			{
				$instance->topic_id    = $new->id;
				$instance->category_id = $new->category_id;
			}
		}

		return true;
	}

	/**
	 * @param   KunenaTopic  $old  old
	 * @param   KunenaTopic  $new  new
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function merge(KunenaTopic $old, KunenaTopic $new): bool
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Move all user topics which do not exist in new topic
		$queries[] = "UPDATE #__kunena_user_read AS ur
			INNER JOIN #__kunena_user_read AS o ON o.user_id = ur.user_id
			SET ur.topic_id={$db->quote($new->id)}, ur.category_id={$db->quote($new->category_id)}
			WHERE o.topic_id={$db->quote($old->id)} AND ur.topic_id IS NULL";

		// Merge user topics information that exists in both topics
		$queries[] = "UPDATE #__kunena_user_read AS ur
			INNER JOIN #__kunena_user_read AS o ON o.user_id = ur.user_id
			SET ur.message_id = LEAST( o.message_id, ur.message_id ),
				ur.time = LEAST( o.time, ur.time )
				WHERE ur.topic_id = {$db->quote($new->id)}
				AND o.topic_id = {$db->quote($old->id)}";

		// Delete all user topics from the shadow topic
		$queries[] = "DELETE FROM #__kunena_user_read WHERE topic_id={$db->quote($old->id)}";

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

		// Update internal state
		self::reloadTopic($old->id);
		self::reloadTopic($new->id);

		return true;
	}

	/**
	 * @param   int  $id  id
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	protected static function reloadTopic(int $id): void
	{
		if (empty(self::$_topics [$id]))
		{
			return;
		}

		$idlist = implode(',', array_keys(self::$_topics [$id]));
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_user_read'))
			->where($db->quoteName('user_id') . ' IN (' . $idlist . ')')
			->andWhere($db->quoteName('topic_id') . ' = ' . $db->quote($id));
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('user_id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// TODO: is there a bug?
		foreach (self::$_topics [$id] as $instance)
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function recount(): bool
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__user_read', 'ur'))
			->innerJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('ur.topic_id'))
			->set($db->quoteName('ur.category_id') . ' = ' . $db->quoteName('t.category_id'));
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

		return true;
	}

	/**
	 * @param   int  $days  days
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function purge($days = 365): bool
	{
		// Purge items that are older than x days (defaulting to a year)
		$db        = Factory::getContainer()->get('DatabaseDriver');
		$timestamp = Factory::getDate()->toUnix() - 60 * 60 * 24 * $days;
		$query     = $db->getQuery(true);
		$query->delete($db->quoteName('#__kunena_user_read'))
			->where($db->quoteName('time') . ' < ' . $db->quote($timestamp));
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

		return true;
	}
}
