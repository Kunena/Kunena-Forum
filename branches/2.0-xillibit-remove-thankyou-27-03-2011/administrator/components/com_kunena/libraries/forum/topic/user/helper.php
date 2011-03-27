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

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.forum.topic.user');

/**
 * Kunena Forum Topic User Helper Class
 */
class KunenaForumTopicUserHelper {
	// Global for every instance
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumTopicUser object
	 *
	 * @access	public
	 * @param	identifier		The user topic to load - Can be only an integer.
	 * @return	KunenaForumTopicUser		The user topic object.
	 * @since	1.7
	 */
	static public function get($topic = null, $user = null, $reload = false) {
		if ($topic instanceof KunenaForumTopic) {
			$topic = $topic->id;
		}
		$topic = intval ( $topic );
		$user = KunenaUser::getInstance($user);

		if ($topic < 1)
			return new KunenaForumTopicUser (0, $user);

		if ($reload || empty ( self::$_instances [$user->userid][$topic] )) {
			self::$_instances [$user->userid][$topic] = array_pop(KunenaForumTopicUserHelper::getTopics ( $topic, $user ));
		}

		return self::$_instances [$user->userid][$topic];
	}

	static public function getTopics($ids = false, $user=null) {
		$user = KunenaUser::getInstance($user);
		if ($ids === false) {
			return self::$_instances[$user->userid];
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadTopics($ids, $user);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$user->userid][$id])) {
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	static function move($old, $new) {
		$db = JFactory::getDBO ();
		$query ="UPDATE #__kunena_user_topics SET topic_id={$db->quote($new->id)}, category_id={$db->quote($new->category_id)} WHERE topic_id={$db->quote($old->id)}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		return true;
	}

	static function merge($old, $new) {
		$db = JFactory::getDBO ();

		// Move all user topics which do not exist in new topic
		$queries[] = "UPDATE jos_kunena_user_topics AS ut
			INNER JOIN jos_kunena_user_topics AS o ON o.user_id = ut.user_id
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

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query ();
			if (KunenaError::checkDatabaseError ())
				return false;
		}
		return true;
	}

	static function recount($topicids=false, $start=0, $end=0) {
		$db = JFactory::getDBO ();

		if (is_array($topicids)) {
			$where = 'AND m.thread IN ('.implode(',', $topicids).')';
			$where2 = 'AND ut.topic_id IN ('.implode(',', $topicids).')';
		} elseif ((int)$topicids) {
			$where = 'AND m.thread='.(int)$topicids;
			$where2 = 'AND ut.topic_id='.(int)$topicids;
		} else {
			$where = '';
			$where2 = '';
		}
		if ($end) {
			$where .= " AND (m.thread BETWEEN {$start} AND {$end})";
			$where2 .= " AND (ut.topic_id BETWEEN {$start} AND {$end})";
		}

		// Create missing user topics and update post count and last post if there are posts by that user
		$query ="INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner)
					SELECT m.userid AS user_id, m.thread AS topic_id, m.catid AS category_id, SUM(m.hold=0) AS posts, MAX(IF(m.hold=0,m.id,0)) AS last_post_id, MAX(IF(m.parent=0,1,0)) AS owner
					FROM #__kunena_messages AS m
					WHERE m.userid>0 AND m.moved=0 {$where}
					GROUP BY m.userid, m.thread
				ON DUPLICATE KEY UPDATE category_id=VALUES(category_id), posts=VALUES(posts), last_post_id=VALUES(last_post_id)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows = $db->getAffectedRows ();

		// Find user topics where last post doesn't exist and reset values in it
		$query ="UPDATE #__kunena_user_topics AS ut
			LEFT JOIN #__kunena_messages AS m ON ut.last_post_id=m.id AND m.hold=0
			SET posts=0, last_post_id=0
			WHERE m.id IS NULL {$where2}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows += $db->getAffectedRows ();

		// Delete entries that have default values
		$query ="DELETE ut FROM #__kunena_user_topics AS ut WHERE ut.posts=0 AND ut.favorite=0 AND ut.subscribed=0 {$where2}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows += $db->getAffectedRows ();
		return $rows;
	}

	// Internal functions

	static protected function loadTopics($ids, $user) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$user->userid][$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_user_topics WHERE user_id={$db->quote($user->userid)} AND topic_id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('topic_id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumTopicUser ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = $instance;
			} else {
				self::$_instances [$user->userid][$id] = new KunenaForumTopicUser ($id, $user->userid);
			}
		}
		unset ($results);
	}
}