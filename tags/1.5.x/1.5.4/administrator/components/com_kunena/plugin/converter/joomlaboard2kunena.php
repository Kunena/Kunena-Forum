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
dircopy(KUNENA_ROOT_PATH .DS. "components/com_joomlaboard/uploaded", KUNENA_PATH_UPLOADED .DS, false);
dircopy(KUNENA_ROOT_PATH .DS. "components/com_joomlaboard/avatars", KUNENA_PATH_UPLOADED .DS. "avatars", false);

$kunena_db = &JFactory::getDBO();

$kunena_db->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'com_joomlaboard','com_kunena');");
$kunena_db->query();

$kunena_db->setQuery("update #__fb_attachments set filelocation = replace(filelocation,'".KUNENA_PATH .DS. "uploaded','/images/fbfiles');");
if ($kunena_db->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._KUNENA_UP_ATT_10."<br />";
}
$kunena_db->setQuery("update #__fb_messages_text set message = replace(message,'/components/com_kunena/uploaded','/images/fbfiles');");
if ($kunena_db->query()) {
//    echo "<img src='images/tick.png' align='absmiddle'>"._KUNENA_UP_ATT_10_MSG."<br />";
}

// As a last step we recount all forum stats
CKunenaTools::reCountBoards();
?>