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
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');
global $fbConfig;

if ($fbConfig->showstats)
{

if ($fbConfig->showgenstats)
{
$database->setQuery("SELECT COUNT(*) FROM #__users");
$totalmembers = $database->loadResult();

$database->setQuery("SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__fb_categories WHERE parent=0");
$totaltmp = $database->loadObject();
$totaltitles = $totaltmp->titles;
$totalmsgs = $totaltmp->msgs + $totaltitles;
unset($totaltmp);

$database->setQuery("SELECT SUM(parent=0) AS totalcats, SUM(parent>0) AS totalsections
FROM #__fb_categories");
$totaltmp = $database->loadObject();
$totalsections = $totaltmp->totalsections;
$totalcats = $totaltmp->totalcats;
unset($totaltmp);

$fb_queryName = $fbConfig->username ? "username" : "name";
$database->setQuery("SELECT id, $fb_queryName as username FROM #__users WHERE block=0 AND activation='' ORDER BY id DESC LIMIT 0,1");
$_lastestmember = $database->loadObject();
$lastestmember = $_lastestmember->username;
$lastestmemberid =$_lastestmember->id;
unset($_lastestmember);

$todaystart = strtotime(date('Y-m-d'));
$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
$database->setQuery("SELECT SUM(time >= $todaystart AND parent=0) AS todayopen, "
                   ."SUM(time >= $yesterdaystart AND time < $todaystart AND parent=0) AS yesterdayopen, "
                   ."SUM(time >= $todaystart AND parent>0) AS todayanswer, "
                   ."SUM(time >= $yesterdaystart AND time < $todaystart AND parent>0) AS yesterdayanswer "
                   ."FROM #__fb_messages WHERE time >= $yesterdaystart AND hold=0");

$totaltmp = $database->loadObject();
$todayopen = $totaltmp->todayopen?$totaltmp->todayopen:0;
$yesterdayopen = $totaltmp->yesterdayopen?$totaltmp->yesterdayopen:0;
$todayanswer = $totaltmp->todayanswer?$totaltmp->todayanswer:0;
$yesterdayanswer = $totaltmp->yesterdayanswer?$totaltmp->yesterdayanswer:0;
unset($totaltmp);

} // ENDIF: showgenstats

$PopUserCount = $fbConfig->popusercount;
if ($fbConfig->showpopuserstats)
{
	$database->setQuery("SELECT p.userid, p.posts, u.$fb_queryName as username FROM #__fb_users AS p" . "\n LEFT JOIN #__users AS u ON u.id = p.userid" . "\n WHERE p.posts > 0 ORDER BY p.posts DESC LIMIT $PopUserCount");
	$topposters = $database->loadObjectList();

	$topmessage = $topposters[0]->posts;

	$database->setQuery("SELECT u.uhits AS hits, u.userid AS user_id, j.$fb_queryName AS user  FROM #__fb_users AS u"
	. "\n LEFT JOIN #__users AS j ON j.id = u.userid"
	. "\n WHERE u.uhits > 0 ORDER BY u.uhits DESC LIMIT $PopUserCount");
	$topprofiles = $database->loadObjectList();

	$topprofil = $topprofiles[0]->hits;
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
