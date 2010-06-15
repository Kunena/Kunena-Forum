<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/api.php');
kimport('error');

class KunenaRouter {
	static $catidcache = null;
	static $msgidcache = array ();

	// List of reserved functions (if category name is one of these, use always catid)
	static $functions = array ('who', 'announcement', 'poll', 'polls', 'stats', 'myprofile', 'userprofile', 'fbprofile',
		'profile', 'userlist', 'post', 'view', 'help', 'showcat', 'listcat', 'review', 'rules', 'report',
		'latest', 'mylatest', 'noreplies', 'subscriptions', 'favorites', 'userposts', 'unapproved',
		'deleted', 'search', 'advsearch', 'markthisread', 'subscribecat', 'unsubscribecat', 'karma',
		'bulkactions', 'templatechooser', 'credits', 'json', 'rss', 'pdf', 'fb_pdf', 'article', 'entrypage', 'thankyou' );

	function loadCategories() {
		if (self::$catidcache !== null)
			return; // Already loaded

		$db = & JFactory::getDBO ();

		$query = 'SELECT id, name, parent FROM #__kunena_categories WHERE published=1';
		$db->setQuery ( $query );
		self::$catidcache = $db->loadAssocList ( 'id' );
		if (KunenaError::checkDatabaseError()) return;
	}

	/**
	 * Preloads messages, saves SQL queries
	 *
	 * @param $msgs Messages in form of array ('1'=>'subject', ...))
	 */
	function loadMessages($msglist) {
		if (!is_array($msglist)) return;
		self::$msgidcache = self::$msgidcache + $msglist;
	}

	function isCategoryConflict($catid, $catname) {
		foreach (self::$catidcache as $cat) {
			if ($cat ['id'] != $catid && $catname == self::stringURLSafe ( $cat ['name'] ) ) return true;
		}
		return false;
	}

	function filterOutput($str) {
		return JString::trim ( preg_replace ( array ('/\s+/', '/[\$\&\+\,\/\:\;\=\?\@\'\"\<\>\#\%\{\}\|\\\^\~\[\]\`\.]/' ), array ('-', '' ), $str ) );
	}

	function stringURLSafe($str) {
		$kconfig =  KunenaFactory::getConfig ();
		if ($kconfig->sefutf8) {
			$str = self::filterOutput ( $str );
			return urlencode ( $str );
		}
		return JFilterOutput::stringURLSafe ( $str );
	}
	/**
	 * Build SEF URL
	 *
	 * All SEF URLs are formatted like this:
	 *
	 * http://site.com/menuitem/1-category-name/10-subject/[func]/[do]/[param1]-value1/[param2]-value2?param3=value3&param4=value4
	 *
	 * - If catid exists, category will always be in the first segment
	 * - If there is no catid, second segment for message will not be used (param-value: id-10)
	 * - [func] and [do] are the only parameters without value
	 * - all other segments (task, id, userid, page, sel) are using param-value format
	 *
	 * NOTE! Only major variables are using SEF segments
	 *
	 * @param $query
	 * @return segments
	 */
	function BuildRoute(&$query) {
		$parsevars = array ('task', 'id', 'userid', 'page', 'sel' );
		$segments = array ();

		$kconfig = KunenaFactory::getConfig ();
		if (! $kconfig->sef)
			return $segments;

		if (isset ( $query ['Itemid'] ) && $query ['Itemid'] > 0) {
			// If we have Itemid, make sure that we remove identical parameters
			$menu = JSite::getMenu ();
			$menuitem = $menu->getItem ( $query ['Itemid'] );
			if ($menuitem) {
				foreach ( $menuitem->query as $var => $value ) {
					if ($var == 'Itemid' || $var == 'option')
						continue;
					if (isset ( $query [$var] ) && $value == $query [$var]) {
						unset ( $query [$var] );
					}
				}
				if (isset ( $query ['view'] )) {
					$query ['func'] = $query ['view'];
					unset ($query ['view']);
				}
			}
		}

		$db = & JFactory::getDBO ();
		jimport ( 'joomla.filter.output' );

		// We may have catid also in the menu item
		$catfound = isset ( $menuitem->query ['catid'] );
		// If we had identical catid in menuitem, this one will be skipped:
		if (isset ( $query ['catid'] )) {
			$catid = ( int ) $query ['catid'];
			if ($catid != 0) {
				$catfound = true;

				if (self::$catidcache === null)
					self::loadCategories ();
				if (isset ( self::$catidcache [$catid] )) {
					$suf = self::stringURLSafe ( self::$catidcache [$catid] ['name'] );
				}
				if (empty ( $suf ))
					// If translated category name is empty, use catid: 123
					$segments [] = $query ['catid'];
				else if ($kconfig->sefcats && ! in_array ( $suf, self::$functions )) {
					// We want to remove catid: check that there are no conflicts between names
					if (self::isCategoryConflict($catid, $suf)) {
						$segments [] = $query ['catid'] . '-' . $suf;
					} else {
						$segments [] = $suf;
					}
				} else {
					// By default use 123-category_name
					$segments [] = $query ['catid'] . '-' . $suf;
				}
			}
			unset ( $query ['catid'] );
		}

		if ($catfound && isset ( $query ['id'] )) {
			$id = $query ['id'];
			if (! isset ( self::$msgidcache [$id] )) {
				$quesql = 'SELECT subject, id FROM #__kunena_messages WHERE id=' . ( int ) $id;
				$db->setQuery ( $quesql );
				self::$msgidcache [$id] = $db->loadResult ();
				if (KunenaError::checkDatabaseError()) return;
			}
			$suf = self::stringURLSafe ( self::$msgidcache [$id] );
			if (empty ( $suf ))
				$segments [] = $query ['id'];
			else
				$segments [] = $query ['id'] . '-' . $suf;
			unset ( $query ['id'] );
		}

		if (isset ( $query ['func'] )) {
			if ($query ['func'] != 'showcat' && $query ['func'] != 'view' && ! ($query ['func'] == 'listcat' && $catfound)) {
				$segments [] = $query ['func'];
			}
			unset ( $query ['func'] );
		}

		if (isset ( $query ['do'] )) {
			$segments [] = $query ['do'];
			unset ( $query ['do'] );
		}

		foreach ( $parsevars as $var ) {
			if (isset ( $query [$var] )) {
				$segments [] = "{$var}-{$query[$var]}";
				unset ( $query [$var] );
			}
		}

		return $segments;
	}

