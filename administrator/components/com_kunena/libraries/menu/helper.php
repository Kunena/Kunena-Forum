<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Menu
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaMenuHelper {
	public static function cleanCache() {
		if (version_compare(JVERSION, '1.6', '>')) {
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		} else {
			// clean system cache
			$cache = JFactory::getCache('_system');
			$cache->clean();

			// clean mod_mainmenu cache
			$cache = JFactory::getCache('mod_mainmenu');
			$cache->clean();
		}
	}

	/**
	 * Get a list of the menu items (taken from Joomla 2.5.1).
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @see		modules/mod_menu/helper.php
	 */
	public static function getList(&$params) {
		if (version_compare(JVERSION, '1.6', '>')) {
			return self::getList16($params);
		} else {
			return self::getList15($params);
		}
	}

	protected static function getList16(&$params) {
		$menu = JFactory::getApplication()->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$levels = JFactory::getUser()->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;
		$cache = JFactory::getCache('com_kunena.menu', '');
		// FIXME: enable caching after fixing the issues
		if (true) { // !($items = $cache->get($key))) {
			// Initialise variables.
			$list		= array();

			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren');
			$items 		= $menu->getItems('menutype', $params->get('menutype'));

			if ($items) {
				foreach($items as $i => $item) {
					if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start-2], $path))
					) {
						unset($items[$i]);
						continue;
					}

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($lastitem)) {
						$lastitem->deeper		= ($item->level > $lastitem->level);
						$lastitem->shallower	= ($item->level < $lastitem->level);
						$lastitem->level_diff	= ($lastitem->level - $item->level);
					}

					$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

					$lastitem			= $item;
					$item->active		= false;
					$item->flink = $item->link;

					switch ($item->type) {
						case 'separator':
							// No further action needed.
							continue;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link.'&Itemid='.$item->id;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
							break;

						default:
							$router = JSite::getRouter();
							if ($router->getMode() == JROUTER_MODE_SEF) {
								$item->flink = 'index.php?Itemid='.$item->id;
							}
							else {
								$item->flink .= '&Itemid='.$item->id;
							}
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
						$item->flink = JRoute::_($item->flink, false, $item->params->get('secure'));
					} else {
						$item->flink = JRoute::_($item->flink, false);
					}

					$item->title = htmlspecialchars($item->title);
					$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
					$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''));
					$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', '')) : '';
				}

				if (isset($lastitem)) {
					$lastitem->deeper		= (($start?$start:1) > $lastitem->level);
					$lastitem->shallower	= (($start?$start:1) < $lastitem->level);
					$lastitem->level_diff	= ($lastitem->level - ($start?$start:1));
				}
			}

			// FIXME: enable caching after fixing the issues
			//$cache->store($items, $key);
		}
		return $items;
	}

	protected static function getList15(JRegistry &$params) {
		// TODO: support aliases and check that the logic works in every case
		$menu = JFactory::getApplication()->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$level = JFactory::getUser()->get('aid');
		$key = 'menu_items'.$params->toString().$level.'.'.$active->id;
		$cache = JFactory::getCache('com_kunena.menu', '');
		// FIXME: enable caching after fixing the issues
		if (true) { // !($items = $cache->get($key))) {
			// Initialise variables.
			$list		= array();

			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren');
			$rows		= $menu->getItems('menutype', $params->get('menutype'));
			$items		= array();

			if ($rows) {
				$itemsord = array(0=>array());
				foreach($rows as $item) {
					$itemsord[$item->parent][] = $item;
				}
				$heap = $itemsord[0];
				while (!is_null($item = array_shift($heap))) {
					if (empty($item->published) || (isset ( $item->access ) && $item->access > $level)) continue;
					$items[] = $item;
					if (isset($itemsord[$item->id])) {
						$heap = array_merge($itemsord[$item->id], $heap);
					}
				}
				$items = array_values($items);

				foreach($items as $i => &$item) {
					if (($start && $start > $item->sublevel)
						|| ($end && $item->sublevel > $end)
						|| (!$showAll && $item->sublevel > 0 && !in_array($item->parent, $path))
						|| ($start > 0 && !in_array($item->tree[$start-1], $path))
					) {
						unset($items[$i]);
						continue;
					}

					// Menu Link is a special type that is a link to another item
					if ($item->type == 'menulink') {
						$menu = JSite::getMenu();
						$newItem = $menu->getItem($item->query['Itemid']);
						if ($newItem) {
							$tmp = clone($newItem);
							$tmp->name = $item->name;
							$tmp->parent = $item->parent;
							$tmp->sublevel = $item->sublevel;
							$tmp->tree = $item->tree;
							$item = $tmp;
						} else {
							$item = clone($item);
						}
					} else {
						$item = clone($item);
					}

					// Convert object to be Joomla 2.5 compatible
					$item->params = new JParameter($item->params);
					$item->parent_id = $item->parent;
					$item->level = $item->sublevel+1;

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($lastitem)) {
						$lastitem->deeper		= ($item->sublevel > $lastitem->sublevel);
						$lastitem->shallower	= ($item->sublevel < $lastitem->sublevel);
						$lastitem->level_diff	= ($lastitem->sublevel - $item->sublevel);
					}

					$item->parent = (boolean) $menu->getItems('parent', (int) $item->id, true);

					$lastitem			= $item;
					$item->active		= false;
					$item->flink = $item->link;

					switch ($item->type) {
						case 'separator':
							// No further action needed.
							continue;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link.'&Itemid='.$item->id;
							}
							break;

						default:
							$router = JSite::getRouter();
							if ($router->getMode() == JROUTER_MODE_SEF) {
								$item->flink = 'index.php?Itemid='.$item->id;
							}
							else {
								$item->flink .= '&Itemid='.$item->id;
							}
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
						$item->flink = JRoute::_($item->flink, false, $item->params->get('secure'));
					} else {
						$item->flink = JRoute::_($item->flink, false);
					}

					$item->title = htmlspecialchars($item->name);
					$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
					$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''));
					$item->menu_image = htmlspecialchars($item->params->get('menu_image', ''));
					if ($item->menu_image == '-1') $item->menu_image = '';
				}

				if (isset($lastitem)) {
					$lastitem->deeper		= (($start?$start:1) > $lastitem->sublevel);
					$lastitem->shallower	= (($start?$start:1) < $lastitem->sublevel);
					$lastitem->level_diff	= ($lastitem->sublevel - ($start?$start:1));
				}
			}

			// FIXME: enable caching after fixing the issues
			//$cache->store($items, $key);
		}
		return $items;
	}
}