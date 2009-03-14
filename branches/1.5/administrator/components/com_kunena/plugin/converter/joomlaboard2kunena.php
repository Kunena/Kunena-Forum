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
* JoomlaBoard converter
*
**/

defined( '_JEXEC' ) or die('Restricted access');

//copy the attachments to Kunena directory
dircopy(JPATH_ROOT . "/components/com_joomlaboard/uploaded", JPATH_ROOT . "/images/fbfiles", false);
dircopy(JPATH_ROOT . "/components/com_joomlaboard/avatars", JPATH_ROOT . "/images/fbfiles/avatars", false);

$database->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'com_joomlaboard','com_kunena');");
$database->query();

$database->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'".JPATH_ROOT."/components/com_kunena/uploaded','/images/fbfiles');");
if ($database->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._KUNENA_UP_ATT_10."<br />";
}
$database->setQuery("update #__fb_messages_text set message = replace(message,'/components/com_kunena/uploaded','/images/fbfiles');");
if ($database->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._KUNENA_UP_ATT_10_MSG."<br />";
}

// As a last step we recount all forum stats
CKunenaTools::reCountBoards();
?>