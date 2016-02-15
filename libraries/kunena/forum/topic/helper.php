<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumTopicHelper
 */
abstract class KunenaForumTopicHelper
{
	/**
	 * @var KunenaForumTopic[]
	 */
	protected static $_instances = array();

	/**
	 * Returns KunenaForumTopic object.
	 *
	 * @param int  $identifier	The topic to load - Can be only an integer.
	 * @param bool $reload
	 *
	 * @return KunenaForumTopic
	 */
	static public function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof KunenaForumTopic)
		{
			return $identifier;
		}

		$id = (int) $identifier;

		if ($id < 1)
		{
			return new KunenaForumTopic;
		}

		if (empty(self::$_instances[$id]))
		{
			$instance = new KunenaForumTopic;
			// Only load topics which haven't been preloaded before (including missing ones).
			$instance->load(!array_key_exists($id, self::$_instances) ? $id : null);
			$instance->id = $id;
			self::$_instances[$id] = $instance;
		}
		elseif ($reload)
		{
			self::$_instances[$id]->load();
		}

		return self::$_instances[$id];
	}

	/**
	 * @param mixed $ids
	 * @param bool  $value
	 * @param mixed $user
	 *
	 * @return int
	 */
	public static function subscribe($ids, $value = true, $user = null)
	{
		// Pre-load all items
		KunenaForumTopicUserHelper::getTopics($ids, $user);
		$count = 0;
		foreach ($ids as $id)
		{
			$usertopic = KunenaForumTopicUserHelper::get($id, $user);

			if ($usertopic->subscribed != (int)$value)
			{
				$count++;
			}

			$usertopic->subscribed = (int)$value;
			$usertopic->save();
		}

		return $count;
	}

	/**
	 * @param mixed $ids
	 * @param bool  $value
	 * @param mixed $user
	 *
	 * @return int
	 */
	public static function favorite($ids, $value = true, $user = null)
	{
		// Pre-load all items
		KunenaForumTopicUserHelper::getTopics($ids, $user);
		$count = 0;

		foreach ($ids as $id)
		{
			$usertopic = KunenaForumTopicUserHelper::get($id, $user);

			if ($usertopic->favorite != (int)$value)
			{
				$count++;
			}

			$usertopic->favorite = (int)$value;
			$usertopic->save();
		}

		return $count;
	}

	/**
	 * @param mixed  $ids
	 * @param string $authorise
	 *
	 * @return KunenaForumTopic[]
	 */
	static public function getTopics($ids = false, $authorise='read')
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($ids === false)
		{
			return self::$_instances;
		}
		elseif (is_array ($ids) )
		{
			$ids = array_unique($ids);
		}
		else
		{
			$ids = array($ids);
		}

		self::loadTopics($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
				$list [$id] = self::$_instances [$id];
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $list;
	}

	/**
	 * @param mixed $ids
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUser[]
	 */
	static public function getUserTopics($ids = false, $user = null)
	{
		if ($ids === false)
		{
			$ids = array_keys(self::$_instances);
		}

		return KunenaForumTopicUserHelper::getTopics($ids, $user);
	}

	/**
	 * @param mixed $ids
	 * @param mixed $user
	 *
	 * @return array
	 */
	static public function getKeywords($ids = false, $user = false)
	{
		if ($ids === false)
		{
			$ids = array_keys(self::$_instances);
		}

		return KunenaKeywordHelper::getByTopics($ids, $user);
	}

	/**
	 * @param mixed $categories
	 * @param int   $limitstart
	 * @param int   $limit
	 * @param array $params
	 *
	 * @return array|KunenaForumTopic[]
	 */
	static public function getLatestTopics($categories=false, $limitstart=0, $limit=0, $params=array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$db = JFactory::getDBO ();
		$config = KunenaFactory::getConfig ();

		if ($limit < 1 && empty($params['nolimit']))
		{
			$limit = $config->threads_per_page;
		}

		$reverse = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$user = isset($params['user']) ? KunenaUserHelper::get($params['user']) : KunenaUserHelper::getMyself();
		$hold = isset($params['hold']) ? (string) $params['hold'] : 0;
		$moved = isset($params['moved']) ? (string) $params['moved'] : 0;
		$where = isset($params['where']) ? (string) $params['where'] : '';

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

		$categories = KunenaForumCategoryHelper::getCategories($categories, $reverse);
		$catlist = array();

		foreach ($categories as $category)
		{
			$catlist += $category->getChannels();
		}

		if (empty($catlist))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return array(0, array());
		}

		$catlist = implode(',', array_keys($catlist));

		$whereuser = array();
		if (!empty($params['started'])) $whereuser[] = 'ut.owner=1';
		if (!empty($params['replied'])) $whereuser[] = '(ut.owner=0 AND ut.posts>0)';
		if (!empty($params['posted'])) $whereuser[] = 'ut.posts>0';
		if (!empty($params['favorited'])) $whereuser[] = 'ut.favorite=1';
		if (!empty($params['subscribed'])) $whereuser[] = 'ut.subscribed=1';

		if ($config->keywords || $config->userkeywords)
		{
			$kwids = array();

			if (!empty($params['keywords']))
			{
				$keywords = KunenaKeywordHelper::getByKeywords($params['keywords']);
				foreach ($keywords as $keyword) {
					$kwids[] = $keyword->id;
				}
				$kwids = implode(',', $kwids);
			}

			//TODO: add support for keywords (example:)
			/* SELECT tt.*, COUNT(*) AS score FROM #__kunena_keywords_map AS km
			INNER JOIN #__kunena_topics` AS tt ON km.topic_id=tt.id
			WHERE km.keyword_id IN (1,2) AND km.user_id IN (0,62)
			GROUP BY topic_id
			ORDER BY score DESC, tt.last_post_time DESC */
		}

		$wheretime = ($starttime ? " AND {$post_time_field}>{$db->Quote($starttime)}" : '');
		$whereuser = ($whereuser ? " AND ut.user_id={$db->Quote($user->userid)} AND (".implode(' OR ',$whereuser).')' : '');
		$where = "tt.hold IN ({$hold}) AND tt.category_id IN ({$catlist}) {$whereuser} {$wheretime} {$where}";

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

		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();

		if (KunenaError::checkDatabaseError() || !$total)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return array(0, array());
		}

		// If out of range, use last page
		if ($limit && $total < $limitstart)
		{
			$limitstart = intval($total / $limit) * $limit;
		}

		// Get items
		if ($whereuser)
		{
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_user_topics AS ut
				INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id
				WHERE {$where} ORDER BY {$orderby}";
		}
		else
		{
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_topics AS tt
				LEFT JOIN #__kunena_user_topics AS ut ON tt.id=ut.topic_id AND ut.user_id={$db->Quote($user->userid)}
				WHERE {$where} ORDER BY {$orderby}";
		}

		$db->setQuery ( $query, $limitstart, $limit );
		$results = (array) $db->loadAssocList ('id');

		if (KunenaError::checkDatabaseError())
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

			return array(0, array());
		}

		$topics = array();

		foreach ( $results as $id=>$result )
		{
			$instance = new KunenaForumTopic ($result);
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			$topics[$id] = $instance;
		}

		unset ($results);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return array($total, $topics);
	}

	/**
	 * Method to delete selected topics.
	 *
	 * @param array|int $ids
	 *
	 * @return int	Count of deleted topics.
	 */
	public static function delete($ids)
	{
		if (empty($ids))
		{
			return 0;
		}

		if (is_array($ids))
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
		// TODO: delete keywords
		// Delete topics
		$queries[] = "DELETE FROM #__kunena_topics WHERE id IN ({$idlist})";

		$db = JFactory::getDBO ();
		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		return $db->getAffectedRows();
	}

	/**
	 * Method to trash topics. They will be marked as deleted, but still exist in database.
	 *
	 * @param array|int $ids
	 *
	 * @return int	Count of trashed topics.
	 */
	public static function trash($ids)
	{
		if (empty($ids))
		{
			return 0;
		}

		if (is_array($ids))
		{
			$idlist = implode(',', $ids);
		}
		else
		{
			$idlist = (int) $ids;
		}

		$db = JFactory::getDBO ();
		$queries[] = "UPDATE #__kunena_messages SET hold='2' WHERE thread IN ({$idlist})";
		$queries[] = "UPDATE #__kunena_topics SET hold='2' WHERE id IN ({$idlist})";

		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		return $db->getAffectedRows();
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 */
	public static function cleanup()
	{
		self::$_instances = array();
	}

	/**
	 * @param mixed $ids
	 * @param int  $start
	 * @param int  $end
	 *
	 * @return bool|int
	 */
	public static function recount($ids = false, $start = 0, $end = 0)
	{
		$db = JFactory::getDBO ();

		if (is_array($ids))
		{
			$threads = 'AND m.thread IN ('.implode(',', $ids).')';
		}
		elseif ((int)$ids)
		{
			$threads = 'AND m.thread='.(int)$ids;
		}
		else
		{
			$threads = '';
		}

		if ($end) {
			if ($start < 1) $start = 1;
			$topics = " AND (tt.id BETWEEN {$start} AND {$end})";
		} else {
			$topics = '';
		}

		// Mark all empty topics as deleted
		$query ="UPDATE #__kunena_topics AS tt
			LEFT JOIN #__kunena_messages AS m ON m.thread=tt.id AND tt.hold=m.hold
			SET tt.hold = 4,
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
			WHERE tt.moved_id=0 AND tt.hold!=4 AND m.id IS NULL {$topics} {$threads}";
		$db->setQuery($query);
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		$rows = $db->getAffectedRows ();

		// Find out if there are deleted topics with visible replies.
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (
				SELECT m.thread, MIN(m.hold) AS hold FROM #__kunena_messages AS m WHERE m.hold IN (0,1) {$threads} GROUP BY thread
			) AS c ON tt.id=c.thread
			SET tt.hold = c.hold
			WHERE tt.moved_id=0 {$topics}";
		$db->setQuery($query);
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		$rows += $db->getAffectedRows ();

		// Recount total posts, total attachments and update first & last post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
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
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		$rows += $db->getAffectedRows ();

		return $rows;
	}

	/**
	 * @param KunenaForumTopic[] $topics
	 * @param mixed            $user
	 *
	 * @return array
	 */
	static public function fetchNewStatus(array $topics, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->shownew || empty($topics) || !$user->exists())
		{
			return array();
		}

		$session = KunenaFactory::getSession ();

		$ids = array();

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
			$idstr = implode ( ",", $ids );

			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT m.thread AS id, MIN(m.id) AS lastread, SUM(1) AS unread
				FROM #__kunena_messages AS m
				LEFT JOIN #__kunena_user_read AS ur ON ur.topic_id=m.thread AND user_id={$db->Quote($user->userid)}
				WHERE m.hold=0 AND m.moved=0 AND m.thread IN ({$idstr}) AND m.time>{$db->Quote($session->getAllReadTime())} AND (ur.time IS NULL OR m.time>ur.time)
				GROUP BY thread" );
			$topiclist = (array) $db->loadObjectList ('id');
			KunenaError::checkDatabaseError ();
		}

		$list = array();
		foreach ( $topics as $topic )
		{
			if (isset($topiclist[$topic->id]))
			{
				$topic->lastread = $topiclist[$topic->id]->lastread;
				$topic->unread = $topiclist[$topic->id]->unread;
			}
			else
			{
				$topic->lastread = $topic->last_post_id;
				$topic->unread = 0;
			}

			$list[$topic->id] = $topic->lastread;
		}

		return $list;
	}

	// Internal functions

	/**
	 * @param array $ids
	 */
	static protected function loadTopics(array $ids)
	{
		foreach ($ids as $i => $id)
		{
			$id = intval($id);

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
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_topics WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id )
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaForumTopic ($results[$id]);
 				$instance->exists(true);
 				self::$_instances [$id] = $instance;
			}
			else
			{
				self::$_instances [$id] = null;
			}
		}

		unset ($results);
	}
}
