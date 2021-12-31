<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Route
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.environment.uri');
jimport('joomla.html.parameter');
jimport('joomla.filter.output');

KunenaRoute::initialize();

/**
 * Class KunenaRoute
 * @since Kunena
 */
abstract class KunenaRoute
{
	/**
	 * @var array
	 * @since Kunena
	 */
	public static $views = array(
		'attachment'   => array('layout' => 'default', 'thumb' => 0, 'download' => 0),
		'announcement' => array('layout' => 'default'),
		'category'     => array('layout' => 'default', 'catid' => '0'),
		'common'       => array('layout' => 'default'),
		'credits'      => array('layout' => 'default'),
		'home'         => array(),
		'misc'         => array('layout' => 'default'),
		'search'       => array('layout' => 'default'),
		'statistics'   => array('layout' => 'default'),
		'topic'        => array('layout' => 'default'),
		'topics'       => array('layout' => 'default'),
		'user'         => array('layout' => 'default', 'userid' => '0'),
	);

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $layouts = array('create' => 1, 'default' => 1, 'edit' => 1, 'manage' => 1, 'moderate' => 1, 'user' => 1);

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $sefviews = array('' => 1, 'home' => 1, 'category' => 1, 'topic' => 1);

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $parsevars = array('do' => 1, 'task' => 1, 'mode' => 1, 'catid' => 1, 'id' => 1, 'mesid' => 1, 'userid' => 1, 'page' => 1, 'sel' => 1);

	/**
	 * @var integer
	 * @since Kunena
	 */
	public static $time = 0;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $adminApp = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $config = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $menus = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $menu = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $default = false;

	/**
	 * @var null
	 * @since Kunena
	 */
	public static $active = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $home = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $search = false;

	/**
	 * @var null
	 * @since Kunena
	 */
	public static $current = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $childlist = false;

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $subtree = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $parent = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public static $uris = array();

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public static $urisSave = false;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $filtered = array();

