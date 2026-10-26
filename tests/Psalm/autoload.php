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

$joomlaRoot = rtrim((string) getenv('KUNENA_TEST_JOOMLA'), '/\\');

if ($joomlaRoot === '' || !is_file($joomlaRoot . '/libraries/vendor/autoload.php')) {
    fwrite(STDERR, "Set KUNENA_TEST_JOOMLA to a Joomla installation (the folder with libraries/vendor/autoload.php).\n");

    exit(1);
}

// The files which the autoloader of Joomla loads stop without this constant (and Psalm with them, without a message).
\define('_JEXEC', 1);

require_once $joomlaRoot . '/libraries/vendor/autoload.php';
