<?php
/**
 * @version $Id: topic.php 3759 2010-10-20 13:48:28Z mahagr $
 * Kunena Component - KunenaForumTopicHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.user');
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic');
kimport ('kunena.forum.topic.user.helper');

/**
 * Kunena Forum Topic Helper Class
 */
class KunenaForumTopicHelper {
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumTopic object
	 *
	 * @access	public
	 * @param	identifier		The topic to load - Can be only an integer.
	 * @return	KunenaForumTopic		The topic object.
	 * @since	1.7
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumTopic) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumTopic ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumTopic ( $id );
		}

		return self::$_instances [$id];
	}

	public function subscribe($ids, $value=1, $user=null) {
		// Pre-load all items
		$usertopics = KunenaForumTopicUserHelper::getTopics($ids, $user);
		$count = 0;
		foreach ($ids as $id) {
			$usertopic = KunenaForumTopicUserHelper::get($id, $user);
			if ($usertopic->subscribed != (int)$value) $count++;
			$usertopic->subscribed = (int)$value;
			$usertopic->save();
		}
		return $count;
	}

	public function favorite($ids, $value=1, $user=null) {
		// Pre-load all items
		$usertopics = KunenaForumTopicUserHelper::getTopics($ids, $user);
		$count = 0;
		foreach ($ids as $id) {
			$usertopic = KunenaForumTopicUserHelper::get($id, $user);
			if ($usertopic->favorite != (int)$value) $count++;
			$usertopic->favorite = (int)$value;
			$usertopic->save();
		}
		return $count;
	}

	static public function getTopics($ids = false, $authorise='read') {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadTopics($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise)) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	static public function getUserTopics($ids = false, $user=null) {
		if ($ids === false) {
			$ids = array_keys(self::$_instances);
		}
		return KunenaForumTopicUserHelper::getTopics($ids, $user);
	}

	static public function getLatestTopics($categories=false, $limitstart=0, $limit=0, $params=array()) {
		$db = JFactory::getDBO ();
		if ($limit < 1) $limit = KunenaFactory::getConfig ()->threads_per_page;

		$reverse = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$user = isset($params['user']) ? KunenaUser::getInstance($params['user']) : KunenaUser::getInstance();
		$hold = isset($params['hold']) ? (string) $params['hold'] : 0;
		$where = isset($params['where']) ? (string) $params['where'] : '';

		if (strstr('ut.last_', $orderby)) {
			$post_time_field = 'ut.last_post_time';
		} elseif (strstr('tt.first_', $orderby)) {
			$post_time_field = 'tt.first_post_time';
		} else {
			$post_time_field = 'tt.last_post_time';
		}

		$whereuser = array();
		if (!empty($params['started'])) $whereuser[] = 'ut.owner=1';
		if (!empty($params['replied'])) $whereuser[] = '(ut.owner=0 AND ut.posts>0)';
		if (!empty($params['posted'])) $whereuser[] = 'ut.posts>0';
		if (!empty($params['favorited'])) $whereuser[] = 'ut.favorite=1';
		if (!empty($params['subscriped'])) $whereuser[] = 'ut.subscribed=1';

		$catlist = implode(',', array_keys(KunenaForumCategoryHelper::getCategories($categories, $reverse)));

		$wheretime = ($starttime ? " AND {$post_time_field}>{$db->Quote($starttime)}" : '');
		$whereuser = ($whereuser ? " AND ut.user_id={$db->Quote($user->userid)} AND (".implode(' OR ',$whereuser).')' : '');
		$where = "tt.moved_id='0' AND tt.hold IN ({$hold}) AND tt.category_id IN ({$catlist}) {$whereuser} {$wheretime} {$where}";

		// Get total count
		if ($whereuser)
			$query = "SELECT COUNT(*) FROM #__kunena_user_topics AS ut INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id WHERE {$where}";
		else
			$query = "SELECT COUNT(*) FROM #__kunena_topics AS tt WHERE {$where}";
		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$total) return array(0, array());

		// Get items
		if ($whereuser)
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_user_topics AS ut
				INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id
				WHERE {$where} ORDER BY {$orderby}";
		else
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_topics AS tt
				LEFT JOIN #__kunena_user_topics AS ut ON tt.id=ut.topic_id AND ut.user_id={$db->Quote($user->userid)}
				WHERE {$where} ORDER BY {$orderby}";
		$db->setQuery ( $query, $limitstart, $limit );
		$topics = ( array ) $db->loadObjectList ('id');
		if (KunenaError::checkDatabaseError()) return array(0, array());

		return array($total, $topics);
	}

	static function recount() {
		$db = JFactory::getDBO ();

		// Recount total posts, total attachments
		$query ="UPDATE jos_kunena_topics AS tt
			INNER JOIN (SELECT m.thread, COUNT(DISTINCT m.id) AS posts, COUNT(a.id) as attachments
				FROM jos_kunena_messages AS m
				LEFT JOIN jos_kunena_attachments AS a ON m.id=a.mesid
				WHERE m.hold=0
				GROUP BY m.thread) AS t ON t.thread=tt.id
			SET tt.posts=t.posts,
				tt.attachments=t.attachments";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update first post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (SELECT thread, MIN(time) AS time FROM #__kunena_messages WHERE hold=0 GROUP BY thread) AS l ON tt.id=l.thread
			INNER JOIN #__kunena_messages AS m ON l.thread=m.thread AND m.time=l.time
			INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
			SET tt.first_post_id = m.id,
				tt.first_post_time = m.time,
				tt.first_post_userid = m.userid,
				tt.first_post_message = t.message,
				tt.first_post_guest_name = IF(m.userid>0,null,m.name)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update last post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (SELECT thread, MAX(time) AS time FROM #__kunena_messages WHERE hold=0 GROUP BY thread) AS l ON tt.id=l.thread
			INNER JOIN #__kunena_messages AS m ON l.thread=m.thread AND m.time=l.time
			INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
			SET tt.last_post_id = m.id,
				tt.last_post_time = m.time,
				tt.last_post_userid = m.userid,
				tt.last_post_message = t.message,
				tt.last_post_guest_name = IF(m.userid>0,null,m.name)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;
	}

	// Internal functions

	static protected function loadTopics($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode($ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_topics WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumTopic ();
				$instance->bind ( $results[$id] );
				$instance->_exists = true;
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}
}