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
global $is_Moderator;

function KunenaShowcatPagination($catid, $page, $totalpages, $maxpages) {
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
        $output .= CKunenaLink::GetCategoryPageLink('showcat', $catid, 1, 1, $rel='follow');
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
            $output .= CKunenaLink::GetCategoryPageLink('showcat', $catid, $i, $i, $rel='follow');
        }
    }

    if ($endpage < $totalpages)
    {
	if ($endpage < $totalpages-1)
        {
	    $output .= "...";
	}

        $output .= CKunenaLink::GetCategoryPageLink('showcat', $catid, $totalpages, $totalpages, $rel='follow');
    }

    $output .= '</span>';
    return $output;
}

require_once(KUNENA_PATH_LIB .DS. 'kunena.authentication.php');

//Security basics begin
//Securing passed form elements:
$catid = (int)$catid;

//resetting some things:
$moderatedForum = 0;
$lockedForum = 0;
$lockedTopic = 0;
$topicSticky = 0;

unset($allow_forum);

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

if (!$is_Moderator)
{
    //check Access Level Restrictions but don't bother for Moderators
    //get all the info on this forum:
    $database->setQuery("SELECT id,pub_access,pub_recurse,admin_access,admin_recurse FROM #__fb_categories where id='$catid'");
    $row = $database->loadObjectList();
    	check_dberror("Unable to load category detail.");
    //Do user identification based upon the ACL
    $letPass = 0;
    $letPass = CKunenaAuthentication::validate_user($row[0], $allow_forum, $aro_group->group_id, $acl);
}

