<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

jimport ('joomla.filter.output');
kimport('kunena.error');
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');

class KunenaRouter {
	static $catidcache = null;

	// List of reserved views (if category name is one of these, use always catid)
	// Contains array of default variable=>value pairs, which can be removed from URI
	static $views = array (
		'home'=>array(),
		'category'=>array('layout'=>'default'),
		'categories'=>array('layout'=>'default', 'catid'=>'0'),
		'topic'=>array('layout'=>'default'),
		'topics'=>array('layout'=>'default'),
		'user'=>array('layout'=>'default', 'userid'=>'0'),
		'users'=>array('layout'=>'default'),
		'statistics'=>array('layout'=>'default'),
	);
	// List of legacy views from previous releases
	static $functions = array (
		'listcat'=>1,
		'showcat'=>1,
		'latest'=>1,
		'mylatest'=>1,
		'noreplies'=>1,
		'subscriptions'=>1,
		'favorites'=>1,
		'userposts'=>1,
		'unapproved'=>1,
		'deleted'=>1,
		'view'=>1,
		'profile'=>1,
		'myprofile'=>1,
		'userprofile'=>1,
		'fbprofile'=>1,
		'moderateuser'=>1,
		'userlist'=>1,
		'rss'=>1,
		'post'=>1,

		'announcement'=>1,
		'article'=>1,
		'who'=>1,
		'poll'=>1,
		'polls'=>1,
		'stats'=>1,
		'help'=>1,
		'review'=>1,
		'rules'=>1,
		'report'=>1,
		'search'=>1,
		'advsearch'=>1,
		'markthisread'=>1,
		'subscribecat'=>1,
		'unsubscribecat'=>1,
		'karma'=>1,
		'bulkactions'=>1,
		'templatechooser'=>1,
		'credits'=>1,
		'json'=>1,
		'pdf'=>1,
		'entrypage'=>1,
		'thankyou'=>1,
		'fb_pdf'=>1,
	);
	function loadCategories() {
		if (self::$catidcache !== null)
			return; // Already loaded

		$categories = KunenaForumCategoryHelper::getCategories();
		self::$catidcache = array();
		foreach ($categories as $id=>$category) {
			self::$catidcache[$id] = self::stringURLSafe ( $category->name );
		}
	}

