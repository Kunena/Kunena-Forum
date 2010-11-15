<?php
/**
 * @version $Id: topic.php 3759 2010-10-20 13:48:28Z mahagr $
 * Kunena Component - KunenaForumTopicUserHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
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
	static public function get($id = null, $user = null, $reload = false) {
		$user = KunenaUser::getInstance($user);
		if ($id instanceof KunenaForumTopicUser) {
			return $id;
		}
		$id = intval ( $id );
		if ($id < 1)
			return new KunenaForumTopicUser (0, $user);

		if ($reload || empty ( self::$_instances [$user->userid][$id] )) {
			self::$_instances [$user->userid][$id] = new KunenaForumTopicUser ( $id, $user );
		}

		return self::$_instances [$user->userid][$id];
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
		self::loadTopics($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$user->userid][$id])) {
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	static function recount() {
		$db = JFactory::getDBO ();

		// Clear posting information if last post has been deleted
		$query ="UPDATE #__kunena_user_topics AS ut LEFT JOIN #__kunena_messages AS m ON ut.last_post_id=m.id AND m.hold=0
					SET posts=0, last_post_id=0, owner=0
					WHERE m.id IS NULL";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Create missing user topics and update post count and last post
		$query ="INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner)
					SELECT userid AS user_id, thread AS topic_id, catid AS category_id, COUNT(*) AS posts, MAX(id) AS last_post_id, MAX(IF(parent=0,1,0)) AS owner
					FROM #__kunena_messages WHERE userid>0 AND moved=0 AND hold=0
					GROUP BY user_id, topic_id
					ON DUPLICATE KEY UPDATE posts=VALUES(posts), last_post_id=VALUES(last_post_id)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Delete entries that have default values
		$query ="DELETE FROM #__kunena_user_topics WHERE posts=0 AND favorite=0 AND subscribed=0";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;
	}

	// Internal functions

	static protected function loadTopics($ids, $user) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$user->userid][$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode($ids);
		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_user_topics WHERE user_id={$db->quote($user->userid)} AND topic_id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('topic_id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new $c ();
				$instance->bind ( $results[$id] );
				$instance->_exists = true;
				self::$_instances [$user->userid][$id] = $instance;
			} else {
				self::$_instances [$user->userid][$id] = null;
			}
		}
		unset ($results);
	}
}