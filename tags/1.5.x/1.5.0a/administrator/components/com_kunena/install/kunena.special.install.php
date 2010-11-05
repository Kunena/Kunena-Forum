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
* Kunena Install file
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

//
// This is a place to add custom install code required
// Most of the work should be done by the comupgrade class
// and the install/upgrade xml file. This is truly for one
// off special code that can't be put into the xml file directly.
//

global $mainframe;


$database = &JFactory::getDBO();

// Kill notices (we have many..)
error_reporting (E_ALL ^ E_NOTICE);

include_once (KUNENA_PATH .DS. "class.kunena.php");

//DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');

// Install sample data on initial install (this will not get executed for upgrades)

$posttime = CKunenaTools::fbGetInternalTime();

$query="INSERT INTO `#__fb_categories` VALUES (1, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_DESC)."', '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER)."', '', 0, 0, 0, NULL);";
$database->setQuery($query);
$database->query() or trigger_dbwarning('Unable to insert sample top category');

$query="INSERT INTO `#__fb_categories` VALUES (2, 1, '".addslashes(_KUNENA_SAMPLE_FORUM1_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM1_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM1_HEADER)."', '', 0, 0, 0, NULL);";
$database->setQuery($query);
$database->query() or trigger_dbwarning('Unable to insert sample Forum 1');

$query="INSERT INTO `#__fb_categories` VALUES (3, 1, '".addslashes(_KUNENA_SAMPLE_FORUM2_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM2_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM2_HEADER)."', '', 0, 0, 0, NULL);";
$database->setQuery($query);
$database->query() or trigger_dbwarning('Unable to insert sample Forum 2');

$query="INSERT INTO `#__fb_messages` VALUES (1, 0, 1, 2, 'Kunena', 62, 'info@kunena.com', '".addslashes(_KUNENA_SAMPLE_POST1_SUBJECT)."', $posttime, '127.0.0.1', 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);";
$database->setQuery($query);
$database->query() or trigger_dbwarning('Unable to insert sample post');

$query="INSERT INTO `#__fb_messages_text` VALUES (1, '".addslashes(_KUNENA_SAMPLE_POST1_TEXT)."');";
$database->setQuery($query);
$database->query() or trigger_dbwarning('Unable to insert sample post text');

CKunenaTools::reCountBoards();

?>