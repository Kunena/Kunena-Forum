<?php
/**
* @version $Id: showcat.php 979 2008-08-13 05:47:29Z fxstein $
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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $fbConfig;
global $is_Moderator;

require_once(JB_ABSSOURCESPATH . 'fb_auth.php');

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
    $letPass = fb_auth::validate_user($row[0], $allow_forum, $aro_group->group_id, $acl);
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
    							f.thread AS favthread,
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

    foreach ($messagelist as $message)
    {
        $threadids[] = $message->id;
        $messages[$message->parent][] = $message;
        $last_reply[$message->id] = $message;
        $hits[$message->id] = $message->hits;
        $messagetext[$message->id] = substr(smile::purify($message->messagetext), 0, 500);
    }

    if (count($threadids) > 0)
    {
        $idstr = @join("','", $threadids);
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
    if (file_exists(JB_ABSTMPLTPATH . '/fb_pathway.php')) {
        require_once(JB_ABSTMPLTPATH . '/fb_pathway.php');
    }
    else {
        require_once(JB_ABSPATH . '/template/default/fb_pathway.php');
    }

    //Get the category name for breadcrumb
    unset($objCatInfo, $objCatParentInfo);
    $database->setQuery("SELECT * from #__fb_categories where id = {$catid}");
    $database->loadObject($objCatInfo);
    	check_dberror('Unable to get categories.');
    //Get the Category's parent category name for breadcrumb
    $database->setQuery("SELECT name,id FROM #__fb_categories WHERE id = {$objCatInfo->parent}");
    $database->loadObject($objCatParentInfo);
    	check_dberror('Unable to get parent category.');;
    //check if this forum is locked
    $forumLocked = $objCatInfo->locked;
    //check if this forum is subject to review
    $forumReviewed = $objCatInfo->review;

	//meta description and keywords
	$metaKeys=(_FB_CATEGORIES . ', ' . $objCatParentInfo->name . ', ' . $objCatInfo->name . ', ' . $fbConfig->board_title . ', ' . $GLOBALS['mosConfig_sitename']);
	$metaDesc=($objCatParentInfo->name . ' - ' . $objCatInfo->name .' - ' . $fbConfig->board_title);

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
?>
<!--</div>
</td>
</tr>
</table>-->
<!-- / Pathway -->
<?php if($objCatInfo->headerdesc) { ?>
<div class="headerdesc"><?php echo $objCatInfo->headerdesc; ?></div>
<?php } ?>
<?php
    //(JJ)
    if (file_exists(JB_ABSTMPLTPATH . '/fb_sub_category_list.php')) {
        include(JB_ABSTMPLTPATH . '/fb_sub_category_list.php');
    }
    else {
        include(JB_ABSPATH . '/template/default/fb_sub_category_list.php');
    }
?>
    <!-- top nav -->

    <table border = "0" cellspacing = "0" class = "jr-topnav" cellpadding = "0">
        <tr>
            <td class = "jr-topnav-left">
                <?php
                //go to bottom
                echo '<a name="forumtop" /> ';
                echo fb_link::GetSamePageAnkerLink('forumbottom', $fbIcons['bottomarrow'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);
                ?>

                <?php
                if ((($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                {
                    //this user is allowed to post a new topic:
                    echo fb_link::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }

                echo '</td><td class="jr-topnav-right">';

                //pagination 1
                if (count($messages[0]) > 0)
                {
                    echo '<div class="jr-pagenav">'. _PAGE;

                    if (($page - 2) > 1)
                    {
                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, 1, 1, $rel='follow', $class='jr-pagenav-nb');
                    }

                    for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
                    {
                        if ($page == $i) {
                            echo "<class=\"jr-pagenav-nb-act\"> $i</class>";
                        }
                        else {
                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, $i, $i, $rel='follow', $class='jr-pagenav-nb');
                        }
                    }

                    if ($page + 2 < ceil($total / $threads_per_page))
                    {
                        echo "<class=\"jr-pagenav-nb\"> ...&nbsp;</class>";

                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, ceil($total / $threads_per_page), ceil($total / $threads_per_page), $rel='follow', $class='jr-pagenav-nb');
                    }

                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <!-- /top nav -->

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
            if (file_exists(JB_ABSTMPLTPATH . '/flat.php')) {
                include(JB_ABSTMPLTPATH . '/flat.php');
            }
            else {
                include(JB_ABSPATH . '/template/default/flat.php');
            }
        else if (file_exists(JB_ABSTMPLTPATH . '/thread.php')) {
            include(JB_ABSTMPLTPATH . '/thread.php');
        }
        else {
            include(JB_ABSPATH . '/template/default/thread.php');
        }
    }
    else
    {
        echo "<p align=\"center\">";
        echo '<br /><br />' . _SHOWCAT_NO_TOPICS;
        echo "</p>";
    }
    ?>
    <!-- bottom nav -->

    <table border = "0" cellspacing = "0" class = "jr-bottomnav" cellpadding = "0">
        <tr>
            <td class = "jr-topnav-left">
                <?php
                //go to top
                echo '<a name="forumbottom" />';
                echo fb_link::GetSamePageAnkerLink('forumtop', $fbIcons['toparrow'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);
                ?>

                <?php
                if ((($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                {
                    //this user is allowed to post a new topic:
                    echo fb_link::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }

                echo '</td><td class="jr-topnav-right">';

                //pagination 2
                if (count($messages[0]) > 0)
                {
                    echo '<div class="jr-pagenav">'. _PAGE;

                    if (($page - 2) > 1)
                    {
                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, 1, 1, $rel='follow', $class='jr-pagenav-nb');
                    }

                    for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
                    {
                        if ($page == $i) {
                            echo "<class=\"jr-pagenav-nb-act\"> $i</class>";
                        }
                        else {
                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, $i, $i, $rel='follow', $class='jr-pagenav-nb');
                        }
                    }

                    if ($page + 2 < ceil($total / $threads_per_page))
                    {
                        echo "<class=\"jr-pagenav-nb\"> ...&nbsp;</class>";

                        echo ' '.fb_link::GetCategoryPageLink('showcat', $catid, ceil($total / $threads_per_page), ceil($total / $threads_per_page), $rel='follow', $class='jr-pagenav-nb');
                    }

                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <!-- -->



<!-- Category List Bottom -->

<table  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
  <thead>
    <tr>
      <td style="padding-left:20px;" align="left" > <?php
                    if ($my->id != 0)
                    {
                        echo fb_link::GetCategoryLink('markThisRead', $catid, _GEN_MARK_THIS_FORUM_READ, $rel='nofollow');
                    }
                    ?>
                    <!-- Mod List -->


                        <?php
                        //get the Moderator list for display
                        $database->setQuery("select * from #__fb_moderation left join #__users on #__users.id=#__fb_moderation.userid where #__fb_moderation.catid=$catid");
                        $modslist = $database->loadObjectList();
                        	check_dberror("Unable to load moderators.");
                        ?>

                        <?php
                        if (count($modslist) > 0)
                        { ?>
        <div class = "fbbox-bottomarea-modlist" style=" font-size:10px;">
          <?php
                            echo '' . _GEN_MODERATORS . ": ";

                            foreach ($modslist as $mod) {
                                echo '&nbsp;'.fb_link::GetProfileLink($mod->userid, $mod->username).'&nbsp; ';
                            } ?>
        </div>
        <?php  } ?>
        <!-- /Mod List -->
      </td>
      <td  align="right" style="padding-right:20px;"> <?php

                    //(JJ) FINISH: CAT LIST BOTTOM

                    if ($fbConfig->enableforumjump)
                        require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');

                    ?>
      </td>
    </tr>
  </thead>
</table>

<!-- / Category List Bottom -->



<?php
}
else
{
	echo _FB_NO_ACCESS;
}

function showChildren($category, $prefix = "", &$allow_forum)
{
    global $database;
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