	/**
	 * @param   bool $object object
	 *
	 * @return boolean|\Joomla\CMS\Uri\Uri|null|string
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function current($object = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$uri = self::prepare();

		if (!$uri)
		{
			return false;
		}

		if ($object)
		{
			return $uri;
		}

		$result = $uri->getQuery();
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @param   null $uri uri
	 *
	 * @return boolean|\Joomla\CMS\Uri\Uri|null
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected static function prepare($uri = null)
	{
		static $current = array();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!$uri || (is_string($uri) && $uri[0] == '&'))
		{
			if (!isset($current[$uri]))
			{
				$get = self::$current->getQuery(true);
				$uri = $current[$uri] = Uri::getInstance('index.php?' . http_build_query($get) . $uri);
				self::setItemID($uri);
				$uri->delVar('defaultmenu');
				$uri->delVar('language');
			}
			else
			{
				$uri = $current[$uri];
			}
		}
		elseif (is_numeric($uri))
		{
			if (!isset(self::$menu[intval($uri)]))
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return false;
			}

			$item = self::$menu[intval($uri)];
			$uri  = Uri::getInstance("{$item->link}&Itemid={$item->id}");
		}
		elseif ($uri instanceof \Joomla\CMS\Uri\Uri)
		{
			// Nothing to do
		}
		else
		{
			$uri = new \Joomla\CMS\Uri\Uri((string) $uri);
		}

		$option = $uri->getVar('option');
		$Itemid = $uri->getVar('Itemid');

		if (!$option && !$Itemid)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return false;
		}
		elseif ($option && $option != 'com_kunena')
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return false;
		}
		elseif ($Itemid && (!isset(self::$menu[$Itemid]) || self::$menu[$Itemid]->component != 'com_kunena'))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return false;
		}

		// Support legacy URIs
		$legacy_urls = self::$config->get('legacy_urls', 1);

		if ($legacy_urls && $uri->getVar('func'))
		{
			$result = KunenaRouteLegacy::convert($uri);
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			if (!$result)
			{
				return false;
			}

			return $uri;
		}

		// Check URI
		switch ($uri->getVar('view', 'home'))
		{
			case 'announcement':
				if ($legacy_urls)
				{
					KunenaRouteLegacy::convert($uri);
				}
				break;

			case 'attachment':
			case 'category':
			case 'common':
			case 'credits':
			case 'home':
			case 'misc':
			case 'search':
			case 'statistics':
			case 'topic':
			case 'topics':
			case 'user':
			case 'users':
				break;

			default:
				if (!$legacy_urls || !KunenaRouteLegacy::convert($uri))
				{
					KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

					return false;
				}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $uri;
	}

	/**
	 * @param   \Joomla\CMS\Uri\Uri $uri uri
	 *
	 * @return integer
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected static function setItemID(\Joomla\CMS\Uri\Uri $uri)
	{
		static $candidates = array();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$view   = $uri->getVar('view');
		$catid  = (int) $uri->getVar('catid');
		$Itemid = (int) $uri->getVar('Itemid');
		$key    = $view . $catid;

		if (!isset($candidates[$key]))
		{
			if (self::$search === false)
			{
				self::build();
			}

			$search = array();

			if (self::$home)
			{
				// Search from the current home menu
				$search[self::$home->id] = 1;

				// Then search from all linked home menus
				if (isset(self::$search['home'][self::$home->id]))
				{
					$search += self::$search['home'][self::$home->id];
				}
			}

			// Finally search from other home menus
			$search += self::$search['home'];

			// Find all potential candidates
			$candidates[$key] = array();

			foreach ($search as $id => $dummy)
			{
				$follow = !empty(self::$menu[$id]) ? self::$menu[$id] : null;

				if ($follow && self::checkHome($follow, $catid))
				{
					$candidates[$key] += !empty(self::$search[$view][$follow->id]) ? self::$search[$view][$follow->id] : array();

					if ($view == 'topic')
					{
						$candidates[$key] += !empty(self::$search['category'][$follow->id]) ? self::$search['category'][$follow->id] : array();
					}

					$candidates[$key][$follow->id] = $follow->id;
				}
			}

			// Don't forget lonely candidates
			$candidates[$key] += !empty(self::$search[$view][0]) ? self::$search[$view][0] : array();

			if ($view == 'topic')
			{
				$candidates[$key] += !empty(self::$search['category'][0]) ? self::$search['category'][0] : array();
			}
		}

		// Check current menu item first
		$bestcount = ($Itemid && isset(self::$menu[$Itemid])) ? self::checkItem(self::$menu[$Itemid], $uri) : 0;
		$bestid    = $bestcount ? $Itemid : 0;

		// Then go through all candidates
		foreach ($candidates[$key] as $id)
		{
			$item       = self::$menu[$id];
			$matchcount = self::checkItem($item, $uri);

			if ($matchcount > $bestcount)
			{
				// This is our best candidate this far
				$bestid    = $item->id;
				$bestcount = $matchcount;
			}
		}

		$uri->setVar('Itemid', $bestid);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $bestid;
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	protected static function build()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (self::$search === false)
		{
			$user         = KunenaUserHelper::getMyself();
			$language     = strtolower(Factory::getDocument()->getLanguage());
			self::$search = false;

			if (KunenaConfig::getInstance()->get('cache_mid'))
			{
				// FIXME: Experimental caching.
				$cache        = self::getCache();
				self::$search = unserialize($cache->get('search', "com_kunena.route.v1.{$language}.{$user->userid}"));
			}

			if (self::$search === false)
			{
				self::$search['home'] = array();

				foreach (self::$menu as $item)
				{
					// Skip menu items that aren't pointing to Kunena or are using wrong language.
					if (($item->component != 'com_kunena' && $item->type != 'alias')
						|| ($item->language != '*' && strtolower($item->language) != $language)
					)
					{
						continue;
					}

					// Follow links.
					if ($item->type == 'alias')
					{
						if (empty($item->query['Itemid']) || empty(self::$menu[$item->query['Itemid']]))
						{
							continue;
						}

						$item = self::$menu[$item->query['Itemid']];

						if ($item->component != 'com_kunena' || ($item->language != '*' && strtolower($item->language) != $language))
						{
							continue;
						}
					}

					// Ignore legacy menu items without view in it.
					if (!isset($item->query['view']))
					{
						continue;
					}

					// Save Kunena menu items so that we can make fast searches
					$home                                                                 = self::getHome($item);
					self::$search[$item->query['view']][$home ? $home->id : 0][$item->id] = $item->id;
				}

				if (isset($cache))
				{
					$cache->store(serialize(self::$search), 'search', "com_kunena.route.v1.{$language}.{$user->userid}");
				}
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return \Joomla\CMS\Cache\CacheController
	 * @since Kunena
	 */
	protected static function getCache()
	{
		return Factory::getCache('mod_menu', 'output');
	}

