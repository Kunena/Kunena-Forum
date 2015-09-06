<?php
/**
 * Command line script for executing PHPMD during a Travis build.
 *
 * This CLI is used instead normal travis.yml execution to avoid error in travis build when
 * PHPMD exits with 2.
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT,com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHPMD checks.\033[0m\n");

$messDetectorWarnings = shell_exec(REPO_BASE . '/vendor/bin/phpmd ' . REPO_BASE . '/components/ text codesize,unusedcode,controversial');
fwrite(STDOUT, $messDetectorWarnings);

exit(0);
