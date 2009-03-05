<?php
/**
* @version $Id: kunena.special.upgrade.1.0.5.php 103 2009-01-23 23:23:23Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Kunena Upgrade file for 1.0.5
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

global $mainframe;

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

// now lets do some checks and upgrades to 1.0.2 version of attachment table
$database->setQuery("select from #__fb_attachments where filelocation like '%" . $mainframe->getCfg("absolute_path") . "%'");

// if >0 then it means we are on fb version below 1.0.2
$is_101_version = $database->loadResult();

if ($is_101_version) {
    // now do the upgrade
    $database->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'" . $mainframe->getCfg("absolute_path") . "/components/com_kunena/uploaded','/images/fbfiles');");
    if ($database->query()) print '<li class="fbscslist">Attachment table successfully upgraded to 1.0.2+ version schema!</li>';
    else
    {
    	print '<li class="fbscslisterror">Attachment table was not successfully upgraded to 1.0.2+ version schema!</li>';
    	trigger_dbwarning("Unable to upgrade attachement table.");
    }

    $database->setQuery("update #__fb_messages_text set message = replace(message,'/components/com_kunena/uploaded','/images/fbfiles');");
    if ($database->query()) print '<li class="fbscslist">Attachments in messages table successfully upgraded to 1.0.2+ version schema!</li>';
    else
    {
    	print '<li class="fbscslist">Attachments in messages table were not successfully upgraded to 1.0.2+ version schema!</li>';
    	trigger_dbwarning("Unable to upgrade attachements in messages table.");
    }

    //backward compatibility . all the cats are by default moderated
    $database->setQuery("UPDATE `#__fb_categories` SET `moderated` = '1';");
    $database->query() or trigger_dbwarning("Unable to update categories.");;
}

?>