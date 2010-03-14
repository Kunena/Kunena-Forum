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

$app =& JFactory::getApplication();
$fbConfig =& CKunenaConfig::getInstance();
$fbSession =& CKunenaSession::getInstance();

function KunenaLatestxPagination($func, $sel, $page, $totalpages, $maxpages) {
    $startpage = ($page - floor($maxpages/2) < 1) ? 1 : $page - floor($maxpages/2);
    $endpage = $startpage + $maxpages;
    if ($endpage > $totalpages) {
	$startpage = ($totalpages-$maxpages) < 1 ? 1 : $totalpages-$maxpages;
	$endpage = $totalpages;
    }

    $output = '<span class="fb_pagination">'._PAGE;

    if (($startpage) > 1)
    {
	if ($endpage < $totalpages) $endpage--;
        $output .= CKunenaLink::GetLatestPageLink($func, 1, 'follow', '',$sel);
	if (($startpage) > 2)
        {
	    $output .= "...";
	}
    }

    for ($i = $startpage; $i <= $endpage && $i <= $totalpages; $i++)
    {
        if ($page == $i) {
            $output .= "<strong>$i</strong>";
        }
        else {
            $output .= CKunenaLink::GetLatestPageLink($func, $i, 'follow', '',$sel);
        }
    }

    if ($endpage < $totalpages)
    {
	if ($endpage < $totalpages-1)
        {
	    $output .= "...";
	}

        $output .= CKunenaLink::GetLatestPageLink($func, $totalpages, 'follow', '',$sel);
    }

    $output .= '</span>';
    return $output;
}

if (!$kunena_my->id && $func == "mylatest")
{
        	header("HTTP/1.1 307 Temporary Redirect");
        	header("Location: " . htmlspecialchars_decode(CKunenaLink::GetShowLatestURL()));
        	$app->close();
}

require_once (KUNENA_PATH_LIB .DS. 'kunena.authentication.php');

//resetting some things:
$lockedForum = 0;
$lockedTopic = 0;
$topicSticky = 0;

if ('' == $sel || (!$kunena_my->id && $sel == 0)) {
/*
    if($kunena_my->id != 0) { $sel="0"; }	// Users: show messages after last visit
    else { $sel="720"; }		// Others: show 1 month as default
*/
    $sel="720";
}
$show_list_time = $sel;

//start the latest x
if ($sel == 0) {
    $querytime = ($prevCheck - $fbConfig->fbsessiontimeout); //move 30 minutes back to compensate for expired sessions
}
else
{
    //Time translation
    $back_time = $sel * 3600; //hours*(mins*secs)
    $querytime = time() - $back_time;
}

