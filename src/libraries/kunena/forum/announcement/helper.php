<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Forum.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaForumAnnouncementHelper
 *
 * @since  K1.X
 */
abstract class KunenaForumAnnouncementHelper
{
	/**
	 * @var KunenaForumAnnouncement[]
	 * @since Kunena
	 */
	public static $_instances = false;

	/**
	 * Returns the global KunenaForumAnnouncement object, only creating it if it doesn't already exist.
	 *
	 * @param   int  $identifier Announcement to load - Can be only an integer.
	 * @param   bool $reload     reload
	 *
	 * @return KunenaForumAnnouncement
	 * @since Kunena
	 */
	public static function get($identifier = null, $reload = false)
	{
		if ($identifier instanceof KunenaForumAnnouncement)
		{
			return $identifier;
		}

		if (!is_numeric($identifier))
		{
			return new KunenaForumAnnouncement;
		}

		$id = intval($identifier);

		if (empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaForumAnnouncement(array('id' => $id));
			self::$_instances [$id]->load();
		}
		elseif ($reload)
		{
			self::$_instances [$id]->load();
		}

		return self::$_instances [$id];
	}

	/**
	 * Get url
	 *
	 * @param   string $layout layout
	 * @param   bool   $xhtml  xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getUrl($layout = null, $xhtml = true)
	{
		$uri = self::getUri($layout);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * Get uri
	 *
	 * @param   string $layout layout
	 *
	 * @return \Joomla\CMS\Uri\Uri
	 * @since Kunena
	 */
	public static function getUri($layout = null)
	{
		$uri = new \Joomla\CMS\Uri\Uri('index.php?option=com_kunena&view=announcement');

		if ($layout)
		{
			$uri->setVar('layout', $layout);
		}

		return $uri;
	}

	/**
	 * Get Announcements
	 *
	 * @param   int  $start  start
	 * @param   int  $limit  limit
	 * @param   bool $filter filter
	 *
	 * @return KunenaForumAnnouncement[]
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getAnnouncements($start = 0, $limit = 1, $filter = true)
	{
		$db       = Factory::getDBO();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		if ($filter)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from('#__kunena_announcement')
				->order('id DESC')
				->where('(published = 1)')
				->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')')
				->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');
		}
		else
		{
			$query = $db->getQuery(true)
				->select('*')
				->from('#__kunena_announcement')
				->order('id DESC');
		}

		$db->setQuery($query, $start, $limit);

		try
		{
			$results = (array) $db->loadAssocList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		self::$_instances = array();
		$list             = array();

		foreach ($results as $announcement)
		{
			if (isset(self::$_instances [$announcement['id']]))
			{
				continue;
			}

			$instance = new KunenaForumAnnouncement($announcement);
			$instance->exists(true);
			self::$_instances [$instance->id] = $instance;
			$list[]                           = $instance;
		}

		unset($results);

		return $list;
	}

	/**
	 * Get Count
	 *
	 * @param   bool $filter filter
	 *
	 * @return integer
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getCount($filter = true)
	{
		$db       = Factory::getDBO();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		if ($filter)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from('#__kunena_announcement')
				->order('id DESC')
				->where('(published = 1)')
				->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')')
				->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');
		}
		else
		{
			$query = $db->getQuery(true)
				->select('*')
				->from('#__kunena_announcement')
				->order('id DESC');
		}

		$db->setQuery($query);

		try
		{
			$total = (int) $db->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $total;
	}

	/**
	 * Free up memory by cleaning up all cached items.
	 *
	 * @return void
	 * @since Kunena
	 */
	public static function cleanup()
	{
		self::$_instances = array();
	}
}
