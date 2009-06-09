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
$fbSession =& CKunenaSession::getInstance();
$kunena_db = &JFactory::getDBO();
// $kunena_my = &JFactory::getUser();
?>
<!--  sub cat -->
<?php
//    show forums within the categories
$kunena_db->setQuery("SELECT id, name, locked,review, pub_access, pub_recurse, admin_access, admin_recurse FROM #__fb_categories WHERE parent='{$catid}' AND published='1' ORDER BY ordering");
$rows = $kunena_db->loadObjectList();
check_dberror("Unable to load categories.");

$smileyList = smile::getEmoticons(0);

$allow_forum = ($fbSession->allowed != '')?explode(',', $fbSession->allowed):array();
foreach ($rows as $rownum=>$row)
{
	if (!in_array($row->id, $allow_forum)) unset ($rows[$rownum]);
}

$tabclass = array
(
    "sectiontableentry1",
    "sectiontableentry2"
);

$k = 0;

if (sizeof($rows) == 0)
    ;
else
{
?>
    <!-- B: List Cat -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable<?php echo $objCatInfo->class_sfx; ?>"  width="100%" id = "fb_cat<?php echo $objCatInfo->id ; ?>"  border = "0" cellspacing = "0" cellpadding = "0">
        <thead>
            <tr>
                <th colspan = "5" align="left">
                    <div class = "fb_title_cover fbm" >
                        <?php
                        echo CKunenaLink::GetCategoryLink('showcat', $objCatInfo->id, stripslashes($objCatInfo->name), $rel='follow', $class='fb_title fbl');
                        ?>

                        <?php
						if ($objCatInfo->description != "") {
							$tmpforumdesc = stripslashes(smile::smileReplace($objCatInfo->description, 0, $fbConfig->disemoticons, $smileyList));
							$tmpforumdesc = nl2br($tmpforumdesc);
							$tmpforumdesc = smile::htmlwrap($tmpforumdesc, $fbConfig->wrap);
							echo $tmpforumdesc;
						}
                        ?>
                    </div>

                    <img id = "BoxSwitch_<?php echo $objCatInfo->id ; ?>__catid_<?php echo $objCatInfo->id ; ?>" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>

        <tbody id = "catid_<?php echo $objCatInfo->id ; ?>">
           <tr class = "fb_sth fbs ">
                        <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" width="1%">&nbsp;</th>
                        <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" align="left"><?php echo _GEN_FORUM; ?></th>
                        <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center" width="5%"><?php echo _GEN_TOPICS; ?></th>

                        <th class = "th-4 <?php echo $boardclass; ?>sectiontableheader" align="center" width="5%">
<?php echo _GEN_REPLIES; ?>
                        </th>

                        <th class = "th-5 <?php echo $boardclass; ?>sectiontableheader" align="left" width="25%">
<?php echo _GEN_LAST_POST; ?>
                        </th>
                    </tr>

            <?php
            foreach ($rows as $singlerow)
            {
                $letPass = 1;

                if ($letPass)
                {
                    //    $k=for alternating row colours:
                    $k = 1 - $k;
                    //count the number of topics posted in each forum
                    $kunena_db->setQuery("SELECT id FROM #__fb_messages WHERE catid='{$singlerow->id}' AND parent='0' AND hold='0'");
                    $num = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");
                    $numtopics = count($num);
                    //count the number of replies posted in each forum
                    $kunena_db->setQuery("SELECT id FROM #__fb_messages WHERE catid='{$singlerow->id}' AND parent!='0' AND hold='0'");
                    $num = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");
                    $numreplies = count($num);
                    //Get the last post from each forum
                    $kunena_db->setQuery("SELECT MAX(time) FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='0' AND moved!='1'");
                    $lastPosttime = $kunena_db->loadResult();
                    	check_dberror("Unable to get max time.");
                    //changed lastPosttime to lastptime
                    $lastptime = KUNENA_timeformat(CKunenaTools::fbGetShowTime($lastPosttime));
                    $lastPosttime = (int)$lastPosttime;

                    if ($kunena_my->id != 0)
                    {
                        //    get all threads with posts after the users last visit; don't bother for guests
                        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='0' AND time>'{$prevCheck}' GROUP BY thread");
                        $newThreadsAll = $kunena_db->loadObjectList();
                        	check_dberror("Unable to load messages.");

                        if (count($newThreadsAll) == 0)
                            $newThreadsAll = array ();

                        //Get the topics this user has already read this session from #__fb_sessions
                        $readTopics = "";
                        $kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'");
                        $readTopics = $kunena_db->loadResult();
                        	check_dberror("Unable to load read topics.");
                        //make it into an array
                        $read_topics = explode(',', $readTopics);
                    }

                    //    Get the forumdescription
                    $kunena_db->setQuery("SELECT description, id FROM #__fb_categories WHERE id='{$singlerow->id}'");
                    $forumDesc = $kunena_db->loadResult();
                    	check_dberror("Unable to load categories.");
                    //    Get the forumsubparent categories
                    $kunena_db->setQuery("SELECT id, name FROM #__fb_categories WHERE parent='{$singlerow->id}' AND published='1'");
                    $forumparents = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load categories.");
                    //    get pending messages if user is a Moderator for that forum
                    $kunena_db->setQuery("SELECT userid FROM #__fb_moderation WHERE catid='{$singlerow->id}'");
                    $moderatorList = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load moderators.");
                    $modIDs[] = array ();
                    array_splice($modIDs, 0);

                    if (count($moderatorList) > 0)
                    {
                        foreach ($moderatorList as $ml) {
                            $modIDs[] = $ml->userid;
                        }
                    }

                    $nummodIDs = count($modIDs);
                    $numPending = 0;

                    if ((in_array($kunena_my->id, $modIDs)) || $is_admin == 1)
                    {
                        $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='1'");
                        $numPending = $kunena_db->loadResult();
                        $is_Mod = 1;
                    }

                    $numPending = (int)$numPending;
                    //    get latest post info
                    $latestname = "";
                    $latestcatid = "";
                    $latestid = "";

                    if ($lastPosttime != 0)
                    {
                        unset($obj_lp);
                        $kunena_db->setQuery("SELECT id, thread, catid,name, subject, userid FROM #__fb_messages WHERE time='{$lastPosttime}' AND hold='0' AND moved!='1'", 0, 1);
                        $obj_lp = $kunena_db->loadObject();
                        $latestname = $obj_lp->name;
                        $latestcatid = $obj_lp->catid;
                        $latestid = $obj_lp->id;
                        $latestsubject = $obj_lp->subject;
                        $latestuserid = $obj_lp->userid;
                        $latestthread = $obj_lp->thread;
                        // count messages in thread
                        $kunena_db->setQuery("SELECT COUNT(id) FROM #__fb_messages WHERE thread='{$latestthread}' AND hold='0'");
                        $latestcount = $kunena_db->loadResult();
                        $latestpage = ceil($latestcount / $fbConfig->messages_per_page);
                    }

                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '" id="fb_cat'.$singlerow->id.'" >';
					echo '<td class="td-1 " align="center">';

					$categoryicon = '';

					$cxThereisNewInForum = 0;
                    if ($fbConfig->shownew && $kunena_my->id != 0)
                    {
                        //Check if unread threads are in any of the forums topics
                        $newPostsAvailable = 0;

                        foreach ($newThreadsAll as $nta)
                        {
                            if (!in_array($nta->thread, $read_topics)) {
                                $newPostsAvailable++;
                            }
                        }

                        if ($newPostsAvailable > 0 && count($newThreadsAll) != 0)
                        {
                            $cxThereisNewInForum = 1;

                            // Check Unread    Cat Images
                            if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_on.gif")) {
                                $categoryicon .= "<img src=\"" . KUNENA_URLCATIMAGES . $singlerow->id . "_on.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                            }
                            else {
                                $categoryicon .= isset($fbIcons['unreadforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadforum'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '"/>' : stripslashes($fbConfig->newchar);
                            }
                        }
                        else
                        {
                            // Check Read Cat Images
                            if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_off.gif")) {
                                $categoryicon .=  "<img src=\"" . KUNENA_URLCATIMAGES . $singlerow->id . "_off.gif\" border=\"0\" class='forum-cat-image' alt=\" \"  />";
                            }
                            else {
                                $categoryicon .=  isset($fbIcons['readforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readforum'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '"/>' : stripslashes($fbConfig->newchar);
                            }
                        }
                    }
                    // Not Login Cat Images
                    else
                    {
                        if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_notlogin.gif")) {
                            $categoryicon .=  "<img src=\"" . KUNENA_URLCATIMAGES . $singlerow->id . "_notlogin.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                        }
                        else {
                            $categoryicon .=  isset($fbIcons['notloginforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['notloginforum'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '"/>' : stripslashes($fbConfig->newchar);
                        }
                    }
                    echo CKunenaLink::GetCategoryLink('listcat', $singlerow->id, $categoryicon, 'follow');
					echo '</td>';
                    echo '<td class="td-2"  align="left"><div class="'
                             . $boardclass . 'thead-title fbl">'.CKunenaLink::GetCategoryLink('showcat', $singlerow->id, stripslashes($singlerow->name), 'follow');

                    //new posts available
                    if ($cxThereisNewInForum == 1 && $kunena_my->id > 0) {
                        echo '<sup><span class="newchar">&nbsp;(' . $newPostsAvailable . ' ' . stripslashes($fbConfig->newchar) . ")</span></sup>";
                    }

                    $cxThereisNewInForum = 0;

                    if ($singlerow->locked)
                    {
                        echo
                            isset($fbIcons['forumlocked']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH
                                . $fbIcons['forumlocked'] . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . _GEN_LOCKED_FORUM . '">';
                        $lockedForum = 1;
                    }

                    if ($singlerow->review)
                    {
                        echo isset($fbIcons['forummoderated']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH
                                 . $fbIcons['forummoderated'] . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '"/>';
                        $moderatedForum = 1;
                    }

                    echo '</div>';

                    if ($forumDesc != "") {
                        $tmpforumdesc = stripslashes(smile::smileReplace($forumDesc, 0, $fbConfig->disemoticons, $smileyList));
                        $tmpforumdesc = nl2br($tmpforumdesc);
                        $tmpforumdesc = smile::htmlwrap($tmpforumdesc, $fbConfig->wrap);
                        echo '<div class="' . $boardclass . 'thead-desc  fbm">' . $tmpforumdesc . ' </div>';
                    }

                    if (count($forumparents) > 0)
                    {
                        if(count($forumparents)==1) {
                          echo '<div class="' . $boardclass . 'thead-child  fbs"><b>' . _KUNENA_CHILD_BOARD . ' </b>';
                        } else {
                          echo '<div class="' . $boardclass . 'thead-child  fbs"><b>' . _KUNENA_CHILD_BOARDS . ' </b>';
                        };

                        foreach ($forumparents as $forumparent)
                        {
            ?>

            <?php //Begin: parent read unread iconset
                            if ($fbConfig->showchildcaticon)
                            {
                                //
                                if ($fbConfig->shownew && $kunena_my->id != 0)
                                {
                                    //    get all threads with posts after the users last visit; don't bother for guests
                                    $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE catid='{$forumparent->id}' AND hold='0' and time>'{$prevCheck}' GROUP BY thread");
                                    $newPThreadsAll = $kunena_db->loadObjectList();
                                    	check_dberror("Unable to load messages.");

                                    if (count($newPThreadsAll) == 0)
                                        $newPThreadsAll = array ();
            ?>

            <?php

                                    //Check if unread threads are in any of the forums topics
                                    $newPPostsAvailable = 0;

                                    foreach ($newPThreadsAll as $npta)
                                    {
                                        if (!in_array($npta->thread, $read_topics)) {
                                            $newPPostsAvailable++;
                                        }
                                    }

                                    if ($newPPostsAvailable > 0 && count($newPThreadsAll) != 0)
                                    {

                                        // Check Unread    Cat Images
                                        if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_on_childsmall.gif")) {
                                            echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_on_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                        }
                                        else {
                                            echo isset($fbIcons['unreadforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes($fbConfig->newchar);
                                        }
                                    }
                                    else
                                    {
                                        // Check Read Cat Images
                                        if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_off_childsmall.gif")) {
                                            echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_off_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                        }
                                        else {
                                            echo isset($fbIcons['readforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($fbConfig->newchar);
                                        }
                                    }
                                }

                                // Not Login Cat Images
                                else
                                {
                                    if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_notlogin_childsmall.gif")) {
                                        echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_notlogin_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                    }
                                    else {
                                        echo isset($fbIcons['notloginforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['notloginforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($fbConfig->newchar);
                                    }
            ?>

            <?php
                                }
                            //
                            }
                            // end: parent read unread iconset
            ?>

            <?php
                            echo CKunenaLink::GetCategoryLink('showcat', $forumparent->id, stripslashes($forumparent->name), 'follow').' &nbsp;';
                        }

                        echo "</div>";
                    }

                    //get the Moderator list for display
                    $kunena_db->setQuery("SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$singlerow->id}'");
                    $modslist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load moderators.");

                    // moderator list
                    if (count($modslist) > 0)
                    {
                        echo '<div class="' . $boardclass . 'thead-moderators  fbs">' . _GEN_MODERATORS . ": ";

						$mod_cnt = 0;
						foreach ($modslist as $mod) {
							if ($mod_cnt) echo ', '; 
							$mod_cnt++;
							echo CKunenaLink::GetProfileLink($fbConfig, $mod->userid, ($fbConfig->username ? $mod->username : $mod->name));
                        }

                        echo '</div>';
                    }

                    if ($is_Moderator)
                    {
                        if ($numPending > 0)
                        {
                            echo '<div class="fbs" ><font color="red">';
                            echo ' ' . CKunenaLink::GetCategoryReviewListLink($singlerow->id, $numcolor.' '.$numPending.' '. _SHOWCAT_PENDING, 'nofollow');
                            echo '</font></div>';
                        }
                    }

                    echo "</td>";
                    echo " <td class=\"td-3  fbm\" align=\"center\" >$numtopics</td>";
                    echo " <td class=\"td-4  fbm\" align=\"center\" >$numreplies</td>";

                    if ($numtopics != 0)
                    {
            ?>

                        <td class = "td-5" align="left">
                            <div class = "<?php echo $boardclass ?>latest-subject fbm">
                                <?php echo CKunenaLink::GetThreadLink('view', $latestcatid, $latestthread, kunena_htmlspecialchars(stripslashes($latestsubject)), kunena_htmlspecialchars(stripslashes($latestsubject)), $rel='nofollow');?>
                            </div>

                            <div class = "<?php echo $boardclass ?>latest-subject-by  fbs">
<?php echo _GEN_BY; ?>

                                <?php echo CKunenaLink::GetProfileLink($fbConfig, $latestuserid, $latestname, $rel='nofollow');?>

                    | <?php echo $lastptime; ?> <?php echo CKunenaLink::GetThreadPageLink($fbConfig, 'view', $latestcatid, $latestthread, $latestpage, $fbConfig->messages_per_page, (isset($fbIcons['latestpost']) ? '<img src="'
             . KUNENA_URLICONSPATH . $fbIcons['latestpost'] . '" border="0" alt="' . _SHOW_LAST . '" title="' . _SHOW_LAST . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'icon_newest_reply.gif" border="0"   alt="' . _SHOW_LAST . '" />'), $latestid, $rel='nofollow');?>
                            </div>
                        </td>

                        </tr>

            <?php
                    }
                    else {
                        echo ' <td class="td-5"  align="left">' . _NO_POSTS . '</td></tr>';
                    }
                }
            }
            ?>
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</div>
<?php
}
?>

<!-- F: List Cat -->
<!--  /sub cat   -->
