<?php
/**
 * Command line script for executing PHP Debug Checker during a Travis build.
 *
 * This CLI is used instead normal travis.yml execution to avoid error in travis build when
 * PHPMD exits with 2.
 *
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @example: php .travis/phpmd.php component/ libraries/
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHP Debug Missed Debug Code Checker.\033[0m\n");

// Check for arguments
if (2 > count($argv))
{
    fwrite(STDOUT, "\033[32;1mPlease add path to check with PHP Debug Missed Code Checker.\033[0m\n");
    exit(1);
}

for($i=1;$i < count($argv);$i++)
{
    $folderToCheck = REPO_BASE . '/' .$argv[$i];

    if (!file_exists($folderToCheck))
    {
        fwrite(STDOUT, "\033[32;1mFolder: " . $argv[$i] . " does not exist\033[0m\n");
        continue;
    }

    fwrite(STDOUT, "\033[32;1m- Checking missed debug code at: " . $argv[$i] . "\033[0m\n");
    $vardumpCheck = shell_exec('grep -r --include "*.php" var_dump ' . $folderToCheck);
    $consolelogCheck = shell_exec('grep -r --include "*.js" console.log ' . $folderToCheck);


    if ($vardumpCheck)
    {
        fwrite(STDOUT, "\033[31;1mWARNING: Missed Debug code detected: var_dump was found\033[0m\n");
        fwrite(STDOUT, $vardumpCheck);
        exit(1);
    }

    if ($consolelogCheck)
    {
        fwrite(STDOUT, "\033[31;1mWARNING: Missed Debug code detected: console.log was found\033[0m\n");
        fwrite(STDOUT, $consolelogCheck);
        exit(1);
    }
}

exit(0);
