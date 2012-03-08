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

jimport ('joomla.database.table');

// FIXME: do only in some functions
KunenaMenuHelper::initialize();

abstract class KunenaMenuHelper {
	public static $items = array();
	public static $filtered = array();
	public static $aliases = array();
	public static $invalid = array();
	public static $legacy = array();
	public static $same = array();
	public static $structure = null;
	public static $parent = null;

	/**
	 * Get a list of the menu items (taken from Joomla 2.5.1).
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @see		modules/mod_menu/helper.php
	 */
	static function getList(&$params) {
		if (version_compare(JVERSION, '1.6', '>')) {
			return self::getList16($params);
		} else {
			return self::getList15($params);
		}
	}

	static function getList16(&$params) {
		$menu = JFactory::getApplication()->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$levels = JFactory::getUser()->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;
		$cache = JFactory::getCache('com_kunena.menu', '');
		if (!($items = $cache->get($key))) {
			// Initialise variables.
			$list		= array();

			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren');
			$items 		= $menu->getItems('menutype', $params->get('menutype'));

			$lastitem	= 0;

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

					if (isset($items[$lastitem])) {
						$items[$lastitem]->deeper		= ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower	= ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff	= ($items[$lastitem]->level - $item->level);
					}

					$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

					$lastitem			= $i;
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

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff	= ($items[$lastitem]->level - ($start?$start:1));
				}
			}

