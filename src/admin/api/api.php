<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;

if (defined('KUNENA_LOADED'))
{
	return;
}

/**
 *
 */
define('KUNENA_PROFILER', KunenaConfig::getInstance()->profiler);

// Component name and database prefix
/**
 *
 */
define('KUNENA_COMPONENT_NAME', 'com_kunena');
/**
 *
 */
define('KUNENA_COMPONENT_LOCATION', 'components');
/**
 *
 */
define('KUNENA_NAME', substr(KUNENA_COMPONENT_NAME, 4));

// Component paths
/**
 *
 */
define('KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME);
/**
 *
 */
define('KPATH_SITE', JPATH_ROOT . '/' . KPATH_COMPONENT_RELATIVE);
/**
 *
 */
define('KPATH_ADMIN', JPATH_ADMINISTRATOR . '/' . KPATH_COMPONENT_RELATIVE);
/**
 *
 */
define('KPATH_MEDIA', JPATH_ROOT . '/media/' . KUNENA_NAME);
/**
 *
 */
define('KPATH_FRAMEWORK', JPATH_ROOT . '/libraries/kunena');

// URLs
/**
 *
 */
define('KURL_COMPONENT', 'index.php?option=' . KUNENA_COMPONENT_NAME);
/**
 *
 */
define('KURL_SITE', Uri::Root() . KPATH_COMPONENT_RELATIVE . '/');
/**
 *
 */
define('KURL_MEDIA', Uri::Root() . 'media/' . KUNENA_NAME . '/');

// Kunena has been initialized
/**
 *
 */
define('KUNENA_LOADED', 1);
