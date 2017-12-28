<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Framework
 * @subpackage  Forum.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Forum Topic Rate Helper Class
 *
 * @since 5.0
 */
abstract class KunenaForumTopicRateHelper
{
	protected static $_instances = array();

	/**
	 * Returns KunenaForumTopicRate object
	 *
	 * @access    public
	 *
	 * @param null     $identifier
	 * @param bool     $reload
	 *
	 * @return KunenaForumTopicRate The rate object.
	 * @internal  param The $identifier rate object to load - Can be only an integer.
	 *
	 * @since     5.0
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
			return new KunenaForumTopicRate();
		}

		if ($reload || empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaForumTopicRate($id);
		}

		return self::$_instances [$id];
	}

	/**
	 * Return sum of all rates gived to a topics by all users
	 *
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
			->where('topic_id = ' . $db->escape($id));
		$db->setQuery($query);

		return round($db->loadResult());
	}
}