			$cache->store($items, $key);
		}
		return $items;
	}

	static function getList15(JRegistry &$params) {
		// TODO: support aliases and check that the logic works in every case
		$menu = JFactory::getApplication()->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$level = JFactory::getUser()->get('aid');
		$key = 'menu_items'.$params->toString().$level.'.'.$active->id;
		$cache = JFactory::getCache('com_kunena.menu', '');
		if (!($items = $cache->get($key))) {
			// Initialise variables.
			$list		= array();

			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren');
			$items 		= $menu->getItems('menutype', $params->get('menutype'));

			$lastitem	= 0;

			if ($items) {
				foreach($items as $i => $item) {
					if (($start && $start > $item->sublevel)
						|| ($end && $item->sublevel > $end)
						|| (!$showAll && $item->sublevel > 0 && !in_array($item->parent, $path))
						|| ($start > 0 && !in_array($item->tree[$start-1], $path)
						|| (empty($item->published) || (isset ( $item->access ) && $item->access > $level)))
					) {
						unset($items[$i]);
						continue;
					}

					$itemparams = new JParameter($item->params);

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem])) {
						$items[$lastitem]->deeper		= ($item->sublevel > $items[$lastitem]->sublevel);
						$items[$lastitem]->shallower	= ($item->sublevel < $items[$lastitem]->sublevel);
						$items[$lastitem]->level_diff	= ($items[$lastitem]->sublevel - $item->sublevel);
					}

					$lastitem			= $i;
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
							$item->flink = 'index.php?Itemid='.($itemparams->get('aliasoptions'));
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
						$item->flink = JRoute::_($item->flink, false, $itemparams->get('secure'));
					} else {
						$item->flink = JRoute::_($item->flink, false);
					}

					$item->title = htmlspecialchars($item->name);
					$item->anchor_css = htmlspecialchars($itemparams->get('menu-anchor_css', ''));
					$item->anchor_title = htmlspecialchars($itemparams->get('menu-anchor_title', ''));
					$item->menu_image = htmlspecialchars($itemparams->get('menu_image', ''));
				}

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->sublevel);
					$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->sublevel);
					$items[$lastitem]->level_diff	= ($items[$lastitem]->sublevel - ($start?$start:1));
				}
			}

			$cache->store($items, $key);
		}
		return $items;
	}

	public static function initialize() {
		self::load();
		self::build();
	}

	/**
	 * Loads the entire menu table into memory (taken from Joomla 1.7.3).
	 *
	 * @return array
	 */
	protected static function load() {
		// Initialise variables.
		$db = JFactory::getDbo();

		if (version_compare(JVERSION, '1.6', '>')) {
			// Joomla 1.6+
			$query = $db->getQuery(true);
			$query->select('m.id, m.menutype, m.title, m.alias, m.path AS route, m.link, m.type, m.level, m.language');
			$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id');
			$query->select('e.element as component, m.published');
			$query->from('#__menu AS m');
			$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
			$query->where('m.parent_id > 0');
			$query->where('m.client_id = 0');
			$query->order('m.lft');

		} else {
			// Joomla 1.5
			$query = "SELECT m.id, m.menutype, m.name AS title, m.alias, '' AS route, m.link, m.type, m.sublevel AS level, '*' AS language,
				m.browserNav, m.access, m.params, m.home, '' AS img, '' AS template_style_id, m.componentid AS component_id, m.parent AS parent_id,
				c.option AS component, m.published
				FROM #__menu AS m
				LEFT JOIN #__components AS c ON m.componentid = c.id
				ORDER BY m.sublevel, m.parent, m.ordering";
		}

		// Set the query
		$db->setQuery($query);
		if (!(self::$items = $db->loadObjectList('id'))) {
			JError::raiseWarning(500, JText::sprintf('JERROR_LOADING_MENUS', $db->getErrorMsg()));
			return false;
		}

		foreach(self::$items as &$item) {
			// Get parent information.
			$parent_tree = array();
			if (version_compare(JVERSION, '1.6', '>')) {
				// Joomla 1.6+
				if (isset(self::$items[$item->parent_id])) {
					$parent_tree = self::$items[$item->parent_id]->tree;
				}

			} else {
				// Joomla 1.5
				$parent_route = '';
				if(($parent = $item->parent_id) && (isset(self::$items[$parent])) &&
					(is_object(self::$items[$parent])) && (isset(self::$items[$parent]->route)) && isset(self::$items[$parent]->tree)) {
					$parent_route = self::$items[$parent]->route.'/';
					$parent_tree  = self::$items[$parent]->tree;
				}

				// Create route
				$item->route = $parent_route . $item->alias;
			}

			// Create tree
			$parent_tree[] = $item->id;
			$item->tree = $parent_tree;

			// Create the query array.
			$url = str_replace('index.php?', '', $item->link);
			$url = str_replace('&amp;', '&', $url);

			parse_str($url, $item->query);
		}
	}

	public static function getLegacy() {
		$items = array();
		foreach (self::$legacy as $itemid) {
			$items[$itemid] = self::$items[$itemid];
		}
		return $items;
	}

	public static function fixLegacy() {
		$items = array();
		foreach (self::$legacy as $itemid) {
			$item = self::$items[$itemid];
			KunenaRouteLegacy::convertMenuItem($item);
			$table = JTable::getInstance ( 'menu' );
			$table->load($item->id);
			$data = array (
				'link' => $item->link,
				'params' => $item->params,
			);
			if (! $table->bind ( $data ) || ! $table->check () || ! $table->store ()) {
				return $table->getError ();
			}
		}
	}

	public static function delete($itemid) {
		// Only delete Kunena menu items
		if (!isset(self::$items[$itemid])) return false;
		$table = JTable::getInstance ( 'menu' );
		return $table->delete($itemid);
	}

	public static function getAll() {
		$items = array();
		foreach (self::$filtered as $itemid=>$targetid) {
			if ($targetid) $items[$itemid] = self::$items[$itemid];
		}
		return $items;
	}

	public static function getAliases() {
		$items = array();
		foreach (self::$aliases as $itemid=>$targetid) {
			$items[$itemid] = self::$items[$itemid];
		}
		return $items;
	}

	public static function getInvalid() {
		$items = array();
		foreach (self::$invalid as $itemid=>$targetid) {
			$items[$itemid] = self::$items[$itemid];
		}
		return $items;
	}

	public static function getConflicts() {
		$items = array();
		foreach (self::$same as $alias=>$list) {
			if (count($list)>1) {
				$items += $list;
			}
		}
		return $items;
	}

	protected static function build() {
		if (!isset(self::$structure)) {
			self::$structure = array();
			foreach ( self::$items as $item ) {
				if (! is_object ( $item ))
					continue;

				$itemid = null;
				if (($item->type == 'menulink' || $item->type == 'alias') && !empty($item->query['Itemid'])) {
					$realitem = empty(self::$items[$item->query['Itemid']]) ? null : self::$items[$item->query['Itemid']];
					if (is_object ($realitem) && $realitem->type == 'component' && $realitem->component == 'com_kunena') {
						$itemid = $item->query['Itemid'];
						self::$aliases[$item->id] = $itemid;
					} elseif (!$realitem) {
						$itemid = 0;
						self::$invalid[$item->id] = $itemid;
					}
					$view = 'alias';

				} elseif ($item->type == 'component' && $item->component == 'com_kunena') {
					$itemid = $item->id;
					$view = empty($item->query['view']) ? 'legacy' : $item->query['view'];
				}

				if ($itemid !== null) {
					$language = isset($item->language) ? strtolower($item->language) : '*';
					$home = self::getHome($item);
					self::$filtered[$item->id] = $itemid;
					self::$same[$item->route][$item->id] = $item;
					self::$structure[$language][$home ? $home->id : 0][$view][$item->id] = $itemid;
					if (KunenaRouteLegacy::isLegacy($view)) {
						self::$legacy[$item->id] = $item->id;
					}
				}
			}
		}
	}

	protected static function getHome($item) {
		if (!$item) return null;
		$id = $item->id;
		if (!isset(self::$parent[$id])) {
			if ($item->type == 'component' && $item->component == 'com_kunena' && isset($item->query['view']) && ($item->query['view'] == 'home' || $item->query['view'] == 'entrypage')) {
				self::$parent[$id] = $item;
			} else {
				$parent = isset(self::$items[$item->parent_id]) ? self::$items[$item->parent_id] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}
		return self::$parent[$id];
	}
}