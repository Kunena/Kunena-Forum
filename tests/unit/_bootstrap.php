<?php
// Here you can initialize variables that will be available to your tests

define("_JEXEC", 'true');

// Maximise error reporting.
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overridden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('JPATH_TESTS'))
{
	define('JPATH_TESTS', realpath(__DIR__));
}
if (!defined('JPATH_TEST_DATABASE'))
{
	define('JPATH_TEST_DATABASE', JPATH_TESTS . '/stubs/database');
}
if (!defined('JPATH_TEST_STUBS'))
{
	define('JPATH_TEST_STUBS', JPATH_TESTS . '/stubs');
}

// Installation path of joomla, we use the cache directory here (Robo run testing site)..
if (!defined('JINSTALL_PATH'))
{
	define('JINSTALL_PATH', dirname(JPATH_TESTS) . '/cache');
}

if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', JINSTALL_PATH . '/libraries');
}
if (!defined('JPATH_LIBRARIES'))
{
	define('JPATH_LIBRARIES', JINSTALL_PATH . '/libraries');
}
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', JINSTALL_PATH);
}
if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', realpath(JPATH_BASE));
}
if (!defined('JPATH_CACHE'))
{
	define('JPATH_CACHE', JPATH_BASE . '/cache');
}
if (!defined('JPATH_CONFIGURATION'))
{
	define('JPATH_CONFIGURATION', JPATH_BASE);
}
if (!defined('JPATH_SITE'))
{
	define('JPATH_SITE', JPATH_ROOT);
}
if (!defined('JPATH_ADMINISTRATOR'))
{
	define('JPATH_ADMINISTRATOR', JPATH_ROOT . '/administrator');
}
if (!defined('JPATH_INSTALLATION'))
{
	define('JPATH_INSTALLATION', JPATH_ROOT . '/installation');
}
if (!defined('JPATH_MANIFESTS'))
{
	define('JPATH_MANIFESTS', JPATH_ADMINISTRATOR . '/manifests');
}
if (!defined('JPATH_PLUGINS'))
{
	define('JPATH_PLUGINS', JPATH_BASE . '/plugins');
}
if (!defined('JPATH_THEMES'))
{
	define('JPATH_THEMES', JPATH_BASE . '/templates');
}
if (!defined('JDEBUG'))
{
	define('JDEBUG', false);
}

if (!defined('JPATH_COMPONENT_SITE'))
{
	define('JPATH_COMPONENT_SITE', dirname(dirname(JPATH_BASE)) . '/src/component/com_weblinks');
}

if (!defined('JPATH_COMPONENT_ADMINISTRATOR'))
{
	define('JPATH_COMPONENT_ADMINISTRATOR', dirname(dirname(JPATH_BASE)) . '/src/administrator/component/com_weblinks');
}

$_SERVER['HTTP_HOST'] = "localhost";

// Import the platform in legacy mode.
require_once JPATH_PLATFORM . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

require_once JPATH_PLATFORM . '/platform.php';
require_once JPATH_PLATFORM . '/loader.php';

// Setup the autoloaders.
JLoader::setup();
