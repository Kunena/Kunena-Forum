<?php

/**
 * Prepares a minimalist framework for the unit tests and the integration tests of Kunena.
 *
 * The tests of Kunena run with or without a Joomla installation (like the tests of Joomla, they only need the framework of
 * Joomla, not a running site):
 *
 *  - Without Joomla the classes of Kunena which need nothing of Joomla are tested; the tests which need the framework of Joomla
 *    (see UnitTestCase::requireJoomla()) are skipped, and the tests of the database (the Integration suite) are skipped.
 *  - With the path of a Joomla installation (the environment variable KUNENA_TEST_JOOMLA, or the constant KTEST_JOOMLA_ROOT in
 *    phpunit.xml) the framework of Joomla is loaded as Joomla does it for its own tests, so the real classes of Joomla are used.
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

// phpcs:disable PSR1.Files.SideEffects

\define('_JEXEC', 1);

// Maximise error reporting.
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set a fixed precision to avoid round related issues.
ini_set('precision', '14');

$root = \dirname(__DIR__, 2);

/** @var \Composer\Autoload\ClassLoader $kunenaLoader */
$kunenaLoader = require $root . '/vendor/autoload.php';

// The code of Kunena: Kunena\Forum\Libraries\Html\KunenaParser is src/libraries/kunena/src/Html/KunenaParser.php.
$kunenaLoader->addPsr4('Kunena\\Forum\\Libraries\\', $root . '/src/libraries/kunena/src/');
$kunenaLoader->addPsr4('Kunena\\Forum\\Site\\', $root . '/src/site/src/');
$kunenaLoader->addPsr4('Kunena\\Forum\\Administrator\\', $root . '/src/admin/src/');

// The tests themselves: Kunena\Tests\Unit\Libraries\Kunena\Html\KunenaMapTest is tests/Unit/Libraries/Kunena/Html/KunenaMapTest.php.
$kunenaLoader->addPsr4('Kunena\\Tests\\Unit\\', $root . '/tests/Unit/');
$kunenaLoader->addPsr4('Kunena\\Tests\\Integration\\', $root . '/tests/Integration/');

// The installation of Joomla whose framework is used (optional).
$joomlaRoot = \defined('KTEST_JOOMLA_ROOT') ? (string) \constant('KTEST_JOOMLA_ROOT') : (string) getenv('KUNENA_TEST_JOOMLA');
$joomlaRoot = $joomlaRoot !== '' ? rtrim(str_replace('\\', '/', $joomlaRoot), '/') : '';

if ($joomlaRoot !== '' && is_file($joomlaRoot . '/libraries/loader.php')) {
    // The constants of Joomla, as the unit tests of Joomla define them.
    $constants = [
        'JPATH_BASE'          => $joomlaRoot,
        'JPATH_ROOT'          => $joomlaRoot,
        'JPATH_PLATFORM'      => $joomlaRoot . '/libraries',
        'JPATH_LIBRARIES'     => $joomlaRoot . '/libraries',
        'JPATH_CONFIGURATION' => $joomlaRoot,
        'JPATH_SITE'          => $joomlaRoot,
        'JPATH_ADMINISTRATOR' => $joomlaRoot . '/administrator',
        'JPATH_CACHE'         => $joomlaRoot . '/administrator/cache',
        'JPATH_API'           => $joomlaRoot . '/api',
        'JPATH_INSTALLATION'  => $joomlaRoot . '/installation',
        'JPATH_MANIFESTS'     => $joomlaRoot . '/administrator/manifests',
        'JPATH_PLUGINS'       => $joomlaRoot . '/plugins',
        'JPATH_THEMES'        => $joomlaRoot . '/templates',
        'JDEBUG'              => false,
    ];

    foreach ($constants as $name => $value) {
        if (!\defined($name)) {
            \define($name, $value);
        }
    }

    if (!class_exists('JLoader')) {
        require_once JPATH_LIBRARIES . '/loader.php';
    }

    JLoader::setup();

    /** @var \Composer\Autoload\ClassLoader $joomlaLoader */
    $joomlaLoader = require JPATH_LIBRARIES . '/vendor/autoload.php';

    // The loader of Joomla decorates the one of Composer; the one of Composer is taken out first (as Joomla does).
    class_exists('\\Joomla\\CMS\\Autoload\\ClassLoader');

    $joomlaLoader->unregister();

    spl_autoload_register([new \Joomla\CMS\Autoload\ClassLoader($joomlaLoader), 'loadClass'], true, true);

    // The namespaces of the extensions which are installed in that Joomla.
    require_once JPATH_LIBRARIES . '/namespacemap.php';

    $extensionLoader = new \JNamespacePsr4Map();
    $extensionLoader->load();

    \defined('JVERSION') or \define('JVERSION', (new \Joomla\CMS\Version())->getShortVersion());

    // The classes of Kunena are those of this checkout, also when the Joomla installation has its own copy.
    $kunenaLoader->register(true);
} else {
    // No Joomla: a small stand-in for the class of the parameters of a category.
    require_once __DIR__ . '/Support/RegistryStub.php';
}
