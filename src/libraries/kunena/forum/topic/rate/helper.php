<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Kunena Forum Topic Rate Helper Class
 *
 * @since 5.0
 */
abstract class KunenaForumTopicRateHelper
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_instances = array();

	/**
	 * Returns KunenaForumTopicRate object
	 *
	 * @access  public
	 *
	 * @internal  param The $identifier rate object to load - Can be only an integer.
	 *
	 * @param   bool  $reload      reload
	 *
	 * @param   null  $identifier  identifier
	 *
	 * @return  KunenaForumTopicRate The rate object.
	 * @since     5.0
	 * @throws  Exception
	 */
	public static function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof KunenaForumTopicRate)
		{
			return $identifier;
		}

		$id = intval($identifier);

		if ($id < 1)
		{
			return new KunenaForumTopicRate;
		}

		if ($reload || empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaForumTopicRate($id);
		}

		return self::$_instances [$id];
	}

	/**
	 * Return sum of all rates given to a topics by all users
	 *
	 * @param   integer  $id  id
	 *
	 * @return  float
	 * @since   Kunena 6.0
	 */
	public static function getSelected($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('(SUM(' . $db->quoteName('rate') . ')/COUNT(' . $db->quoteName('rate') . ')) AS ' . $db->quoteName('selected'))
			->from($db->quoteName('#__kunena_rate'))
			->where($db->quoteName('topic_id') . ' = ' . $db->quote($id));
		$db->setQuery($query);

		return round($db->loadResult());
	}

	/**
	 * Return sum of all rates given to a topics by all users
	 *
	 * @param   integer  $id  id
	 *
	 * @return  float
	 * @since   Kunena 6.0
	 */
	public static function getCount($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('(COUNT(' . $db->quoteName('rate') . ')) AS ' . $db->quoteName('selected'))
			->from($db->quoteName('#__kunena_rate'))
			->where($db->quoteName('topic_id') . ' = ' . $db->quote($id));
		$db->setQuery($query);

		return round($db->loadResult());
	}

	/**
	 * Return rate by id and userid
	 *
	 * @param   integer  $id  id
	 *
	 * @param            $userid
	 *
	 * @return  float
	 * @since   Kunena 6.0
	 */
	public static function getRate($id, $userid)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('rate'))
			->from($db->quoteName('#__kunena_rate'))
			->where($db->quoteName('topic_id') . ' = ' . $db->quote($id))
			->andWhere($db->quoteName('userid') . ' = ' . $db->quote($userid));
		$db->setQuery($query);

		return round($db->loadResult());
	}
}
