<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// Dont allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $fbConfig;

if ($fbConfig->showstats)
{

if ($fbConfig->showgenstats)
{
$database->setQuery("SELECT COUNT(*) FROM #__users");
$totalmembers = $database->loadResult();

$database->setQuery("SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__fb_categories WHERE parent=0");
$database->loadObject($totaltmp);
$totaltitles = $totaltmp->titles;
$totalmsgs = $totaltmp->msgs + $totaltitles;
unset($totaltmp);

$database->setQuery("SELECT COUNT(*) FROM #__fb_categories WHERE parent=0");
$totalcats = $database->loadResult();

$database->setQuery("SELECT COUNT(*) FROM #__fb_categories");
$totalsections = $database->loadResult() - $totalcats;

unset($_lastestmember);
$fb_queryName = $fbConfig->username ? "username" : "name";
$database->setQuery("SELECT id, $fb_queryName as username FROM #__users WHERE block=0 AND activation='' ORDER BY id DESC LIMIT 0,1");
$database->loadObject($_lastestmember);
$lastestmember = $_lastestmember->username;
$lastestmemberid =$_lastestmember->id;

$today = date('Y-m-d');
$yesterday = time() - (1 * 24 * 60 * 60);
$yesterday = date('Y-m-d', $yesterday);
$todaystart = strtotime(date("Y-m-d 00:00:01"));
$todayend = strtotime(date("Y-m-d 23:59:59"));
$yesterdaystart = strtotime(date("$yesterday 00:00:01")); #NOTE: 00:00:00 is daystart
$yesterdayend = strtotime(date("$yesterday 23:59:59"));

$database->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE time > $todaystart AND time < $todayend AND parent>0");
$todaytotal = $database->loadResult();

$database->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE time > $yesterdaystart AND time < $yesterdayend AND parent>0");
$yesterdaytotal = $database->loadResult();

$database->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE time > $todaystart AND time < $todayend AND parent=0");
$todaystitle = $database->loadResult();

$database->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE time > $yesterdaystart AND time < $yesterdayend AND parent=0");
$yesterdaystitle = $database->loadResult();

} // ENDIF: showgenstats

$PopUserCount = $fbConfig->popusercount;
if ($fbConfig->showpopuserstats)
{

$database->setQuery("SELECT p.userid, p.posts, u.$fb_queryName as username FROM #__fb_users AS p" . "\n LEFT JOIN #__users AS u ON u.id = p.userid" . "\n WHERE p.posts > 0 ORDER BY p.posts DESC LIMIT $PopUserCount");
$topposters = $database->loadObjectList();

$topmessage = $topposters[0]->posts;

if ($fbConfig->fb_profile == "cb") {
$database->setQuery("SELECT u.$fb_queryName AS user, p.hits, p.user_id FROM #__users AS u"
. "\n LEFT JOIN #__comprofiler AS p ON p.user_id = u.id"
. "\n WHERE p.hits > 0 ORDER BY p.hits DESC LIMIT $PopUserCount");
$topprofiles = $database->loadObjectList();

$topprofil = $topprofiles[0]->hits;

} else {
$database->setQuery("SELECT u.uhits AS hits, u.userid AS user_id, j.$fb_queryName AS user  FROM #__fb_users AS u"
. "\n LEFT JOIN #__users AS j ON j.id = u.userid"
. "\n WHERE u.uhits > 0 ORDER BY u.uhits DESC LIMIT $PopUserCount");
$topprofiles = $database->loadObjectList();

$topprofil = $topprofiles[0]->hits;
} // ENDIF: fb_profile

} // ENDIF: showpopuserstats

$PopSubjectCount = $fbConfig->popsubjectcount;
if ($fbConfig->showpopsubjectstats)
{

$database->setQuery("SELECT * FROM #__fb_messages WHERE moved=0 AND hold=0 AND parent=0 ORDER BY hits DESC LIMIT $PopSubjectCount");
$toptitles = $database->loadObjectList();

$toptitlehits = $toptitles[0]->hits;
} // ENDIF: showpopsubjectstats

} // ENDIF: showstats
?>
