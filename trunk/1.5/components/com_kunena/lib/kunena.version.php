<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');


global $mainframe;
require_once (JPATH_ROOT . '/components/com_kunena/lib/kunena.debug.php');

// use default translations if none are available
if (!defined('_KUNENA_INSTALLED_VERSION')) DEFINE('_KUNENA_INSTALLED_VERSION', 'Installed version');
if (!defined('_KUNENA_COPYRIGHT')) DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
if (!defined('_KUNENA_LICENSE')) DEFINE('_KUNENA_LICENSE', 'License');

class CKunenaVersion {
	/**
	* Retrieve installed Kunena version as array.
	*
	* @return array Contains fields: version, versiondate, build, versionname
	*/
	function versionArray()
	{
		global $database;
		static $kunenaversion;

		if (!$kunenaversion)
		{
			$versionTable = '#__fb_version';
			$database->setQuery( 	"SELECT
							`version`,
							`versiondate`,
							`installdate`,
							`build`,
							`versionname`
						FROM `$versionTable`
						ORDER BY `id` DESC LIMIT 1;" );
			$kunenaversion = $database->loadObject();
			if(!$kunenaversion) {
				$kunenaversion = new StdClass();
				$kunenaversion->version = '1.0.x';
				$kunenaversion->versiondate = 'NOT INSTALLED';
				$kunenaversion->installdate = 'Not installed';
				$kunenaversion->build = '';
				$kunenaversion->versionname = 'Unknown';
			}
		}
		return $kunenaversion;
	}

	/** 
	* Retrieve installed Kunena version as string.
	*
	* @return string "X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname]"
	*/
	function version()
	{
		$version = CKunenaVersion::versionArray();
		return 'Kunena '.$version->version.' | '.$version->versiondate.' | '.$version->build.' [ '.$version->versionname.' ]';
	}

	/** 
	* Retrieve installed Kunena version, copyright and license as string.
	*
	* @return string "Installed version: Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname] | © Copyright: Kunena | License: GNU GPL"
	*/
	function versionHTML()
	{
		$version = CKunenaVersion::version();
		return _KUNENA_INSTALLED_VERSION.': '.$version.' | '._KUNENA_COPYRIGHT.': &copy; 2008-2009 <a href = "http://www.Kunena.com" target = "_blank">Kunena</a>  | '._KUNENA_LICENSE.': <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a>';
	}

	/** 
	* Retrieve MySQL Server version.
	*
	* @return string MySQL version
	*/
	function MySQLVersion()
	{
		global $database;
		static $mysqlversion;
		if (!$mysqlversion)
		{
			$database->setQuery("SELECT VERSION() as mysql_version");
			$mysqlversion = $database->loadResult();
			if (!$mysqlversion) $mysqlversion = 'unknown';
		}
		return $mysqlversion;
	}

	/**
	* Retrieve PHP Server version.
	*
	* @return string PHP version
	*/
	function PHPVersion()
	{
		return phpversion();
	}
}
?>