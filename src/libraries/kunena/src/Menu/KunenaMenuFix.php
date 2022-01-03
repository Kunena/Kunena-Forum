<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Menu
 *
 * @copyright   (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Menu;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Exception\KunenaException;
use StdClass;

KunenaMenuFix::initialize();

/**
 * Class KunenaMenuFix
 *
 * @since   Kunena 6.0
 */
abstract class KunenaMenuFix
{
	/**
	 * @var     array|StdClass[]
	 * @since   Kunena 6.0
	 */
	public static $items = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $filtered = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $aliases = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $invalid = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $legacy = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $same = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $structure = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $parent = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $parentid;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function initialize(): void
	{
		self::load();
		self::build();
	}

	/**
	 * Loads the entire menu table into memory (taken from Joomla 1.7.3).
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected static function load(): void
	{
		// Initialise variables.
		$db = Factory::getContainer()->get('DatabaseDriver');

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
			throw new KunenaException(Text::sprintf('JERROR_LOADING_MENUS', $e->getMessage()), 500);
		}

		foreach (self::$items as $item)
		{
			// Get parent information.
			$parent_tree = [];

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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected static function build(): void
	{
		if (!isset(self::$structure))
		{
			self::$structure = [];

			foreach (self::$items as $item)
			{
				if (!\is_object($item))
				{
					continue;
				}

				$itemid = null;
				$view   = null;

				if ($item->type == 'alias' && !empty($item->query['Itemid']))
				{
					$realitem = empty(self::$items[$item->query['Itemid']]) ? null : self::$items[$item->query['Itemid']];

					if (\is_object($realitem) && $realitem->type == 'component' && $realitem->component == 'com_kunena')
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
				}
			}
		}
	}

	/**
	 * @param   StdClass  $item  item
	 *
	 * @return  object|boolean
	 *
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
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getLegacy(): array
	{
		$items = [];

		foreach (self::$legacy as $itemid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return  array|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function fixLegacy(): ?array
	{
		$errors = [];

		foreach (self::$legacy as $itemid)
		{
			$item = self::$items[$itemid];
			$table = Table::getInstance('menu');
			$table->load($item->id);
			$data = [
				'link'   => $item->link,
				'params' => $item->params,
			];

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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function delete(int $itemid): bool
	{
		// Only delete Kunena menu items
		if (!isset(self::$items[$itemid]))
		{
			return false;
		}

		$table  = Table::getInstance('menu');
		$result = $table->delete($itemid);
		KunenaMenuHelper::cleanCache();

		return $result;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getAll(): array
	{
		$items = [];

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
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getAliases(): array
	{
		$items = [];

		foreach (self::$aliases as $itemid => $targetid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getInvalid(): array
	{
		$items = [];

		foreach (self::$invalid as $itemid => $targetid)
		{
			$items[$itemid] = self::$items[$itemid];
		}

		return $items;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getConflicts(): array
	{
		return [];
	}
}
