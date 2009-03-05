<?php
/**
* @version $Id: who.class.php 1070 2008-10-06 08:11:18Z fxstein $
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
defined( '_JEXEC' ) or die('Restricted access');

global $fbConfig;
$database = &JFactory::getDBO();
//Get some variables
$id = intval(JRequest::getInt('id',"","REQUEST"));

$catid = intval(JRequest::getInt('catid',"","REQUEST"));
//$func = mosGetParam($_REQUEST, 'func');
$task =intval(JRequest::getVar('task',"","REQUEST"));
$replyto = intval(JRequest::getInt('replyto',"","REQUEST"));
$do = intval(JRequest::getVar('do',"","REQUEST"));

$now = time();
$past = $now - $fbConfig->fbsessiontimeout;
$myip = getenv('REMOTE_ADDR');
$my = &JFactory::getUser();


if ($my->id > 0) {
    $isuser = 1;
    }
else {
    $isuser = 0;
    }

//Delete non online users from db
$database->setQuery("DELETE FROM #__fb_whoisonline WHERE time < '$past'");
$database->query();

$database->setQuery("SELECT COUNT(*) FROM #__fb_whoisonline WHERE userip='$myip' AND userid='$my->id'");
$online = $database->loadResult();

unset ($row);

if ($task == 'listcat' || $func == 'showcat') {
    $database->setQuery("SELECT name FROM #__fb_categories WHERE id = {$catid}");
    $what = $database->loadResult();
    }
else if ($func == 'latest') {
    $what = _KUNENA_LATEST_POSTS;
    }
else if ($id) {
    $database->setQuery("SELECT subject FROM #__fb_messages WHERE id = {$id}");
    $what = $database->loadResult();
    }
else if ($replyto) {
    $database->setQuery("SELECT subject FROM #__fb_messages WHERE id = {$replyto}");
    $what = $database->loadResult();
    }
else if ($do == 'reply') {
    $database->setQuery("SELECT name FROM #__fb_categories WHERE id = {$catid}");
    $what = $database->loadResult();
    }
else if ($func == 'post' && $do == 'edit') {
    $database->setQuery("SELECT name FROM #__fb_messages WHERE id = {$id}");
    $what = $database->loadResult();
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
            . "\n WHERE userid={$my->id} AND userip='{$myip}'";
    $database->setQuery($sql);
    }
else {
    $sql = "INSERT INTO #__fb_whoisonline (`userid` , `time`, `what`, `task`, `do`, `func`,`link`, `userip`, `user`) "
            . "\n VALUES ('{$my->id}', '{$now}', '{$what}','{$task}','{$do}','{$func}','{$link}', '{$myip}', '{$isuser}')";

    $database->setQuery($sql);
    }

$database->query();
echo $database->getErrorMsg();
?>