<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();
if (defined ( 'KUNENA_LOADED' ))
	return;

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT . DS . 'media' . DS . KUNENA_NAME );

/**
 * Intelligent library importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.6
 */
function kimport($path)
{
	static $paths = array();
	if (isset($paths[$path])) return true;

	$res = false;
	if (substr($path, 0, 7) == 'kunena.') {
		$file = KPATH_ADMIN . '/libraries/' . str_replace( '.', '/', substr($path, 7));
		if (is_dir($file)) {
			$parts = explode( '/', $file );
			$file .= '/'.array_pop( $parts );
		}
		$file .= '.php';
		if (file_exists($file)) {
			$class = str_replace( '.', '', $path);
			JLoader::register($class, $file);
			$paths[$path] = 1;
			$res = true;
		}
	} else {
		$res = JLoader::import($path, dirname(__FILE__).'/libraries');
	}
	return $res;
}

// Version information
class KunenaForum {
	private function __construct() {}

	public static function isSvn() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function isCompatible($version, $build=false) {
		if (version_compare($version, '2.0.0-DEV', '<')) {
			return false;
		}
		if (version_compare($version, self::version(), '>')) {
			return false;
		}
		if ($build && $build < self::versionBuild()) {
			return false;
		}
		return true;
	}

	public static function version() {
		return '@kunenaversion@';
	}

	public static function versionDate() {
		return '@kunenaversiondate@';
	}

	public static function versionName() {
		return '@kunenaversionname@';
	}

	public static function versionBuild() {
		return '@kunenaversionbuild@';
	}

	public static function getVersionInfo() {
		$version = new stdClass();
		$version->version = self::version();
		$version->date = self::versionDate();
		$version->name = self::versionName();
		$version->build = self::versionBuild();
		return $version;
	}

	public static function enabled() {
		return false;
	}
}

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
