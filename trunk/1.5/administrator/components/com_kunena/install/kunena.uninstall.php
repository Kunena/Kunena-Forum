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
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

//Get right Language file
if (file_exists(KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . $lang . '.php')) {
    include (KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . $lang . '.php');
}
else {
    include (KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.english.php');
}

include_once(JPATH_ROOT."/administrator/components/com_kunena/lib/fx.upgrade.class.php");

function com_uninstall()
{
    // Really nothing to do as the database table stay as they are.
    // Nothing to be removed from the database.
    // If somebody wants to truly remove that data phpAdmin is required to drop all
    // Kunena tables manually.
}
?>
