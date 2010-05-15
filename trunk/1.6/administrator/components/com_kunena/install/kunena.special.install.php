<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Kunena Install file
* component: com_kunena
**/

defined( '_JEXEC' ) or die();


//
// This is a place to add custom install code required
// Most of the work should be done by the comupgrade class
// and the install/upgrade xml file. This is truly for one
// off special code that can't be put into the xml file directly.
//

$kunena_db = &JFactory::getDBO();

include_once (KUNENA_PATH .DS. "class.kunena.php");
include_once (KUNENA_PATH_LIB .DS. "kunena.timeformat.class.php");

// Install sample data on initial install (this will not get executed for upgrades)
$posttime = CKunenaTimeformat::internalTime ();

$query="REPLACE INTO `#__kunena_categories` (`id`, `parent`, `name`, `cat_emoticon`, `locked`, `alert_admin`, `moderated`, `moderators`, `pub_access`, `pub_recurse`, `admin_access`, `admin_recurse`, `ordering`, `future2`, `published`, `checked_out`, `checked_out_time`, `review`, `hits`, `description`, `headerdesc`, `class_sfx`, `allow_polls`, `id_last_msg`, `numTopics`, `numPosts`) VALUES
(1, 0, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE'))."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_MAIN_CATEGORY_DESC'))."', '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER'))."', '', 0, 1, 1, 0),
(2, 1, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM1_TITLE'))."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM1_DESC'))."', '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM1_HEADER'))."', '', 0, 1, 1, 0),
(3, 1, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM2_TITLE'))."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM2_DESC'))."', '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_FORUM2_HEADER'))."', '', 0, 0, 0, 0);
";
$kunena_db->setQuery($query);
$kunena_db->query();
CKunenaTools::checkDatabaseError();

$query="REPLACE INTO `#__kunena_messages` VALUES (1, 0, 1, 2, 'Kunena', 62, 'info@kunena.com', '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_POST1_SUBJECT'))."', $posttime, '127.0.0.1', 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);";
$kunena_db->setQuery($query);
$kunena_db->query();
CKunenaTools::checkDatabaseError();

$query="REPLACE INTO `#__kunena_messages_text` VALUES (1, '".$kunena_db->quote(JText::_('COM_KUNENA_SAMPLE_POST1_TEXT'))."');";
$kunena_db->setQuery($query);
$kunena_db->query();
CKunenaTools::checkDatabaseError();

CKunenaTools::reCountBoards();

CKunenaTools::createMenu();