	function isCategoryConflict($catid, $catname) {
		$keys = array_keys(self::$catidcache, $catname);
		return count($keys) > 1;
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
	 * http://site.com/menuitem/1-category-name/10-subject/[view]/[layout]/[param1]-value1/[param2]-value2?param3=value3&param4=value4
	 *
	 * - If catid exists, category will always be in the first segment
	 * - If there is no catid, second segment for message will not be used (param-value: id-10)
	 * - [view] and [layout] are the only parameters without value
	 * - all other segments (task, id, userid, page, sel) are using param-value format
	 *
	 * NOTE! Only major variables are using SEF segments
	 *
	 * @param $query
	 * @return segments
	 */
	function BuildRoute(&$query) {
		$parsevars = array ('do', 'task', 'userid', 'id', 'mesid', 'page', 'sel' );
		$segments = array ();

		// If Kunena SEF is not enabled, do nothing
		$config = KunenaFactory::getConfig ();
		if (! $config->sef) {
			return $segments;
		}

		// DEPRECATED: Legacy support: URI has func instead of view
		if (isset ( $query ['func'])) {
			$query ['view'] = $query ['func'];
			unset ($query ['func']);
		}

		// Get menu item
		if (isset ( $query ['Itemid'] )) {
			$query ['Itemid'] = (int) $query ['Itemid'];
			$menuitem = JFactory::getApplication()->getMenu ()->getItem ( $query ['Itemid'] );
			if (!$menuitem) {
				// Itemid doesn't exist or is invalid
				unset ($query ['Itemid']);
			}
		}

		// Safety check: we need view in order to create SEF URLs
		if (!isset ( $menuitem->query ['view'] ) && !isset ( $query ['view'] )) {
			return $segments;
		}

		// Get view for later use (query wins menu item)
		$view = isset ( $query ['view'] ) ? (string) preg_replace( '/[^A-Z_]/i', '', $query ['view'] ) : $menuitem->query ['view'];

		// Get default values for URI variables
		if (isset(self::$views[$view])) {
			$defaults = self::$views[$view];
		}
		// Check all URI variables and remove those which aren't needed
		foreach ( $query as $var => $value ) {
			if (isset ( $defaults [$var] ) && !isset ( $menuitem->query [$var] ) && $value == $defaults [$var] ) {
				// Remove URI variable which has default value
				unset ( $query [$var] );
			} elseif ( isset ( $menuitem->query [$var] ) && $value == $menuitem->query [$var] && $var != 'Itemid' && $var != 'option' ) {
				// Remove URI variable which has the same value as menu item
				unset ( $query [$var] );
			}
		}

		// We may have catid also in the menu item (it will not be in URI)
		$numeric = isset ( $menuitem->query ['catid'] );

		// Support URIs like: /forum/12-my_category
		if (isset ( $query ['catid'] )) {
			// TODO: ensure that we have view=categories/category/topic
			$catid = ( int ) $query ['catid'];
			if ($catid) {
				$numeric = true;

				// Load categories
				self::loadCategories ();
				if (isset ( self::$catidcache [$catid] )) {
					$catname = self::$catidcache [$catid];
				}
				if (empty ( $catname )) {
					// If category name is empty (or doesn't exist), use numeric catid
					$segments [] = $catid;
				} elseif ($config->sefcats && ! isset ( self::$views[$catname] ) && !self::isCategoryConflict($catid, $catname)) {
					// If there's no naming conflict, we can use category name
					$segments [] = $catname;
				} else {
					// By default use 123-category_name
					$segments [] = "{$catid}-{$catname}";
				}
				// This segment fully defines category and categories views so the variable is no longer needed
				if ($view == 'category' || $view == 'categories') {
					unset ( $query ['view'] );
				}
				// DEPRECATED: Legacy support for func=listcat/showcat
				if ($view == 'listcat' || $view == 'showcat') {
					unset ( $query ['view'] );
				}
			}
			unset ( $query ['catid'] );
		}

		// Support URIs like: /forum/12-category/123-topic
		if (isset ( $query ['id'] ) && $numeric) {
			$id = (int) $query ['id'];
			if ($id) {
				$subject = self::stringURLSafe ( KunenaForumTopicHelper::get($id)->subject );
				if (empty ( $subject )) {
					$segments [] = $id;
				} else {
					$segments [] = "{$id}-{$subject}";
				}
				// This segment fully defines topic view so the variable is no longer needed
				if ($view == 'topic') {
					unset ( $query ['view'] );
				}
				// DEPRECATED: Legacy support for func=view
				if ($view == 'view') {
					unset ( $query ['view'] );
				}
			}
			unset ( $query ['id'] );
		} else {
			// No id available, do not use numeric variable for mesid
			$numeric = false;
		}

		// View gets added only when we do not use short URI for category/topic
		if (isset ( $query ['view'] )) {
			// Use filtered value
			$segments [] = $view;
			unset ( $query ['view'] );
		}

		// Support URIs like: /forum/12-category/123-topic/reply
		if (isset ( $query ['layout'] )) {
			// Use filtered value
			$segments [] = (string) preg_replace( '/[^A-Z_]/i', '', $query ['layout'] );
			unset ( $query ['layout'] );
		} elseif (isset ( $query ['do'] )) {
			// DEPRECATED: Legacy support for do
			$segments [] = (string) preg_replace( '/[^A-Z_]/i', '', $query ['do'] );
			unset ( $query ['do'] );
		}

		// Support URIs like: /forum/12-category/123-topic/reply/124
		if (isset ( $query ['mesid'] ) && $numeric) {
			$segments [] = (int) $query ['mesid'];
			unset ( $query ['mesid'] );
		}

		// Rest of the known parameters are in var-value form
		foreach ( $parsevars as $var ) {
			if (isset ( $query [$var] )) {
				$segments [] = "{$var}-{$query[$var]}";
				unset ( $query [$var] );
			}
		}

		return $segments;
	}

	function ParseRoute($segments) {
		// Get current menu item and get query variables from it
		$active = JFactory::getApplication()->getMenu ()->getActive ();
		$vars = isset ( $active->query ) ? $active->query : array ('view'=>'home');
		if (empty($vars['view']) || $vars['view']=='home' || $vars['view']=='entrypage') {
			$vars['view'] = '';
		}

		// Fix bug in Joomla 1.5 when using /components/kunena instead /component/kunena
		if (!$active && $segments[0] == 'kunena') array_shift ( $segments );

		// Enable SEF category feature
		$sefcats = KunenaFactory::getConfig ()->sefcats;

		// Handle all segments
		while ( ($segment = array_shift ( $segments )) !== null ) {
			$seg = explode ( ':', $segment );
			$var = array_shift ( $seg );
			$value = array_shift ( $seg );

			if (is_numeric ( $var )) {
				// Numeric variable is always catid or id
				$value = $var;
				if (empty($vars ['catid'])) {
					// First number is always category
					$var = 'catid';
					$vars ['view'] = KunenaForumCategoryHelper::get($value)->parent_id ? 'category' : 'categories';
				} elseif (empty($vars ['id'])) {
					// Second number is always topic
					$var = 'id';
					$vars ['view'] = 'topic';
				} elseif (empty($vars ['mesid'])) {
					// Third number is always message
					$var = 'mesid';
				} else {
					// Invalid parameter, skip it
					continue;
				}
			} elseif (empty ( $var ) && empty ( $value )) {
				// Invalid parameter, skip it
				continue;
			} elseif ($sefcats && empty($vars ['catid']) && ($value !== null || ! isset ( self::$views[$var] ))) {
				// We have SEF category: translate category name into catid=123
				// TODO: cache filtered values to gain some speed -- I would like to start using category names instead of catids if it gets fast enough
				$var = 'catid';
				$value = 0;
				$catname = strtr ( $segment, ':', '-' );
				$categories = KunenaForumCategoryHelper::getCategories();
				foreach ( $categories as $category ) {
					if ($catname == self::filterOutput ( $category->name ) || $catname == JFilterOutput::stringURLSafe ( $category->name )) {
						$value = (int) $category->id;
						break;
					}
				}
				$vars ['view'] = KunenaForumCategoryHelper::get($value)->parent_id ? 'category' : 'categories';
			} elseif ($value === null) {
				// Variable must be either view or layout
				$value = $var;
				if (isset ( self::$views[$var] ) || isset ( self::$functions[$var] )) {
					$var = 'view';
				} elseif (!empty($vars ['view']) && empty($vars ['layout'])) {
					$var = 'layout';
				} else {
					// Unknown parameter: continue
					if (!empty($vars ['view'])) continue;
					// Oops: unknown view or non-existing category
					$var = 'view';
				}
			}
			$vars [$var] = $value;
			$sefcats = 0;
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