	function ParseRoute($segments) {
		$funcitems = array (array ('func' => 'showcat', 'var' => 'catid' ), array ('func' => 'view', 'var' => 'id' ) );
		$doitems = array ('func', 'do' );
		$counter = 0;
		$funcpos = $dopos = 0;
		$vars = array ();

		$kconfig =  KunenaFactory::getConfig ();

		// Get current menu item
		$menu = JSite::getMenu ();
		$active = $menu->getActive ();

		// Fill data from the menu item
		$menuquery = isset ( $active->query ) ? $active->query : array ();
		foreach ( $menuquery as $var => $value ) {
			$vars [$var] = $value;
		}
		if (isset ( $vars ['view'] )) {
			$vars ['func'] = $vars ['view'];
			unset ( $vars ['view'] );
		}
		if (isset ( $vars ['func']) && $vars ['func'] == 'entrypage') {
			unset ( $vars ['func'] );
		}

		while ( ($segment = array_shift ( $segments )) !== null ) {
			$seg = explode ( ':', $segment );
			$var = array_shift ( $seg );
			$value = array_shift ( $seg );

			// If SEF categories are allowed we need to translate category name to catid
			if ($kconfig->sefcats && $counter == 0 && ($value !== null || ! in_array ( $var, self::$functions ))) {
				self::loadCategories ();
				$catname = strtr ( $segment, ':', '-' );
				foreach ( self::$catidcache as $cat ) {
					if ($catname == self::filterOutput ( $cat ['name'] ) || $catname == JFilterOutput::stringURLSafe ( $cat ['name'] )) {
						$var = $cat ['id'];
						break;
					}
				}
			}

			if (empty ( $var ))
				continue; // Empty parameter

			if (is_numeric ( $var )) {
				// Numeric value is always category or id (in this order)
				$value = $var;
				if (!isset($vars ['catid']) || $vars ['catid'] < 1) {
					$var = 'catid';
				} else if (!isset($vars ['id']) || $vars ['id'] < 1) {
					$var = 'id';
				} else {
					// Unknown parameter, skip it
					continue;
				}
			} else if ($value === null) {
				// Variable must be either func or do
				$value = $var;
				if (in_array ( $var, self::$functions )) {
					$var = 'func';
				} else if (isset($vars ['func']) && !isset($vars ['do'])) {
					$var = 'do';
				} else {
					// Unknown parameter: continue
					if (isset($vars ['func'])) continue;
					// Oops: unknown function or non-existing category
					$var = 'func';
				}
			}
			$vars [$var] = $value;
			$counter++;
		}

		if (isset($vars['catid']) && (!isset($vars ['func']) || $vars ['func'] == 'listcat')) {
			// If we have catid, function cannot be listcat
			$vars ['func'] = 'showcat';
		}
		if (isset($vars['id']) && $vars ['func'] == 'showcat') {
			// If we have id, function cannot be showcat
			$vars ['func'] = 'view';
		}
		// Check if we should use listcat instead of showcat
		if (isset ( $vars ['func'] ) && $vars ['func'] == 'showcat') {
			if (empty ( $vars ['catid'] )) {
				$parent = 0;
			} else {
				$db = & JFactory::getDBO ();
				$quesql = 'SELECT parent FROM #__kunena_categories WHERE id=' . ( int ) $vars ['catid'];
				$db->setQuery ( $quesql );
				$parent = $db->loadResult ();
				if (KunenaError::checkDatabaseError()) return;
			}
			if (! $parent)
				$vars ['func'] = 'listcat';
		}
		return $vars;
	}

}

function KunenaBuildRoute(&$query) {
	return KunenaRouter::BuildRoute ( $query );
}

function KunenaParseRoute($segments) {
	return KunenaRouter::ParseRoute ( $segments );
}
