<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Keyword
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Keyword Helper Class
 */
class KunenaKeywordHelper {
	/**
	 * @var array|KunenaKeyword[]
	 */
	protected static $_instances = array();
	protected static $_topics = array();
	protected static $_users = array();

	private function __construct() {}

	/**
	 * Returns KunenaKeyword object
	 *
	 * @param	mixed	$identifier		The keywords to load - by Id
	 * @param	bool	$reload
	 * @return	KunenaKeyword		The keyword object.
	 * @since	1.7
	 */
	static public function get($identifier, $reload = false) {
		if ($identifier instanceof KunenaKeyword) {
			return $identifier;
		}
		$keyword = self::cleanKeyword($identifier);
		if (!$keyword) return false;
		if ($reload || empty ( self::$_instances [$keyword] )) {
			self::loadKeywords(array($keyword), true);
		}

		return self::$_instances [$keyword];
	}

	static public function cleanKeyword($keyword) {
		// Keyword must always be a string
		if (!is_string($keyword))
			return '';

		// Do not allow starting or trailing space, odd/repeating space characters, double quotes
		return preg_replace(array('/\s+/u', '/[",]/u'), array(' ', ''), trim($keyword));
	}

	static public function cleanKeywords($keywords, $glue=null) {
		if (is_string($keywords)) {
			// By default accept strings like this: keyword, "My tag", many different tags
			if ($glue === null) $glue = '[\s,]*"([^"]+)"[\s,]*|[\s,]+';
			if ($glue) {
				$keywords = preg_split('/'.$glue.'/u', $keywords, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			} else {
				$keywords = array($keywords);
			}
		}
		if (is_array($keywords)) {
			foreach ($keywords as $id=>&$keyword) {
				$keyword = self::cleanKeyword($keyword);
				if (empty($keyword)) {
					// Remove empty keywords
					unset($keywords[$id]);
				}
			}
		} else {
			$keywords = array();
		}
		return $keywords;
	}

	static public function getByKeywords($keywords, $glue=null) {
		$keywords = self::cleanKeywords($keywords, $glue);
		if (empty($keywords))
			return array();

		self::loadKeywords($keywords);

		$list = array ();
		foreach ( $keywords as $keyword ) {
			if (!empty(self::$_instances [$keyword])) {
				$list [$keyword] = self::$_instances [$keyword];
			}
		}
		return $list;
	}

	static public function getByTopics($ids = false, $userid=0) {
		$userid = (int) $userid;
		if ($ids === false) {
			$ids = array_keys(KunenaForumTopicHelper::getTopics());
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
			$single = true;
		}
		self::loadTopics($ids, $userid);

		$list = array ();
		if (isset($single)) {
			$list = !empty(self::$_users [$userid][end($ids)]) ? self::$_users [$userid][end($ids)] : array();
		} else {
			foreach ( $ids as $id ) {
				if (!empty(self::$_users [$userid][$id])) {
					$list [$id] = self::$_users [$userid][$id];
				}
			}
		}
		return $list;
	}

	static public function setTopicKeywords($keywords, $topicid, $userid, $glue=null) {
		// Load keywords from the topic
		if (!isset(self::$_topics [$topicid][$userid])) {
			self::loadTopics(array($topicid), $userid);
		}
		$oldkeywords = isset(self::$_topics [$topicid][$userid]) ? self::$_topics [$topicid][$userid] : array();

		// Load new keywords missing from the topic
		$keywords = self::cleanKeywords($keywords, $glue);
		self::loadKeywords($keywords);

		$keywords = array_flip($keywords);
		$dellist = array_diff_key ( $oldkeywords, $keywords );
		$addlist = array_diff_key ( $keywords, $oldkeywords );
		foreach ($dellist as $keyword=>$i) {
			$instance = self::$_instances [$keyword];
			$instance->delTopic($topicid, $userid);
		}
		foreach ($addlist as $keyword=>$i) {
			$instance = self::$_instances [$keyword];
			$instance->addTopic($topicid, $userid);
		}
		return $keywords;
	}

	static function recount($ids=false) {
	}

	// Internal functions

	static protected function loadKeywords($keywords, $reload=false) {
		$db = JFactory::getDBO ();
		foreach ($keywords as $keyword) {
			if ($reload || !isset(self::$_instances [$keyword])) {
				self::$_instances [$keyword] = new KunenaKeyword ($keyword);
				$list[] = $db->quote($keyword);
			}
		}
		if (empty($list))
			return;

		$list = implode(',', $list);
		$query = "SELECT * FROM #__kunena_keywords WHERE name IN ({$list})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		foreach ( $results as $result ) {
			self::$_instances [$result['name']]->bind ( $result );
			self::$_instances [$result['name']]->exists(true);
		}
		unset ($results);
	}

	static protected function loadTopics($ids, $userid, $reload=false) {
		foreach ($ids as $i=>$id) {
			if (!$id || (!$reload && isset(self::$_topics [$id][$userid]))) {
				unset($ids[$i]);
			} else {
				self::$_topics [$id][$userid] = array();
			}
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_keywords_map AS m INNER JOIN #__kunena_keywords AS k ON m.keyword_id=k.id WHERE m.topic_id IN ({$idlist}) AND m.user_id IN (0,{$userid})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		foreach ( $results as $result ) {
			if (!isset(self::$_instances [$result['name']])) {
				self::$_instances [$result['name']] = new KunenaKeyword ($result['name']);
				self::$_instances [$result['name']]->bind ( $result );
				self::$_instances [$result['name']]->exists(true);
			}
			self::$_topics [$result['topic_id']][$result['user_id']][$result['name']] = 1;
			self::$_users [$result['user_id']][$result['topic_id']][$result['name']] = 1;
		}
		unset ($results);
	}
}
