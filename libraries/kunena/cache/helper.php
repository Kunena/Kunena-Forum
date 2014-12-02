<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Cache
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaCacheHelper has helper functions to clear all caches that affects Kunena.
 */
abstract class KunenaCacheHelper {
	/**
	 * Clear all cache types. Please avoid using this function except after installation and
	 * when user wants to do it manually.
	 */
	public static function clearAll() {
		self::clearKunena();
		self::clearSystem();
		self::clearMenu();
		self::clearAccess();
		self::clearCacheFiles();
		self::clearTemplateFiles();
		self::clearStatCache();
		self::clearCompiledPHP();
	}

	/**
	 * Perform normal cache cleanup.
	 */
	public static function clear() {
		self::clearKunena();
		self::clearSystem();
		self::clearMenu();

	}

	/**
	 * Clear Kunena cache.
	 */
	public static function clearKunena() {
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('com_kunena');
	}

	/**
	 * Crear Joomla system cache.
	 */
	public static function clearSystem() {
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache();
		$cache->clean('_system');
	}

	/**
	 * Clear Joomla menu cache.
	 */
	public static function clearMenu() {
		KunenaMenuHelper::cleanCache();
	}

	/**
	 * Clear Kunena access cache.
	 */
	public static function clearAccess() {
		KunenaAccess::getInstance()->clearCache();
	}

	/**
	 * Clear cached files from Kunena.
	 */
	public static function clearCacheFiles() {
		// Delete all cached files.
		$cacheDir = JPATH_CACHE.'/kunena';
		if (is_dir($cacheDir)) JFolder::delete($cacheDir);
		JFolder::create($cacheDir);
	}

	/**
	 * Clear cached template files.
	 */
	public static function clearTemplateFiles() {
		// Delete all cached files.
		$cacheDir = JPATH_ROOT."/media/kunena/cache";
		if (is_dir($cacheDir)) JFolder::delete($cacheDir);
		JFolder::create($cacheDir);
	}

	/**
	 * Clear PHP statcache (contains file size etc).
	 */
	public static function clearStatCache() {
		clearstatcache();
	}

	/**
	 * Clear compiled PHP files, handy during installation when PHP files change.
	 */
	public static function clearCompiledPHP() {
		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache')) {
			@apc_clear_cache();
		}
		// Remove all compiled files from XCache.
		if (function_exists('xcache_clear_cache')) {
			@xcache_clear_cache(XC_TYPE_PHP);
		}
	}
}