	/**
	 * @param   mixed $item item
	 *
	 * @return null
	 * @since Kunena
	 */
	public static function getHome($item)
	{
		if (!$item)
		{
			return false;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$id = $item->id;

		if (!isset(self::$parent[$id]))
		{
			if ($item->component == 'com_kunena' && isset($item->query['view']) && $item->query['view'] == 'home')
			{
				self::$parent[$id] = $item;
			}
			else
			{
				$parentId          = $item->parent_id;
				$parent            = isset(self::$menu[$parentId]) ? self::$menu[$parentId] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$parent[$id];
	}

	/**
	 * @param   mixed   $item  item
	 * @param   integer $catid catid
	 *
	 * @return integer
	 * @since Kunena
	 */
	protected static function checkHome($item, $catid)
	{
		static $cache = array();

		if (!$catid)
		{
			return 1;
		}

		if (!isset($cache[$item->id]))
		{
			$params = $item->params;
			$catids = $params->get('catids', array());

			if (!is_array($catids))
			{
				$catids = explode(',', $catids);
			}

			if (!empty($catids))
			{
				$catids = array_combine($catids, $catids);
			}

			unset($catids[0], $catids['']);
			$cache[$item->id] = (array) $catids;
		}

		return intval(empty($cache[$item->id]) || isset($cache[$item->id][$catid]));
	}

	/**
	 * @param   mixed               $item item
	 * @param   \Joomla\CMS\Uri\Uri $uri  uri
	 *
	 * @return integer
	 * @since Kunena
	 * @throws null
	 */
	protected static function checkItem($item, \Joomla\CMS\Uri\Uri $uri)
	{
		$authorise = self::$menus->authorise($item->id);

		if (!$authorise)
		{
			return 0;
		}

		$catid = (int) $uri->getVar('catid');

		if (!empty($item->query['view']))
		{
			switch ($item->query['view'])
			{
				case 'home':
					$matchcount = self::checkHome($item, $catid);
					break;
				case 'category':
				case 'topic':
					$matchcount = self::checkCategory($item, $uri);
					break;
				default:
					$matchcount = self::check($item, $uri);
			}

			return $matchcount;
		}
		else
		{
			return 1;
		}
	}

	/**
	 * @param   mixed               $item item
	 * @param   \Joomla\CMS\Uri\Uri $uri  url
	 *
	 * @return integer
	 * @throws null
	 * @since Kunena
	 */
	protected static function checkCategory($item, \Joomla\CMS\Uri\Uri $uri)
	{
		static $cache = array();
		$catid = (int) $uri->getVar('catid');
		$check = self::check($item, $uri);

		if (!$check || !$catid)
		{
			return $check;
		}

		if (!isset($cache[$item->id]))
		{
			$cache[$item->id] = array();

			if (!empty($item->query['catid']))
			{
				$cache[$item->id]                        = KunenaForumCategoryHelper::getChildren($item->query['catid']);
				$cache[$item->id][$item->query['catid']] = KunenaForumCategoryHelper::get($item->query['catid']);
			}
		}

		return intval(isset($cache[$item->id][$catid])) * 8;
	}

	/**
	 * @param   mixed               $item item
	 * @param   \Joomla\CMS\Uri\Uri $uri  uri
	 *
	 * @return integer
	 * @since Kunena
	 */
	protected static function check($item, \Joomla\CMS\Uri\Uri $uri)
	{
		$hits = 0;

		foreach ($item->query as $var => $value)
		{
			if ($value != $uri->getVar($var))
			{
				return 0;
			}

			$hits++;
		}

		return $hits;
	}

	/**
	 * Get the referrer page.
	 *
	 * If there's no referrer or it's external, Kunena will return default page.
	 * Also referrers back to tasks are removed.
	 *
	 * @param   string $default Default page to return into.
	 * @param   string $anchor  Anchor (location in the page).
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getReferrer($default = null, $anchor = null)
	{
		$app = Factory::getApplication();

		$referrer = $app->input->server->getString('HTTP_REFERER');

		if ($referrer)
		{
			$uri = new \Joomla\CMS\Uri\Uri($referrer);

			// Make sure we do not return into a task -- or if task is SEF encoded, make sure it fails.
			$uri->delVar('task');
			$uri->delVar(Session::getFormToken());

			// Check that referrer was from the same domain and came from the Joomla frontend or backend.
			$base = $uri->toString(array('scheme', 'host', 'port', 'path'));
			$host = $uri->toString(array('scheme', 'host', 'port'));

			// Referrer should always have host set and it should come from the same base address.
			if (empty($host) || stripos($base, Uri::base()) !== 0)
			{
				$uri = null;
			}
		}

		if (!isset($uri))
		{
			if ($default == null)
			{
				$default = $app->isClient('site') ? 'index.php?option=com_kunena' : 'administrator/index.php?option=com_kunena';
			}

			$default = self::_($default);
			$uri     = new \Joomla\CMS\Uri\Uri($default);
		}

		if ($anchor)
		{
			$uri->setFragment($anchor);
		}

		return $uri->toString(array('path', 'query', 'fragment'));
	}

	/**
	 * @param   null $uri   uri
	 * @param   bool $xhtml xhtml
	 * @param   int  $ssl   ssl
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function _($uri = null, $xhtml = true, $ssl = 0)
	{
		if (self::$adminApp)
		{
			if ($uri instanceof \Joomla\CMS\Uri\Uri)
			{
				$uri = $uri->toString();
			}

			if (substr($uri, 0, 14) == 'administrator/')
			{
				// Use default routing in administration
				return Route::_(substr($uri, 14), $xhtml, $ssl);
			}
			else
			{
				return Uri::root(true) . "/{$uri}";
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$key = (self::$home ? self::$home->id : 0) . '-' . (int) $xhtml . (int) $ssl . ($uri instanceof \Joomla\CMS\Uri\Uri ? $uri->toString() : (string) $uri);

		if (!$uri || (is_string($uri) && $uri[0] == '&'))
		{
			$key = 'a' . (self::$active ? self::$active->id : '') . '-' . $key;
		}

		if (isset(self::$uris[$key]))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return self::$uris[$key];
		}

		$uri = self::prepare($uri);

		if (!$uri)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return false;
		}

		if (!$uri->getVar('Itemid'))
		{
			self::setItemID($uri);
		}

		$fragment = $uri->getFragment();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '(t)') : null;
		self::$uris[$key] = Route::_('index.php?' . $uri->getQuery(), $xhtml, $ssl) . ($fragment ? '#' . $fragment : '');
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '(t)') : null;
		self::$urisSave = true;
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$uris[$key];
	}

	/**
	 * @param   \Joomla\CMS\Uri\Uri $uri    uri
	 * @param   bool                $object object
	 *
	 * @return \Joomla\CMS\Uri\Uri|string
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function normalize($uri = null, $object = false)
	{
		if (self::$adminApp)
		{
			// Use default routing in administration
			return $object ? $uri : 'index.php?' . $uri->getQuery();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$uri = self::prepare($uri);

		if (!$uri)
		{
			return false;
		}

		if (!$uri->getVar('Itemid'))
		{
			self::setItemID($uri);
		}

		$result = $object ? $uri : 'index.php?' . $uri->getQuery();
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public static function getMenu()
	{
		return self::$home;
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public static function cacheLoad()
	{
		if (!KunenaConfig::getInstance()->get('cache_url'))
		{
			return;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$user  = KunenaUserHelper::getMyself();
		$cache = self::getCache();

		// TODO: can use viewlevels instead of userid
		$data = $cache->get($user->userid, 'com_kunena.route.v1');

		if ($data !== false)
		{
			list(self::$subtree, self::$uris) = unserialize($data);
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public static function cacheStore()
	{
		if (!KunenaConfig::getInstance()->get('cache_url'))
		{
			return;
		}

		if (!self::$urisSave)
		{
			return;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$user  = KunenaUserHelper::getMyself();
		$data  = array(self::$subtree, self::$uris);
		$cache = self::getCache();

		// TODO: can use viewlevels instead of userid
		$cache->store(serialize($data), $user->userid, 'com_kunena.route.v1');
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @param   string $string  string
	 * @param   null   $default default
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public static function stringURLSafe($string, $default = null)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!isset(self::$filtered[$string]))
		{
			self::$filtered[$string] = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($string);

			// Remove beginning and trailing "whitespace", fixes #1130 where category alias creation fails on error: Duplicate entry '-'.
			self::$filtered[$string] = trim(self::$filtered[$string], '-_ ');

			if ($default && empty(self::$filtered[$string]))
			{
				self::$filtered[$string] = $default;
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$filtered[$string];
	}

	/**
	 * @param   string $alias alias
	 *
	 * @return array
	 * @since Kunena
	 */
	public static function resolveAlias($alias)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$db    = Factory::getDbo();
		$query = "SELECT * FROM #__kunena_aliases WHERE alias LIKE {$db->Quote($alias . '%')}";
		$db->setQuery($query);
		$aliases = $db->loadObjectList();

		$vars = array();

		foreach ($aliases as $object)
		{
			if (Joomla\String\StringHelper::strtolower($alias) == Joomla\String\StringHelper::strtolower($object->alias))
			{
				$var         = $object->type != 'legacy' ? $object->type : 'view';
				$vars [$var] = $object->type != 'layout' ? $object->item : preg_replace('/.*\./', '', $object->item);

				if ($var == 'catid')
				{
					$vars ['view'] = 'category';
				}

				break;
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $vars;
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	public static function initialize()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		self::$config = KunenaFactory::getConfig();

		if (Factory::getApplication()->isClient('administrator'))
		{
			self::$adminApp = true;
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return;
		}

		self::$menus   = Factory::getApplication()->getMenu();
		self::$menu    = self::$menus->getMenu();
		self::$default = self::$menus->getDefault();
		$active        = self::$menus->getActive();

		// Get the full request URI.
		$uri = clone Uri::getInstance();

		// Get current route.
		self::$current = new \Joomla\CMS\Uri\Uri('index.php');

		if ($active)
		{
			foreach ($active->query as $key => $value)
			{
				self::$current->setVar($key, $value);
			}

			self::$current->setVar('Itemid', (int) $active->id);

			if ($active->type == 'component' && $active->component == 'com_kunena' && isset($active->query['view']))
			{
				self::$active = $active;
			}
		}

		// If values are both in GET and POST, they are only stored in POST
		$post = Factory::getApplication()->input->post->getArray(array());

		foreach ($post as $key => $value)
		{
			if (in_array($key, array('view', 'layout', 'task')) && !preg_match('/[^a-zA-Z0-9_.]/i', $value))
			{
				self::$current->setVar($key, $value);
			}
		}

		// Make sure that request URI is not broken
		$get = Factory::getApplication()->input->get->getArray(array());

		foreach ($get as $key => $value)
		{
			if (preg_match('/[^a-zA-Z]/', $key))
			{
				continue;
			}

			if (in_array($key, array('q', 'query', 'searchuser')))
			{
				// Allow all values
			}
			// TODO: we need to find a way to here deal with arrays: &foo[]=bar
			elseif (gettype($value) == 'string')
			{
				if (preg_match('/[^a-zA-Z0-9_ ]/i', $value))
				{
					// Illegal value
					continue;
				}
			}

			self::$current->setVar($key, $value);
		}

		if (self::$current->getVar('start'))
		{
			self::$current->setVar('limitstart', self::$current->getVar('start'));
			self::$current->delVar('start');
		}

		self::$home = self::getHome(self::$active);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @since Kunena
	 * @return void
	 */
	public static function cleanup()
	{
		self::$filtered = array();
		self::$uris     = array();
	}

	/**
	 * @param   KunenaForumCategory $category category
	 * @param   bool                $xhtml    xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getCategoryUrl(KunenaForumCategory $category, $xhtml = true)
	{
		return self::_("index.php?option=com_kunena&view=category&catid={$category->id}", $xhtml);
	}

	/**
	 * @param   KunenaForumCategory $category category
	 *
	 * @return array|boolean|integer
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getCategoryItemid(KunenaForumCategory $category)
	{
		return self::getItemID("index.php?option=com_kunena&view=category&catid={$category->id}");
	}

	/**
	 * @param   null $uri uri
	 *
	 * @return array|boolean|integer
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getItemID($uri = null)
	{
		if (self::$adminApp)
		{
			// There are no itemids in administration
			return 0;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$uri = self::prepare($uri);

		if (!$uri)
		{
			return false;
		}

		if (!$uri->getVar('Itemid'))
		{
			self::setItemID($uri);
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $uri->getVar('Itemid');
	}

	/**
	 * Fix itemid when there is no item id.
	 *
	 * @return array|boolean|integer
	 * @throws Exception
	 * @throws null
	 * @since Kunena 5.1
	 */
	public static function fixMissingItemID()
	{
		$component = ComponentHelper::getComponent('com_kunena');
		$items     = Factory::getApplication()->getMenu('site')->getItems('component_id', $component->id);

		if ($items)
		{
			return $items[0]->id;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * @param   KunenaForumTopic    $topic    topic
	 * @param   bool                $xhtml    xhtml
	 * @param   null                $action   actions
	 * @param   KunenaForumCategory $category category
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getTopicUrl(KunenaForumTopic $topic, $xhtml = true, $action = null, KunenaForumCategory $category = null)
	{
		if (!$category)
		{
			$category = $topic->getCategory();
		}

		return self::_($topic->getUri($category, $action), $xhtml);
	}

	/**
	 * @param   KunenaForumMessage  $message  message
	 * @param   bool                $xhtml    xhtml
	 * @param   KunenaForumTopic    $topic    topic
	 * @param   KunenaForumCategory $category category
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getMessageUrl(KunenaForumMessage $message, $xhtml = true, KunenaForumTopic $topic = null, KunenaForumCategory $category = null)
	{
		// FIXME: not yet fully implemented...
		if (!$category)
		{
			$category = $message->getCategory();
		}

		if (!$topic)
		{
			$topic = $message->getTopic();
		}

		return self::_("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$topic->id}", $xhtml);
	}

	/**
	 * @param   KunenaUser $user  user
	 * @param   bool       $xhtml xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public static function getUserUrl(KunenaUser $user, $xhtml = true)
	{
		return self::_("index.php?option=com_kunena&view=user&userid={$user->userid}", $xhtml);
	}

	/**
	 * This method implements unicode slugs instead of transliteration.
	 * It has taken from Joomla 1.7.3 with the difference that urls are not lower case.
	 *
	 * @param   string $string String to process
	 *
	 * @return  string  Processed string
	 * @since Kunena
	 */
	protected static function stringURLUnicodeSlug($string)
	{
		// Replace double byte whitespaces by single byte (East Asian languages)
		$str = preg_replace('/\xE3\x80\x80/', ' ', $string);

		// Remove any '-' from the string as they will be used as concatenator.
		// Would be great to let the spaces in but only Firefox is friendly with this

		$str = str_replace('-', ' ', $str);

		// Replace forbidden characters by whitespaces
		$str = preg_replace('#[:\#\*"@+=;!><&\.,%()\]\/\'\\\\|\[]#', "\x20", $str);

		// Delete all '?'
		$str = str_replace('?', '', $str);

		// Trim white spaces at beginning and end of alias and make lowercase
		$str = trim($str);

		// Remove any duplicate whitespace and replace whitespaces by hyphens
		$str = preg_replace('#\x20+#', '-', $str);

		return $str;
	}
}
