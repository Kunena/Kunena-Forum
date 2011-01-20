<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForumMessageHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.forum.message');
kimport ('kunena.forum.topic.helper');

/**
 * Kunena Forum Message Helper Class
 */
class KunenaForumMessageHelper {
	// Global for every instance
	protected static $_instances = array();

	private function __construct() {}

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

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumMessage ( $id );
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
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	static public function getMessagesByTopic($topic, $start=0, $limit=0, $ordering='ASC', $hold=0) {
		$topic = KunenaForumTopicHelper::get($topic);
		if (!$topic->exists())
			return array();

		if ($start < 0)
			$start = 0;
		if ($limit < 1)
			$limit = KunenaFactory::getConfig()->messages_per_page;
		$ordering = strtoupper($ordering);
		if ($ordering != 'DESC')
			$ordering = 'ASC';

		return self::loadMessagesByTopic($topic->id, $start, $limit, $ordering, $hold);
	}

	// Internal functions

	static protected function loadMessages($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode($ids);
		$db = JFactory::getDBO ();
		$query = "SELECT m.*, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumMessage ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}

	static protected function loadMessagesByTopic($topic_id, $start=0, $limit=0, $ordering='ASC', $hold=0) {
		$db = JFactory::getDBO ();
		$query = "SELECT m.*, t.message
			FROM #__kunena_messages AS m
			INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
			WHERE m.thread={$db->quote($topic_id)} AND m.hold IN ({$hold}) ORDER BY m.time {$ordering}";
		$db->setQuery ( $query, $start, $limit );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		$list = array();
		foreach ( $results as $id=>$result ) {
			$instance = new KunenaForumMessage ();
			$instance->bind ( $result );
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			$list[$start++] = $instance;
		}
		unset ($results);
		return $list;
	}
}