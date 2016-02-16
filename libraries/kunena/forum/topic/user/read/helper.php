<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic.User.Read
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumTopicUserReadHelper
 */
abstract class KunenaForumTopicUserReadHelper
{
	/**
	 * @var array|KunenaForumTopicUserRead[]
	 */
	protected static $_instances = array();
	/**
	 * @var array|KunenaForumTopicUserRead[]
	 */
	protected static $_topics = array();

	/**
	 * Returns KunenaForumTopicUserRead object.
	 *
	 * @param mixed $topic	User topic to load.
	 * @param mixed $user
	 * @param bool $reload
	 *
	 * @return KunenaForumTopicUserRead
	 */
	static public function get($topic = null, $user = null, $reload = false)
	{
		if ($topic instanceof KunenaForumTopic)
		{
			$topic = $topic->id;
		}

		$topic = intval ( $topic );
		$user = KunenaUserHelper::get($user);

		if ($topic < 1)
		{
			return new KunenaForumTopicUserRead (null, $user);
		}

		if (!$user->userid)
		{
			return new KunenaForumTopicUserRead ($topic, 0);
		}

		if ($reload || empty ( self::$_instances [$user->userid][$topic] ))
		{
			$topics = self::getTopics ( $topic, $user );
			self::$_instances [$user->userid][$topic] = self::$_topics [$topic][$user->userid] = array_pop($topics);
		}

		return self::$_instances [$user->userid][$topic];
	}

	/**
	 * @param bool|array $ids
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUserRead[]
	 */
	static public function getTopics($ids = false, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($ids === false)
		{
			return isset(self::$_instances[$user->userid]) ? self::$_instances[$user->userid] : array();
		}
		elseif (!is_array ($ids))
		{
			$ids = array($ids);
		}

		// Convert topic objects into ids
		foreach ($ids as $i=>$id)
		{
			if ($id instanceof KunenaForumTopic) $ids[$i] = $id->id;
		}

		$ids = array_unique($ids);
		self::loadTopics($ids, $user);

		$list = array ();
		foreach ( $ids as $id )
		{
			if (!empty(self::$_instances [$user->userid][$id])) {
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	/**
	 * @param KunenaForumTopic $old
	 * @param KunenaForumTopic $new
	 *
	 * @return bool
	 */
	public static function move($old, $new)
	{
		// Update database
		$db = JFactory::getDBO ();
		$query ="UPDATE #__kunena_user_read SET topic_id={$db->quote($new->id)}, category_id={$db->quote($new->category_id)} WHERE topic_id={$db->quote($old->id)}";
		$db->setQuery($query);
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
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
				$instance->topic_id = $new->id;
				$instance->category_id = $new->category_id;
			}
		}

		return true;
	}

	/**
	 * @param KunenaForumTopic $old
	 * @param KunenaForumTopic $new
	 *
	 * @return bool
	 */
	public static function merge($old, $new)
	{
		$db = JFactory::getDBO ();

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
			$db->query ();

			if (KunenaError::checkDatabaseError ())
			{
				return false;
			}
		}

		// Update internal state
		self::reloadTopic($old->id);
		self::reloadTopic($new->id);

		return true;
	}

	/**
	 * @return bool
	 */
	static public function recount()
	{
		$db = JFactory::getDBO ();
		$query = "UPDATE #__kunena_user_read AS ur
			INNER JOIN #__kunena_topics AS t ON t.id=ur.topic_id
			SET ur.category_id=t.category_id";
		$db->setQuery($query);
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		return true;
	}

	/**
	 * @param int $days
	 *
	 * @return bool
	 */
	static public function purge($days = 365)
	{
		// Purge items that are older than x days (defaulting to a year)
		$db = JFactory::getDBO ();
		$timestamp = JFactory::getDate()->toUnix() - 60*60*24*$days;
		$query = "DELETE FROM #__kunena_user_read WHERE time<{$db->quote($timestamp)}";
		$db->setQuery($query);
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		return true;
	}

	// Internal functions

	/**
	 * @param array      $ids
	 * @param KunenaUser $user
	 */
	static protected function loadTopics(array $ids, KunenaUser $user)
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
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_user_read WHERE user_id={$db->quote($user->userid)} AND topic_id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('topic_id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id )
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaForumTopicUserRead ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = $instance;
			}
			else
			{
				self::$_instances [$user->userid][$id] = self::$_topics [$id][$user->userid] = new KunenaForumTopicUserRead ($id, $user->userid);
			}
		}

		unset ($results);
	}

	/**
	 * @param int $id
	 */
	static protected function reloadTopic($id)
	{
		if (empty(self::$_topics [$id]))
		{
			return;
		}

		$idlist = implode(',', array_keys(self::$_topics [$id]));
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_user_read WHERE user_id IN ({$idlist}) AND topic_id={$id}";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('user_id');
		KunenaError::checkDatabaseError ();

		// TODO: is there a bug?
		foreach ( self::$_topics [$id] as $instance )
		{
			if (isset($results[$instance->user_id]))
			{
				$instance->bind ( $results[$instance->user_id] );
				$instance->exists(true);
			}
			else
			{
				$instance->reset();
			}
		}

		unset ($results);
	}
}
