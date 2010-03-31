<?php
/**
 * @version $Id$
 * Kunena Component - Kunena Factory
 * @package Kunena
 *
 * @Copyright (C) 2009 www.kunena.com All rights reserved
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
	static $menu = null;

	public static function _($uri, $xhtml = true) {
		jimport ( 'joomla.environment.uri' );
		$link = new JURI ( $uri );
		$query = $link->getQuery ( true );

		$Itemid = self::getItemID ( $query );
		if ($Itemid > 0) $link->setVar ( 'Itemid', $Itemid );

		//echo $link->getQuery();
		return JRoute::_ ( 'index.php?' . $link->getQuery () );
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
				if ($item->published && (!isset ( $item->access ) || $item->access <= $my->aid)) {
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
				}
				if (!empty(self::$childlist[$menutype][$id])) {
					$todo = $todo + self::$childlist[$menutype][$id];
				}
			}
		}
		return self::$subtree[$menutype];

	}

	protected static function getSubMenus($Itemid) {
		if (!isset(self::$subtree[$Itemid])) {
			self::$subtree[$Itemid] = array();
			$menutype = self::$menu[$Itemid]->menutype;
			$todo = array(intval($Itemid));
			while (($id = array_shift($todo)) !== null) {
				$item = self::$menu[$id];
				if ($item->type == 'component' && $item->component == 'com_kunena') {
					self::$subtree[$Itemid][$id] = $id;
				}
				if (!empty(self::$childlist[$menutype][$id])) {
					$todo = $todo + self::$childlist[$menutype][$id];
				}
			}
		}
		return self::$subtree[$Itemid];
	}

	protected static function isMatch($item, $query) {
		$hits = 0;
		$catid = false;
		if (isset($item->query['catid'])) {
			$catid = true;
		}
		foreach ( $item->query as $var => $value ) {
			if (!isset ( $query [$var] ) || $value != $query [$var]) {
				if (!$catid || $var!='func')
					return false;
			} else {
				$hits++;
			}
		}
		return $hits;
	}

	protected static function findItemID($list, $query) {
		$bestmatch = 0;
		$Itemid = 0;
		foreach ($list as $id) {
			$current = self::$menu[$id];
			$matchcount = self::isMatch ( $current, $query );
			if ($matchcount > $bestmatch) {
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

	protected static function getItemID($query) {
		// If someone really wants to pass itemid to KunenaRoute, let him do that
		if (isset ( $query ['Itemid'] )) {
			//echo "FIXED {$query['Itemid']} ";
			return $query ['Itemid'];
		}

		self::buildMenuTree();

		$Itemid = 0;
		// Search current tree
/*
		if (self::$active) {
			$menutype = self::$menu[self::$active]->menutype;
			$list = self::getMenuItems($menutype);
			$Itemid = self::findItemID($list, $query);
		}
*/
		if (!$Itemid) {
			$menutype = 'kunenamenu';
			$list = self::getMenuItems($menutype);
			$Itemid = self::findItemID($list, $query);
		}
		return $Itemid;
	}
}
