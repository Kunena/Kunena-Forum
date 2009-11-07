<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

$app =& JFactory::getApplication();
global $mainframe;
header ('Content-type: application/xml');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<!-- generator=\"Kunena 1.6.0Dev\"> -->";
?>

<rss version="0.91">
    <channel>
        <title><?php echo stripslashes($app->getCfg('sitename')); ?> - Forum</title>
        <description>Kunena Site Syndication</description>
        <link><?php echo JURI::root(); ?></link>
        <lastBuildDate><?php echo date("r");?></lastBuildDate>
        <generator>Kunena 1.6.0Dev</generator>
        <image>
          <url><?php echo KURL_COMPONENT_MEDIA . 'images/'; ?>rss.png</url>
	        <title>Powered by Kunena</title>
	        <link><?php echo JURI::root(); ?></link>
	        <description>Kunena Site Syndication</description>
        </image>
<?php 
        foreach ($this->rssfeed['rssfeedresults'] as $row)
        {
            echo "        <item>\n";
            echo "            <title>" . stripslashes($row->subject) . " - " . JText::_('K_BY') . ": " . stripslashes($row->name) . "</title>" . "\n";
            echo "            <link>"  . JHtml::_('klink.thread', 'url', $row->thread, stripslashes($row->subject), stripslashes($row->subject), NULL, NULL, NULL, NULL, NULL, NULL, $row->id) . "</link>" . "\n";         
            echo "            <description>" . $row->message . "</description>" . "\n";
            echo "            <pubDate>" . date('r', $row->time) . "</pubDate>" . "\n";
            echo "        </item>\n";
        }
?>
    </channel>
</rss>
<?php
echo $return; 
$mainframe->close(); ?>
