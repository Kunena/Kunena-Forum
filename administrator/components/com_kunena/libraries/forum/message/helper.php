<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Message Helper Class
 */
abstract class KunenaForumMessageHelper {
	// Global for every instance
	protected static $_instances = array();
	protected static $_location = array();

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access	public
	 * @param	identifier		The message to load - Can be only an integer.
	 * @return	KunenaForumMessage		The message object.
	 * @since	1.7
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumMessage) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumMessage ();

		if (empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumMessage ( array('id'=>$id) );
			self::$_instances [$id]->load();
		} elseif ($reload) {
			self::$_instances [$id]->load();
		}

		return self::$_instances [$id];
	}

	static public function getMessages($ids = false, $authorise='read') {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadMessages($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			// TODO: authorisation needs topics to be loaded, make sure that they are! (performance increase)
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	static public function getMessagesByTopic($topic, $start=0, $limit=0, $ordering='ASC', $hold=0, $orderbyid = false) {
		$topic = KunenaForumTopicHelper::get($topic);
		if (!$topic->exists())
			return array();
		$total = $topic->getTotal();

		if ($start < 0)
			$start = 0;
		if ($limit < 1)
			$limit = KunenaFactory::getConfig()->messages_per_page;
		// If out of range, use last page
		if ($total < $start)
			$start = intval($total / $limit) * $limit;
		$ordering = strtoupper($ordering);
		if ($ordering != 'DESC')
			$ordering = 'ASC';

		return self::loadMessagesByTopic($topic->id, $start, $limit, $ordering, $hold, $orderbyid);
	}

	static public function getLatestMessages($categories=false, $limitstart=0, $limit=0, $params=array()) {
		$reverse = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'm.time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$mode = isset($params['mode']) ? $params['mode'] : 'recent';
		$user = isset($params['user']) ? $params['user'] : false;
		$where = isset($params['where']) ? (string) $params['where'] : '';
		$childforums = isset($params['childforums']) ? (bool) $params['childforums'] : false;

		$db = JFactory::getDBO();
		// FIXME: use right config setting
		if ($limit < 1 && empty($params['nolimit'])) $limit = KunenaFactory::getConfig ()->threads_per_page;

		$cquery = new KunenaDatabaseQuery();
		$cquery->select('COUNT(*)')
			->from('#__kunena_messages AS m')
			->innerJoin('#__kunena_messages_text AS t ON m.id = t.mesid')
			->where('m.moved=0'); // TODO: remove column

		$rquery = new KunenaDatabaseQuery();
		$rquery->select('m.*, t.message')
			->from('#__kunena_messages AS m')
			->innerJoin('#__kunena_messages_text AS t ON m.id = t.mesid')
			->where('m.moved=0') // TODO: remove column
			->order($orderby);

		$authorise = 'read';
		$hold = 'm.hold=0';
		$userfield = 'm.userid';
		switch ($mode) {
			case 'unapproved':
				$authorise = 'approve';
				$hold = "m.hold=1";
				break;
			case 'deleted':
				$authorise = 'undelete';
				$hold = "m.hold>=2";
				break;
			case 'mythanks':
				$userfield = 'th.userid';
				$cquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				$rquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				break;
			case 'thankyou':
				$userfield = 'th.targetuserid';
				$cquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				$rquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				break;
			case 'recent':
			default:
		}
		if (is_array($categories) && in_array(0, $categories)) {
			$categories = false;
		}
		$categories = KunenaForumCategoryHelper::getCategories($categories, $reverse, 'topic.'.$authorise);
		if ($childforums) {
			$categories += KunenaForumCategoryHelper::getChildren($categories, -1, false, array('action'=>'topic.'.$authorise));
		}
		$catlist = array();
		foreach ($categories as $category) {
			$catlist += $category->getChannels();
		}
		if (empty($catlist)) return array(0, array());
		$allowed = implode(',', array_keys($catlist));
		$cquery->where("m.catid IN ({$allowed})");
		$rquery->where("m.catid IN ({$allowed})");

		$cquery->where($hold);
		$rquery->where($hold);
		if ($user) {
			$cquery->where("{$userfield}={$db->Quote($user)}");
			$rquery->where("{$userfield}={$db->Quote($user)}");
		}

		// Negative time means no time
		if ($starttime == 0) {
			$starttime = KunenaFactory::getSession ()->lasttime;
		} elseif ($starttime > 0) {
			$starttime = JFactory::getDate ()->toUnix () - ($starttime * 3600);
		}
		if ($starttime > 0) {
			$cquery->where("m.time>{$db->Quote($starttime)}");
			$rquery->where("m.time>{$db->Quote($starttime)}");
		}
		if ($where) {
			$cquery->where($where);
			$rquery->where($where);
		}

		$db->setQuery ( $cquery );
		$total = ( int ) $db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$total) return array(0, array());

		// If out of range, use last page
		if ($limit && $total < $limitstart)
			$limitstart = intval($total / $limit) * $limit;

		$db->setQuery ( $rquery, $limitstart, $limit );
		$results = $db->loadAssocList ();
		if (KunenaError::checkDatabaseError()) return array(0, array());

		$messages = array();
		foreach ( $results as $result ) {
			$instance = new KunenaForumMessage ($result);
			$instance->exists(true);
			self::$_instances [$instance->id] = $instance;
			$messages[$instance->id] = $instance;
		}
		unset ($results);
		return array($total, $messages);
	}

	public static function getLocation($mesid, $direction = null, $hold = null) {
		if (is_null($direction)) $direction = KunenaUserHelper::getMyself()->getMessageOrdering();
		if (!$hold) {
			$me = KunenaUserHelper::getMyself();
			$mes_instance = KunenaForumMessageHelper::get($mesid);
			$hold = KunenaAccess::getInstance()->getAllowedHold($me->userid, $mes_instance->catid, false);
		}
		if (!isset(self::$_location [$mesid])) {
			self::loadLocation(array($mesid));
		}
		$location = self::$_location [$mesid];
		$count = 0;
		foreach ($location->hold as $meshold=>$values) {
			if (isset($hold[$meshold])) {
				$count += $values[$direction == 'asc' ? 'before' : 'after'];
				if ($direction == 'both') $count += $values['before'];
			}
		}
		return $count;
	}

	public static function loadLocation($mesids) {
		// NOTE: if you already know the location using this code just takes resources
		if (!is_array($mesids)) $mesids = explode ( ',', $mesids );
		$list = array();
		$ids = array();
		foreach ($mesids as $id) {
			if ($id instanceof KunenaForumMessage) {
				$id = $id->id;
			} else {
				$id = (int) $id;
			}
			if (!isset(self::$_location [$id])) {
				$ids[$id] = $id;
				self::$_location [$id] = new stdClass();
				self::$_location [$id]->hold = array('before'=>0, 'after'=>0);
			}
		}
		if (empty($ids))
			return;

		$idlist = implode ( ',', $ids );
		$db = JFactory::getDBO ();
		$db->setQuery ( "SELECT m.id, mm.hold, m.catid AS category_id, m.thread AS topic_id,
				SUM(mm.time<m.time) AS before_count,
				SUM(mm.time>m.time) AS after_count
			FROM #__kunena_messages AS m
			INNER JOIN #__kunena_messages AS mm ON m.thread=mm.thread
			WHERE m.id IN ({$idlist})
			GROUP BY m.id, mm.hold" );
		$results = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError();

		foreach ($results as $result) {
			$instance = self::$_location [$result->id];
			if (!isset($instance->id)) {
				$instance->id = $result->id;
				$instance->category_id = $result->category_id;
				$instance->topic_id = $result->topic_id;
				self::$_location [$instance->id] = $instance;
			}
			$instance->hold[$result->hold] = array('before'=>$result->before_count, 'after'=>$result->after_count);
		}
	}

	public static function cleanup() {
		self::$_instances = array();
		self::$_location = array();
	}

	public static function recount($topicids=false) {
		$db = JFactory::getDBO ();

		if (is_array($topicids)) {
			$where = 'WHERE m.thread IN ('.implode(',', $topicids).')';
		} elseif ((int)$topicids) {
			$where = 'WHERE m.thread='.(int)$topicids;
		} else {
			$where = '';
		}

		// Update catid in all messages
		$query ="UPDATE #__kunena_messages AS m
			INNER JOIN #__kunena_topics AS tt ON tt.id=m.thread
			SET m.catid=tt.category_id {$where}";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		return $db->getAffectedRows ();
	}

	// Internal functions

	static protected function loadMessages($ids) {
		foreach ($ids as $i=>$id) {
			$id = intval($id);
			if (!$id || isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT m.*, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumMessage ($results[$id]);
				$instance->exists(true);
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}

	static protected function loadMessagesByTopic($topic_id, $start=0, $limit=0, $ordering='ASC', $hold=0, $orderbyid = false) {
		$db = JFactory::getDBO ();
		$query = "SELECT m.*, t.message
			FROM #__kunena_messages AS m
			INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
			WHERE m.thread={$db->quote($topic_id)} AND m.hold IN ({$hold}) ORDER BY m.time {$ordering}";
		$db->setQuery ( $query, $start, $limit );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		$location = ($orderbyid || $ordering == 'ASC') ? $start : KunenaForumTopicHelper::get($topic_id)->getTotal($hold) - $start - 1;
		$order = ($ordering == 'ASC') ? 1 : -1;
		$list = array();
		foreach ( $results as $id=>$result ) {
			$instance = new KunenaForumMessage ($result);
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			$list[$orderbyid ? $id : $location] = $instance;
			$location += $order;
		}
		unset ($results);
		return $list;
	}
}
