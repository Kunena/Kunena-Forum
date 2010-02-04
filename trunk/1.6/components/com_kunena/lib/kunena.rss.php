<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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

defined( '_JEXEC' ) or die();


// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

$kunena_app =& JFactory::getApplication();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();

$hours = 0;

switch (JString::strtolower($kunena_config->rsshistory))
{
	case 'month':
		$hours = 720;
		break;
	case 'year':
		$hours = 720 * 12;
		break;
	default: // default to 1 week for all other values
		$hours = 168;
}
$querytime = time() - $hours * 3600; // Limit threads to those who have been posted to in the last month

if ($kunena_config->rsstype == 'thread')
{
	$query = "SELECT
    	*
     FROM
        #__fb_messages AS a
        JOIN (  SELECT id,thread, MAX(time) AS lastposttime, name AS lastpostname
                FROM #__fb_messages
                WHERE time >'{$querytime}' AND hold='0' AND moved='0'
                GROUP BY 1) AS b ON b.thread = a.thread
        JOIN (  SELECT mesid, message AS lastpostmessage
                FROM #__fb_messages_text
                ) AS c ON c.mesid = b.id
     WHERE
        a.parent='0'
        AND a.moved='0'
        AND a.hold='0'
     GROUP BY a.thread
     ORDER BY b.lastposttime DESC";
}
else
{
	$query = "SELECT
    	*
     FROM
        #__fb_messages AS a
        JOIN (  SELECT id,thread, MAX(time) AS lastposttime, name AS lastpostname
                FROM #__fb_messages
                WHERE time >'{$querytime}' AND hold='0' AND moved='0'
                GROUP BY 1) AS b ON b.thread = a.thread
        JOIN (  SELECT mesid, message AS lastpostmessage
                FROM #__fb_messages_text
                ) AS c ON c.mesid = b.id
     WHERE
        a.parent='0'
        AND a.moved='0'
        AND a.hold='0'
     ORDER BY b.lastposttime DESC";
}

$kunena_db->setQuery($query);
$rows = $kunena_db->loadObjectList();
	check_dberror("Unable to load messages.");

header ('Content-type: application/xml');
echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
echo '<!-- generator="Kunena ' . KUNENA_VERSION . '"> -->';
echo '<rss version="0.91">';
echo '    <channel>';
echo '        <title>' . stripslashes(kunena_htmlspecialchars($kunena_app->getCfg('sitename'))) .' - Forum</title>';
echo '        <description>Kunena Site Syndication</description>';
echo '        <link>' . JURI::root() . '</link>';
echo '        <lastBuildDate>' . date("r") . '</lastBuildDate>';
echo '        <generator>Kunena ' . KUNENA_VERSION . '</generator>';
echo '        <image>';
echo '	        <url>' . KUNENA_URLICONSPATH . 'rss.gif</url>';
echo '	        <title>Powered by Kunena</title>';
echo '	        <link>' . JURI::root() . '</link>';
echo '	        <description>Kunena Site Syndication</description>';
echo '        </image>';

        foreach ($rows as $row)
        {
        	$kunena_db->setQuery("SELECT MAX(time) AS maxtime, COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$row->thread}'");
            $thisThread = $kunena_db->loadObject();
            check_dberror("Unable to load last post time.");
            $latestPostTime = $thisThread->maxtime;

            //get the latest post itself
            $kunena_db->setQuery("SELECT a.id, a.name, a.userid, a.catid, c.id AS catid, c.name as catname FROM #__fb_messages AS a LEFT JOIN #__fb_categories AS c ON a.catid=c.id WHERE a.time='{$latestPostTime}'");
            $result = $kunena_db->loadObject();
            check_dberror("Unable to load last post.");
            echo "        <item>\n";
            echo "            <title>" . JText::_(COM_KUNENA_GEN_SUBJECT) . ": " . CKunenaTools::parseText (kunena_htmlspecialchars($row->subject)) . " - " . JText::_(COM_KUNENA_GEN_BY) . ": " . stripslashes(kunena_htmlspecialchars($row->lastpostname)) . "</title>" . "\n";
            echo "            <link>";
            $uri =& JURI::getInstance(JURI::base());
            $itemlink = $uri->toString(array('scheme', 'host', 'port')) . CKunenaLink::GetThreadPageURL($kunena_config, 'view', $row->catid, $row->thread, ceil($thisThread->totalmessages / $kunena_config->messages_per_page), $kunena_config->messages_per_page, $result->id);
            echo $itemlink;
            echo "</link>\n";
            $words = $row->lastpostmessage;
            $words = smile::purify($words);
            echo "            <description>" . kunena_htmlspecialchars($words) . "</description>" . "\n";
            echo "            <pubDate>" . date('r', $row->lastposttime) . "</pubDate>" . "\n";
            echo "        </item>\n";
        }
echo '    </channel>';
echo '</rss>';
