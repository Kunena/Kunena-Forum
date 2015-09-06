<?php
/**
 * Command line script for executing detecting PHP Parse Errors.
 *
 * This CLI is used instead normal travis.yml execution to avoid error in travis build when
 * PHPMD exits with 2.
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @example: php .travis/phppec.php component/ libraries/
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHP Parse Error checks.\033[0m\n");

if (2 > count($argv))
{
    fwrite(STDOUT, "\033[32;1mPlease add path to check for PHP Parse Errors.\033[0m\n");
    exit(1);
}

$errors = 0;

for($i=1;$i < count($argv);$i++)
{
    $folderToCheck = REPO_BASE . '/' .$argv[$i];

    if (!file_exists($folderToCheck))
    {
        fwrite(STDOUT, "\033[31;1mFolder: " . $argv[$i] . " does not exist\033[0m\n");
        continue;
    }

    fwrite(STDOUT, "\033[32;1m- Checking errors at: " . $argv[$i] . "\033[0m\n");
    $parseErrors = shell_exec('find ' . $folderToCheck .' -name "*.php" -exec php -l {} \; | grep "Parse error";');

    if ($parseErrors)
    {
        $errors = 1;
        fwrite(STDOUT, "\033[31;1mParse error found:\033[0m\n");
        fwrite(STDOUT, $parseErrors);
    }
}

if ($errors)
{
    exit(1);
}

exit(0);