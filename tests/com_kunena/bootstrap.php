<?php
/**
 * Loads Joomla framework for unit testing.
 *
 */

// Maximise error reporting.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define expected Joomla constants.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

// Load Joomla framework
define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));
require_once JPATH_BASE.'/includes/defines.php';
require_once JPATH_BASE.'/includes/framework.php';
jimport('joomla.filesystem.path');
jimport('joomla.log.log');
jimport('joomla.environment.request');
jimport('joomla.session.session');

$_SERVER['HTTP_HOST'] = 'http://localhost';
$_SERVER['REQUEST_URI'] = '/index.php';

$app = JFactory::getApplication('site');

if (!defined('KPATH_TESTS'))
{
	define('KPATH_TESTS', dirname(__FILE__));
}

// Exclude all of the tests from code coverage reports
if (class_exists('PHP_CodeCoverage_Filter')) {
	PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(JPATH_BASE . '/tests');
} else {
	PHPUnit_Util_Filter::addDirectoryToFilter(JPATH_BASE . '/tests');
}

// Set error handling.
JError::setErrorHandling(E_NOTICE, 'ignore');
JError::setErrorHandling(E_WARNING, 'ignore');
JError::setErrorHandling(E_ERROR, 'ignore');

// Load Kunena API
require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';