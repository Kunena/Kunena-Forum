<?php
/**
 * Command line script for checking that Apache is working and we see the Joomla installer in the browser of Travis
 *
 * This CLI is used instead normal travis.yml execution to avoid error in travis build when
 * PHPMD exits with 2.
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Welcome message
fwrite(STDOUT, "\033[32;1mChecking Apache working and Joomla is ready to install in the Travis webserver.\033[0m\n");

if (2 > count($argv))
{
    fwrite(STDOUT, "\033[32;1mPlease localhost URL to check for PHP Joomla Installer.\033[0m\n");
    exit(1);
}

$joomlaUrl = $argv[1];

$foundJoomlaInstaller = shell_exec('curl ' . $joomlaUrl . ' | grep "Joomla";');

if (!$foundJoomlaInstaller)
{
    fwrite(STDOUT, "\033[31;1mWeb server not working in Travis\033[0m\n");
    exit(1);
}

fwrite(STDOUT, "\033[32;1mWeb server is working in Travis\033[0m\n");
exit(0);
