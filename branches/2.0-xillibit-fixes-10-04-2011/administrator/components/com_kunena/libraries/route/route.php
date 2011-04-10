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
kimport ('kunena.user.herlper');
jimport('joomla.html.parameter');

KunenaRoute::initialize();

abstract class KunenaRoute {
	static $adminApp = false;
	static $config = false;
	static $menus = false;
	static $menu = false;
	static $default = false;
	static $active = false;
	static $home = false;
	static $search = false;

	static $childlist = false;
	static $subtree = array();
	static $parent = array();
	static $uris = array();
	static $urisSave = false;

	public static function current($object = false) {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$uri = self::prepare();
		if (!$uri) return false;
		if ($object) return $uri;
		$result = $uri->getQuery ();
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $result;
	}

	public static function getItemID($uri = null) {
		if (self::$adminApp) {
			// There are no itemids in administration
			return 0;
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$uri = self::prepare($uri);
		if (!$uri) return false;
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $uri->getVar('Itemid');
	}

	public static function _($uri = null, $xhtml = true, $ssl=0) {
		if (self::$adminApp) {
			// Use default routing in administration
			return JRoute::_($uri, $xhtml, $ssl);
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$key = (self::$home ? self::$home->id : 0) .'-'.(int)$xhtml.(int)$ssl. ($uri instanceof JURI ? $uri->toString () : (string) $uri);
		if (!$uri || (is_string($uri) && $uri[0]=='&')) {
			$key = 'a'.(self::$active ? self::$active->id : '') . '-' . $key;
		}
		if (isset(self::$uris[$key])) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return self::$uris[$key];
		}
		$uri = self::prepare($uri);
		if (!$uri) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return false;
		}
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}

		$fragment = $uri->getFragment();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'(t)') : null;
		self::$uris[$key] = JRoute::_ ( 'index.php?' . $uri->getQuery (), $xhtml, $ssl ) . ($fragment ? '#'.$fragment : '');
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'(t)') : null;
		self::$urisSave = true;
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$uris[$key];
	}

	public static function normalize($uri = null, $object = false) {
		if (self::$adminApp) {
			// Use default routing in administration
			return $object ? $uri : 'index.php?' . $uri->getQuery ();
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$uri = self::prepare($uri);
		if (!$uri) return false;
		if (!$uri->getVar('Itemid')) {
			self::setItemID ( $uri );
		}
		$result = $object ? $uri : 'index.php?' . $uri->getQuery ();
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $result;
	}

	public static function getMenu() {
		return self::$home;
	}

	protected static function getHome($item) {
		if (!$item) return null;
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$id = $item->id;
		if (!isset(self::$parent[$id])) {
			if ($item->type == 'component' && $item->component == 'com_kunena' && isset($item->query['view']) && $item->query['view'] == 'home') {
				self::$parent[$id] = $item;
			} else {
				// Support both Joomla 1.5 and 1.6
				$parentid = isset($item->parent_id) ? $item->parent_id : $item->parent;
				$parent = isset(self::$menu[$parentid]) ? self::$menu[$parentid] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$parent[$id];
	}

	public static function cacheLoad() {
		$user = KunenaUserHelper::getMyself();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$cache = JFactory::getCache('_system', 'output');
		$data = $cache->get($user->userid, 'com_kunena.route');
		if ($data !== false) {
			list(self::$subtree, self::$uris) = unserialize($data);
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	public static function cacheStore() {
		if (!self::$urisSave) return;
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$user = KunenaUserHelper::getMyself();
		$data = array(self::$subtree, self::$uris);
		$cache = JFactory::getCache('_system', 'output');
		$cache->store(serialize($data), $user->userid, 'com_kunena.route');
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	public static function initialize() {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		self::$config = KunenaFactory::getConfig ();
		if (JFactory::getApplication()->isAdmin()) {
			self::$adminApp = true;
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return;
		}
		self::$menus = JFactory::getApplication()->getMenu ();
		self::$menu = self::$menus->getMenu ();
		self::$default = self::$menus->getDefault();
		$active = self::$menus->getActive ();
		if ($active && $active->type == 'component' && $active->component == 'com_kunena' && isset($active->query['view'])) {
			self::$active = $active;
		} else {
			self::$active = null;
		}
		self::$home = self::getHome(self::$active);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	protected static function prepare($uri = null) {
		static $current = array();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if (!$uri || (is_string($uri) && $uri[0] == '&')) {
			if (!isset($current[$uri])) {
				$current[$uri] = JURI::getInstance('index.php?'.http_build_query(JRequest::get( 'get' )).$uri);
				$current[$uri]->delVar ( 'Itemid' );
			}
			$uri = $current[$uri];
		} elseif (is_numeric($uri)) {
			if (!isset(self::$menu[intval($uri)])) {
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
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
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return false;
		} elseif ($option && $option != 'com_kunena') {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return false;
		} elseif ($Itemid && (!isset(self::$menu[$Itemid]) || self::$menu[$Itemid]->component != 'com_kunena')) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return false;
		}
		// Support legacy URIs
		if ($uri->getVar('func')) {
			$result = KunenaRouteLegacy::convert($uri);
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
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
				if (!$result) {
					KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
					return false;
				}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $uri;
	}

	protected static function build() {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
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
						self::$search[$item->query['view']][self::$home ? self::$home->id : 0][$item->id] = $item->id;
					}
				}
				$cache->store(serialize(self::$search), 'search', 'com_kunena.route');
			}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	protected static function setItemID($uri) {
		static $candidates = array();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$view = $uri->getVar('view');
		$catid = (int) $uri->getVar('catid');
		$key = $view.$catid;
		if (!isset($candidates[$key])) {
			if (self::$search === false) self::build();
			$search = array();
			if (self::$home) {
				// Search from the current home menu
				$search[self::$home->id] = 1;
				// Then search from all linked home menus
				$search += self::$search['home'][self::$home->id];
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
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
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