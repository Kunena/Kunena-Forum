<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Menu
 *
 * @copyright     Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @copyright     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaMenuHelper
 * @since Kunena
 */
abstract class KunenaMenuHelper
{
	/**
	 * @since Kunena
	 */
	public static function cleanCache()
	{
		// @var \Joomla\CMS\Cache\Cache|\Joomla\CMS\Cache\CacheController $cache

		$cache = \Joomla\CMS\Factory::getCache();
		$cache->clean('mod_menu');
	}

	/**
	 * Get a list of the menu items (taken from Joomla 2.5.1).
	 * This only method need to be used only in frontend part
	 *
	 * @param   \Joomla\Registry\Registry $params The module options.
	 *
	 * @return    array
	 * @throws Exception
	 * @see        modules/mod_menu/helper.php
	 * @since      Kunena
	 */
	public static function getList(&$params)
	{
		$app  = \Joomla\CMS\Factory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$user   = \Joomla\CMS\Factory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key   = 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;
		$cache = \Joomla\CMS\Factory::getCache('mod_menu', '');

		if (!($items = $cache->get($key)))
		{
			// Initialise variables.
			$list = array();
			$db   = \Joomla\CMS\Factory::getDbo();

			$path    = $active->tree;
			$start   = (int) $params->get('startLevel');
			$end     = 0;
			$showAll = 1;
			$items   = $menu->getItems('menutype', $params->get('menutype'));

			$lastitem = 0;

			if ($items)
			{
				foreach ($items as $i => $item)
				{
					if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start - 2], $path))
					)
					{
						unset($items[$i]);
						continue;
					}

					$item->deeper     = false;
					$item->shallower  = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem]))
					{
						$items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
					}

					$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

					$lastitem     = $i;
					$item->active = false;
					$item->flink  = $item->link;

					switch ($item->type)
					{
						case 'separator':
							// No further action needed.
							continue;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
							{
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link . '&Itemid=' . $item->id;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
							break;

						default:
							$router = JSite::getRouter();

							if ($router->getMode() == JROUTER_MODE_SEF)
							{
								$item->flink = 'index.php?Itemid=' . $item->id;
							}
							else
							{
								$item->flink .= '&Itemid=' . $item->id;
							}
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
					{
						$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
					}
					else
					{
						$item->flink = JRoute::_($item->flink);
					}

					$item->title        = htmlspecialchars($item->title);
					$item->anchor_css   = '';
					$item->anchor_title = '';
					$item->anchor_rel   = '';
					$item->menu_image   = '';
				}

				if (isset($items[$lastitem]))
				{
					$items[$lastitem]->deeper     = (($start ? $start : 1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower  = (($start ? $start : 1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
				}
			}

			$cache->store($items, $key);
		}

		return $items;
	}
}
