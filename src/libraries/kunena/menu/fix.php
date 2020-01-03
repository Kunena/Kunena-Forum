<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Menu
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @copyright   (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;

KunenaMenuFix::initialize();

/**
 * Class KunenaMenuFix
 *
 * @since   Kunena 6.0
 */
abstract class KunenaMenuFix
{
	/**
	 * @var array|StdClass[]
	 * @since   Kunena 6.0
	 */
	public static $items = array();

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	public static $filtered = array();

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	public static $aliases = array();

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	public static $invalid = array();

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	public static $legacy = array();

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	public static $same = array();

	/**
	 * @var null
	 * @since   Kunena 6.0
	 */
	public static $structure = null;

	/**
	 * @var null
	 * @since   Kunena 6.0
	 */
	public static $parent = null;

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public static function initialize()
	{
		self::load();
		self::build();
	}

	/**
	 * Loads the entire menu table into memory (taken from Joomla 1.7.3).
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 */
	protected static function load()
	{
		// Initialise variables.
		$db = Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select('m.id, m.menutype, m.title, m.alias, m.path AS route, m.link, m.type, m.level, m.language');
		$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id');
		$query->select('e.element AS component, m.published')
			->from($db->quoteName('#__menu', 'm'))
			->leftJoin($db->quoteName('#__extensions', 'e') . ' ON ' . $db->quoteName('m.component_id') . ' = ' . $db->quoteName('e.extension_id'))
			->where($db->quoteName('m.parent_id') . ' > 0')
			->andWhere($db->quoteName('m.client_id') . ' = 0')
			->order($db->quoteName('m.lft'));

		// Set the query
		$db->setQuery($query);

		try
		{
			self::$items = $db->loadObjectList('id');
		}
		catch (ExecutionFailureException $e)
		{
			throw new Exception(Text::sprintf('JERROR_LOADING_MENUS', $e->getMessage()), 500);
		}

		foreach (self::$items as &$item)
		{
			// Get parent information.
			$parent_tree = array();

			if (isset(self::$items[$item->parent_id]))
			{
				$parent_tree = self::$items[$item->parent_id]->tree;
			}

			// Create tree
			$parent_tree[] = $item->id;
			$item->tree    = $parent_tree;

			// Create the query array.
			$url = str_replace('index.php?', '', $item->link);
			$url = str_replace('&amp;', '&', $url);

			parse_str($url, $item->query);
		}
	}

	/**
	 * @return void
	 * @since   Kunena 6.0
	 */
	protected static function build()
	{
		if (!isset(self::$structure))
		{
			self::$structure = array();

			foreach (self::$items as $item)
			{
				if (!is_object($item))
				{
					continue;
				}

				$itemid = null;
				$view   = null;

				if ($item->type == 'alias' && !empty($item->query['Itemid']))
				{
					$realitem = empty(self::$items[$item->query['Itemid']]) ? null : self::$items[$item->query['Itemid']];

					if (is_object($realitem) && $realitem->type == 'component' && $realitem->component == 'com_kunena')
					{
						$itemid                   = $item->query['Itemid'];
						self::$aliases[$item->id] = $itemid;
					}
					elseif (!$realitem)
					{
						$itemid                   = 0;
						self::$invalid[$item->id] = $itemid;
					}

					$view = 'alias';
				}
				elseif ($item->type == 'component' && $item->component == 'com_kunena')
				{
					$itemid = $item->id;
					$view   = empty($item->query['view']) ? 'legacy' : $item->query['view'];
				}

				if ($itemid !== null && $view)
				{
					$language                                                            = isset($item->language) ? strtolower($item->language) : '*';
					$home                                                                = self::getHome($item);
					self::$filtered[$item->id]                                           = $itemid;
					self::$same[$item->route][$item->id]                                 = $item;
					self::$structure[$language][$home ? $home->id : 0][$view][$item->id] = $itemid;

					if (KunenaRouteLegacy::isLegacy($view))
					{
						self::$legacy[$item->id] = $item->id;
					}
				}
			}
		}
	}

	/**
	 * @param   StdClass  $item  item
	 *
	 * @return object|boolean
	 * @since   Kunena 6.0
	 */
	protected static function getHome($item)
	{
		if (!$item)
		{
			return false;
		}

		$id = $item->id;

		if (!isset(self::$parent[$id]))
		{
			if ($item->type == 'component' && $item->component == 'com_kunena' && isset($item->query['view']) && ($item->query['view'] == 'home' || $item->query['view'] == 'entrypage'))
			{
				self::$parent[$id] = $item;
			}
			else
			{
				$parent            = isset(self::$items[$item->parent_id]) ? self::$items[$item->parent_id] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}

		return self::$parent[$id];
	}

	/**
	 * @return array
	 * @since   Kunena 6.0
	 */
	public static function getLegacy()
	{
		$items = array();

		foreach (self::$legacy as $itemid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return array|null
	 * @since Kunena
	 * @throws Exception
	 */
	public static function fixLegacy()
	{
		$errors = array();

		foreach (self::$legacy as $itemid)
		{
			$item = self::$items[$itemid];
			KunenaRouteLegacy::convertMenuItem($item);
			$table = Joomla\CMS\Table\Table::getInstance('menu');
			$table->load($item->id);
			$data = array(
				'link'   => $item->link,
				'params' => $item->params,
			);

			if (!$table->bind($data) || !$table->check() || !$table->store())
			{
				$errors[] = "{$item->route} (#{$item->id}): {$table->getError()}";
			}
		}

		KunenaMenuHelper::cleanCache();

		return !empty($errors) ? $errors : null;
	}

	/**
	 * @param   integer  $itemid  itemid
	 *
	 * @return boolean
	 * @since   Kunena 6.0
	 */
	public static function delete($itemid)
	{
		// Only delete Kunena menu items
		if (!isset(self::$items[$itemid]))
		{
			return false;
		}

		$table  = Joomla\CMS\Table\Table::getInstance('menu');
		$result = $table->delete($itemid);
		KunenaMenuHelper::cleanCache();

		return $result;
	}

	/**
	 * @return array
	 * @since   Kunena 6.0
	 */
	public static function getAll()
	{
		$items = array();

		foreach (self::$filtered as $itemid => $targetid)
		{
			if ($targetid)
			{
				$items[$itemid] = self::$items[$itemid];
			}
		}

		return $items;
	}

	/**
	 * @return array
	 * @since   Kunena 6.0
	 */
	public static function getAliases()
	{
		$items = array();

		foreach (self::$aliases as $itemid => $targetid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return array
	 * @since   Kunena 6.0
	 */
	public static function getInvalid()
	{
		$items = array();

		foreach (self::$invalid as $itemid => $targetid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return array
	 * @since  Kunena
	 */
	public static function getConflicts()
	{
		return array();
	}
}
