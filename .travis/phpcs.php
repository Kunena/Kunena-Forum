<?php
/**
 * Command line script for executing PHPCS during a Travis build.
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Require Composer autoloader
if (!file_exists(REPO_BASE . '/vendor/autoload.php'))
{
	fwrite(STDOUT, "\033[37;41mThis script requires Composer to be set up, please run 'composer install' first.\033[0m\n");
}

require REPO_BASE . '/vendor/autoload.php';

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHP_CodeSniffer checks.\033[0m\n");

// Ignored files
$ignored = array(
	REPO_BASE . '/component/admin/views/*/tmpl/*',
	REPO_BASE . '/component/admin/layouts/*',
	REPO_BASE . '/component/site/views/*/tmpl/*',
	REPO_BASE . '/component/site/layouts/*',
);

// Build the options for the sniffer
$options = array(
	'files'        => array(
		REPO_BASE . '/plugins',
		REPO_BASE . '/components',
		REPO_BASE . '/libraries',
	),
	'standard'     => array( REPO_BASE . '/.travis/phpcs/Joomla'),
	'ignored'      => $ignored,
	'showProgress' => true,
	'verbosity' => false
);

// Instantiate the sniffer
$phpcs = new PHP_CodeSniffer_CLI;

// Ensure PHPCS can run, will exit if requirements aren't met
$phpcs->checkRequirements();

// Run the sniffs
$numErrors = $phpcs->process($options);

// If there were errors, output the number and exit the app with a fail code
if ($numErrors)
{
	fwrite(STDOUT, sprintf("\033[37;41mThere were %d issues detected.\033[0m\n", $numErrors));
	exit(1);
}
else
{
	fwrite(STDOUT, "\033[32;1mThere were no issues detected.\033[0m\n");
	exit(0);
}
