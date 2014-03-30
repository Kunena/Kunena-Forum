<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Menu
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaMenuHelper
 */
abstract class KunenaMenuHelper {
	public static function cleanCache() {
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('mod_menu');
	}

	/**
	 * Get a list of the menu items (taken from Joomla 2.5.1).
	 * This only method need to be used only in frontend part
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @see		modules/mod_menu/helper.php
	 */
	public static function getList(&$params) {
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$levels = JFactory::getUser()->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;
		$cache = JFactory::getCache('com_kunena.menu', '');
		// FIXME: enable caching after fixing the issues
		$items = array();
		if (true) { // !($items = $cache->get($key))) {
			// Initialise variables.
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
							$router = $app::getRouter();
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
}
