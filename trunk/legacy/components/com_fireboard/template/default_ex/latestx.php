<?php
/**
* @version $Id: latestx.php 1070 2008-10-06 08:11:18Z fxstein $
* Fireboard Component
* @package Fireboard
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
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;

$mainframe->addCustomHeadTag('<script type="text/javascript" src="' . JB_TMPLTURL . '/plugin/jtip/jquery.dimensions.js"></script>');
$mainframe->addCustomHeadTag('<script type="text/javascript" src="' . JB_TMPLTURL . '/plugin/jtip/jquery.cluetip.js"></script>');
$mainframe->addCustomHeadTag('<script type="text/javascript" src="' . JB_TMPLTURL . '/plugin/jtip/demo.js"></script>');
$mainframe->addCustomHeadTag('<script type="text/javascript" src="' . JB_TMPLTURL . '/js/cube_common.js"></script>');
require_once (JB_ABSSOURCESPATH . 'fb_auth.php');

if (file_exists(JB_ABSTMPLTPATH . '/smile.class.php'))
{
	include (JB_ABSTMPLTPATH . '/smile.class.php');
}
else
{
	include (JB_ABSPATH . '/template/default/smile.class.php');
}

//meta description and keywords
$metaKeys=(_FB_ALL_DISCUSSIONS . ', ' . $fbConfig->board_title . ', ' . $GLOBALS['mosConfig_sitename']);
$metaDesc=(_FB_ALL_DISCUSSIONS . ' - ' . $fbConfig->board_title);

if( FBTools::isJoomla15() )
{
	$document =& JFactory::getDocument();
	$cur = $document->get( 'description' );
	$metaDesc = $cur .'. ' . $metaDesc;
	$document =& JFactory::getDocument();
	$document->setMetadata( 'keywords', $metaKeys );
	$document->setDescription($metaDesc);
}
else
{
    $mainframe->appendMetaTag( 'keywords',$metaKeys );
	$mainframe->appendMetaTag( 'description' ,$metaDesc );
}

//resetting some things:
$lockedForum = 0;
$lockedTopic = 0;
$topicSticky = 0;

//start the latest x
if ($sel == "0") {
    $querytime = ($prevCheck - $fbConfig->fbsessiontimeout); //move 30 minutes back to compensate for expired sessions
}
else
{
    if ("" == $sel) {
        $sel = 720;
    } //take 720 hours ~ 1 month as default
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

 //check if $sel has a reasonable value and not a Unix timestamp:
$since = false;
if ($sel == "0")
{
	$lastvisit = date(_DATETIME, $querytime);
	$since = true;
}

if ($func == "mylatest")
{
	$query = "SELECT count(distinct tmp.thread) FROM
				(SELECT thread
					FROM #__fb_messages
					WHERE userid=$my->id AND hold=0 AND moved=0 AND catid IN ($fbSession->allowed)
				UNION ALL
				 SELECT m.thread As thread
					FROM #__fb_messages AS m
					JOIN #__fb_favorites AS f ON m.thread = f.thread
					WHERE f.userid=$my->id AND m.parent = 0 AND hold=0 and moved=0 AND catid IN ($fbSession->allowed)) AS tmp";
}
else
{
	$query = "Select count(distinct thread) FROM #__fb_messages WHERE time >'$querytime' AND hold=0 AND moved=0 AND catid IN ($fbSession->allowed)";
}
$database->setQuery($query);
$total = (int)$database->loadResult();
	check_dberror('Unable to count total threads');

$query = 			"SELECT
                        a.*,
                        t.message AS messagetext,
                        m.mesid AS attachmesid,
                        f.thread AS favthread,
                        u.avatar,
                        c.name AS catname,
                        b.lastpost
                    FROM
                        #__fb_messages AS a ";

if ($func == "mylatest")
{
	$query .=			"JOIN (  SELECT mm.thread, MAX(mm.time) AS lastpost
                                FROM #__fb_messages AS mm
                                JOIN ( SELECT thread
                                		FROM #__fb_messages
                                		WHERE userid=$my->id
                                		GROUP BY 1
                                		UNION ALL
                                		SELECT thread
                                		FROM #__fb_favorites
                                		WHERE userid=$my->id) AS tt ON mm.thread = tt.thread
                                WHERE hold=0 AND moved=0 AND catid IN ($fbSession->allowed)
                                GROUP BY 1) AS b ON b.thread = a.thread ";
}
else
{
	$query .=			"JOIN (  SELECT thread, MAX(time) AS lastpost
                                FROM #__fb_messages
                                WHERE time >'$querytime'
                                AND hold=0 AND moved=0 AND catid IN ($fbSession->allowed)
                                GROUP BY 1) AS b ON b.thread = a.thread ";
}

$query .=				"JOIN #__fb_messages_text AS t ON a.thread = t.mesid
                        LEFT JOIN #__fb_categories  AS c ON c.id = a.catid
                        LEFT JOIN #__fb_attachments AS m ON m.mesid = a.id
                        LEFT JOIN #__fb_favorites AS f ON  f.thread = a.id && f.userid = $my->id"
                        .(($fbConfig->avatar_src == "cb")?
                    " LEFT JOIN #__comprofiler AS u ON u.user_id = a.userid ":
                    " LEFT JOIN #__fb_users AS u ON u.userid = a.userid ")."
                    WHERE
                        a.parent=0
                        AND a.moved=0
                        AND a.hold=0
                    GROUP BY a.id
                    ORDER BY ". ($func=="mylatest"?"f.thread DESC, ":"") ."lastpost DESC
                    LIMIT $offset,$threads_per_page";

$database->setQuery($query);
$msglist = $database->loadObjectList();
	check_dberror("Unable to load messages.");

if ($msglist) foreach ($msglist as $message)
{
	$threadids[]                  = $message->id;
	$messages[$message->parent][] = $message;
	$last_reply[$message->id]     = $message;
	$hits[$message->id]           = $message->hits;
	// Message text for tooltips
	$messagetext[$message->id]	  = substr(smile::purify($message->messagetext), 0, 500);
}
if (count($threadids) > 0)
{
	$idstr = @join("','", $threadids);
	$query = "	SELECT
					a.id,
					a.parent,
					a.thread,
					a.catid,
					a.subject,
					a.name,
					a.time,
					a.topic_emoticon,
					a.locked,
					a.ordering,
					a.userid ,
					a.moved,
					u.avatar
				FROM #__fb_messages AS a "
					.(($fbConfig->avatar_src == "cb")?
    				"LEFT  JOIN #__comprofiler AS u ON u.user_id = a.userid"
    				:"LEFT  JOIN #__fb_users AS u ON u.userid = a.userid")."
    			WHERE a.thread IN ('$idstr')
     				AND a.id NOT IN ('$idstr')
     				AND a.hold=0";

    $database->setQuery($query);
    $msglist = $database->loadObjectList();
    	check_dberror("Unable to load messages.");
	if ($msglist) foreach ($msglist as $message)
	{
		$messages[$message->parent][] = $message;
		$thread_counts[$message->thread]++;
		$last_reply[$message->thread] = $last_reply[$message->thread]->time < $message->time ? $message : $last_reply[$message->thread];
	}
}
?>

<!-- B: List Actions -->
	<div class="fb_list_actions"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tr>
                                    <td   class="fb_list_actions_info_all" width="100%">
    <strong><?php echo $total; ?></strong> <?php echo _FB_DISCUSSIONS; ?>
								</td>
									<?php if ($func!='mylatest') {?>
                                    <td class="fb_list_times_all">

									<?php  $show_list_time = mosGetParam($_REQUEST, 'sel', '');  ?>
									<select class="inputboxusl" onchange="document.location.href=this.options[this.selectedIndex].value;" size="1" name="select" style = "margin:0; padding:0; width:100px;">
									 <option <?php if ($show_list_time =='720') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest'); ?>"><?php echo _SHOW_MONTH ; ?></option>
									  <option <?php if ($show_list_time =='0') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=0'); ?>"><?php echo _SHOW_LASTVISIT; ?></option>
									  <option <?php if ($show_list_time =='4') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=4'); ?>"><?php echo _SHOW_4_HOURS; ?></option>
									  <option <?php if ($show_list_time =='8') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=8'); ?>"><?php echo _SHOW_8_HOURS; ?></option>
									  <option <?php if ($show_list_time =='12') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=12'); ?>"><?php echo _SHOW_12_HOURS; ?></option>
									  <option <?php if ($show_list_time =='24') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=24'); ?>"><?php echo _SHOW_24_HOURS; ?></option>
									  <option <?php if ($show_list_time =='48') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=48'); ?>"><?php echo _SHOW_48_HOURS; ?></option>
									  <option <?php if ($show_list_time =='168') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=168'); ?>"><?php echo _SHOW_WEEK; ?></option>
									  <option <?php if ($show_list_time =='8760') {?> selected="selected"  <?php }?> value="<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel=8760'); ?>"><?php echo _SHOW_YEAR; ?></option>
									</select>

                                  </td>
                                  	<?php } ?>
                                    <td class="fb_list_jump_all">

                                    <?php if ($fbConfig->enableforumjump)
 									 require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
 									 ?>

                                   </td>

                                   <td class="fb_list_pages_all" nowrap="nowrap">


								<?php
                                //pagination 1
								if (count($messages[0]) > 0)
								{

									echo _PAGE;
									if (($page - 2) > 1)
									{
									    echo fb_link::GetLatestPageLink($func, 1, 'follow', 'fb_list_pages_link',$sel);
										echo "...&nbsp;";
									}
									for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
									{
										if ($page == $i)
										{
											echo "$i";
										}
										else
										{
                                            echo fb_link::GetLatestPageLink($func, $i, 'follow', 'fb_list_pages_link',$sel);
										}
									}
									if ($page + 2 < ceil($total / $threads_per_page))
									{
										echo "...&nbsp;";
									    echo fb_link::GetLatestPageLink($func, ceil($total / $threads_per_page), 'follow', 'fb_list_pages_link',$sel);
									}

								}
								?>

                                </td>

                                  </tr>
                                </table>


				</div>
  <!-- F: List Actions -->
<?php
if (count($threadids) > 0)
{

				//get all readTopics in an array
				$readTopics = "";
				$database->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid=$my->id");
				$readTopics = $database->loadResult();
					check_dberror('Unable to load read topics.');
				if (count($readTopics) == 0)
				{
					$readTopics = "0";
				} //make sure at least something is in there..
				//make it into an array
				$read_topics = explode(',', $readTopics);
				if (file_exists(JB_ABSTMPLTPATH . '/flat.php'))
				{
					include (JB_ABSTMPLTPATH . '/flat.php');
				}
				else
				{
					include (JB_ABSPATH . '/template/default/flat.php');
				}
				?>
<!-- B: List Actions -->
	<div class="fb_list_actions">
     <span class="fb_list_actions_info" ><?php echo _FB_TOTAL_THREADS; ?>  <strong><?php echo $total; ?></strong></span>
								<?php

								//pagination 1
								if (count($messages[0]) > 0)
								{
									echo '<span   class="fb_list_pages" >';
									echo _PAGE;
									if (($page - 2) > 1)
									{
									    echo fb_link::GetLatestPageLink($func, 1, 'follow', 'fb_list_pages_link',$sel);
										echo "...&nbsp;";
									}
									for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
									{
										if ($page == $i)
										{
											echo "$i";
										}
										else
										{
                                            echo fb_link::GetLatestPageLink($func, $i, 'follow', 'fb_list_pages_link',$sel);
										}
									}
									if ($page + 2 < ceil($total / $threads_per_page))
									{
										echo "...&nbsp;";
									    echo fb_link::GetLatestPageLink($func, ceil($total / $threads_per_page), 'follow', 'fb_list_pages_link',$sel);
									}
									echo '</span>';
								}
								?>
	</div>
  <!-- F: List Actions -->
<?php
}
?>
<div class="clr"></div>
<?php

	if ($fbConfig->showstats > 0)
    {
		//(JJ) BEGIN: STATS
		if (file_exists(JB_ABSTMPLTPATH . '/plugin/stats/stats.class.php')) {
			include_once (JB_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
		}
		else {
			include_once (JB_ABSPATH . '/template/default/plugin/stats/stats.class.php');
		}

		if (file_exists(JB_ABSTMPLTPATH . '/plugin/stats/frontstats.php')) {
			include (JB_ABSTMPLTPATH . '/plugin/stats/frontstats.php');
		}
		else {
			include (JB_ABSPATH . '/template/default/plugin/stats/frontstats.php');
		}
	}
    //(JJ) FINISH: STATS

	if ($fbConfig->showwhoisonline > 0)
    {

		//(JJ) BEGIN: WHOISONLINE
		if (file_exists(JB_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
			include (JB_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
		}
		else {
			include (JB_ABSPATH . '/template/default/plugin/who/whoisonline.php');
		}
		//(JJ) FINISH: WHOISONLINE

	}

?>