<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Tests
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

/*
 * The classes of Joomla for Psalm (psalm.xml): the autoloader of a Joomla installation, the one of the environment variable
 * KUNENA_TEST_JOOMLA (like for the unit tests). Without them, Psalm does not know the types of Joomla and loses the data of
 * the request on the way.
 */

use Composer\Autoload\ClassLoader;

$joomlaRoot =rtrim((string) getenv('KUNENA_TEST_JOOMLA'), '/\\');

if ($joomlaRoot === '' || !is_file($joomlaRoot . '/libraries/vendor/autoload.php')) {
    fwrite(STDERR, "Set KUNENA_TEST_JOOMLA to a Joomla installation (the folder with libraries/vendor/autoload.php).\n");

    exit(1);
}

// The files which the autoloader of Joomla loads stop without this constant (and Psalm with them, without a message).
\define('_JEXEC', 1);

$loader = require $joomlaRoot . '/libraries/vendor/autoload.php';

// The classes of the components of Joomla (the PluginsModel, the TaskPluginTrait of the scheduler...): Joomla keeps their
// namespaces in administrator/cache/autoload_psr4.php, which needs the paths of the installation. The namespaces of Kunena are
// left out: they lead through the links of the site to this checkout, which Psalm analyses itself.
// The paths are also the ones which Psalm uses for the files included with them (require_once KPATH_SITE . '/...'): the ones of
// Joomla lead to the installation, the ones of Kunena to this checkout.
$kunena = \dirname(__DIR__, 2) . '/src';
$paths  = [
    'JPATH_ROOT'               => $joomlaRoot,
    'JPATH_BASE'               => $joomlaRoot,
    'JPATH_SITE'               => $joomlaRoot,
    'JPATH_PUBLIC'             => $joomlaRoot,
    'JPATH_ADMINISTRATOR'      => $joomlaRoot . '/administrator',
    'JPATH_API'                => $joomlaRoot . '/api',
    'JPATH_CACHE'              => $joomlaRoot . '/cache',
    'JPATH_COMPONENT'          => $kunena . '/site',
    'JPATH_CONFIGURATION'      => $joomlaRoot,
    'JPATH_INSTALLATION'       => $joomlaRoot . '/installation',
    'JPATH_LIBRARIES'          => $joomlaRoot . '/libraries',
    'JPATH_MANIFESTS'          => $joomlaRoot . '/administrator/manifests',
    'JPATH_PLATFORM'           => $joomlaRoot . '/libraries',
    'JPATH_PLUGINS'            => $joomlaRoot . '/plugins',
    'JPATH_THEMES'             => $joomlaRoot . '/templates',
    'KPATH_ADMIN'              => $kunena . '/admin',
    'KPATH_FRAMEWORK'          => $kunena . '/libraries/kunena',
    'KPATH_SITE'               => $kunena . '/site',
    'KPATH_MEDIA'              => $kunena . '/media/kunena',
    'KPATH_COMPONENT_RELATIVE' => 'components/com_kunena',
];

foreach ($paths as $name => $path) {
    if (!\defined($name)) {
        \define($name, $path);
    }
}

$map = $joomlaRoot . '/administrator/cache/autoload_psr4.php';

if (is_file($map) && $loader instanceof ClassLoader) {
    foreach ((array) require $map as $namespace => $folders) {
        if (!str_starts_with($namespace, 'Kunena\\')) {
            $loader->addPsr4($namespace, (array) $folders);
        }
    }
}
