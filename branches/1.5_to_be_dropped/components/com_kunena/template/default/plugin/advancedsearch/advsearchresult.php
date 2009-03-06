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

defined( '_JEXEC' ) or die('Restricted access');

$searchword = JRequest::getVar('searchword');
$titleonly = intval(JRequest::getVar('titleonly'));
$searchuser = JRequest::getVar('searchuser');
$starteronly = intval(JRequest::getVar('starteronly'));
$exactname = intval(JRequest::getVar('exactname'));
$replyless = intval(JRequest::getVar('replyless'));
$replylimit = intval(JRequest::getVar('replylimit'));
$searchdate = JRequest::getVar('searchdate');
$beforeafter = JRequest::getVar('beforeafter');
$sortby = JRequest::getVar('sortby');
$order = JRequest::getVar('order');
$catid = JRequest::getVar('catid');

// searchword must contain a minimum of 3 characters
if ($searchword && strlen($searchword) < 3 || strlen($searchword) == '0') {
    $mainframe->redirect( JURI::base() .'index.php?option=com_kunena&amp;func=advsearch&amp;Itemid=' . KUNENA_COMPONENT_ITEMID);
}

$searchword = strval($searchword);
$searchword = htmlspecialchars($searchword);
$searchword = trim(stripslashes($searchword));

//connect db and get data that we want to find
$query = "SELECT * FROM #__sb_messages";
?>