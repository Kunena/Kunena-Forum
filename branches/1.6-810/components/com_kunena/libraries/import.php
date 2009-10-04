<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

// Define the Kunena Libraries path constant.
if (!defined('KPATH_LIBRARIES')) {
	define('KPATH_LIBRARIES', realpath(dirname(__FILE__)));
}

/**
 * Kunena Libraries intelligent file importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.0
 */
function kimport($path)
{
	return JLoader::import($path, KPATH_LIBRARIES);
}

/*
 * Kunena System Constants.
 */

// Version information
define ('KUNENA_VERSION', '@kunenaversion@');
define ('KUNENA_VERSION_DATE', '@kunenaversiondate@');
define ('KUNENA_VERSION_NAME', '@kunenaversionname@');
define ('KUNENA_VERSION_BUILD', '@kunenaversionbuild@');

// Default values
define('KUNENA_COMPONENT_NAME', 'com_kunena');
define('KUNENA_LANGUAGE_DEFAULT', 'english');
define('KUNENA_TEMPLATE_DEFAULT', 'default');

// Set the legacy language constant.
$language = JFactory::getLanguage();
define('KUNENA_LANGUAGE', $language->getBackwardLang());

// File system paths
define('KUNENA_COMPONENT_RELPATH', 'components' .DS. KUNENA_COMPONENT_NAME);

define('KUNENA_ROOT_PATH', JPATH_ROOT);
define('KUNENA_ROOT_PATH_ADMIN', JPATH_ADMINISTRATOR);

define('KUNENA_PATH', KUNENA_ROOT_PATH .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_TABLES', KUNENA_PATH .DS. 'tables');
define('KUNENA_PATH_MODELS', KUNENA_PATH .DS. 'models');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');
define('KUNENA_PATH_TEMPLATE_DEFAULT', KUNENA_PATH_TEMPLATE .DS. KUNENA_TEMPLATE_DEFAULT);

define('KUNENA_PATH_ADMIN', KUNENA_ROOT_PATH_ADMIN .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_ADMIN_LIB', KUNENA_PATH_ADMIN .DS. 'lib');
define('KUNENA_PATH_ADMIN_LANGUAGE', KUNENA_PATH_ADMIN .DS. 'language');
define('KUNENA_PATH_ADMIN_INSTALL', KUNENA_PATH_ADMIN .DS. 'install');
define('KUNENA_PATH_ADMIN_IMAGES', KUNENA_PATH_ADMIN .DS. 'images');

// Kunena uploaded files directory
define('KUNENA_PATH_UPLOADED', KUNENA_ROOT_PATH . '/images/kunenafiles');

// Files
define('KUNENA_FILE_LANGUAGE_DEFAULT', KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . KUNENA_LANGUAGE_DEFAULT . '.php');
define('KUNENA_FILE_LANGUAGE', KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . KUNENA_LANGUAGE . '.php');
define('KUNENA_FILE_INSTALL', KUNENA_PATH_ADMIN .DS. 'manifest.xml');

// URLs

define('KUNENA_RELURL', 'index.php?option=com_kunena');

// Constants

// Minimum version requirements
DEFINE('KUNENA_MIN_PHP',   '5.0.3');
DEFINE('KUNENA_MIN_MYSQL', '5.0.0');

// Time related
define ('KUNENA_SECONDS_IN_HOUR', 3600);
define ('KUNENA_SECONDS_IN_YEAR', 31536000);

// Database defines
define ('KUNENA_DB_MISSING_COLUMN', 1054);
