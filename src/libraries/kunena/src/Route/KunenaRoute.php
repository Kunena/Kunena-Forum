<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Route
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Route;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

KunenaRoute::initialize();

/**
 * Class KunenaRoute
 *
 * @since   Kunena 6.0
 */
abstract class KunenaRoute
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $views = [
		'attachment'   => ['layout' => 'default', 'thumb' => 0, 'download' => 0],
		'announcement' => ['layout' => 'default'],
		'category'     => ['layout' => 'default', 'catid' => '0'],
		'common'       => ['layout' => 'default'],
		'credits'      => ['layout' => 'default'],
		'home'         => [],
		'misc'         => ['layout' => 'default'],
		'search'       => ['layout' => 'default'],
		'statistics'   => ['layout' => 'default'],
		'topic'        => ['layout' => 'default'],
		'topics'       => ['layout' => 'default'],
		'user'         => ['layout' => 'default', 'userid' => '0'],
	];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $layouts = ['create' => 1, 'default' => 1, 'edit' => 1, 'manage' => 1, 'moderate' => 1, 'user' => 1];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $sefviews = ['' => 1, 'home' => 1, 'category' => 1, 'topic' => 1];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $parsevars = ['do' => 1, 'task' => 1, 'mode' => 1, 'catid' => 1, 'id' => 1, 'mesid' => 1, 'userid' => 1, 'page' => 1, 'sel' => 1];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public static $time = 0;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $adminApp = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $config = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $menus = false;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public static $menu = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $default = false;

	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	public static $active = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $home = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $search = false;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $current = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $childlist = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $subtree = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $parent = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public static $uris = [];

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public static $urisSave = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $filtered = [];

	/**
	 * @param   bool  $object  object
	 *
	 * @return  boolean|Uri|null|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function current($object = false)
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
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
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @param   null  $uri  uri
	 *
	 * @return  boolean|Uri|null
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	protected static function prepare($uri = null)
	{
		static $current = [];
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!$uri || (\is_string($uri) && $uri[0] == '&'))
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
			if (!isset(self::$menu[\intval($uri)]))
			{
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return false;
			}

			$item = self::$menu[\intval($uri)];
			$uri  = Uri::getInstance("{$item->link}&Itemid={$item->id}");
		}
		elseif ($uri instanceof Uri)
		{
			// Nothing to do
		}
		else
		{
			$uri = new Uri((string) $uri);
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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $uri;
	}

	/**
	 * @param   Uri  $uri  uri
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 * @throws  null
	 * @throws  Exception
	 */
	protected static function setItemID(Uri $uri): int
	{
		static $candidates = [];
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

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

			$search = [];

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
			$candidates[$key] = [];

			foreach ($search as $id => $dummy)
			{
				$follow = !empty(self::$menu[$id]) ? self::$menu[$id] : null;

				if ($follow && self::checkHome($follow, $catid))
				{
					$candidates[$key] += !empty(self::$search[$view][$follow->id]) ? self::$search[$view][$follow->id] : [];

					if ($view == 'topic')
					{
						$candidates[$key] += !empty(self::$search['category'][$follow->id]) ? self::$search['category'][$follow->id] : [];
					}

					$candidates[$key][$follow->id] = $follow->id;
				}
			}

			// Don't forget lonely candidates
			$candidates[$key] += !empty(self::$search[$view][0]) ? self::$search[$view][0] : [];

			if ($view == 'topic')
			{
				$candidates[$key] += !empty(self::$search['category'][0]) ? self::$search['category'][0] : [];
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
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $bestid;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	protected static function build(): void
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (self::$search === false)
		{
			$user         = KunenaUserHelper::getMyself();
			$language     = strtolower(Factory::getApplication()->getDocument()->getLanguage());
			self::$search = false;

			if (KunenaConfig::getInstance()->get('cache_mid'))
			{
				// FIXME: Experimental caching.
				$cache        = self::getCache();
				self::$search = unserialize($cache->get('search', "com_kunena.route.v1.{$language}.{$user->userid}"));
			}

			if (self::$search === false)
			{
				self::$search         = [];
				self::$search['home'] = [];

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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  CacheController
	 *
	 * @since   Kunena 6.0
	 */
	protected static function getCache(): CacheController
	{
		return Factory::getCache('mod_menu', 'output');
	}

	/**
	 * @param   mixed  $item  item
	 *
	 * @return  null
	 *
	 * @since   Kunena 6.0
	 */
	public static function getHome($item)
	{
		if (!$item)
		{
			return false;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$id = $item->id;

		if (!isset(self::$parent[$id]))
		{
			if ($item->component == 'com_kunena' && isset($item->query['view']) && $item->query['view'] == 'home')
			{
				self::$parent[$id] = $item;
			}
			else
			{
				$parentid          = $item->parent_id;
				$parent            = isset(self::$menu[$parentid]) ? self::$menu[$parentid] : null;
				self::$parent[$id] = self::getHome($parent);
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$parent[$id];
	}

	/**
	 * @param   mixed    $item   item
	 * @param   integer  $catid  catid
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	protected static function checkHome($item, int $catid): int
	{
		static $cache = [];

		if (!$catid)
		{
			return 1;
		}

		if (!isset($cache[$item->id]))
		{
			$params = $item->getParams();
			$catids = $params->get('catids', []);

			if (!\is_array($catids))
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

		return \intval(empty($cache[$item->id]) || isset($cache[$item->id][$catid]));
	}

	/**
	 * @param   mixed  $item  item
	 * @param   Uri    $uri   uri
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 * @throws  null
	 */
	protected static function checkItem($item, Uri $uri)
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

		return 1;
	}

	/**
	 * @param   mixed  $item  item
	 * @param   Uri    $uri   url
	 *
	 * @return  integer
	 *
	 * @since   Kunena
	 * @throws  null
	 */
	protected static function checkCategory($item, Uri $uri)
	{
		static $cache = [];
		$catid = (int) $uri->getVar('catid');
		$check = self::check($item, $uri);

		if (!$check || !$catid)
		{
			return $check;
		}

		if (!isset($cache[$item->id]))
		{
			$cache[$item->id] = [];

			if (!empty($item->query['catid']))
			{
				$cache[$item->id]                        = KunenaCategoryHelper::getChildren($item->query['catid']);
				$cache[$item->id][$item->query['catid']] = KunenaCategoryHelper::get($item->query['catid']);
			}
		}

		return \intval(isset($cache[$item->id][$catid])) * 8;
	}

	/**
	 * @param   mixed  $item  item
	 * @param   Uri    $uri   uri
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	protected static function check($item, Uri $uri): int
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
	 * @param   string  $default  Default page to return into.
	 * @param   string  $anchor   Anchor (location in the page).
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getReferrer($default = null, $anchor = null): string
	{
		$app = Factory::getApplication();

		$referrer = $app->input->server->getString('HTTP_REFERER');

		if ($referrer)
		{
			$uri = new Uri($referrer);

			// Make sure we do not return into a task -- or if task is SEF encoded, make sure it fails.
			$uri->delVar('task');
			$uri->delVar(Session::getFormToken());

			// Check that referrer was from the same domain and came from the Joomla frontend or backend.
			$base = $uri->toString(['scheme', 'host', 'port', 'path']);
			$host = $uri->toString(['scheme', 'host', 'port']);

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
			$uri     = new Uri($default);
		}

		if ($anchor)
		{
			$uri->setFragment($anchor);
		}

		return $uri->toString(['path', 'query', 'fragment']);
	}

	/**
	 * @param   null  $uri    uri
	 * @param   bool  $xhtml  xhtml
	 * @param   int   $ssl    ssl
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function _($uri = null, $xhtml = true, $ssl = 0)
	{
		if (self::$adminApp)
		{
			if ($uri instanceof Uri)
			{
				$uri = $uri->toString();
			}

			if (substr($uri, 0, 14) == 'administrator/')
			{
				// Use default routing in administration
				return Route::_(substr($uri, 14), $xhtml, $ssl);
			}

			return Uri::root(true) . "/{$uri}";
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$key = (self::$home ? self::$home->id : 0) . '-' . (int) $xhtml . (int) $ssl . ($uri instanceof Uri ? $uri->toString() : (string) $uri);

		if (!$uri || (\is_string($uri) && $uri[0] == '&'))
		{
			$key = 'a' . (self::$active ? self::$active->id : '') . '-' . $key;
		}

		if (isset(self::$uris[$key]))
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return self::$uris[$key];
		}

		$uri = self::prepare($uri);

		if (!$uri)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return false;
		}

		if (!$uri->getVar('Itemid'))
		{
			self::setItemID($uri);
		}

		$fragment = $uri->getFragment();
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '(t)') : null;
		self::$uris[$key] = Route::_('index.php?' . $uri->getQuery(), $xhtml, $ssl) . ($fragment ? '#' . $fragment : '');
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '(t)') : null;
		self::$urisSave = true;
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$uris[$key];
	}

	/**
	 * @param   Uri   $uri     uri
	 * @param   bool  $object  object
	 *
	 * @return  Uri|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function normalize($uri = null, $object = false)
	{
		if (self::$adminApp)
		{
			// Use default routing in administration
			return $object ? $uri : 'index.php?' . $uri->getQuery();
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

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
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function getMenu()
	{
		return self::$home;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public static function cacheLoad(): void
	{
		if (!KunenaConfig::getInstance()->get('cache_url'))
		{
			return;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$user  = KunenaUserHelper::getMyself();
		$cache = self::getCache();

		// TODO: can use viewlevels instead of userid
		$data = $cache->get($user->userid, 'com_kunena.route.v1');

		if ($data !== false)
		{
			list(self::$subtree, self::$uris) = unserialize($data);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public static function cacheStore(): void
	{
		if (!KunenaConfig::getInstance()->get('cache_url'))
		{
			return;
		}

		if (!self::$urisSave)
		{
			return;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$user  = KunenaUserHelper::getMyself();
		$data  = [self::$subtree, self::$uris];
		$cache = self::getCache();

		// TODO: can use viewlevels instead of userid
		$cache->store(serialize($data), $user->userid, 'com_kunena.route.v1');
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @param   string  $string   string
	 * @param   null    $default  default
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public static function stringURLSafe($string, $default = null)
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!isset(self::$filtered[$string]))
		{
			self::$filtered[$string] = ApplicationHelper::stringURLSafe($string);

			// Remove beginning and trailing "whitespace", fixes #1130 where category alias creation fails on error: Duplicate entry '-'.
			self::$filtered[$string] = trim(self::$filtered[$string], '-_ ');

			if ($default && empty(self::$filtered[$string]))
			{
				self::$filtered[$string] = $default;
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$filtered[$string];
	}

	/**
	 * @param   string  $alias  alias
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function resolveAlias(string $alias): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$app   = Factory::getApplication();
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_aliases'))
			->where($db->quoteName('alias') . ' LIKE ' . $db->quote($alias . '%'));

		$db->setQuery($query);

		try
		{
			$aliases = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage());
		}

		$vars = [];

		foreach ($aliases as $object)
		{
			if (StringHelper::strtolower($alias) == StringHelper::strtolower($object->alias))
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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $vars;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public static function initialize(): void
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		self::$config = KunenaFactory::getConfig();

		if (Factory::getApplication()->isClient('administrator'))
		{
			self::$adminApp = true;
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return;
		}

		self::$menus   = Factory::getApplication()->getMenu();
		self::$menu    = self::$menus->getMenu();
		self::$default = self::$menus->getDefault();
		$active        = self::$menus->getActive();

		// Get current route.
		self::$current = new Uri('index.php');

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
		$post = Factory::getApplication()->input->post->getArray([]);

		foreach ($post as $key => $value)
		{
			if (\in_array($key, ['view', 'layout', 'task']) && !preg_match('/[^a-zA-Z0-9_.]/i', $value))
			{
				self::$current->setVar($key, $value);
			}
		}

		// Make sure that request URI is not broken
		$get = Factory::getApplication()->input->get->getArray([]);

		foreach ($get as $key => $value)
		{
			if (preg_match('/[^a-zA-Z]/', $key))
			{
				continue;
			}

			if (\in_array($key, ['query', 'searchuser']))
			{
				// Allow all values
			}
			// TODO: we need to find a way to here deal with arrays: &foo[]=bar
			elseif (\gettype($value) == 'string')
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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function cleanup(): void
	{
		self::$filtered = [];
		self::$uris     = [];
	}

	/**
	 * @param   KunenaCategory  $category  category
	 * @param   bool            $xhtml     xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 * @throws null
	 * @throws Exception
	 */
	public static function getCategoryUrl(KunenaCategory $category, bool $xhtml = true)
	{
		return self::_("index.php?option=com_kunena&view=category&catid={$category->id}", $xhtml);
	}

	/**
	 * @param   KunenaCategory  $category  category
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getCategoryItemid(KunenaCategory $category)
	{
		return self::getItemID("index.php?option=com_kunena&view=category&catid={$category->id}");
	}

	/**
	 * @param   null  $uri  uri
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getItemID($uri = null)
	{
		if (self::$adminApp)
		{
			// There are no itemids in administration
			return 0;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$uri = self::prepare($uri);

		if (!$uri)
		{
			return false;
		}

		if (!$uri->getVar('Itemid'))
		{
			self::setItemID($uri);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $uri->getVar('Itemid');
	}

	/**
	 * Fix itemid when there is no item id.
	 *
	 * @return  integer
	 *
	 * @since   Kunena 5.1
	 * @throws  null
	 * @throws  Exception
	 */
	public static function fixMissingItemID(): int
	{
		$component = ComponentHelper::getComponent('com_kunena');
		$items     = Factory::getApplication()->getMenu('site')->getItems('component_id', $component->id);

		if ($items)
		{
			return $items[0]->id;
		}

		return 0;
	}

	/**
	 * @param   KunenaTopic          $topic     topic
	 * @param   bool                 $xhtml     xhtml
	 * @param   null                 $action    actions
	 * @param   KunenaCategory|null  $category  category
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function getTopicUrl(KunenaTopic $topic, bool $xhtml = true, $action = null, KunenaCategory $category = null)
	{
		if (!$category)
		{
			$category = $topic->getCategory();
		}

		return self::_($topic->getUri($category, $action), $xhtml);
	}

	/**
	 * @param   KunenaMessage        $message   message
	 * @param   bool                 $xhtml     xhtml
	 * @param   KunenaTopic|null     $topic     topic
	 * @param   KunenaCategory|null  $category  category
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function getMessageUrl(KunenaMessage $message, $xhtml = true, KunenaTopic $topic = null, KunenaCategory $category = null)
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
	 * @param   KunenaUser  $user   user
	 * @param   bool        $xhtml  xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public static function getUserUrl(KunenaUser $user, $xhtml = true)
	{
		return self::_("index.php?option=com_kunena&view=user&userid={$user->userid}", $xhtml);
	}

	/**
	 * This method implements \unicode slugs instead of transliteration.
	 * It has taken from Joomla 1.7.3 with the difference that urls are not lower case.
	 *
	 * @param   string  $string  String to process
	 *
	 * @return  string  Processed string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function stringURLUnicodeSlug(string $string): string
	{
		// Replace double byte whitespaces by single byte (East Asian languages)
		$str = preg_replace('/\xE3\x80\x80/', ' ', $string);

		// Remove any '-' from the string as they will be used as concatenated.
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
