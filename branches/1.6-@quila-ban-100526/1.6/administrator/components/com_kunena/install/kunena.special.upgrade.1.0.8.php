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

defined ( '_JEXEC' ) or die ();

$temporary = 1;

$kunena_db = JFactory::getDBO ();

$kunena_db->setQuery ( "CREATE TABLE #__kunena_favorites_temp SELECT thread, userid FROM #__kunena_favorites WHERE userid>0 GROUP BY thread, userid" );
if ($kunena_db->query () == FALSE) {
	$temporary = 0;
	//	check_dbwarning("Unable to fix fb_favorites table. All Favorites will be removed.");
}
$kunena_db->setQuery ( "TRUNCATE #__kunena_favorites" );
$kunena_db->query ();
$kunena_db->setQuery ( "ALTER TABLE `#__kunena_favorites` DROP INDEX `thread`" );
$kunena_db->query ();
$kunena_db->setQuery ( "ALTER TABLE `#__kunena_favorites` ADD UNIQUE `thread`(`thread`,`userid`)" );
$kunena_db->query (); // or check_dberror("Unable to alter fb_favorites table, please contact Kunena team at www.kunena.com!");
if ($temporary) {
	$kunena_db->setQuery ( "INSERT INTO #__kunena_favorites (thread,userid) SELECT thread, userid FROM #__kunena_favorites_temp" );
	$kunena_db->query (); //  or check_dbwarning("Unable to fix fb_favorites table. All Favorites will be removed.");
	$kunena_db->setQuery ( "DROP TABLE #__kunena_favorites_temp" );
	$kunena_db->query (); // or check_dbwarning("Unable to remove temporary table (#__kunena_favorites_temp).");
}

$temporary = 1;
$kunena_db->setQuery ( "CREATE TABLE #__kunena_subscriptions_temp SELECT thread, userid, future1 FROM #__kunena_subscriptions WHERE userid>0 GROUP BY thread, userid" );
if ($kunena_db->query () == FALSE) {
	$temporary = 0;
	//	check_dbwarning("Unable to fix fb_subscriptions table. All Subscriptions will be removed.");
}
$kunena_db->setQuery ( "TRUNCATE #__kunena_subscriptions" );
$kunena_db->query ();
$kunena_db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` DROP INDEX `thread`" );
$kunena_db->query ();
$kunena_db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` ADD UNIQUE `thread`(`thread`,`userid`)" );
$kunena_db->query (); // or check_dberror("Unable to alter fb_subscriptions table, please contact Kunena team at www.kunena.com!");
if ($temporary) {
	$kunena_db->setQuery ( "INSERT INTO #__kunena_subscriptions (thread,userid,future1) SELECT thread, userid, future1 FROM #__kunena_subscriptions_temp" );
	$kunena_db->query (); // or check_dbwarning("Unable to fix fb_subscriptions table. All Subscriptions will be removed.");
	$kunena_db->setQuery ( "DROP TABLE #__kunena_subscriptions_temp" );
	$kunena_db->query (); // or check_dbwarning("Unable to remove temporary table (#__kunena_subscriptions_temp).");
}

