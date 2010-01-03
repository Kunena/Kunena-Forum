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

require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.debug.php');

// use default translations if none are available
if (!defined('_KUNENA_INSTALLED_VERSION')) DEFINE('_KUNENA_INSTALLED_VERSION', 'Installed version');
if (!defined('_KUNENA_COPYRIGHT')) DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
if (!defined('_KUNENA_LICENSE')) DEFINE('_KUNENA_LICENSE', 'License');

class CKunenaVersion {
	/**
	* Retrieve Kunena version from manifest.xml
	*
	* @return string version
	*/
	function versionXML()
	{
		$data = JApplicationHelper::parseXMLInstallFile(KUNENA_FILE_INSTALL);
		if ($data) {
			return $data['version'];
		}
		return 'ERROR';
	}

	/**
	* Retrieve installed Kunena version as array.
	*
	* @return array Contains fields: version, versiondate, build, versionname
	*/
	function versionArray()
	{
		static $kunenaversion=NULL;

		if (!$kunenaversion)
		{
			$kunena_db = &JFactory::getDBO();
			$versionTable = '#__fb_version';
			$kunena_db->setQuery("SELECT version, versiondate, installdate, build, versionname FROM `{$versionTable}` ORDER BY id DESC", 0, 1);
			$kunenaversion = $kunena_db->loadObject();
			if(!$kunenaversion) {
				$kunenaversion = new StdClass();
				$kunenaversion->version = CKunenaVersion::versionXML();
				$kunenaversion->versiondate = 'UNKNOWN';
				$kunenaversion->installdate = '0000-00-00';
				$kunenaversion->build = '0000';
				$kunenaversion->versionname = 'NOT INSTALLED';
			}
			$xmlversion = CKunenaVersion::versionXML();
			if($kunenaversion->version != $xmlversion) {
				$kunenaversion->version = CKunenaVersion::versionXML();
				$kunenaversion->versionname = 'NOT UPGRADED';
			}
			$kunenaversion->version = JString::strtoupper($kunenaversion->version);
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
		static $mysqlversion=NULL;
		if (!$mysqlversion)
		{
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery("SELECT VERSION() AS mysql_version");
			$mysqlversion = $kunena_db->loadResult();
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
