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

$fbConfig =& CKunenaConfig::getInstance();

//Get some variables
$id = intval(JRequest::getVar('id'));
$catid = intval(JRequest::getVar('catid'));
//$func = JRequest::getVar('func');
$task = JRequest::getVar('task');
$replyto = intval(JRequest::getVar('replyto'));
$do = JRequest::getVar('do');

$now = time();
$past = $now - $fbConfig->fbsessiontimeout;
$myip = getenv('REMOTE_ADDR');

if ($kunena_my->id > 0) {
    $isuser = 1;
    }
else {
    $isuser = 0;
    }

//Delete non online users from db
$kunena_db->setQuery("DELETE FROM #__fb_whoisonline WHERE time < '$past'");
$kunena_db->query();

$kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_whoisonline WHERE userip='$myip' AND userid='$kunena_my->id'");
$online = $kunena_db->loadResult();

unset ($row);

if ($task == 'listcat' || $func == 'showcat') {
    $kunena_db->setQuery("SELECT name FROM #__fb_categories WHERE id = {$catid}");
    $what = $kunena_db->loadResult();
    }
else if ($func == 'latest') {
    $what = _KUNENA_ALL_DISCUSSIONS;
    }
else if ($id) {
    $kunena_db->setQuery("SELECT subject FROM #__fb_messages WHERE id = {$id}");
    $what = $kunena_db->loadResult();
    }
else if ($replyto) {
    $kunena_db->setQuery("SELECT subject FROM #__fb_messages WHERE id = {$replyto}");
    $what = $kunena_db->loadResult();
    }
else if ($do == 'reply') {
    $kunena_db->setQuery("SELECT name FROM #__fb_categories WHERE id = {$catid}");
    $what = $kunena_db->loadResult();
    }
else if ($func == 'post' && $do == 'edit') {
    $kunena_db->setQuery("SELECT name FROM #__fb_messages WHERE id = {$id}");
    $what = $kunena_db->loadResult();
    }
else if ($func == 'who') {
    $what = _KUNENA_WHO_LATEST_POSTS;
    }
else {
    $what = _KUNENA_WHO_MAINPAGE;
    }

$link = $_SERVER['REQUEST_URI'];
$what = addslashes($what);
$link = addslashes($link);

if ($online == 1) {
    $sql = "UPDATE #__fb_whoisonline SET time='{$now}', what='{$what}', do= '{$do}', task= '{$task}', link= '{$link}', func= '{$func}'"
            . "\n WHERE userid={$kunena_my->id} AND userip='{$myip}'";
    $kunena_db->setQuery($sql);
    }
else {
    $sql = "INSERT INTO #__fb_whoisonline (`userid` , `time`, `what`, `task`, `do`, `func`,`link`, `userip`, `user`) "
            . "\n VALUES ('{$kunena_my->id}', '{$now}', '{$what}','{$task}','{$do}','{$func}','{$link}', '{$myip}', '{$isuser}')";

    $kunena_db->setQuery($sql);
    }

$kunena_db->query();
echo $kunena_db->getErrorMsg();
?>
