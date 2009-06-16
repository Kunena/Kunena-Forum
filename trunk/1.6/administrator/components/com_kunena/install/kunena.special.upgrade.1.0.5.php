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
* Kunena Upgrade file for 1.0.5
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml
$kunena_db =& JFactory::getDBO();

$root = strtr(JPATH_ROOT, "\\", "/");
// now lets do some checks and upgrades to 1.0.2 version of attachment table
$kunena_db->setQuery("SELECT COUNT(*) FROM #__kunena_attachments WHERE filelocation LIKE '%com_fireboard/uploaded%'", 0, 1);

// if >0 then it means we are on kunena version below 1.0.2
$is_101_version = $kunena_db->loadResult();

if ($is_101_version) {
    // now do the upgrade
    $kunena_db->setQuery("update #__kunena_attachments set filelocation = replace(filelocation,'{$root}/components/com_fireboard/uploaded','/images/kunenafiles');");
    if ($kunena_db->query()) print '<li class="kunenascslist">Attachment table successfully upgraded to 1.0.2+ version schema!</li>';
    else
    {
    	print '<li class="kunenascslisterror">Attachment table was not successfully upgraded to 1.0.2+ version schema!</li>';
    	trigger_dbwarning("Unable to upgrade attachement table.");
    }

    $kunena_db->setQuery("update #__kunena_messages_text set message = replace(message,'/components/com_fireboard/uploaded','/images/kunenafiles');");
    if ($kunena_db->query()) print '<li class="kunenascslist">Attachments in messages table successfully upgraded to 1.0.2+ version schema!</li>';
    else
    {
    	print '<li class="kunenascslist">Attachments in messages table were not successfully upgraded to 1.0.2+ version schema!</li>';
    	trigger_dbwarning("Unable to upgrade attachements in messages table.");
    }

    //backward compatibility . all the cats are by default moderated
    $kunena_db->setQuery("UPDATE `#__kunena_categories` SET `moderated` = '1';");
    $kunena_db->query() or trigger_dbwarning("Unable to update categories.");;
}

?>
