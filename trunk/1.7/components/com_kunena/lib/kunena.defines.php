<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2010 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
defined( '_JEXEC' ) or die();

// TODO: DEPRECATED

// File system paths
define('KUNENA_COMPONENT_RELPATH', 'components' .DS. KUNENA_COMPONENT_NAME);

define('KUNENA_ROOT_PATH', JPATH_ROOT);

define('KUNENA_PATH', KUNENA_ROOT_PATH .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_FUNCS', KUNENA_PATH .DS. 'funcs');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');

// Kunena uploaded files directory
define('KUNENA_RELPATH_UPLOADED', 'media/kunena/attachments');
define('KUNENA_PATH_UPLOADED', KUNENA_ROOT_PATH .DS. KUNENA_RELPATH_UPLOADED);

// Kunena uploaded avatars directory
define('KUNENA_RELPATH_AVATAR_UPLOADED', '/media/kunena/avatars');
define('KUNENA_PATH_AVATAR_UPLOADED', KUNENA_ROOT_PATH . KUNENA_RELPATH_AVATAR_UPLOADED);