if ($letPass || $is_Moderator)
{
    $threads_per_page = $fbConfig->threads_per_page;

    if ($catid <= 0) {
        //make sure we got a valid category id
        $catid = 1;
    }

    $view = $view == "" ? $settings[current_view] : $view;
    setcookie("fboard_settings[current_view]", $view, time() + 31536000, '/');
    /*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
    $page = (int)$page;
    $page = $page < 1 ? 1 : $page;
    $offset = ($page - 1) * $threads_per_page;
    $row_count = $page * $threads_per_page;
    $database->setQuery("Select count(*) FROM #__fb_messages WHERE parent = '0' AND catid= '$catid' AND hold = '0' ");
    $total = (int)$database->loadResult();
    	check_dberror('Unable to get message count.');
    $database->setQuery("SELECT
    							a.*,
    							t.message AS messagetext,
    							m.mesid AS attachmesid,
    							(f.thread>0) AS myfavorite,
    							u.avatar,
    							MAX(b.time) AS lastpost
    						FROM  #__fb_messages  AS a
    							JOIN #__fb_messages_text AS t ON a.thread = t.mesid
    							LEFT  JOIN #__fb_messages AS b ON b.thread = a.thread
    							LEFT  JOIN #__fb_attachments AS m ON m.mesid = a.id
    							LEFT  JOIN #__fb_favorites AS f ON  f.thread = a.id && f.userid = $my->id
        						" .(($fbConfig->avatar_src == "cb")?
	                    		"LEFT JOIN #__comprofiler AS u ON u.user_id = a.userid":
	                    		"LEFT JOIN #__fb_users AS u ON u.userid = a.userid")."
    						WHERE a.parent =  '0'
    						AND a.catid = $catid
    						AND a.hold = '0'
    						GROUP BY id
    						ORDER BY ordering DESC , lastpost DESC
    						LIMIT $offset,$threads_per_page");
    $messagelist = $database->loadObjectList();
    	check_dberror("Unable to load messages.");

    $favthread = array();
    foreach ($messagelist as $message)
    {
        $threadids[] = $message->id;
        $messages[$message->parent][] = $message;
        $last_reply[$message->id] = $message;
	$last_read[$message->id]->lastread = $last_reply[$message->thread];
	$last_read[$message->id]->unread = 0;
        $hits[$message->id] = $message->hits;
        $messagetext[$message->id] = substr(smile::purify($message->messagetext), 0, 500);
    }

    if (count($threadids) > 0)
    {
        $idstr = @join("','", $threadids);

        $database->setQuery("SELECT
        					thread AS id,
        					count(thread) AS favcount
					FROM #__fb_favorites
       					WHERE thread IN ('$idstr') GROUP BY thread");
        $favlist = $database->loadObjectList();
        check_dberror("Unable to load messages.");

	foreach($favlist AS $fthread)
	{
		$favthread[$fthread->id] = $fthread->favcount;
	}
	unset($favlist, $fthread);

        $database->setQuery("SELECT
        						a.*,
        						u.avatar
        					FROM #__fb_messages AS a
        						" .(($fbConfig->avatar_src == "cb")?
	                    		"LEFT JOIN #__comprofiler AS u ON u.user_id = a.userid":
	                    		"LEFT JOIN #__fb_users AS u ON u.userid = a.userid")."
        					WHERE a.thread IN ('$idstr')
        					AND a.id NOT IN ('$idstr')
        					AND a.hold=0");
        $messagelist = $database->loadObjectList();
        	check_dberror("Unable to load messages.");

        foreach ($messagelist as $message)
        {
            $messages[$message->parent][] = $message;
            $thread_counts[$message->thread]++;
            $last_reply[$message->thread] = ($last_reply[$message->thread]->time < $message->time) ? $message : $last_reply[$message->thread];
            $last_read[$message->id]->lastread = $last_reply[$message->thread];
        }

        $database->setQuery("SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages "
                           ."WHERE thread IN ('$idstr') AND time>'$prevCheck' GROUP BY thread");
        $msgidlist = $database->loadObjectList();
        check_dberror("Unable to get unread messages count and first id.");

        foreach ($msgidlist as $msgid)
        {
            if (!in_array($msgid->thread, $read_topics)) $last_read[$msgid->thread] = $msgid;
        }
    }

    //get number of pending messages
    $database->setQuery("select count(*) from #__fb_messages where catid='$catid' and hold=1");
    $numPending = $database->loadResult();
    	check_dberror('Unable to get number of pending messages.');
    //@rsort($messages[0]);
?>
<!-- Pathway -->
<?php
    if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
        require_once(KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
    }
    else {
        require_once(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
    }
?>
<!-- / Pathway -->
<?php
    //Get the category name for breadcrumb
    unset($objCatInfo, $objCatParentInfo);
    $database->setQuery("SELECT * from #__fb_categories where id = {$catid}");
    $objCatInfo = $database->loadObject();
    	check_dberror('Unable to get categories.');
    //Get the Category's parent category name for breadcrumb
    $database->setQuery("SELECT name,id FROM #__fb_categories WHERE id = {$objCatInfo->parent}");
    $objCatParentInfo = $database->loadObject();
    	check_dberror('Unable to get parent category.');;
    //check if this forum is locked
    $forumLocked = $objCatInfo->locked;
    //check if this forum is subject to review
    $forumReviewed = $objCatInfo->review;

	//meta description and keywords
	$metaKeys=(_KUNENA_CATEGORIES . ', ' . stripslashes($objCatParentInfo->name) . ', ' . stripslashes($objCatInfo->name) . ', ' . stripslashes($fbConfig->board_title) . ', ' . $mainframe->getCfg('sitename'));
	$metaDesc=(stripslashes($objCatParentInfo->name) . ' - ' . stripslashes($objCatInfo->name) .' - ' . stripslashes($fbConfig->board_title));

	$document =& JFactory::getDocument();
	$cur = $document->get( 'description' );
	$metaDesc = $cur .'. ' . $metaDesc;
	$document =& JFactory::getDocument();
	$document->setMetadata( 'keywords', $metaKeys );
	$document->setDescription($metaDesc);
?>
<?php if($objCatInfo->headerdesc) { ?>
<table class="fb_forum-headerdesc" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<?php
		$headerdesc = stripslashes(smile::smileReplace($objCatInfo->headerdesc, 0, $fbConfig->disemoticons, $smileyList));
        $headerdesc = nl2br($headerdesc);
        //wordwrap:
        $headerdesc = smile::htmlwrap($headerdesc, $fbConfig->wrap);
		echo $headerdesc;
		?>
		</td>
	</tr>
</table>
<?php } ?>

<!-- B: List Actions -->

	<table class="fb_list_actions" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="fb_list_actions_goto">
                <?php
                //go to bottom
                echo '<a name="forumtop" /> ';
                echo CKunenaLink::GetSamePageAnkerLink('forumbottom', $fbIcons['bottomarrow'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);
                ?>

		</td><td class="fb_list_actions_forum" width="100%">


                <?php
                if ((($fbConfig->pubwrite == 0 && $my->id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                {
                    //this user is allowed to post a new topic:
                    $forum_new = CKunenaLink::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }
                if ($my->id != 0)
                {
                    $forum_markread = CKunenaLink::GetCategoryLink('markThisRead', $catid, $fbIcons['markThisForumRead'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['markThisForumRead'] . '" alt="' . _GEN_MARK_THIS_FORUM_READ . '" title="' . _GEN_MARK_THIS_FORUM_READ . '" border="0" />' : _GEN_MARK_THIS_FORUM_READ, $rel='nofollow');
                }

		if (isset($forum_new) || isset($forum_markread))
		{
	        echo '<div class="fb_message_buttons_cover" style="text-align: left;">';
	        echo $forum_new;
	        echo ' '.$forum_markread;
	        echo '</div>';
		}
		?>

		</td><td class="fb_list_pages_all" nowrap="nowrap">

		<?php
                //pagination 1
		if (count($messages[0]) > 0)
		{
			$maxpages = 9 - 2; // odd number here (show - 2)
			$totalpages = ceil($total / $threads_per_page);
			echo $pagination = KunenaShowcatPagination($catid, $page, $totalpages, $maxpages);
		}
                ?>
            </td>
        </tr>
    </table>

<!-- F: List Actions -->

<?php
    //(JJ)
    if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_sub_category_list.php')) {
        include(KUNENA_ABSTMPLTPATH . '/fb_sub_category_list.php');
    }
    else {
        include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_sub_category_list.php');
    }
?>

    <?php
    //get all readTopics in an array
    $readTopics = "";
    $database->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid=$my->id");
    $readTopics = $database->loadResult();
    	check_dberror('Unable to get read topics.');

    if (count($readTopics) == 0) {
        $readTopics = "0";
    } //make sure at least something is in there..
    //make it into an array
    $read_topics = explode(',', $readTopics);

    if (count($messages) > 0)
    {
        if ($view == "flat")
            if (file_exists(KUNENA_ABSTMPLTPATH . '/flat.php')) {
                include(KUNENA_ABSTMPLTPATH . '/flat.php');
            }
            else {
                include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'flat.php');
            }
        else if (file_exists(KUNENA_ABSTMPLTPATH . '/thread.php')) {
            include(KUNENA_ABSTMPLTPATH . '/thread.php');
        }
        else {
            include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'thread.php');
        }
    }
    else
    {
        echo "<p align=\"center\">";
        echo '<br /><br />' . _SHOWCAT_NO_TOPICS;
        echo "</p>";
    }
    ?>

<!-- B: List Actions Bottom -->

	<table class="fb_list_actions_bottom" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		<td class="fb_list_actions_goto">
                <?php
                //go to top
                echo '<a name="forumbottom" />';
                echo CKunenaLink::GetSamePageAnkerLink('forumtop', $fbIcons['toparrow'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);
                ?>

		</td><td class="fb_list_actions_forum" width="100%">

                <?php
		if (isset($forum_new) || isset($forum_markread))
		{
	        echo '<div class="fb_message_buttons_cover" style="text-align: left;">';
	        echo $forum_new;
	        echo ' '.$forum_markread;
	        echo '</div>';
		}
		?>

		</td><td class="fb_list_pages_all" nowrap="nowrap">

		<?php
		//pagination 2
                if (count($messages[0]) > 0)
		{
			echo $pagination;
		}
		?>
		</td>
		</tr>
	</table>
	<?php
	echo '<div class = "'. $boardclass .'forum-pathway-bottom">';
	echo $pathway1;
	echo '</div>';
	?>

<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->

<table class="fb_list_bottom" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
	<tr>
		<td class="fb_list_moderators">

			<!-- Mod List -->

			<?php
			//get the Moderator list for display
			$database->setQuery("select * from #__fb_moderation left join #__users on #__users.id=#__fb_moderation.userid where #__fb_moderation.catid=$catid");
			$modslist = $database->loadObjectList();
			check_dberror("Unable to load moderators.");

			if (count($modslist) > 0):
			?>

			<div class = "fbbox-bottomarea-modlist">

                        <?php
				echo '' . _GEN_MODERATORS . ": ";
				foreach ($modslist as $mod) {
					echo CKunenaLink::GetProfileLink($fbConfig, $mod->userid, $mod->username).'&nbsp; ';
				} ?>
			</div>
	<?php endif; ?>
	<!-- /Mod List -->
      </td>
      <td class="fb_list_categories"> <?php

                    //(JJ) FINISH: CAT LIST BOTTOM

                    if ($fbConfig->enableforumjump)
                        require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');

                    ?>
      </td>
    </tr>
</table>

<!-- F: Category List Bottom -->



<?php
}
else
{
	echo _KUNENA_NO_ACCESS;
}

function showChildren($category, $prefix = "", &$allow_forum)
{
    $database =& JFactory::getDBO();
    $database->setQuery("SELECT id, name, parent FROM #__fb_categories WHERE parent='$category'  and published='1' order by ordering");
    $forums = $database->loadObjectList();
    	check_dberror("Unable to load categories.");

    foreach ($forums as $forum)
    {
        if (in_array($forum->id, $allow_forum)) {
            echo("<option value=\"{$forum->id}\">$prefix {$forum->name}</option>");
        }

        showChildren($forum->id, $prefix . "---", $allow_forum);
    }
}
?>
