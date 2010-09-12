<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2010 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

// Load new API
require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/api.php');

// Default values
define('KUNENA_TEMPLATE_DEFAULT', 'default');

// File system paths
define('KUNENA_COMPONENT_RELPATH', 'components' .DS. KUNENA_COMPONENT_NAME);

define('KUNENA_ROOT_PATH', JPATH_ROOT);
define('KUNENA_ROOT_PATH_ADMIN', JPATH_ADMINISTRATOR);

define('KUNENA_PATH', KUNENA_ROOT_PATH .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_FUNCS', KUNENA_PATH .DS. 'funcs');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');
define('KUNENA_PATH_TEMPLATE_DEFAULT', KUNENA_PATH_TEMPLATE .DS. KUNENA_TEMPLATE_DEFAULT);

define('KUNENA_PATH_ADMIN', KUNENA_ROOT_PATH_ADMIN .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_ADMIN_LIB', KUNENA_PATH_ADMIN .DS. 'lib');
define('KUNENA_PATH_ADMIN_INSTALL', KUNENA_PATH_ADMIN .DS. 'install');
define('KUNENA_PATH_ADMIN_IMAGES', KUNENA_PATH_ADMIN .DS. 'images');

// Kunena uploaded files directory
define('KUNENA_RELPATH_UPLOADED', 'media/kunena/attachments');
define('KUNENA_PATH_UPLOADED', KUNENA_ROOT_PATH .DS. KUNENA_RELPATH_UPLOADED);

// Kunena uploaded avatars directory
define('KUNENA_RELPATH_AVATAR_UPLOADED', '/media/kunena/avatars');
define('KUNENA_PATH_AVATAR_UPLOADED', KUNENA_ROOT_PATH . KUNENA_RELPATH_AVATAR_UPLOADED);

// Kunena legacy uploaded files directory
define('KUNENA_RELPATH_UPLOADED_LEGACY', '/images/fbfiles');
define('KUNENA_PATH_UPLOADED_LEGACY', KUNENA_ROOT_PATH . KUNENA_RELPATH_UPLOADED_LEGACY);

// The tunmbnail folder is relative to any image file folder
define('KUNENA_FOLDER_THUMBNAIL', 'thumb');

// Files
define('KUNENA_FILE_INSTALL', KUNENA_PATH_ADMIN .DS. 'kunena.xml');

// Legacy version information
define ('KUNENA_VERSION', Kunena::version());
define ('KUNENA_VERSION_DATE', Kunena::versionDate());
define ('KUNENA_VERSION_NAME', Kunena::versionName());
define ('KUNENA_VERSION_BUILD', Kunena::versionBuild());

// Time related
define ('KUNENA_SECONDS_IN_HOUR', 3600);
define ('KUNENA_SECONDS_IN_YEAR', 31536000);

// Database defines
define ('KUNENA_DB_MISSING_COLUMN', 1054);
