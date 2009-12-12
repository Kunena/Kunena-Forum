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

require_once(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_kunena' .DS. 'api.php');

/*
 * Kunena System Constants.
 */

// Default values
define('KUNENA_LANGUAGE_DEFAULT', 'english');
define('KUNENA_TEMPLATE_DEFAULT', 'default');

// Set the legacy language constant.
$language = JFactory::getLanguage();
define('KUNENA_LANGUAGE', $language->getBackwardLang());

// File system paths
define('KUNENA_ROOT_PATH', JPATH_ROOT);
define('KUNENA_ROOT_PATH_ADMIN', JPATH_ADMINISTRATOR);

define('KUNENA_PATH', KUNENA_ROOT_PATH .DS. KPATH_COMPONENT_RELATIVE);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_TABLES', KUNENA_PATH .DS. 'tables');
define('KUNENA_PATH_MODELS', KUNENA_PATH .DS. 'models');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');
define('KUNENA_PATH_TEMPLATE_DEFAULT', KUNENA_PATH_TEMPLATE .DS. KUNENA_TEMPLATE_DEFAULT);

define('KUNENA_PATH_ADMIN', KUNENA_ROOT_PATH_ADMIN .DS. KPATH_COMPONENT_RELATIVE);
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

// Time related
define ('KUNENA_SECONDS_IN_HOUR', 3600);
define ('KUNENA_SECONDS_IN_YEAR', 31536000);

// Database defines
define ('KUNENA_DB_MISSING_COLUMN', 1054);
