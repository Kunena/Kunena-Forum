<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Kunena Upgrade file for 1.0.8
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

$temporary = 1;

$kunena_db =& JFactory::getDBO();

$kunena_db->setQuery("CREATE TABLE #__fb_temp SELECT thread, userid FROM #__fb_favorites WHERE userid>0 GROUP BY thread, userid");
if ($kunena_db->query() == FALSE) {
	$temporary=0;
	trigger_dbwarning("Unable to fix fb_favorites table. All Favorites will be removed.");
}
$kunena_db->setQuery("TRUNCATE #__fb_favorites");
$kunena_db->query();
$kunena_db->setQuery("ALTER TABLE `#__fb_favorites` DROP INDEX `thread`, ADD UNIQUE `thread`(`thread`,`userid`)");
$kunena_db->query() or trigger_dberror("Unable to alter fb_favorites table, please contact Kunena team at www.kunena.com!");
if ($temporary) {
	$kunena_db->setQuery("INSERT INTO #__fb_favorites (thread,userid) SELECT thread, userid FROM #__fb_temp");
	$kunena_db->query() or trigger_dbwarning("Unable to fix fb_favorites table. All Favorites will be removed.");
	$kunena_db->setQuery("DROP TABLE #__fb_temp");
	$kunena_db->query() or trigger_dbwarning("Unable to remove temporary table (#__fb_temp).");
}

$temporary = 1;
$kunena_db->setQuery("CREATE TABLE #__fb_temp SELECT thread, userid, future1 FROM #__fb_subscriptions WHERE userid>0 GROUP BY thread, userid");
if ($kunena_db->query() == FALSE) {
	$temporary=0;
	trigger_dbwarning("Unable to fix fb_subscriptions table. All Subscriptions will be removed.");
}
$kunena_db->setQuery("TRUNCATE #__fb_subscriptions");
$kunena_db->query();
$kunena_db->setQuery("ALTER TABLE `#__fb_subscriptions` DROP INDEX `thread`, ADD UNIQUE `thread`(`thread`,`userid`)");
$kunena_db->query() or trigger_dberror("Unable to alter fb_subscriptions table, please contact Kunena team at www.kunena.com!");
if ($temporary) {
	$kunena_db->setQuery("INSERT INTO #__fb_subscriptions (thread,userid,future1) SELECT thread, userid, future1 FROM #__fb_temp");
	$kunena_db->query() or trigger_dbwarning("Unable to fix fb_subscriptions table. All Subscriptions will be removed.");
	$kunena_db->setQuery("DROP TABLE #__fb_temp");
	$kunena_db->query() or trigger_dbwarning("Unable to remove temporary table (#__fb_temp).");
}

?>
