<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// no direct access
defined( '_JEXEC' ) or die();


require_once (JPATH_ROOT . '/components/com_kunena/lib/kunena.defines.php');

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
		return Kunena::getVersionInfo();
	}

	/**
	* Retrieve installed Kunena version as string.
	*
	* @return string "X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname]"
	*/
	function version()
	{
		$version = CKunenaVersion::versionArray();
		return 'Kunena '.$version->version.' | '.$version->date.' | '.$version->build.' [ '.$version->name.' ]';
	}

	/**
	* Retrieve installed Kunena version, copyright and license as string.
	*
	* @return string "Installed version: Kunena X.Y.Z | YYYY-MM-DD | BUILDNUMBER [versionname] | © Copyright: Kunena | License: GNU GPL"
	*/
	function versionHTML()
	{
		$version = CKunenaVersion::version();
		return JText::_('COM_KUNENA_INSTALLED_VERSION').': '.$version.' | '.JText::_('COM_KUNENA_COPYRIGHT').': &copy; 2008-2011 <a href = "http://www.kunena.org" target = "_blank">Kunena</a>  | '.JText::_('COM_KUNENA_LICENSE').': <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a>';
	}
}
?>
