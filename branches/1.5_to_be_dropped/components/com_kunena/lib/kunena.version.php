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

include_once(JPATH_ROOT."/administrator/components/com_kunena/lib/fx.upgrade.class.php");

$database = &JFactory::getDBO();

// Determine MySQL version
$database->setQuery("SELECT VERSION() as mysql_version");
$mysqlversion = $database->loadResult();

$VersionInfo = fx_Upgrade::getLatestVersion('#__fb_version');
$KunenaDbVersion = $VersionInfo->version.' | '.$VersionInfo->versiondate.' | '.$VersionInfo->build.' [ '.$VersionInfo->versionname.' ]';
$KunenaPHPVersion = phpversion();
$KunenaMySQLVersion = $mysqlversion;
?>