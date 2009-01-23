<?php
/**
* @version $Id: uninstall.fireboard.php 911 2008-08-06 10:24:58Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mainframe;
//Get right Language file
if (file_exists($mainframe->getCfg('absolute_path') . '/administrator/components/com_fireboard/language/' . $mainframe->getCfg('lang') . '.php')) {
    include ($mainframe->getCfg('absolute_path') . '/administrator/components/com_fireboard/language/' . $mainframe->getCfg('lang') . '.php');
}
else {
    include ($mainframe->getCfg('absolute_path') . '/administrator/components/com_fireboard/language/english.php');
}

function com_uninstall()
{
    // Really nothing to do as the database table stay as they are
    // Will need to provide a little utility to convert the pre 1.0.5 configuration settings
    // into the new database table for configurations. This makes upgrades easy and straight
    // forward. Uninstall old version and install new version. All settings, setup and
    // posts will be maintained. Nothing to be removed from the database.
    // If somebody wants to truly remove that data phpAdmin is required to drop all
    // FireBoard tables manually.
}
?>
