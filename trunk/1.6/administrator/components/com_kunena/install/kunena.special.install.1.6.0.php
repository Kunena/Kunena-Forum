<?php
/**
* @version $Id: kunena.special.upgrade.1.6.0.php $
* Kunena Component
* @package Kunena
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Kunena Delete deprecated template for 1.6.0
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

$kunena_db =& JFactory::getDBO();

//Import filesystem libraries.
jimport('joomla.filesystem.folder');

include_once (KUNENA_PATH .DS. "class.kunena.php");

//DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');

// Install sample data on initial install (this will not get executed for upgrades)

$posttime = CKunenaTools::fbGetInternalTime();

$query="INSERT INTO `#__fb_categories` VALUES (1, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_DESC)."', '".addslashes(_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER)."', '', 0, 0, 0, NULL);";
$kunena_db->setQuery($query);
$kunena_db->query() or check_dbwarning('Unable to insert sample top category');

$query="INSERT INTO `#__fb_categories` VALUES (2, 1, '".addslashes(_KUNENA_SAMPLE_FORUM1_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM1_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM1_HEADER)."', '', 0, 0, 0, NULL);";
$kunena_db->setQuery($query);
$kunena_db->query() or check_dbwarning('Unable to insert sample Forum 1');

$query="INSERT INTO `#__fb_categories` VALUES (3, 1, '".addslashes(_KUNENA_SAMPLE_FORUM2_TITLE)."', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '".addslashes(_KUNENA_SAMPLE_FORUM2_DESC)."', '".addslashes(_KUNENA_SAMPLE_FORUM2_HEADER)."', '', 0, 0, 0, NULL);";
$kunena_db->setQuery($query);
$kunena_db->query() or check_dbwarning('Unable to insert sample Forum 2');

$query="INSERT INTO `#__fb_messages` VALUES (1, 0, 1, 2, 'Kunena', 62, 'info@kunena.com', '".addslashes(_KUNENA_SAMPLE_POST1_SUBJECT)."', $posttime, '127.0.0.1', 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);";
$kunena_db->setQuery($query);
$kunena_db->query() or check_dbwarning('Unable to insert sample post');

$query="INSERT INTO `#__fb_messages_text` VALUES (1, '".addslashes(_KUNENA_SAMPLE_POST1_TEXT)."');";
$kunena_db->setQuery($query);
$kunena_db->query() or check_dbwarning('Unable to insert sample post text');

CKunenaTools::reCountBoards();

$kunena_db->setQuery("UPDATE #__fb_config SET template='default',templateimagepath='default'");
$kunena_db->query();
$templatedeprecatedlist = array('default_ex','default_green','default_red','default_gray');
foreach ($templatedeprecatedlist as $template) {  
	if (JFolder::exists(KUNENA_PATH_TEMPLATE. DS . $template)) {
		JFolder::delete(KUNENA_PATH_TEMPLATE. DS . $template);
	}
}
?>
