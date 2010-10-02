<?php
/**
 * @version $Id$
 * Kunena Component - KunenaRoute
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

abstract class KunenaRoute {
	static $kunenaroot = false;
	static $active = false;
	static $childlist = array();
	static $subtree = array();
	static $parent = array();
	static $menu = null;

	public static function current($class = false) {
		$link = JURI::getInstance('index.php?'.http_build_query(JRequest::get( 'get' )));
		if ($class) return $link;
		return $link->getQuery ();
	}

	public static function getItemID($uri = null) {
		jimport ( 'joomla.environment.uri' );
		if (!$uri) {
			$link = self::current(true);
			$link->delVar ( 'Itemid' );
		}
		else if (is_numeric($uri)) {
			return intval($uri);
		} else {
			$link = new JURI ( (string)$uri );
		}
		$query = $link->getQuery ( true );
		$itemid = self::_getItemID ( $query );
		return $itemid;
	}

	public static function _($uri = null, $xhtml = true, $ssl=0) {
		jimport ( 'joomla.environment.uri' );
		if (!$uri) {
			$link = self::current(true);
			$link->delVar ( 'Itemid' );
		}
		else {
			$link = new JURI ( (string)$uri );
		}
		$query = $link->getQuery ( true );
		$Itemid = self::_getItemID ( $query );
		$link->setVar ( 'Itemid', $Itemid );

		return JRoute::_ ( 'index.php?' . $link->getQuery (), $xhtml, $ssl );
	}

	public static function getCurrentMenu() {
		self::buildMenuTree();

		$Itemid = 0;
		if (self::$active) {
			// Find Kunena entry page from current menu
			$root = self::getKunenaRoot(self::$active);
			$list = self::getSubMenus($root);
			if (count($list) > 1) {
				// Current root contains Kunena menu
				$Itemid = $root;
			}
		}
		if ($Itemid)
			return self::$menu[$Itemid];

		return null;
	}

	public function getKunenaMenu() {
		self::buildMenuTree();

		$Itemid = 0;
		if (isset(self::$childlist['kunenamenu'][0])) {
			// Use first item in kunenamenu
			$Itemid = reset(self::$childlist['kunenamenu'][0]);
		}
		if ($Itemid)
			return self::$menu[$Itemid];

		return null;
	}

	public function getMenu() {
		$menu = self::getCurrentMenu();
		if (!$menu) {
			$menu = self::getKunenaMenu();
		}
		return $menu;
	}

	protected static function buildMenuTree() {
		if (self::$menu === null) {
			$my = JFactory::getUser ();
			$menus = JSite::getMenu ();
			self::$menu = $menus->getMenu ();
			$active = $menus->getActive ();
			self::$active = is_object($active) ? $active->id : 0;
			foreach ( self::$menu as $item ) {
				if (! is_object ( $item ))
					continue;
				// FIXME: Joomla 1.5 only!
				if (!empty($item->published) && (!isset ( $item->access ) || $item->access <= $my->aid)) {
					self::$childlist[$item->menutype][$item->parent][$item->id] = $item->id;
				}
			}
		}
	}

	protected static function getMenuItems($menutype) {
		if (!isset(self::$subtree[$menutype])) {
			self::$subtree[$menutype] = array();
			$todo = isset(self::$childlist[$menutype][0]) ? self::$childlist[$menutype][0] : array();
			while (($id = array_shift($todo)) !== null) {
				$item = self::$menu[$id];
				if ($item->type == 'component' && $item->component == 'com_kunena') {
					self::$subtree[$menutype][$id] = $id;
				} else if ($item->type == 'menulink' && !empty($item->query['Itemid'])) {
					// Jump to link
					$link_id = $item->query['Itemid'];
					if (!empty(self::$menu[$link_id]) && self::$menu[$link_id]->type == 'component' && self::$menu[$link_id]->component == 'com_kunena') {
						self::$subtree[$menutype][$id] = $link_id;
					}
				}
				if (!empty(self::$childlist[$menutype][$id])) {
					$todo = $todo + self::$childlist[$menutype][$id];
				}
			}
		}
		return self::$subtree[$menutype];

	}

	protected static function getKunenaRoot($Itemid) {
		if (!$Itemid) return 0;
		if (!isset(self::$parent[$Itemid])) {
			self::$parent[$Itemid] = 0;
			$current = $Itemid;
			while (isset(self::$menu[$current])) {
				$item = self::$menu[$current];
				if ($item->type == 'component' && $item->component == 'com_kunena') {
					self::$parent[$Itemid] = $current;
					if (isset($item->query['view']) && $item->query['view'] == 'entrypage') break;
				}
				// Support J1.6 and J1.5
				$current = isset($item->parent_id) ? $item->parent_id : $item->parent;
			}
		}
		return self::$parent[$Itemid];
	}

	protected static function getSubMenus($Itemid) {
		if (!isset(self::$subtree[$Itemid])) {
			self::$subtree[$Itemid] = array();
			$menutype = '';
			if ( isset(self::$menu[$Itemid]->menutype) ) $menutype = self::$menu[$Itemid]->menutype;
			$todo = array(intval($Itemid));
			while (($id = array_shift($todo)) !== null) {
				if ( isset( self::$menu[$id] ) ) {
					$item = self::$menu[$id];
					if ($item->type == 'component' && $item->component == 'com_kunena') {
						self::$subtree[$Itemid][$id] = $id;
					} else if ($item->type == 'menulink' && !empty($item->query['Itemid'])) {
						// Jump to link
						$link_id = $item->query['Itemid'];
						if (!empty(self::$menu[$link_id]) && self::$menu[$link_id]->type == 'component' && self::$menu[$link_id]->component == 'com_kunena') {
							self::$subtree[$Itemid][$id] = $link_id;
						}
					}
				}
				if (!empty(self::$childlist[$menutype][$id])) {
					$todo = $todo + self::$childlist[$menutype][$id];
				}
			}
		}
		return self::$subtree[$Itemid];
	}

	protected static function checkEntryPage($item, $query) {
		jimport('joomla.html.parameter');
		$params = new JParameter($item->params);
		$func = isset($item->query['func']) ? $item->query['func'] : '';
		$catids = $params->get('catids');
		if (empty ( $query ['catid'] )) return 0;
		if (!is_array($catids)) {
			$catids = explode(',', $params->get('catids'));
		}
		if (empty ( $catids ) || in_array(0, $catids)) return 0;
		if (in_array($query ['catid'], $catids)) return 0;
		return;
	}

	protected static function isMatch($item, $query) {
		$hits = 0;
		$catid = false;
		if (!empty($item->query['catid'])) {
			$catid = true;
		}
		if (isset($item->query['view'])) {
			if (!isset($item->query['func'])) $item->query['func'] = $item->query['view'];
			unset ($item->query['view']);
		}
		if (isset($query['view'])) {
			if (!isset($query['func'])) $query['func'] = $query['view'];
			unset ($query['view']);
		}
		if (isset($item->query['func']) && $item->query['func'] == 'entrypage') return self::checkEntryPage($item, $query);
		foreach ( $item->query as $var => $value ) {
			if (!isset ( $query [$var] ) || $value != $query [$var]) {
				if ($catid && $var=='func') continue;
				if ($var=='catid' && empty($value)) continue;
				return false;
			} else {
				$hits++;
			}
		}
		return $hits;
	}

	protected static function findItemID($list, $query) {
		$bestmatch = -1;
		$Itemid = 0;
		foreach ($list as $id) {
			$current = self::$menu[$id];
			$matchcount = self::isMatch ( $current, $query );
			if ($matchcount !== false && $matchcount > $bestmatch) {
				// Match! This is our best candidate this far
				$Itemid = $current->id;
				if ($matchcount == count ( $query )) {
					// Perfect match! We just found our Itemid!
					//echo "PERFECT {$Itemid} ";
					return $Itemid;
				}
				$bestmatch = $matchcount;
			}
		}
		//echo "ITEMID  {$Itemid} ";
		return $Itemid;
	}

	protected static function _getItemID($query) {
		// If someone really wants to pass itemid to KunenaRoute, let him do that
		if (isset ( $query ['Itemid'] )) {
			//echo "FIXED {$query['Itemid']} ";
			return $query ['Itemid'];
		}

		self::buildMenuTree();

		$Itemid = 0;
		// Search current tree

		if (self::$active) {
			$root = self::getKunenaRoot(self::$active);
			$list = self::getSubMenus($root);
			if (count($list) > 1) {
				$Itemid = self::findItemID($list, $query);
			}
		}

		if (!$Itemid) {
			$menutype = 'kunenamenu';
			$list = self::getMenuItems($menutype);
			$Itemid = self::findItemID($list, $query);
		}
		return $Itemid;
	}
}
