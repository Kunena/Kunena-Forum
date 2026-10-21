<?php

/**
 * Kunena Component
 *
 * Bootstrap of the unit tests: they run without Joomla, so only the logic which does not need Joomla or the database can be
 * tested. The files of Kunena refuse to load without _JEXEC, which Joomla defines.
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\define('_JEXEC', 1);

$root   = \dirname(__DIR__, 2);
$loader = require $root . '/vendor/autoload.php';

// The library of Kunena: Kunena\Forum\Libraries\Html\KunenaParser is src/libraries/kunena/src/Html/KunenaParser.php.
$loader->addPsr4('Kunena\\Forum\\Libraries\\', $root . '/src/libraries/kunena/src/');
$loader->addPsr4('Kunena\\Forum\\Tests\\Unit\\', __DIR__ . '/');

// Joomla is not there: the class of the parameters of a category is a small stand-in.
require_once __DIR__ . '/Support/RegistryStub.php';
