<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

include_once($mainframe->getCfg("absolute_path")."/administrator/components/com_Kunena/sources/boj_upgrade.class.php");

$VersionInfo = boj_Upgrade::getLatestVersion('#__fb_version');
$fbversion = $VersionInfo->version.' | '.$VersionInfo->versiondate.' | '.$VersionInfo->build.' [ '.$VersionInfo->versionname.' ]';

?>