//get the db data with allowed forums and turn it into an array
$threads_per_page = $fbConfig->threads_per_page;
/*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
$page             = (int)$page;
$page             = $page < 1 ? 1 : $page;
$offset           = ($page - 1) * $threads_per_page;
$row_count        = $page * $threads_per_page;

if ($func != "mylatest") {
	$lookcats = explode(',', $fbConfig->latestcategory);
	$catlist = array();
	$latestcats = '';
	foreach ($lookcats as $catnum) {
		if ((int)$catnum && (int)$catnum>0) $catlist[] = (int)$catnum;
	}
	if (count($catlist)) $latestcats = " AND catid IN (". implode(',', $catlist) .") ";
}

 //check if $sel has a reasonable value and not a Unix timestamp:
$since = false;
if ($sel == "0")
{
	$lastvisit = date(_DATETIME, $querytime);
	$since = true;
}

if ($func == "mylatest")
{
	$document->setTitle(_KUNENA_MY_DISCUSSIONS . ' - ' . stripslashes($fbConfig->board_title));
	$query = "SELECT count(distinct tmp.thread) FROM
				(SELECT thread
					FROM #__fb_messages
					WHERE userid=$kunena_my->id AND hold=0 AND moved=0 AND catid IN ($fbSession->allowed)
				UNION ALL
				 SELECT m.thread As thread
					FROM #__fb_messages AS m
					JOIN #__fb_favorites AS f ON m.thread = f.thread
					WHERE f.userid=$kunena_my->id AND m.parent = 0 AND hold=0 and moved=0 AND catid IN ($fbSession->allowed)) AS tmp";
}
else
{
	$document->setTitle(_KUNENA_ALL_DISCUSSIONS . ' - ' . stripslashes($fbConfig->board_title));
	$query = "Select count(distinct thread) FROM #__fb_messages WHERE time >'$querytime'".
			" AND hold=0 AND moved=0 AND catid IN ($fbSession->allowed)" . $latestcats; // if categories are limited apply filter
}
$kunena_db->setQuery($query);
$total = (int)$kunena_db->loadResult();
	check_dberror('Unable to count total threads');
$totalpages = ceil($total / $threads_per_page);

//meta description and keywords
$metaKeys=kunena_htmlspecialchars(stripslashes(_KUNENA_ALL_DISCUSSIONS . ", {$fbConfig->board_title}, " . $app->getCfg('sitename')));
$metaDesc=kunena_htmlspecialchars(stripslashes(_KUNENA_ALL_DISCUSSIONS . " ({$page}/{$totalpages}) - {$fbConfig->board_title}"));

$document =& JFactory::getDocument();
$cur = $document->get( 'description' );
$metaDesc = $cur .'. ' . $metaDesc;
$document =& JFactory::getDocument();
$document->setMetadata( 'robots', 'noindex, follow' );
$document->setMetadata( 'keywords', $metaKeys );
$document->setDescription($metaDesc);

if ($func == "mylatest")
{
	$order = "myfavorite DESC, lastid DESC";
	$query = "SELECT m.thread, MAX(m.id) as lastid, MAX(t.fav) AS myfavorite FROM (
			SELECT thread, 0 AS fav
			FROM #__fb_messages
			WHERE userid='{$kunena_my->id}' AND moved='0' AND hold='0' AND catid IN ({$fbSession->allowed})
			GROUP BY thread
		UNION ALL
			SELECT thread, 1 AS fav FROM #__fb_favorites WHERE userid='{$kunena_my->id}'
		) AS t
		INNER JOIN #__fb_messages AS m ON m.thread=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$fbSession->allowed})
		GROUP BY thread
		ORDER BY {$order}
	";
}
else
{
	$order = "lastid DESC";
	$query = "SELECT thread, MAX(id) AS lastid FROM #__fb_messages WHERE time>'{$querytime}' AND hold='0' AND moved='0' AND catid IN ({$fbSession->allowed})
		GROUP BY thread
		ORDER BY {$order}
	";
}

$kunena_db->setQuery($query, $offset, $threads_per_page);
$threadids = $kunena_db->loadResultArray();
	check_dberror("Unable to load thread list.");
$idstr = @join(",", $threadids);

$favthread = array();
$thread_counts = array();
$messages = array();
$messages[0] = array();
if (count($threadids) > 0)
{
$query = "SELECT a.*, j.id AS userid, t.message AS messagetext, l.myfavorite, l.favcount, l.attachmesid, l.msgcount, l.lastid, u.avatar, c.id AS catid, c.name AS catname
	FROM (
		SELECT m.thread, (f.userid IS NOT null AND f.userid='{$kunena_my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachmesid,
			COUNT(DISTINCT m.id) AS msgcount, MAX(m.id) AS lastid, MAX(m.time) AS lasttime
		FROM #__fb_messages AS m
		LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread
		LEFT JOIN #__fb_attachments AS a ON a.mesid = m.thread
		WHERE m.hold='0' AND m.moved='0' AND m.thread IN ({$idstr})
		GROUP BY thread
	) AS l
	INNER JOIN #__fb_messages AS a ON a.thread = l.thread
	INNER JOIN #__fb_messages_text AS t ON a.thread = t.mesid
	LEFT JOIN #__users AS j ON j.id = a.userid
	LEFT JOIN #__fb_users AS u ON u.userid = j.id
	LEFT JOIN #__fb_categories AS c ON c.id = a.catid
	WHERE (a.parent='0' OR a.id=l.lastid)
	ORDER BY $order";

$kunena_db->setQuery($query);
$messagelist = $kunena_db->loadObjectList();
	check_dberror("Unable to load messages.");

foreach ($messagelist as $message)
{
	$messages[$message->parent][] = $message;
	$messagetext[$message->id] = substr(smile::purify($message->messagetext), 0, 500);
	if ($message->parent==0)
	{
		$hits[$message->id] = $message->hits;
		$thread_counts[$message->id] = $message->msgcount-1;
		$last_read[$message->id]->unread = 0;
		if ($message->favcount) $favthread[$message->id] = $message->favcount;
		if ($message->id == $message->lastid) $last_read[$message->id]->lastread = $last_reply[$message->id] = $message;
	}
	else
	{
		$last_read[$message->thread]->lastread = $last_reply[$message->thread] = $message;
	}
}

    $kunena_db->setQuery("SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages "
                       ."WHERE hold='0' AND moved='0' AND thread IN ({$idstr}) AND time>'{$prevCheck}' GROUP BY thread");
    $msgidlist = $kunena_db->loadObjectList();
    check_dberror("Unable to get unread messages count and first id.");

    foreach ($msgidlist as $msgid)
    {
        if (!in_array($msgid->thread, $read_topics)) $last_read[$msgid->thread] = $msgid;
    }
}
// (JJ) BEGIN: ANNOUNCEMENT BOX
if ($fbConfig->showannouncement > 0)
{
?>
<!-- B: announcementBox -->
<?php
    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php')) {
        require_once (KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php');
    }
    else {
        require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/announcement/announcementbox.php');
    }
?>
<!-- F: announcementBox -->
<?php
}
// (JJ) FINISH: ANNOUNCEMENT BOX

// load module
if (JDocumentHTML::countModules('kunena_announcement'))
{
?>

    <div class = "fb-fb_2">
        <?php
        	$document	= &JFactory::getDocument();
        	$renderer	= $document->loadRenderer('modules');
        	$options	= array('style' => 'xhtml');
        	$position	= 'kunena_announcement';
        	echo $renderer->render($position, $options, null);
	?>
    </div>

<?php
}
?>
<!-- B: List Actions -->
	<table class="fb_list_actions" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="fb_list_actions_info_all">
    <strong><?php echo $total; ?></strong> <?php echo _KUNENA_DISCUSSIONS; ?>
								</td>
									<?php if ($func!='mylatest') {?>
                                    <td class="fb_list_times_all">

									<?php  $show_list_time = JRequest::getInt('sel', 720);  ?>
									<select class="inputboxusl" onchange="document.location.href=this.options[this.selectedIndex].value;" size="1" name="select">
<?php if ($kunena_my->id): ?>									  <option <?php if ($show_list_time =='0') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=0'); ?>"><?php echo _SHOW_LASTVISIT; ?></option><?php endif; ?>
									  <option <?php if ($show_list_time =='4') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=4'); ?>"><?php echo _SHOW_4_HOURS; ?></option>
									  <option <?php if ($show_list_time =='8') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=8'); ?>"><?php echo _SHOW_8_HOURS; ?></option>
									  <option <?php if ($show_list_time =='12') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=12'); ?>"><?php echo _SHOW_12_HOURS; ?></option>
									  <option <?php if ($show_list_time =='24') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=24'); ?>"><?php echo _SHOW_24_HOURS; ?></option>
									  <option <?php if ($show_list_time =='48') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=48'); ?>"><?php echo _SHOW_48_HOURS; ?></option>
									  <option <?php if ($show_list_time =='168') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=168'); ?>"><?php echo _SHOW_WEEK; ?></option>
									  <option <?php if ($show_list_time =='720') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=720'); ?>"><?php echo _SHOW_MONTH ; ?></option>
									  <option <?php if ($show_list_time =='8760') {?> selected="selected"  <?php }?> value="<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=8760'); ?>"><?php echo _SHOW_YEAR; ?></option>
									</select>

                                  </td>
                                  	<?php } ?>
                                    <td class="fb_list_jump_all">

                                    <?php if ($fbConfig->enableforumjump)
 									 require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
 									 ?>

                                   </td>

				<?php
                                //pagination 1
					if (count($messages[0]) > 0)
					{
					    echo '<td class="fb_list_pages_all">';
					    $maxpages = 5 - 2; // odd number here (show - 2)
					    $totalpages = ceil($total / $threads_per_page);
					    echo $pagination = KunenaLatestxPagination($func, $sel, $page, $totalpages, $maxpages);
					    echo '</td>';
					}
				?>

		</tr>
	</table>
  <!-- F: List Actions -->
<?php
if (count($threadids) > 0)
{

				//get all readTopics in an array
				$readTopics = "";
				$kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'");
				$readTopics = $kunena_db->loadResult();
					check_dberror('Unable to load read topics.');
				if (count($readTopics) == 0)
				{
					$readTopics = "0";
				} //make sure at least something is in there..
				//make it into an array
				$read_topics = explode(',', $readTopics);
				if (file_exists(KUNENA_ABSTMPLTPATH . '/flat.php'))
				{
					include (KUNENA_ABSTMPLTPATH . '/flat.php');
				}
				else
				{
					include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'flat.php');
				}
				?>
<!-- B: List Actions -->
	<table class="fb_list_actions" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td   class="fb_list_actions_info_all" width="100%">
				<strong><?php echo $total; ?></strong> <?php echo _KUNENA_DISCUSSIONS; ?>
			</td>

			<?php
				//pagination 1
				if (count($messages[0]) > 0)
				{
					echo '<td class="fb_list_pages_all" nowrap="nowrap">';
					echo $pagination;
					echo '</td>';
				}
			?>
		</tr>
	</table>
  <!-- F: List Actions -->
<?php
}
?>
<div class="clr"></div>
<?php

	if ($fbConfig->showstats > 0)
    {
		//(JJ) BEGIN: STATS
		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php')) {
			include_once (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
		}
		else {
			include_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/stats.class.php');
		}

		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php')) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php');
		}
		else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/frontstats.php');
		}
	}
    //(JJ) FINISH: STATS

	if ($fbConfig->showwhoisonline > 0)
    {

		//(JJ) BEGIN: WHOISONLINE
		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
		}
		else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
		}
		//(JJ) FINISH: WHOISONLINE

	}

?>
