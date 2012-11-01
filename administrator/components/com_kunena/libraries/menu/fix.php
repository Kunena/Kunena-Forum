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

KunenaMenuFix::initialize();

abstract class KunenaMenuFix {
	public static $items = array();
	public static $filtered = array();
	public static $aliases = array();
	public static $invalid = array();
	public static $legacy = array();
	public static $same = array();
	public static $structure = null;
	public static $parent = null;

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
		$errors = array();
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
				$errors[] = "{$item->route} (#{$item->id}): {$table->getError()}";
			}
		}
		KunenaMenuHelper::cleanCache();
		return !empty($errors) ? $errors : null;
	}

	public static function delete($itemid) {
		// Only delete Kunena menu items
		if (!isset(self::$items[$itemid])) return false;
		$table = JTable::getInstance ( 'menu' );
		$result = $table->delete($itemid);
		KunenaMenuHelper::cleanCache();
		return $result;
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
			// There are no conflicts in J1.6+ (only multi-lang support)
			if (count($list)>1 && version_compare(JVERSION, '1.6', '<')) {
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