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

class CKunenaStats {
	protected $_db = null;
	public $totalmembers = false;
	public $totaltitles = false;
	public $totalmsgs = false;
	public $totalsections = false;
	public $totalcats = false;
	public $lastestmember = false;
	public $lastestmemberid = false;
	public $todayopen = false;
	public $yesterdayopen = false;
	public $todayanswer = false;
	public $yesterdayanswer = false;
	public $topposters = false;
	public $topmessage = false;
	public $topprofiles = false;
	public $topprofilehits = false;
	public $toptitles = false;
	public $toptitlehits = false;

	function __construct()
	{
		$this->_db = &JFactory::getDBO();
		$kunena_config =& CKunenaConfig::getInstance();

if ($kunena_config->showstats)
{

if ($kunena_config->showgenstats)
{
$this->_db->setQuery("SELECT COUNT(*) FROM #__users");
$this->totalmembers = $this->_db->loadResult();

$this->_db->setQuery("SELECT SUM(numTopics) AS titles, SUM(numPosts) AS msgs FROM #__fb_categories WHERE parent='0'");
$totaltmp = $this->_db->loadObject();
$this->totaltitles = !empty($totaltmp->titles)?$totaltmp->titles:0;
$this->totalmsgs = !empty($totaltmp->msgs)?$totaltmp->msgs + $this->totaltitles:$this->totaltitles;

$this->_db->setQuery("SELECT SUM(parent='0') AS totalcats, SUM(parent>'0') AS totalsections FROM #__fb_categories");
$totaltmp = $this->_db->loadObject();
$this->totalsections = !empty($totaltmp->totalsections)?$totaltmp->totalsections:0;
$this->totalcats = !empty($totaltmp->totalcats)?$totaltmp->totalcats:0;

$fb_queryName = $kunena_config->username ? "username" : "name";
$this->_db->setQuery("SELECT id, {$fb_queryName} AS username FROM #__users WHERE block='0' AND activation='' ORDER BY id DESC", 0, 1);
$_lastestmember = $this->_db->loadObject();
$this->lastestmember = $_lastestmember->username;
$this->lastestmemberid =$_lastestmember->id;

$todaystart = strtotime(date('Y-m-d'));
$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
$this->_db->setQuery("SELECT SUM(time >= '{$todaystart}' AND parent='0') AS todayopen, "
                   ." SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent='0') AS yesterdayopen, "
                   ." SUM(time >= '{$todaystart}' AND parent>'0') AS todayanswer, "
                   ." SUM(time >= '{$yesterdaystart}' AND time < '{$todaystart}' AND parent>'0') AS yesterdayanswer "
                   ." FROM #__fb_messages WHERE time >= '{$yesterdaystart}' AND hold='0'");

$totaltmp = $this->_db->loadObject();
$this->todayopen = !empty($totaltmp->todayopen)?$totaltmp->todayopen:0;
$this->yesterdayopen = !empty($totaltmp->yesterdayopen)?$totaltmp->yesterdayopen:0;
$this->todayanswer = !empty($totaltmp->todayanswer)?$totaltmp->todayanswer:0;
$this->yesterdayanswer = !empty($totaltmp->yesterdayanswer)?$totaltmp->yesterdayanswer:0;
} // ENDIF: showgenstats

$PopUserCount = $kunena_config->popusercount;
if ($kunena_config->showpopuserstats)
{
	$this->_db->setQuery("SELECT p.userid, p.posts, u.id, u.{$fb_queryName} AS username FROM #__fb_users AS p INNER JOIN #__users AS u ON u.id = p.userid WHERE p.posts > '0' AND u.block=0 ORDER BY p.posts DESC", 0, $PopUserCount);

	$this->topposters = $this->_db->loadObjectList();
	$this->topmessage = !empty($this->topposters[0]->posts)?$this->topposters[0]->posts:0;

if ($kunena_config->fb_profile == "jomsocial") {
		$this->_db->setQuery("SELECT u.id AS user_id, c.view AS hits, u.{$fb_queryName} AS user FROM #__community_users as c"
		. " LEFT JOIN #__users as u on u.id=c.userid "
		. " WHERE c.view>'0' ORDER BY c.view DESC", 0, $PopUserCount);
	}
	elseif ($kunena_config->fb_profile == "cb") {
		$this->_db->setQuery("SELECT c.hits AS hits, u.id AS user_id, u.{$fb_queryName} AS user FROM #__comprofiler AS c"
		. " INNER JOIN #__users AS u ON u.id = c.user_id"
		. " WHERE c.hits>'0' ORDER BY c.hits DESC", 0, $PopUserCount);
	}
	elseif ($kunena_config->fb_profile == "aup") {
		$this->_db->setQuery("SELECT a.profileviews AS hits, u.id AS user_id, u.{$fb_queryName} AS user FROM #__alpha_userpoints AS a"
		. " INNER JOIN #__users AS u ON u.id = a.userid"
		. " WHERE u.profileviews>'0' ORDER BY u.profileviews DESC", 0, $PopUserCount);
	}
	else {
		$this->_db->setQuery("SELECT u.uhits AS hits, u.userid AS user_id, j.id, j.{$fb_queryName} AS user FROM #__fb_users AS u"
		. " INNER JOIN #__users AS j ON j.id = u.userid"
		. " WHERE u.uhits>'0' AND j.block=0 ORDER BY u.uhits DESC", 0, $PopUserCount);
	}

	$this->topprofiles = $this->_db->loadObjectList();
	$this->topprofilehits = !empty($this->topprofiles[0]->hits)?$this->topprofiles[0]->hits:0;
} // ENDIF: showpopuserstats

$PopSubjectCount = $kunena_config->popsubjectcount;
if ($kunena_config->showpopsubjectstats)
{
	$kunena_session =& CKunenaSession::getInstance();
	$this->_db->setQuery("SELECT * FROM #__fb_messages WHERE moved='0' AND hold='0' AND parent='0' AND catid IN ($kunena_session->allowed) ORDER BY hits DESC", 0, $PopSubjectCount);

	$this->toptitles = $this->_db->loadObjectList();
	$this->toptitlehits = !empty($this->toptitles[0]->hits)?$this->toptitles[0]->hits:0;
} // ENDIF: showpopsubjectstats

} // ENDIF: showstats
	}

	public function showStats()
	{
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.php' )) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/stats/stats.php');
		}
	}

	public function showFrontStats()
	{
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php' )) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/stats/frontstats.php');
		}
	}
}
?>
