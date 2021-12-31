<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Rate;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;

/**
 * Kunena Forum Topic Rate Helper Class
 *
 * @since 5.0
 */
abstract class KunenaRateHelper
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Topic\TopicRate object
	 *
	 * @access    public
	 *
	 * @internal  param The $identifier rate object to load - Can be only an integer.
	 *
	 * @param   bool  $reload      reload
	 *
	 * @param   null  $identifier  identifier
	 *
	 * @return  KunenaRate The rate object.
	 *
	 * @since     Kunena 5.0
	 *
	 * @throws  Exception
	 */
	public static function get($identifier = null, $reload = false): KunenaRate
	{
		if ($identifier instanceof KunenaRate)
		{
			return $identifier;
		}

		$id = \intval($identifier);

		if ($id < 1)
		{
			return new KunenaRate;
		}

		if ($reload || empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaRate($id);
		}

		return self::$_instances [$id];
	}

	/**
	 * Return sum of all rates given to a topics by all users
	 *
	 * @param   integer  $id  id
	 *
	 * @return  float
	 *
	 * @since   Kunena 6.0
	 */
	public static function getSelected(int $id): float
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
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
	 *
	 * @since   Kunena 6.0
	 */
	public static function getCount(int $id): float
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
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
	 * @param   integer  $id      id
	 * @param   integer  $userid  userid
	 *
	 * @return  float
	 *
	 * @since   Kunena 6.0
	 */
	public static function getRate(int $id, int $userid): float
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select($db->quoteName('rate'))
			->from($db->quoteName('#__kunena_rate'))
			->where($db->quoteName('topic_id') . ' = ' . $db->quote($id))
			->andWhere($db->quoteName('userid') . ' = ' . $db->quote($userid));
		$db->setQuery($query);

		return round($db->loadResult());
	}
}
