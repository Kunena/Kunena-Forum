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
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic');
kimport ('kunena.forum.topic.user.helper');
kimport ('kunena.keyword.helper');

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
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
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

	static public function getKeywords($ids=false, $user=false) {
		if ($ids === false) {
			$ids = array_keys(self::$_instances);
		}
		return KunenaKeywordHelper::getByTopics($ids, $user);
	}

	static public function getLatestTopics($categories=false, $limitstart=0, $limit=0, $params=array()) {
		$db = JFactory::getDBO ();
		if ($limit < 1) $limit = KunenaFactory::getConfig ()->threads_per_page;

		$reverse = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$user = isset($params['user']) ? KunenaUserHelper::get($params['user']) : KunenaUserHelper::getMyself();
		$hold = isset($params['hold']) ? (string) $params['hold'] : 0;
		$moved = isset($params['moved']) ? (string) $params['moved'] : 0;
		$where = isset($params['where']) ? (string) $params['where'] : '';

		if (strstr('ut.last_', $orderby)) {
			$post_time_field = 'ut.last_post_time';
		} elseif (strstr('tt.first_', $orderby)) {
			$post_time_field = 'tt.first_post_time';
		} else {
			$post_time_field = 'tt.last_post_time';
		}

		$categories = KunenaForumCategoryHelper::getCategories($categories, $reverse);
		$catlist = array();
		foreach ($categories as $category) {
			$catlist += $category->getChannels();
		}
		if (empty($catlist)) return array(0, array());
		$catlist = implode(',', array_keys($catlist));

		$whereuser = array();
		if (!empty($params['started'])) $whereuser[] = 'ut.owner=1';
		if (!empty($params['replied'])) $whereuser[] = '(ut.owner=0 AND ut.posts>0)';
		if (!empty($params['posted'])) $whereuser[] = 'ut.posts>0';
		if (!empty($params['favorited'])) $whereuser[] = 'ut.favorite=1';
		if (!empty($params['subscribed'])) $whereuser[] = 'ut.subscribed=1';

		$kwids = array();
		if (!empty($params['keywords'])) {
			$keywords = KunenaKeywordHelper::getByKeywords($params['keywords']);
			foreach ($keywords as $keyword) {
				$kwids[] = $keyword->$id;
			}
			$kwids = implode(',', $kwids);
		}
		//TODO: add support for keywords (example:)
		/* SELECT tt.*, COUNT(*) AS score FROM #__kunena_keywords_map AS km
		INNER JOIN #__kunena_topics` AS tt ON km.topic_id=tt.id
		WHERE km.keyword_id IN (1,2) AND km.user_id IN (0,62)
		GROUP BY topic_id
		ORDER BY score DESC, tt.last_post_time DESC */

		$wheretime = ($starttime ? " AND {$post_time_field}>{$db->Quote($starttime)}" : '');
		$whereuser = ($whereuser ? " AND ut.user_id={$db->Quote($user->userid)} AND (".implode(' OR ',$whereuser).')' : '');
		$where = "tt.hold IN ({$hold}) AND tt.category_id IN ({$catlist}) {$whereuser} {$wheretime} {$where}";
		if (!$moved) $where .= " AND tt.moved_id='0'";

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
		$results = (array) $db->loadAssocList ('id');
		if (KunenaError::checkDatabaseError()) return array(0, array());

		$topics = array();
		foreach ( $results as $id=>$result ) {
			$instance = new KunenaForumTopic ();
			$instance->bind ( $result );
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			$topics[$id] = $instance;
		}
		unset ($results);
		return array($total, $topics);
	}

	static function recount($ids=false, $start=0, $end=0) {
		$db = JFactory::getDBO ();

		if (is_array($ids)) {
			$threads = 'm.thread IN ('.implode(',', $ids).')';
		} elseif ((int)$ids) {
			$threads = 'm.thread='.(int)$ids;
		} else {
			$threads = '';
		}
		$where = '';
		if ($threads) $where = "AND {$threads}";
		if ($end) {
			$where .= " AND (tt.id BETWEEN {$start} AND {$end})";
		}

		// Mark all empty topics as deleted
		$query ="UPDATE #__kunena_topics AS tt
			LEFT JOIN #__kunena_messages AS m ON m.thread=tt.id AND tt.hold=m.hold
			SET tt.hold = 3,
				tt.posts = 0,
				tt.attachments = 0,
				tt.first_post_id = 0,
				tt.first_post_time = 0,
				tt.first_post_userid = 0,
				tt.first_post_message = '',
				tt.first_post_guest_name = '',
				tt.last_post_id = 0,
				tt.last_post_time = 0,
				tt.last_post_userid = 0,
				tt.last_post_message = '',
				tt.last_post_guest_name = ''
			WHERE tt.moved_id=0 AND tt.hold!=3 AND m.id IS NULL {$where}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows = $db->getAffectedRows ();

		// Recount total posts, total attachments and update first & last post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (
				SELECT m.thread, m.hold, COUNT(DISTINCT m.id) AS posts, COUNT(a.id) as attachments, MIN(m.time) AS mintime, MAX(m.time) AS maxtime
				FROM #__kunena_messages AS m
				LEFT JOIN #__kunena_attachments AS a ON m.id=a.mesid
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
				tt.first_post_guest_name = IF(mmin.userid>0,null,mmin.name),
				tt.last_post_id = mmax.id,
				tt.last_post_time = mmax.time,
				tt.last_post_userid = mmax.userid,
				tt.last_post_message = tmax.message,
				tt.last_post_guest_name = IF(mmax.userid>0,null,mmax.name)
			WHERE moved_id=0 {$where}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows += $db->getAffectedRows ();
		return $rows;
	}

	static public function fetchNewStatus($topics, $user = null) {
		if (empty($topics))
			return array();

		// TODO: Need to convert to topics table design
		$user = KunenaUserHelper::get($user);
		if ($user->userid) {
			$idstr = implode ( ",", array_keys ( $topics ) );
			$readlist = KunenaFactory::getSession ()->readtopics;
			$prevCheck = KunenaFactory::getSession ()->lasttime;
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT thread AS id, MIN(id) AS lastread, SUM(1) AS unread
				FROM #__kunena_messages
				WHERE hold=0 AND moved=0 AND thread NOT IN ({$readlist}) AND thread IN ({$idstr}) AND time>{$db->Quote($prevCheck)}
				GROUP BY thread" );
			$topiclist = (array) $db->loadObjectList ('id');
			KunenaError::checkDatabaseError ();
		}
		$list = array();
		foreach ( $topics as $topic ) {
			if (!isset($topiclist[$topic->id])) {
				$topic->lastread = 0;
				$topic->unread = 0;
			} else {
				$topic->lastread = $topiclist[$topic->id]->lastread;
				$topic->unread = $topiclist[$topic->id]->unread;
				$list[$topic->id] = $topic->lastread;
			}
		}
		return $list;
	}

	// Internal functions

	static protected function loadTopics($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]) || !is_numeric($id))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_topics WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumTopic ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}
}