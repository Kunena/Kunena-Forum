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

defined( '_JEXEC' ) or die('Restricted access');

// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

global $kunena_my;

$kunena_app =& JFactory::getApplication();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();

$hours = 0;

switch (strtolower($kunena_config->rsshistory))
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
	$query = 		"SELECT
						tmp.thread,
						tmp.catid,
						m.id,
						m.subject,
						tmp.lastpostid,
						tmp.lastposttime,
						u.name AS lastpostname,
						t.message AS lastpostmessage,
						tmp.numberposts
					FROM
						(SELECT
	                        a.thread,
	                        a.catid,
	                        MAX(a.id) AS lastpostid,
	                        MAX(a.time) AS lastposttime,
	                        COUNT(*) AS numberposts
	                    FROM
	                        #__fb_messages AS a
	                        JOIN (  SELECT aa.thread
	                                FROM #__fb_messages AS aa
	                                	JOIN #__fb_categories AS bb ON aa.catid = bb.id
	                                WHERE aa.time >'{$querytime}'
	                                AND aa.hold='0' AND aa.moved='0' AND bb.published='1' AND bb.pub_access='0'
	                                GROUP BY 1) AS b ON b.thread = a.thread
	                    WHERE
	                        a.moved='0'
	                        AND a.hold='0'
	                    GROUP BY a.thread, a.catid) AS tmp
	                    JOIN #__fb_messages_text AS t ON tmp.lastpostid = t.mesid
	                    JOIN #__fb_messages AS m ON tmp.thread = m.thread
	                    JOIN #__fb_messages AS u ON tmp.lastpostid = u.id
	                WHERE
	                	m.parent = 0
                    ORDER BY lastposttime DESC";
}
else
{
	$query = 		"SELECT
						l.id AS lastpostid,
						l.time AS lastposttime,
						l.thread,
						count(m.id) AS numberposts,
						l.subject,
						l.userid,
						l.name AS lastpostname,
						l.catid,
						l.catname,
						l.message AS lastpostmessage
					FROM
						#__fb_messages AS m,
						(SELECT
							m.time,
							m.thread,
							m.id,
							m.catid,
							m.userid,
							m.name,
							m.subject,
							c.name AS catname,
							t.message AS message
						FROM
							#__fb_messages AS m,
							#__fb_categories AS c,
							#__fb_messages_text as t
						WHERE
							m.id=t.mesid
							AND m.catid=c.id
							AND c.published='1'
							AND c.pub_access='0'
							AND m.hold='0'
							AND m.moved='0'
							AND m.time >'{$querytime}') AS l
					WHERE
						l.time >= m.time
						AND l.thread=m.thread
						AND m.hold='0'
						AND m.moved='0'
					GROUP BY l.id
					ORDER BY l.time DESC";
}

$kunena_db->setQuery($query);
$rows = $kunena_db->loadObjectList();
	check_dberror("Unable to load messages.");

header ('Content-type: application/xml');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
?>
<!-- generator="Kunena <?php echo KUNENA_VERSION; ?>"> -->
<rss version="0.91">
    <channel>
        <title><?php echo stripslashes(kunena_htmlspecialchars($kunena_app->getCfg('sitename'))); ?> - Forum</title>
        <description>Kunena Site Syndication</description>
        <link><?php echo JURI::root(); ?></link>
        <lastBuildDate><?php echo date("r");?></lastBuildDate>
        <generator>Kunena <?php echo KUNENA_VERSION; ?></generator>
        <image>
	        <url><?php echo KUNENA_URLEMOTIONSPATH; ?>rss.gif</url>
	        <title>Powered by Kunena</title>
	        <link><?php echo JURI::root(); ?></link>
	        <description>Kunena Site Syndication</description>
        </image>
<?php
        foreach ($rows as $row)
        {
            echo "        <item>\n";
            echo "            <title>" . _GEN_SUBJECT . ": " . stripslashes(kunena_htmlspecialchars($row->subject)) . " - " . _GEN_BY . ": " . stripslashes(kunena_htmlspecialchars($row->lastpostname)) . "</title>" . "\n";
            echo "            <link>";
            $uri =& JURI::getInstance(JURI::base());
            $itemlink = $uri->toString(array('scheme', 'host', 'port')) . CKunenaLink::GetThreadPageURL($kunena_config, 'view', $row->catid, $row->thread, ceil($row->numberposts / $kunena_config->messages_per_page), $kunena_config->messages_per_page, $row->lastpostid);
            echo $itemlink;
            echo "</link>\n";
            $words = $row->lastpostmessage;
            $words = smile::purify($words);
            echo "            <description>" . kunena_htmlspecialchars($words) . "</description>" . "\n";
            echo "            <pubDate>" . date('r', $row->lastposttime) . "</pubDate>" . "\n";
            echo "        </item>\n";
        }
?>
    </channel>
</rss>
