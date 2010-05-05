<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// no direct access
defined( '_JEXEC' ) or die();


require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.debug.php');

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
			check_dberror ( 'Unable to load version.' );

			if(!$kunenaversion) {
				$kunenaversion = new StdClass();
				$kunenaversion->version = CKunenaVersion::versionXML();
				$kunenaversion->versiondate = 'UNKNOWN';
				$kunenaversion->installdate = '0000-00-00';
				$kunenaversion->build = '0000';
				$kunenaversion->versionname = 'NOT INSTALLED';
			}
			$xmlversion = CKunenaVersion::versionXML();

			// Special check for svn test installs as the version name in the xml is not set
			if ( JString::strpos ( $kunenaversion->version, '-SVN' ) !== false ){
				//$kunenaversion->version = JString::substr ( $kunenaversion->version, 0, -4 );
				$xmlversion = $xmlversion . '-SVN';
			}

			if( $kunenaversion->version != $xmlversion) {
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
		return JText::_('COM_KUNENA_INSTALLED_VERSION').': '.$version.' | '.JText::_('COM_KUNENA_COPYRIGHT').': &copy; 2008-2010 <a href = "http://www.Kunena.com" target = "_blank">Kunena</a>  | '.JText::_('COM_KUNENA_LICENSE').': <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a>';
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
			check_dberror ( 'Unable to load MySQL version.' );

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
