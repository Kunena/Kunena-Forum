<?php
/**
* @version $Id: joomlaboard.php 567 2008-01-21 21:01:25Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
* 
* JoomlaBoard converter
*
**/

defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

//copy the attachments to fireboard directory
dircopy($mosConfig_absolute_path . "/components/com_joomlaboard/uploaded", $mosConfig_absolute_path . "/images/fbfiles", false);
dircopy($mosConfig_absolute_path . "/components/com_joomlaboard/avatars", $mosConfig_absolute_path . "/images/fbfiles/avatars", false);

$database->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'com_joomlaboard','com_fireboard');");
$database->query();

$database->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'".$mainframe->getCfg("absolute_path")."/components/com_fireboard/uploaded','/images/fbfiles');");
if ($database->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._FB_UP_ATT_10."<br />";
}
$database->setQuery("update #__fb_messages_text set message = replace(message,'/components/com_fireboard/uploaded','/images/fbfiles');");
if ($database->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._FB_UP_ATT_10_MSG."<br />";
}

// As a last step we recount all forum stats
FBTools::reCountBoards();
?>