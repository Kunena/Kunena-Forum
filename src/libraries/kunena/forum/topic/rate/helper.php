<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Framework
 * @subpackage  Forum.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Forum Topic Rate Helper Class
 */
abstract class KunenaForumTopicRateHelper
{
	protected static $_instances = array();

	/**
	 * Returns KunenaForumTopicRate object
	 *
	 * @access    public
	 *
	 * @param    identifier        The topic to load - Can be only an integer.
	 *
	 * @return    KunenaForumTopicRate        The rate object.
	 * @since     2.0
	 */
	static public function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof KunenaForumTopicRate)
		{
			return $identifier;
		}

		$id = intval($identifier);

		if ($id < 1)
		{
			return;
		}

		if ($reload || empty (self::$_instances [$id]))
		{
			unset(self::$_instances [$id]);
			self::loadTopics(array($id));
		}

		return self::$_instances [$id];
	}

	/**
	 * Load users who have rate listed topics
	 *
	 * @param array $ids List of topics IDs
	 */
	static protected function loadTopics($ids)
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

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("*")
			->from("#__kunena_rate")
			->where("topicid IN ({$idlist})");
		$db->setQuery($query);
		$results = (array) $db->loadObjectList();
		KunenaError::checkDatabaseError();

		foreach ($ids as $id)
		{
			self::$_instances [$id] = KunenaForumTopicHelper::get($id);
		}

		foreach ($results as $result)
		{
			self::$_instances [$result->topicid]->_add($result->userid, $result->time);
		}

		unset ($results);
	}

	/**
	 * @param $id
	 *
	 * @return float
	 */
	static public function getSelected($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("(SUM(rate)/COUNT(rate)) as selected")
			->from('#__kunena_rate')
			->where('topicid = ' . $db->escape($id));
		$db->setQuery($query);

		return round($db->loadResult());
	}
}
