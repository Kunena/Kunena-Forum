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
$forumLocked = 0;
$topicLocked = 0;
$topicSticky = 0;

unset($allow_forum);

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

if (in_array($catid, $allow_forum))
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
    $kunena_db->setQuery("Select count(*) FROM #__fb_messages WHERE parent = '0' AND catid= '$catid' AND hold = '0' ");
    $total = (int)$kunena_db->loadResult();
    	check_dberror('Unable to get message count.');
    $kunena_db->setQuery("SELECT
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
    							LEFT  JOIN #__fb_favorites AS f ON  f.thread = a.id && f.userid = $kunena_my->id
        						" .(($fbConfig->avatar_src == "cb")?
	                    		"LEFT JOIN #__comprofiler AS u ON u.user_id = a.userid":
	                    		"LEFT JOIN #__fb_users AS u ON u.userid = a.userid")."
    						WHERE a.parent =  '0'
    						AND a.catid = $catid
    						AND a.hold = '0'
    						GROUP BY id
    						ORDER BY ordering DESC , lastpost DESC
    						LIMIT $offset,$threads_per_page");
    $messagelist = $kunena_db->loadObjectList();
    	check_dberror("Unable to load messages.");

    $favthread = array();
    $threadids = array();
    $messages = array();
    $messages[0] = array();
    $thread_counts = array();
    foreach ($messagelist as $message)
    {
        $threadids[] = $message->id;
        $messages[$message->parent][] = $message;
        $last_reply[$message->id] = $message;
	$last_read[$message->id]->lastread = $last_reply[$message->thread];
	$last_read[$message->id]->unread = 0;
        $hits[$message->id] = $message->hits;
        $thread_counts[$message->id] = 0;
        $messagetext[$message->id] = substr(smile::purify($message->messagetext), 0, 500);
    }

    if (count($threadids) > 0)
    {
        $idstr = @join("','", $threadids);

        $kunena_db->setQuery("SELECT
        					thread AS id,
        					count(thread) AS favcount
					FROM #__fb_favorites
       					WHERE thread IN ('$idstr') GROUP BY thread");
        $favlist = $kunena_db->loadObjectList();
        check_dberror("Unable to load messages.");

	foreach($favlist AS $fthread)
	{
		$favthread[$fthread->id] = $fthread->favcount;
	}
	unset($favlist, $fthread);

        $kunena_db->setQuery("SELECT
        						a.*,
        						u.avatar
        					FROM #__fb_messages AS a
        						" .(($fbConfig->avatar_src == "cb")?
	                    		"LEFT JOIN #__comprofiler AS u ON u.user_id = a.userid":
	                    		"LEFT JOIN #__fb_users AS u ON u.userid = a.userid")."
        					WHERE a.thread IN ('$idstr')
        					AND a.id NOT IN ('$idstr')
        					AND a.hold=0");
        $messagelist = $kunena_db->loadObjectList();
        	check_dberror("Unable to load messages.");

        foreach ($messagelist as $message)
        {
            $messages[$message->parent][] = $message;
            $thread_counts[$message->thread]++;
            $last_reply[$message->thread] = ($last_reply[$message->thread]->time < $message->time) ? $message : $last_reply[$message->thread];
            $last_read[$message->id]->lastread = $last_reply[$message->thread];
        }

        $kunena_db->setQuery("SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages "
                           ."WHERE thread IN ('$idstr') AND time>'$prevCheck' GROUP BY thread");
        $msgidlist = $kunena_db->loadObjectList();
        check_dberror("Unable to get unread messages count and first id.");

        foreach ($msgidlist as $msgid)
        {
            if (!in_array($msgid->thread, $read_topics)) $last_read[$msgid->thread] = $msgid;
        }
    }

    //get number of pending messages
    $kunena_db->setQuery("select count(*) from #__fb_messages where catid='$catid' and hold=1");
    $numPending = $kunena_db->loadResult();
    	check_dberror('Unable to get number of pending messages.');
    //@rsort($messages[0]);
?>
<?php
    //Get the category name for breadcrumb
    unset($objCatInfo, $objCatParentInfo);
    $kunena_db->setQuery("SELECT * from #__fb_categories where id = {$catid}");
    $objCatInfo = $kunena_db->loadObject();
    	check_dberror('Unable to get categories.');
    //Get the Category's parent category name for breadcrumb
    $kunena_db->setQuery("SELECT name,id FROM #__fb_categories WHERE id = {$objCatInfo->parent}");
    $objCatParentInfo = $kunena_db->loadObject();
    	check_dberror('Unable to get parent category.');;
    //check if this forum is locked
    $forumLocked = $objCatInfo->locked;
    //check if this forum is subject to review
    $forumReviewed = $objCatInfo->review;

	//meta description and keywords
	$metaKeys=(_KUNENA_CATEGORIES . ', ' . kunena_htmlspecialchars(stripslashes($objCatParentInfo->name)) . ', ' . kunena_htmlspecialchars(stripslashes($objCatInfo->name)) . ', ' . stripslashes($fbConfig->board_title) . ', ' . $app->getCfg('sitename'));
	$metaDesc=(kunena_htmlspecialchars(stripslashes($objCatParentInfo->name)) . ' - ' . kunena_htmlspecialchars(stripslashes($objCatInfo->name)) .' - ' . stripslashes($fbConfig->board_title));

	$document =& JFactory::getDocument();
	$cur = $document->get( 'description' );
	$metaDesc = $cur .'. ' . $metaDesc;
	$document =& JFactory::getDocument();
	$document->setMetadata( 'keywords', $metaKeys );
	$document->setDescription($metaDesc);
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
<?php if($objCatInfo->headerdesc) { ?>
<table class="fb_forum-headerdesc" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<?php
		$smileyList = smile::getEmoticons(0);
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
                echo CKunenaLink::GetSamePageAnkerLink('forumbottom', isset($fbIcons['bottomarrow']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);
                ?>

		</td><td class="fb_list_actions_forum" width="100%">


                <?php
                if ($is_Moderator || ($forumLocked == 0 && ($kunena_my->id > 0 || $fbConfig->pubwrite)))
                {
                    //this user is allowed to post a new topic:
                    $forum_new = CKunenaLink::GetPostNewTopicLink($catid, isset($fbIcons['new_topic']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }
                if ($kunena_my->id != 0)
                {
                    $forum_markread = CKunenaLink::GetCategoryLink('markThisRead', $catid, isset($fbIcons['markThisForumRead']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['markThisForumRead'] . '" alt="' . _GEN_MARK_THIS_FORUM_READ . '" title="' . _GEN_MARK_THIS_FORUM_READ . '" border="0" />' : _GEN_MARK_THIS_FORUM_READ, $rel='nofollow');
                }

		if (isset($forum_new) || isset($forum_markread))
		{
	        echo '<div class="fb_message_buttons_row" style="text-align: left;">';
	        if (isset($forum_new)) echo $forum_new;
	        if (isset($forum_markread)) echo ' '.$forum_markread;
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
    $kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid=$kunena_my->id");
    $readTopics = $kunena_db->loadResult();
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
                echo CKunenaLink::GetSamePageAnkerLink('forumtop', isset($fbIcons['toparrow']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);
                ?>

		</td><td class="fb_list_actions_forum" width="100%">

                <?php
		if (isset($forum_new) || isset($forum_markread))
		{
	        echo '<div class="fb_message_buttons_row" style="text-align: left;">';
	        if (isset($forum_new)) echo $forum_new;
	        if (isset($forum_markread)) echo ' '.$forum_markread;
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
			$kunena_db->setQuery("select * from #__fb_moderation left join #__users on #__users.id=#__fb_moderation.userid where #__fb_moderation.catid=$catid");
			$modslist = $kunena_db->loadObjectList();
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
    $kunena_db =& JFactory::getDBO();
    $kunena_db->setQuery("SELECT id, name, parent FROM #__fb_categories WHERE parent='$category'  and published='1' order by ordering");
    $forums = $kunena_db->loadObjectList();
    	check_dberror("Unable to load categories.");

    foreach ($forums as $forum)
    {
        if (in_array($forum->id, $allow_forum)) {
            echo("<option value=\"{$forum->id}\">$prefix ".kunena_htmlspecialchars($forum->name)."</option>");
        }

        showChildren($forum->id, $prefix . "---", $allow_forum);
    }
}
?>
