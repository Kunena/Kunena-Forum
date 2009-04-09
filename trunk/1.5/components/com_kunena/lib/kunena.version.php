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

require_once(KUNENA_PATH_ADMIN_LIB .DS. "fx.upgrade.class.php");
include_once(KUNENA_PATH_LIB .DS. "kunena.debug.php");

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
		static $kunenaversion;

		if (!$kunenaversion)
		{
		    $database = &JFactory::getDBO();

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
			    check_dbwarning('Could not load latest Version record.');
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
		static $mysqlversion;
		if (!$mysqlversion)
		{
		    $database = &JFactory::getDBO();

		    $database->setQuery("SELECT VERSION() as mysql_version");
			$mysqlversion = $database->loadResult();
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