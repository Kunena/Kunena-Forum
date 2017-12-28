<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Framework
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

if (defined('KUNENA_LOADED'))
{
	return;
}

// Manually enable code profiling by setting value to 1
define('KUNENA_PROFILER', 0);

// Component name amd database prefix
define('KUNENA_COMPONENT_NAME', 'com_kunena');
define('KUNENA_COMPONENT_LOCATION', 'components');
define('KUNENA_NAME', substr(KUNENA_COMPONENT_NAME, 4));

// Component paths
define('KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME);
define('KPATH_SITE', JPATH_ROOT . '/' . KPATH_COMPONENT_RELATIVE);
define('KPATH_ADMIN', JPATH_ADMINISTRATOR . '/' . KPATH_COMPONENT_RELATIVE);
define('KPATH_MEDIA', JPATH_ROOT . '/media/' . KUNENA_NAME);

// URLs
define('KURL_COMPONENT', 'index.php?option=' . KUNENA_COMPONENT_NAME);
define('KURL_SITE', JUri::Root() . KPATH_COMPONENT_RELATIVE . '/');
define('KURL_MEDIA', JUri::Root() . 'media/' . KUNENA_NAME . '/');

$libraryFile = JPATH_PLATFORM . '/kunena/bootstrap.php';

if (is_file($libraryFile))
{
	require_once $libraryFile;
}

if (JFactory::getApplication()->isSite())
{
	JLoader::registerPrefix('ComponentKunenaController', KPATH_SITE . '/controller');
}

// Kunena has been initialized
define('KUNENA_LOADED', 1);
