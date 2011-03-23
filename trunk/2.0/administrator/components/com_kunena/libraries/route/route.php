<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.environment.uri' );
kimport ('kunena.route.legacy');
jimport('joomla.html.parameter');

abstract class KunenaRoute {
	static $menus = false;
	static $menu = false;
	static $default = false;
	static $active = false;
	static $search = false;

	static $childlist = false;
	static $subtree = array();
	static $parent = array();
	static $uris = array();
	static $urisSave = false;

	public static function current($object = false) {
		if (self::$menus === false) self::initialize();
		$uri = self::prepare();
		if (!$uri) return false;
		if ($object) return $uri;
		return $uri->getQuery ();
	}

	public static function getItemID($uri = null) {
		if (JFactory::getApplication()->isAdmin()) {
			// There are no itemids in administration
			return 0;
		}
		if (self::$menus === false) self::initialize();
		$uri = self::prepare($uri);
		if (!$uri) return false;
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}
		return $uri->getVar('Itemid');
	}

	public static function _($uri = null, $xhtml = true, $ssl=0) {
		if (JFactory::getApplication()->isAdmin()) {
			// Use default routing in administration
			return JRoute::_($uri, $xhtml, $ssl);
		}
		if (self::$menus === false) self::initialize();
		$home = self::getHome(self::$active);

		$key = ($home ? $home->id : 0) .'-'.(int)$xhtml.(int)$ssl. ($uri instanceof JURI ? $uri->toString () : (string) $uri);
		if (isset(self::$uris[$key])) {
			return self::$uris[$key];
		}
		$uri = self::prepare($uri);
		if (!$uri) return false;
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}

		$fragment = $uri->getFragment();
		self::$uris[$key] = JRoute::_ ( 'index.php?' . $uri->getQuery (), $xhtml, $ssl ) . ($fragment ? '#'.$fragment : '');
		self::$urisSave = true;
		return self::$uris[$key];
	}

	public static function normalize($uri = null) {
		if (self::$menus === false) self::initialize();

		$uri = self::prepare($uri);
		if (!$uri) return false;
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}

		return 'index.php?' . $uri->getQuery ();
	}

	public static function getHome($item) {
		if (!$item) return null;
		$id = $item->id;
		if (!isset(self::$parent[$id])) {
			if ($item->type == 'component' && $item->component == 'com_kunena' && isset($item->query['view']) && $item->query['view'] == 'home') {
				self::$parent[$id] = $item;
			} else {
				if (self::$menus === false) self::initialize();
				// Support both Joomla 1.5 and 1.6
				$parentid = isset($item->parent_id) ? $item->parent_id : $item->parent;
				$parent = isset(self::$menu[$parentid]) ? self::$menu[$parentid] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}
		return self::$parent[$id];
	}

	public static function getMenu() {
		if (self::$menus === false) self::initialize();
		return self::getHome(self::$active);
	}

	public static function cacheLoad() {
		$user = KunenaFactory::getUser();
		$cache = JFactory::getCache('_system', 'output');
		$data = $cache->get($user->userid, 'com_kunena.route');
		if ($data === false) return;
		list(self::$subtree, self::$uris) = unserialize($data);
	}

	public static function cacheStore() {
		if (!self::$urisSave) return;
		$user = KunenaFactory::getUser();
		$data = array(self::$subtree, self::$uris);
		$cache = JFactory::getCache('_system', 'output');
		$cache->store(serialize($data), $user->userid, 'com_kunena.route');
	}

	protected static function initialize() {
		self::$menus = JFactory::getApplication()->getMenu ();
		self::$menu = self::$menus->getMenu ();
		self::$default = self::$menus->getDefault();
		$active = self::$menus->getActive ();
		if ($active && $active->type == 'component' && $active->component == 'com_kunena' && isset($item->query['view'])) {
			self::$active = $active;
		} else {
			self::$active = null;
		}
	}

	protected static function prepare($uri = null) {
		static $current = false;
		if (!$uri) {
			if (!$current) {
				$current = JURI::getInstance('index.php?'.http_build_query(JRequest::get( 'get' )));
				$current->delVar ( 'Itemid' );
			}
			$uri = $current;
		} elseif (is_numeric($uri)) {
			if (!isset(self::$menu[intval($uri)])) {
				return false;
			}
			$item = self::$menu[intval($uri)];
			$uri = JURI::getInstance ( "{$item->link}&Itemid={$item->id}" );
		} elseif ($uri instanceof JURI) {
			// Nothing to do
		} else {
			$uri = JURI::getInstance ( (string)$uri );
		}
		$option = $uri->getVar('option');
		$Itemid = $uri->getVar('Itemid');
		if (!$option && !$Itemid) {
			return false;
		} elseif ($option && $option != 'com_kunena') {
			return false;
		} elseif ($Itemid && (!isset(self::$menu[$Itemid]) || self::$menu[$Itemid]->component != 'com_kunena')) {
			return false;
		}
		// Support legacy URIs
		if ($uri->getVar('func')) {
			$result = KunenaRouteLegacy::convert($uri);
			if (!$result) return false;
			return $uri;
		}
		// Check URI
		switch ($uri->getVar('view', 'home')) {
			case 'announcement':
				KunenaRouteLegacy::convert($uri);
				$r = array();
				break;
			case 'category':
				$r = array('catid', 'limitstart', 'limit');
				break;
			case 'common':
				$r = array();
				break;
			case 'credits':
				$r = array();
				break;
			case 'home':
				$r = array();
				break;
			case 'misc':
				$r = array();
				break;
			case 'search':
				$r = array('q', 'titleonly', 'searchuser', 'starteronly', 'exactname', 'replyless',
					'replylimit', 'searchdate', 'beforeafter', 'sortby', 'order', 'childforums', 'catids',
					'show', 'limitstart', 'limit');
				break;
			case 'statistics':
				$r = array();
				break;
			case 'topic':
				$r = array('catid', 'id', 'mesid', 'limitstart', 'limit');
				break;
			case 'topics':
				$r = array('mode', 'userid', 'sel', 'limitstart', 'limit');
				break;
			case 'user':
				$r = array('userid');
				break;
			case 'users':
				$r = array('search', 'limitstart', 'limit');
				break;
			default:
				$result = KunenaRouteLegacy::convert($uri);
				if (!$result) return false;
		}
		return $uri;
	}

	protected static function build() {
		if (self::$search === false) {
			$cache = JFactory::getCache('_system', 'output');
			self::$search = unserialize($cache->get('search', 'com_kunena.route'));
			if (self::$search === false) {
				self::$search['home'] = array();
				foreach ( self::$menu as $item ) {
					if (! is_object ( $item ))
						continue;
					// Follow links
					if ($item->type == 'menulink' && !empty($item->query['Itemid']) && !empty(self::$menu[$item->query['Itemid']])) {
						$item = self::$menu[$item->query['Itemid']];
					}
					// Save Kunena menu items so that we can make fast searches
					if ($item->type == 'component' && $item->component == 'com_kunena' && isset($item->query['view'])) {
						$home = self::getHome($item);
						self::$search[$item->query['view']][$home ? $home->id : 0][$item->id] = $item->id;
					}
				}
				$cache->store(serialize(self::$search), 'search', 'com_kunena.route');
			}
		}
	}

	protected static function setItemID($uri) {
		static $candidates = array();

		$view = $uri->getVar('view');
		$catid = (int) $uri->getVar('catid');
		$key = $view.$catid;
		if (!isset($candidates[$key])) {
			if (self::$search === false) self::build();
			$home = self::getHome(self::$active);
			$search = array();
			if ($home) {
				// Search from the current home menu
				$search[$home->id] = 1;
				// Then search from all linked home menus
				$search += self::$search['home'][$home->id];
			}
			// Finally search from other home menus
			$search += self::$search['home'];

			// Find all potential candidates
			$candidates[$key] = array();
			foreach ($search as $id=>$dummy) {
				$follow = isset(self::$menu[$id]) ? self::$menu[$id] : null;
				if ($follow && self::checkHome($follow, $catid)) {
					$candidates[$key] += !empty(self::$search[$view][$follow->id]) ? self::$search[$view][$follow->id] : array();
					if ($view == 'topic') $candidates[$key] += !empty(self::$search['category'][$follow->id]) ? self::$search['category'][$follow->id] : array();
					$candidates[$key][$follow->id] = $follow->id;
				}
			}
			// Don't forget lonely candidates
			$candidates[$key] += !empty(self::$search[$view][0]) ? self::$search[$view][0] : array();
			if ($view == 'topic') $candidates[$key] += !empty(self::$search['category'][0]) ? self::$search['category'][0] : array();
		}
		$bestid = $bestcount = 0;
		//echo "$key "; print_r($candidates[$key]);
		foreach ($candidates[$key] as $id) {
			$item = self::$menu[$id];
			switch ($item->query['view']) {
				case 'home':
					$matchcount = 1;
					break;
				case 'category':
				case 'topic':
					$matchcount = self::checkCategory($item, $uri);
					break;
				default:
					$matchcount = self::check($item, $uri);
			}
			if ($matchcount > $bestcount) {
				// This is our best candidate this far
				$bestid = $item->id;
				$bestcount = $matchcount;
			}
		}
		$uri->setVar('Itemid', $bestid);
		return $bestid;
	}

	protected static function checkHome($item, $catid) {
		static $cache = array();
		if (!$catid) return true;
		if (!isset($cache[$item->id])) {
			$params = new JParameter($item->params);
			$catids = $params->get('catids', array());
			if (!is_array($catids)) {
				$catids = explode(',', $catids);
			}
			if (!empty($catids)) {
				$catids = array_combine($catids, $catids);
			}
			unset($catids[0], $catids['']);
			$cache[$item->id] = (array) $catids;
		}
		return empty($cache[$item->id]) || isset($cache[$item->id][$catid]);
	}

	protected static function checkCategory($item, $uri) {
		static $cache = array();
		$catid = (int) $uri->getVar('catid');
		if (!$catid) return self::check($item, $uri);
		if (!isset($cache[$item->id])) {
			$cache[$item->id] = array();
			if (!empty($item->query['catid'])) {
				$cache[$item->id] = KunenaForumCategoryHelper::getChildren($item->query['catid']);
				$cache[$item->id][$item->query['catid']] = KunenaForumCategoryHelper::get($item->query['catid']);
			}
		}
		return isset($cache[$item->id][$catid]) * 8;
	}

	protected static function check($item, $uri) {
		$hits = 0;
		foreach ( $item->query as $var => $value ) {
			if ($value != $uri->getVar($var)) {
				return 0;
			}
			$hits++;
		}
		return $hits;
	}
}