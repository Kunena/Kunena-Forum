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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
$fbConfig =& CKunenaConfig::getInstance();

if ($fbConfig->showstats)
{

if ($fbConfig->showgenstats)
{
$database->setQuery("SELECT COUNT(*) FROM #__users");
$totalmembers = $database->loadResult();

$database->setQuery("SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__fb_categories WHERE parent=0");
$database->loadObject($totaltmp);
$totaltitles = !empty($totaltmp->titles)?$totaltmp->titles:0;
$totalmsgs = !empty($totaltmp->msgs)?$totaltmp->msgs + $totaltitles:$totaltitles;
unset($totaltmp);

$database->setQuery("SELECT SUM(parent=0) AS totalcats, SUM(parent>0) AS totalsections
FROM #__fb_categories");
$database->loadObject($totaltmp);
$totalsections = !empty($totaltmp->totalsections)?$totaltmp->totalsections:0;
$totalcats = !empty($totaltmp->totalcats)?$totaltmp->totalcats:0;
unset($totaltmp);

$fb_queryName = $fbConfig->username ? "username" : "name";
$database->setQuery("SELECT id, $fb_queryName as username FROM #__users WHERE block=0 AND activation='' ORDER BY id DESC LIMIT 0,1");
$database->loadObject($_lastestmember);
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

$database->loadObject($totaltmp);
$todayopen = !empty($totaltmp->todayopen)?$totaltmp->todayopen:0;
$yesterdayopen = !empty($totaltmp->yesterdayopen)?$totaltmp->yesterdayopen:0;
$todayanswer = !empty($totaltmp->todayanswer)?$totaltmp->todayanswer:0;
$yesterdayanswer = !empty($totaltmp->yesterdayanswer)?$totaltmp->yesterdayanswer:0;
unset($totaltmp);

} // ENDIF: showgenstats

$PopUserCount = $fbConfig->popusercount;
if ($fbConfig->showpopuserstats)
{
	$database->setQuery("SELECT p.userid, p.posts, u.$fb_queryName as username FROM #__fb_users AS p" . "\n INNER JOIN #__users AS u ON u.id = p.userid" . "\n WHERE p.posts > 0 ORDER BY p.posts DESC LIMIT $PopUserCount");
	$topposters = $database->loadObjectList();

	$topmessage = !empty($topposters[0]->posts)?$topposters[0]->posts:0;

	$database->setQuery("SELECT u.uhits AS hits, u.userid AS user_id, j.$fb_queryName AS user  FROM #__fb_users AS u"
	. "\n INNER JOIN #__users AS j ON j.id = u.userid"
	. "\n WHERE u.uhits > 0 ORDER BY u.uhits DESC LIMIT $PopUserCount");
	$topprofiles = $database->loadObjectList();

	$topprofil = !empty($topprofiles[0]->hits)?$topprofiles[0]->hits:0;
} // ENDIF: showpopuserstats

$PopSubjectCount = $fbConfig->popsubjectcount;
if ($fbConfig->showpopsubjectstats)
{
	$fbSession =& CKunenaSession::getInstance();
	$database->setQuery("SELECT * FROM #__fb_messages WHERE moved='0' AND hold='0' AND parent='0' AND catid IN ($fbSession->allowed) ORDER BY hits DESC LIMIT $PopSubjectCount");
	$toptitles = $database->loadObjectList();
	
	$toptitlehits = !empty($toptitles[0]->hits)?$toptitles[0]->hits:0;
} // ENDIF: showpopsubjectstats

} // ENDIF: showstats
?>
