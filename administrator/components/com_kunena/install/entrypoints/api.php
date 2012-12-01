<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if (defined ( 'KUNENA_LOADED' ))
	return;

// Manually enable code profiling by setting value to 1
define ( 'KUNENA_PROFILER', 0 );

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION .'/'. KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT .'/'. KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR .'/'. KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT .'/media/'. KUNENA_NAME );

// URLs
define ( 'KURL_COMPONENT', 'index.php?option=' . KUNENA_COMPONENT_NAME );
define ( 'KURL_SITE', JUri::Root () . KPATH_COMPONENT_RELATIVE . '/' );
define ( 'KURL_MEDIA', JUri::Root () . 'media/' . KUNENA_NAME . '/' );

// We need following when upgrading from Kunena 1.6.5:
$jversion = new JVersion();
define ( 'KUNENA_JOOMLA_COMPAT', $jversion->RELEASE);

// Register Joomla and Kunena autoloader
if (function_exists('__autoload')) spl_autoload_register('__autoload');
spl_autoload_register('KunenaAutoload');

// Give access to all Kunena tables
jimport('joomla.database.table');
JTable::addIncludePath(KPATH_ADMIN.'/libraries/tables');
// Give access to all JHtml functions
jimport('joomla.html.html');
JHtml::addIncludePath(KPATH_ADMIN.'/libraries/html/html');

/**
 * Intelligent library importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.6
 * @deprecated 2.0
 */
function kimport($path) {}

/**
 * Kunena auto loader
 *
 * @param string $class Class to be registered
 */
function KunenaAutoload($class) {
	if (substr($class, 0, 6) != 'Kunena') return;
	$file = KPATH_ADMIN . '/libraries/' . strtolower(preg_replace( '/([A-Z])/', '/\\1', substr($class, 6)));
	if (is_dir($file)) {
		$fileparts = explode( '/', $file );
		$file .= '/'.array_pop( $fileparts );
	}
	$file .= '.php';
	if (file_exists($file)) {
		require_once $file;
	}
}

// Version information
class KunenaForum {
	private function __construct() {}

	public static function isDev() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function isCompatible($version) {
		// If requested version is smaller than 2.0.0-DEV, it's not compatible
		if (version_compare($version, '2.0.0-DEV', '<')) {
			return false;
		}
		// Check if future version is needed (remove GIT and DEVn from the current version)
		if (version_compare($version, preg_replace('/(-DEV\d*)?(-GIT)?/i', '', self::version()), '>')) {
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

	public static function getVersionInfo() {
		$version = new stdClass();
		$version->version = self::version();
		$version->date = self::versionDate();
		$version->name = self::versionName();
		return $version;
	}

	public static function enabled() {
		return false;
	}

	public static function installed() {
		return false;
	}

	public static function setup() {
		if (class_exists('KunenaFactory')) KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
	}

	public function display($viewName, $layout='default', $template=null, $params = array()) {}
}

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
