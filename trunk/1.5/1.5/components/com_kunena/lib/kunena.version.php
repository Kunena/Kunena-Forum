<?php
/**
* @version $Id: fb_version.php 968 2008-08-12 05:49:08Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

include_once (JPATH_ROOT.'/administrator/components/com_kunena/lib/fx.upgrade.class.php');

$VersionInfo = fx_Upgrade::getLatestVersion('#__fb_version');
$fbversion = $VersionInfo->version.' | '.$VersionInfo->versiondate.' | '.$VersionInfo->build.' [ '.$VersionInfo->versionname.' ]';

?>