<?php
/**
* @version $Id: view.php 1073 2008-10-09 20:10:29Z fxstein $
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

if ($fbConfig->highlightcode)
{
	$mainframe->addCustomHeadTag('
	<script type="text/javascript" src="'.JB_DIRECTURL . '/template/default/plugin/chili/jquery.chili.pack.js"></script>
	<script id="setup" type="text/javascript">
	ChiliBook.recipeFolder     = "'.JB_DIRECTURL . '/template/default/plugin/chili/";
	ChiliBook.stylesheetFolder     = "'.JB_DIRECTURL . '/template/default/plugin/chili/";
	</script>
	');
}

// For joomla mambot support
if ($fbConfig->jmambot) { class t{ var $text = ""; }    }
//

global $is_Moderator;
global $acl;
//securing form elements
$catid = (int)$catid;
$id = (int)$id;

$smileyList = smile::getEmoticons(0);

//ob_start();
$showedEdit = 0;
require_once (JB_ABSSOURCESPATH . 'fb_auth.php');
require_once (JB_ABSSOURCESPATH . 'fb_statsbar.php');

if (!$is_Moderator)
{
    //check Access Level Restrictions but don't bother for Moderators
    unset ($allow_forum);

    $allow_forum = array ();

    //get all the info on this forum:
    $database->setQuery("SELECT id,pub_access,pub_recurse,admin_access,admin_recurse FROM #__fb_categories where id=$catid");
    $row = $database->loadObjectList();
    	check_dberror("Unable to load categories.");

    if ($fbSession->allowed != "na" && !$new_fb_user) {
        $allow_forum = explode(',', $fbSession->allowed);
    }
    else {
        $allow_forum = array ();
    }

    //Do user identification based upon the ACL
    $letPass = 0;
    $letPass = fb_auth::validate_user($row[0], $allow_forum, $aro_group->group_id, $acl);
}

$topicLock = 0;

if ($letPass || $is_Moderator)
{
    $view = $view == "" ? $settings[current_view] : $view;
    setcookie("fboard_settings[current_view]", $view, time() + 31536000, '/');

    $id = (int)$id;

    $database->setQuery("SELECT * FROM #__fb_messages AS a LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE a.id={$id} and a.hold=0");
    unset($this_message);
    $database->loadObject($this_message);
    	check_dberror('Unable to load message.');

    $topicLock = $this_message->locked;

    if (count($this_message) < 1) {
        echo '<p align="center">' . _MODERATION_INVALID_ID . '</p>\n';
    }
    else
    {
        $thread = $this_message->parent == 0 ? $this_message->id : $this_message->thread;

        // Test if this is a valid SEO URL if not we should redirect using a 301 - permanent redirect
        if ($view == "flat" && $thread != $this_message->id)
        {
        	// Invalid SEO URL detected!
        	// Create permanent re-direct and quit
        	// This query to calculate the page this reply is sitting on within this thread
        	$query = "SELECT count(*)
        				FROM #__fb_messages AS a
        				WHERE a.thread=$thread
        					AND a.id<=$this_message->id";
        	$database->setQuery($query);
        	$replyCount = $database->loadResult();
        		check_dberror('Unable to calculate replyCount.');

        	$replyPage = $replyCount > $fbConfig->messages_per_page ? ceil($replyCount / $fbConfig->messages_per_page) : 1;

        	header("HTTP/1.1 301 Moved Permanently");
        	header("Location: " . fb_link::GetThreadPageURL('view', $this_message->catid, $thread, $replyPage, $fbConfig->messages_per_page, $this_message->id) );

        	die();
        }

        if ($my->id)
        {
            //mark this topic as read
            $database->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid={$my->id}");
            $readTopics = $database->loadResult();

            if ($readTopics == "")
            {
                $readTopics = $thread;
            }
            else
            {
                //get all readTopics in an array
                $_read_topics = @explode(',', $readTopics);

                if (!@in_array($thread, $_read_topics)) {
                    $readTopics .= "," . $thread;
                }
            }

            $database->setQuery("UPDATE #__fb_sessions set readtopics='{$readTopics}' WHERE userid={$my->id}");
            $database->query();
        }

        //update the hits counter for this topic & exclude the owner
        if ($this_message->userid != $my->id) {
            $database->setQuery("UPDATE #__fb_messages SET hits=hits+1 WHERE id=$thread AND parent=0");
            $database->query();
        }
        // changed to 0 to fix the missing post when the thread splits over multiple pages
        $i = 0;

        if ($fbConfig->cb_profile && $my->id != 0)
        {
            $database->setQuery("SELECT fbordering from #__comprofiler where user_id=$my->id");
            $fbordering = $database->loadResult();

            if ($fbordering == "_UE_FB_ORDERING_OLDEST") {
                $orderingNum = 0;
            }
            else {
                $orderingNum = 1;
            }
        }
        else
        {
            $database->setQuery("SELECT ordering from #__fb_users where userid=$my->id");
            $orderingNum = $database->loadResult();
        }

        $ordering = $orderingNum ? 'DESC' : 'ASC';
        // Advanced moderator merge-split sort order for merged threads
        $database->setQuery("SELECT * FROM #__fb_messages AS a "
           ."\n LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE (a.thread='$thread' OR a.id='$thread') AND a.hold=0 AND a.catid='$catid' ORDER BY a.time $ordering");

        if ($view != "flat")
        $flat_messages[] = $this_message;

        foreach ($database->loadObjectList()as $message)
        {
            if ($view == "flat")
            {
                $flat_messages[] = $message;

                if ($id == $message->id) {
                    $idmatch = $i;
                }

                $i++;
            }
            else {
                $messages[$message->parent][] = $message;
            }
        }

        if ($view == "flat")
        {
            //prepare threading
            $limit = $fbConfig->messages_per_page;

            if ($idmatch > $limit) {
                $limitstart = (floor($idmatch / $limit)) * $limit;
            }
            else {
                $limitstart = 0;
            }

            $limitstart = intval(mosGetParam($_REQUEST, 'limitstart', $limitstart));
            $total = count($flat_messages);

            if ($total > $limit)
            {
                require_once (JB_JABSPATH . '/includes/pageNavigation.php');
                $pageNav = new mosPageNav($total, $limitstart, $limit);
                // slice out elements based on limits
                $flat_messages = array_slice($flat_messages, $pageNav->limitstart, $pageNav->limit);
            }
            else {
                $total = 0;
            }
        }

        //Get the category name for breadcrumb
        unset($objCatInfo, $objCatParentInfo);
        $database->setQuery("SELECT * from #__fb_categories where id='$catid'");
        $database->loadObject($objCatInfo);
        //Get Parent's cat.name for breadcrumb
        $database->setQuery("SELECT name,id from #__fb_categories WHERE id='$objCatInfo->parent'");
        $database->loadObject($objCatParentInfo);

        //Perform subscriptions check only once
        $fb_cansubscribe = 0;
        if ($fbConfig->allowsubscriptions && ("" != $my_id || 0 != $my_id))
        {
            $database->setQuery("SELECT thread from #__fb_subscriptions where userid=$my_id and thread='$thread'");
            $fb_subscribed = $database->loadResult();

            if ($fb_subscribed == "") {
                $fb_cansubscribe = 1;
            }
        }
        //Perform favorites check only once
        $fb_canfavorite = 0;
        if ($fbConfig->allowfavorites && ("" != $my_id || 0 != $my_id))
        {
            $database->setQuery("SELECT thread from #__fb_favorites where userid=$my_id and thread='$thread'");
            $fb_favorited = $database->loadResult();

            if ($fb_favorited == "") {
                $fb_canfavorite = 1;
            }
        }

        //data ready display now
?>

        <script type = "text/javascript">
        jQuery(function()
        {
            jQuery(".fb_qr_fire").click(function()
            {
                jQuery("#sc" + (jQuery(this).attr("id").split("__")[1])).toggle();
            });
            jQuery(".fb_qm_cncl_btn").click(function()
            {
                jQuery("#sc" + (jQuery(this).attr("id").split("__")[1])).toggle();
            });

        });
        </script>

        <div>
            <?php
            if (file_exists(JB_ABSTMPLTPATH . '/fb_pathway.php')) {
                require_once (JB_ABSTMPLTPATH . '/fb_pathway.php');
            }
            else {
                require_once (JB_ABSPATH . '/template/default/fb_pathway.php');
            }
            ?>
        </div>
        <?php if($objCatInfo->headerdesc) { ?>
        <div class="headerdesc"><?php echo $objCatInfo->headerdesc; ?></div>
        <?php } ?>
        <!-- top nav -->
        <table border = "0" cellspacing = "0" class = "jr-topnav" cellpadding = "0" width="100%">
            <tr>
                <td class = "jr-topnav-left">
                    <?php
                    //go to bottom
                    echo '<a name="forumtop" /> ';
                    echo fb_link::GetSamePageAnkerLink('forumbottom', $fbIcons['bottomarrow'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);

                    if (($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite)
                    {
                        //this user is allowed to post a new topic:
                        echo fb_link::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                    }

                    if ((($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                    {
                        //this user is allowed to reply to this topic:
                        echo fb_link::GetTopicPostReplyLink('reply', $catid, $thread, $fbIcons['topicreply'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['topicreply'] . '" alt="' . _GEN_POST_REPLY . '" title="' . _GEN_POST_REPLY . '" border="0" />' : _GEN_POST_REPLY);
                    }

                    if ($fb_cansubscribe == 1)
                    {
                        // this user is allowed to subscribe - check performed further up to eliminate duplicate checks
                        // for top and bottom navigation
                        echo fb_link::GetTopicPostLink('subscribe', $catid, $id, $fbIcons['subscribe'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['subscribe'] . '" alt="' . _VIEW_SUBSCRIBETXT . '" title="' . _VIEW_SUBSCRIBETXT . '" border="0" />' : _VIEW_SUBSCRIBETXT);
                    }

                    if ($my_id != 0 && $fbConfig->allowsubscriptions && $fb_cansubscribe == 0)
                    {
                        // this user is allowed to unsubscribe
                        echo fb_link::GetTopicPostLink('unsubscribe', $catid, $id, $fbIcons['unsubscribe'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['unsubscribe'] . '" alt="' . _VIEW_UNSUBSCRIBETXT . '" title="' . _VIEW_UNSUBSCRIBETXT . '" border="0" />' : _VIEW_UNSUBSCRIBETXT);
                    }

                    if ($fb_canfavorite == 1)
                    {
                        // this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
                        // for top and bottom navigation
                        echo fb_link::GetTopicPostLink('favorite', $catid, $id, $fbIcons['favorite'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['favorite'] . '" alt="' . _VIEW_FAVORITETXT . '" title="' . _VIEW_FAVORITETXT . '" border="0" />' : _VIEW_FAVORITETXT);
                    }

                    if ($my_id != 0 && $fbConfig->allowfavorites && $fb_canfavorite == 0)
                    {
                        // this user is allowed to unfavorite
                        echo fb_link::GetTopicPostLink('unfavorite', $catid, $id, $fbIcons['unfavorite'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['unfavorite'] . '" alt="' . _VIEW_UNFAVORITETXT . '" title="' . _VIEW_UNFAVORITETXT . '" border="0" />' : _VIEW_UNFAVORITETXT);
                    }
                    // FINISH: FAVORITES
                    ?>
                    <!-- Begin: Total Favorite -->
                    <?php
                    $database->setQuery("SELECT COUNT(*) FROM #__fb_favorites where thread='$thread'");
                    $fb_totalfavorited = $database->loadResult();

                    echo '<span class="fb_totalfavorite">';
                    echo _FB_TOTALFAVORITE;
                    echo $fb_totalfavorited;
                    echo '</span>';
                    ?>
                <!-- Finish: Total Favorite -->
                </td>

                <td class = "jr-topnav-right">
                    <?php
                    if ($total != 0)
                    {
                    ?>

                        <div class = "jr-pagenav">
                            <ul>
                                <li class = "jr-pagenav-text"><?php echo _PAGE ?></li>

                                <li class = "jr-pagenav-nb">

                                <?php
                                // TODO: fxstein - Need to perform SEO cleanup
                                echo $pageNav->writePagesLinks( JB_LIVEURLREL."&amp;func=view&amp;id=$id&amp;catid=$catid");
                                ?>

                                </li>
                            </ul>
                        </div>

                    <?php
                    }
                    ?>
                </td>
            </tr>
        </table>
        <!-- /top nav -->
        <!-- <table border = "0" cellspacing = "0" cellpadding = "0" width = "100%" align = "center"> -->

            <table class = "fb_blocktable<?php echo $objCatInfo->class_sfx; ?>"  id="fb_views" cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
                <thead>
                    <tr>
                        <th align="left">
                             <div class = "fb_title_cover  fbm">
                                <span class = "fb_title fbl"><b><?php echo _FB_TOPIC; ?></b> <?php echo $jr_topic_title; ?></span>
                            </div>
                            <!-- FORUM TOOLS -->

                            <?php

                            //(JJ) BEGIN: RECENT POSTS
                            if (file_exists(JB_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php')) {
                                include (JB_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php');
                            }
                            else {
                                include (JB_ABSPATH . '/template/default/plugin/forumtools/forumtools.php');
                            }

                            //(JJ) FINISH: RECENT POSTS

                            ?>
                        <!-- /FORUM TOOLS -->
                        </th>
                    </tr>
                </thead>

                <tr>
                    <td>
                        <?php
                        $tabclass = array
                        (
                        "sectiontableentry1",
                        "sectiontableentry2"
                        );

                        $mmm = 0;
                        $k = 0;
                        // Set up a list of moderators for this category (limits amount of queries)
                        $database->setQuery("SELECT a.userid FROM #__fb_users AS a" . "\n LEFT JOIN #__fb_moderation AS b" . "\n ON b.userid=a.userid" . "\n WHERE b.catid='$catid'");
                        $catModerators = $database->loadResultArray();


                        /**
                        * note: please check if this routine is fine. there is no need to see for all messages if they are locked or not, either the thread or cat can be locked anyway
                        */

                        //check if topic is locked
                        $_lockTopicID = $this_message->thread;
                        $topicLock = $this_message->locked;

                        if ($_lockTopicID) // prev UNDEFINED $topicID!!
                        {
                            $lockedWhat = _TOPIC_NOT_ALLOWED; // UNUSED
                        }

                        else
                        { //topic not locked; check if forum is locked
                            $database->setQuery("select locked from #__fb_categories where id={$this_message->catid}");
                            $topicLock = $database->loadResult();
                            $lockedWhat = _FORUM_NOT_ALLOWED; // UNUSED
                        }
                        // END TOPIC LOCK

                        if (count($flat_messages) > 0)
                        {
                            foreach ($flat_messages as $fmessage)
                            {

                                $k = 1 - $k;
                                $mmm++;

                                if ($fmessage->parent == 0) {
                                    $fb_thread = $fmessage->id;
                                }
                                else {
                                    $fb_thread = $fmessage->thread;
                                }
                                //Joomla Mambot Support , Thanks hacksider
                                if ($fbConfig->jmambot)
                                {
                                    global $_MAMBOTS;
                                    $row = new t();
                                    $row->text = $fb_message_txt;
                                    $_MAMBOTS->loadBotGroup( 'content' );
                                    $params =& new mosParameters( '' );
                                    $results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, 0 ), true );
                                    $msg_text = $row->text;
                                }
                                else
                                {
                                    $msg_text = $fb_message_txt;
                                }
                                /* Fininsh Joomla Mambot Support */

                                //set page title
                                if ($leaf->parent == 0)
                                {
                                    $mainframe->setPageTitle($fmessage->subject . ' - ' . $fbConfig->board_title);
                                }

                                //meta description and keywords
								$metaKeys=($fmessage->subject. ', ' .$objCatParentInfo->name . ', ' . $fbConfig->board_title . ', ' . $GLOBALS['mosConfig_sitename']);
								$metaDesc=($fmessage->subject . ' - ' .$objCatParentInfo->name . ' - ' . $objCatInfo->name .' - ' . $fbConfig->board_title);

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
								    $mainframe->appendMetaTag( 'keywords', $metaKeys );
								    $mainframe->appendMetaTag( 'description', $metaDesc );
								}

                                //filter out clear html
                                $fmessage->name = htmlspecialchars($fmessage->name);
                                $fmessage->email = htmlspecialchars($fmessage->email);
                                $fmessage->subject = htmlspecialchars($fmessage->subject);

                                //Get userinfo needed later on, this limits the amount of queries
                                unset($userinfo);
                                $database->setQuery("SELECT  a.*,b.name,b.username,b.gid FROM #__fb_users as a LEFT JOIN #__users as b on b.id=a.userid where a.userid='$fmessage->userid'");
                                $database->loadObject($userinfo);
                                //get the username:
                                $fb_username = "";

                                if ($fbConfig->username) {
                                    $fb_queryName = "username";
                                }
                                else {
                                    $fb_queryName = "name";
                                }

                                $fb_username = $userinfo->$fb_queryName;

                                if ($fb_username == "" || $fbConfig->changename) {
                                    $fb_username = $fmessage->name;
                                }

                                $msg_id = $fmessage->id;
                                $lists["userid"] = $fmessage->userid;
                                $msg_username = $fmessage->email != "" && $my_id > 0 && $fbConfig->showemail ? fb_link::GetEmailLink($fmessage->email, $fb_username) : stripslashes($fb_username);

                                if ($fbConfig->allowavatar)
                                {
                                    $Avatarname = $userinfo->username;

                                    if ($fbConfig->avatar_src == "clexuspm") {
                                        $msg_avatar = '<span class="fb_avatar"><img src="' . MyPMSTools::getAvatarLinkWithID($fmessage->userid) . '" /></span>';
                                    }
                                    elseif ($fbConfig->avatar_src == "cb")
                                    {
                                        $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='$fmessage->userid' AND avatarapproved='1'");
                                        $avatar = $database->loadResult();

                                        if ($avatar != '')
                                        {
                                            //added or modified by mambojoe
                                            //This now  has the right path to the upload directory and also handles the thumbnail and gallery photos.
                                            $imgpath = JB_JLIVEURL . '/images/comprofiler/';

                                            if (eregi("gallery/", $avatar) == false)
                                            $imgpath .= "tn" . $avatar;
                                            else
                                            $imgpath .= $avatar;

                                            $msg_avatar = '<span class="fb_avatar"><img src="' . $imgpath . '" alt="" /></span>';
                                            //added or modified by mambojoe
                                        }
                                        else {
                                            $imgpath = JB_JLIVEURL."/components/com_comprofiler/plugin/language/default_language/images/tnnophoto.jpg";
                                            $msg_avatar = '<span class="fb_avatar"><img src="' . $imgpath . '" alt="" /></span>';
                                        }
                                    }
                                    else
                                    {
                                        $avatar = $userinfo->avatar;

                                        if ($avatar != '') {
                                            $msg_avatar = '<span class="fb_avatar"><img border="0" src="' . FB_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" /></span>';
                                        }

                                        else {$msg_avatar = '<span class="fb_avatar"><img  border="0" src="' . FB_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" /></span>'; }
                                    }
                                }

                                if ($fbConfig->showuserstats)
                                {
                                    //user type determination
                                    $ugid = $userinfo->gid;
                                    $uIsMod = 0;
                                    $uIsAdm = 0;
                                    $uIsMod = in_array($fmessage->userid, $catModerators);

                                    if ($ugid > 0) { //only get the groupname from the ACL if we're sure there is one
                                        $agrp = strtolower($acl->get_group_name($ugid, 'ARO'));
                                    }

                                    if ($ugid == 0) {
                                        $msg_usertype = _VIEW_VISITOR;
                                    }
                                    else
                                    {
                                        if (strtolower($agrp) == "administrator" || strtolower($agrp) == "superadministrator" || strtolower($agrp) == "super administrator")
                                        {
                                            $msg_usertype = _VIEW_ADMIN;
                                            $uIsAdm = 1;
                                        }
                                        elseif ($uIsMod) {
                                            $msg_usertype = _VIEW_MODERATOR;
                                        }
                                        else {
                                            $msg_usertype = _VIEW_USER;
                                        }
                                    }

                                    //done usertype determination, phew...
                                    //# of post for this user and ranking
                                    if ($fmessage->userid)
                                    {
                                        $numPosts = (int)$userinfo->posts;

                                        //ranking
                                        $rText = ''; $showSpRank = false;
                                        if ($fbConfig->showranking)
                                        {

                                            if ($showSpRank = $userinfo->rank != '0')
                                            {
                                                //special rank
                                                $database->setQuery("SELECT * FROM #__fb_ranks WHERE rank_id = '$userinfo->rank'");
                                            } else {
                                                //post count rank
                                                $database->setQuery("SELECT * FROM #__fb_ranks WHERE ((rank_min <= $numPosts) AND (rank_special = 0))  ORDER BY rank_min DESC LIMIT 1");
                                            }
                                            $database->loadObject($rank);
                                            $rText = $rank->rank_title;
                                            $rImg = JB_URLRANKSPATH . $rank->rank_image;
                                        }

                                        if ($uIsMod and !$showSpRank)
                                        {
                                            $rText = _RANK_MODERATOR;
                                            $rImg = JB_URLRANKSPATH . 'rankmod.gif';
                                        }

                                        if ($uIsAdm and !$showSpRank)
                                        {
                                            $rText = _RANK_ADMINISTRATOR;
                                            $rImg = JB_URLRANKSPATH . 'rankadmin.gif';
                                        }

                                        if ($fbConfig->rankimages) {
                                            $msg_userrankimg = '<img src="' . $rImg . '" alt="" />';
                                        }

                                        $msg_userrank = $rText;





                                        $useGraph = 0; //initialization

                                        if (!$fbConfig->poststats)
                                        {
                                            $msg_posts = '<div class="viewcover">' .
                                              "<table  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr>" .
                                              "<strong>" . _POSTS . " $numPosts" . "</strong>" .
                                              "</td></tr>" . "</table></div>";

                                            $useGraph = 0;
                                        }
                                        else
                                        {
                                            $myGraph = new phpGraph;
                                            //$myGraph->SetGraphTitle(_POSTS);
                                            $myGraph->AddValue(_POSTS, $numPosts);
                                            $myGraph->SetRowSortMode(0);
                                            $myGraph->SetBarImg(JB_URLGRAPHPATH . "col" . $fbConfig->statscolor . "m.png");
                                            $myGraph->SetBarImg2(JB_URLEMOTIONSPATH . "graph.gif");
                                            $myGraph->SetMaxVal($maxPosts);
                                            $myGraph->SetShowCountsMode(2);
                                            $myGraph->SetBarWidth(4); //height of the bar
                                            $myGraph->SetBorderColor("#333333");
                                            $myGraph->SetBarBorderWidth(0);
                                            $myGraph->SetGraphWidth(64); //should match column width in the <TD> above -5 pixels
                                            //$myGraph->BarGraphHoriz();
                                            $useGraph = 1;
                                        }
                                    }
                                }

                                //karma points and buttons
                                if ($fbConfig->showkarma && $fmessage->userid != '0')
                                {
                                    $karmaPoints = $userinfo->karma;
                                    $karmaPoints = (int)$karmaPoints;
                                    $msg_karma = "<strong>" . _KARMA . ":</strong> $karmaPoints";

                                    if ($my->id != '0' && $my->id != $fmessage->userid)
                                    {
                                        $msg_karmaminus = fb_link::GetKarmaLink('decrease', $catid, $fmessage->id, $fmessage->userid, '<img src="'.($fbIcons['karmaminus']?(JB_URLICONSPATH . "" . $fbIcons['karmaminus']):(JB_URLEMOTIONSPATH . "karmaminus.gif")).'" alt="Karma-" border="0" title="' . _KARMA_SMITE . '" align="middle" />' );
                                        $msg_karmaplus  = fb_link::GetKarmaLink('increase', $catid, $fmessage->id, $fmessage->userid, '<img src="'.($fbIcons['karmaplus']?(JB_URLICONSPATH . "" . $fbIcons['karmaplus']):(JB_URLEMOTIONSPATH . "karmaplus.gif")).'" alt="Karma+" border="0" title="' . _KARMA_APPLAUD . '" align="middle" />' );
                                    }
                                }
                                /*let's see if we should use Missus integration */
                                if ($fbConfig->pm_component == "missus" && $fmessage->userid && $my->id)
                                {
                                    //we should offer the user a Missus link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms
                                    = "<a href=\"" . sefRelToAbs('index.php?option=com_missus&amp;func=newmsg&amp;user=' . $fmessage->userid . '&amp;subject=' . _GEN_FORUM . ': ' . urlencode(utf8_encode($fmessage->subject))) . "\"><img src='";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= JB_URLICONSPATH . "" . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= JB_URLICONSPATH  . $fbIcons['pms'];;
                                    }

                                    $msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }

                                /*let's see if we should use JIM integration */
                                if ($fbConfig->pm_component == "jim" && $fmessage->userid && $my->id)
                                {
                                    //we should offer the user a JIM link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . sefRelToAbs('index.php?option=com_jim&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src='";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= JB_URLICONSPATH . "" . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= JB_URLICONSPATH  .  $fbIcons['pms'];;
                                    }

                                    $msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }
                                /*let's see if we should use uddeIM integration */
                                if ($fbConfig->pm_component == "uddeim" && $fmessage->userid && $my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . sefRelToAbs('index.php?option=com_uddeim&amp;task=new&recip=' . $fmessage->userid) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= JB_URLICONSPATH . '' . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= JB_URLEMOTIONSPATH . "sendpm.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }
                                /*let's see if we should use myPMS2 integration */
                                if ($fbConfig->pm_component == "pms" && $fmessage->userid && $my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . sefRelToAbs('index.php?option=com_pms&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= JB_URLICONSPATH . "" . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= JB_URLEMOTIONSPATH . "sendpm.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }

                                // online - ofline status
                                if ($fmessage->userid > 0)
                                {
                                    $sql = "SELECT count(userid) FROM #__session WHERE userid=" . $fmessage->userid;
                                    $database->setQuery($sql);
                                    $isonline = $database->loadResult();

                                    if ($isonline && $userinfo->showOnline ==1 ) {
                                        $msg_online .= $fbIcons['onlineicon'] ? '<img src="'
                                        . JB_URLICONSPATH . '' . $fbIcons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . JB_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
                                    }
                                    else {
                                        $msg_online .= $fbIcons['offlineicon'] ? '<img src="'
                                        . JB_URLICONSPATH . '' . $fbIcons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . JB_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
                                    }
                                }
                                /* ClexusPM integration */
                                if ($fbConfig->pm_component == "clexuspm" && $fmessage->userid && $my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->aid;
                                    $msg_pms = "<a href=\"" . sefRelToAbs('index.php?option=com_mypms&amp;task=new&amp;to=' . $fmessage->userid . '&title=' . $fmessage->subject) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= JB_URLICONSPATH . "" . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= JB_JLIVEURL . "/components/com_mypms/images/icons/message_12px.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                    //mypms pro profile link
                                    $msg_profile = "<a href=\"" . MyPMSTools::getProfileLink($fmessage->userid) . "\"><img src=\"";

                                    if ($fbIcons['userprofile']) {
                                        $msg_profile .= JB_URLICONSPATH . '' . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profile .= JB_JLIVEURL . "/components/com_mypms/images/managecontact_icon.gif";
                                    }

                                    $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                    //mypms add buddy link
                                    $msg_buddy = "<a href=\"" . sefRelToAbs('index.php?option=com_mypms&amp;user=' . $PMSName . '&amp;task=addbuddy') . "\"><img src=\"";

                                    if ($fbIcons['pms2buddy']) {
                                        $msg_buddy .= JB_URLICONSPATH . "" . $fbIcons['pms2buddy'];
                                    }
                                    else {
                                        $msg_buddy .= JB_JLIVEURL . "/components/com_mypms/images/messages/addbuddy.gif";
                                    }

                                    $msg_buddy .= "\" alt=\"" . _VIEW_ADDBUDDY . "\" border=\"0\" title=\"" . _VIEW_ADDBUDDY . "\" /></a>";
                                    $database->setQuery("SELECT icq,ym,msn,aim,website,location FROM #__mypms_profiles WHERE user='" . $PMSName . "'");
                                    $mostables = $database->loadObjectList();
                                    	check_dberror("Unable to load mypms profile.");

                                    foreach ($mostables as $mostables)
                                    {
                                        if ($mostables->aim)
                                        $msg_aim = "<a href=\"aim:goim?screenname=" . str_replace(" ", "+", $mostables->aim) . "\"><img src=\"" . JB_URLEMOTIONSPATH . "aim.png\" border=0 alt=\"\" /></a>";

                                        if ($mostables->icq)
                                        $msg_icq = "<a href=\"http://www.icq.com/whitepages/wwp.php?uin=" . $mostables->icq . "\"><img src=\"" . JB_URLEMOTIONSPATH . "icq.png\" border=0 alt=\"\" /></a>";

                                        if ($mostables->msn)
                                        $msg_msn = "<a href=\"" . sefRelToAbs('index.php?option=com_mypms&amp;task=showprofile&amp;user=' . $PMSName) . "\"><img src=\"" . JB_URLEMOTIONSPATH . "msn.png\" border=0 alt=\"\" /></a>";

                                        if ($mostables->ym)
                                        $msg_yahoo = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $mostables->ym . "&.src=pg\"><img src=\"http://opi.yahoo.com/online?u=" . $mostables->ym . "&m=g&t=0\" border=0 alt=\"\" /></a>";

                                        if ($mostables->location)
                                        $msg_loc = $mostables->location;
                                    }

                                    unset ($mostables);
                                }

                                //Check if the Community Builder settings are on, and set the variables accordingly.
                                if ($fbConfig->fb_profile == "cb")
                                {
                                    if ($fbConfig->cb_profile && $fmessage->userid > 0)
                                    {
                                        $msg_prflink = sefRelToAbs('index.php?option=com_comprofiler&amp;task=userProfile&amp;user=' . $fmessage->userid . '');
                                        $msg_profile = "<a href=\"" . sefRelToAbs('index.php?option=com_comprofiler&amp;task=userProfile&amp;user=' . $fmessage->userid . '') . "\">                                              <img src=\"";

                                        if ($fbIcons['userprofile']) {
                                            $msg_profile .= JB_URLICONSPATH . "" . $fbIcons['userprofile'];
                                        }
                                        else {
                                            $msg_profile .= JB_JLIVEURL . "/components/com_comprofiler/images/profiles.gif";
                                        }

                                        $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                    }
                                }
                                else if ($fbConfig->fb_profile == "clexuspm")
                                {
                                    //mypms pro profile link
                                    $msg_prflink = MyPMSTools::getProfileLink($fmessage->userid);
                                    $msg_profile = "<a href=\"" . MyPMSTools::getProfileLink($fmessage->userid) . "\"><img src=\"";

                                    if ($fbIcons['userprofile']) {
                                        $msg_profile .= JB_URLICONSPATH . '' . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profile .= JB_JLIVEURL . "/components/com_mypms/images/managecontact_icon.gif";
                                    }

                                    $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                }
                                else if ($userinfo->gid > 0)
                                {
                                    //FireBoard Profile link.
                                    $msg_prflink = sefRelToAbs(JB_LIVEURLREL.'&amp;func=fbprofile&amp;task=showprf&amp;userid=' . $fmessage->userid);
                                    $msg_profile = "<a href=\"" . sefRelToAbs(JB_LIVEURLREL.'&amp;func=fbprofile&amp;task=showprf&amp;userid=' . $fmessage->userid) . "\"> <img src=\"";

                                    if ($fbIcons['userprofile']) {
                                        $msg_profile .= JB_URLICONSPATH . "" . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profile .= JB_URLICONSPATH . "profile.gif";
                                    }

                                    $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                }


                                // Begin: Additional Info //

                                if ($userinfo->gender != '') {
                                    $gender = _FB_NOGENDER;
                                    if ($userinfo->gender ==1)  {
                                        $gender = ''._FB_MYPROFILE_MALE.'';
                                        $msg_gender = $fbIcons['msgmale'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgmale'] . '" border="0" alt="'._FB_MYPROFILE_GENDER.': '.$gender.'" title="'._FB_MYPROFILE_GENDER.': '.$gender.'" />' : ''._FB_MYPROFILE_GENDER.': '.$gender.'';
                                    }

                                    if ($userinfo->gender ==2)  {
                                        $gender = ''._FB_MYPROFILE_FEMALE.'';
                                        $msg_gender = $fbIcons['msgfemale'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgfemale'] . '" border="0" alt="'._FB_MYPROFILE_GENDER.': '.$gender.'" title="'._FB_MYPROFILE_GENDER.': '.$gender.'" />' : ''._FB_MYPROFILE_GENDER.': '.$gender.'';
                                    }

                                }

                                if ($userinfo->personalText != '') {
                                    $msg_personal = $userinfo->personalText;
                                }

                                if ($userinfo->ICQ != '') {
                                    $msg_icq = '<a href="http://www.icq.com/people/cmd.php?uin='.$userinfo->ICQ.'&action=message"><img src="http://status.icq.com/online.gif?icq='.$userinfo->ICQ.'&img=5" title="ICQ#: '.$userinfo->ICQ.'" alt="ICQ#: '.$userinfo->ICQ.'" /></a>';
                                }
                                if ($userinfo->location != '') {
                                    $msg_location = $fbIcons['msglocation'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msglocation'] . '" border="0" alt="'._FB_MYPROFILE_LOCATION.': '.$userinfo->location.'" title="'._FB_MYPROFILE_LOCATION.': '.$userinfo->location.'" />' : ' '._FB_MYPROFILE_LOCATION.': '.$userinfo->location.'';
                                }
                                if ($userinfo->birthdate !='0001-01-01' AND $userinfo->birthdate !='0000-00-00' and $userinfo->birthdate !='') {
                                    $msg_birthdate = $fbIcons['msgbirthdate'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgbirthdate'] . '" border="0" alt="'._FB_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'" title="'._FB_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'" />' : ' '._FB_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'';
                                }

                                if ($userinfo->AIM != '') {
                                    $msg_aim = $fbIcons['msgaim'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgaim'] . '" border="0" alt="'.$userinfo->AIM.'" title="AIM: '.$userinfo->AIM.'" />' : 'AIM: '.$userinfo->AIM.'';
                                }
                                if ($userinfo->MSN != '') {
                                    $msg_msn = $fbIcons['msgmsn'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgmsn'] . '" border="0" alt="'.$userinfo->MSN.'" title="MSN: '.$userinfo->MSN.'" />' : 'MSN: '.$userinfo->MSN.'';
                                }
                                if ($userinfo->YIM != '') {
                                    $msg_yim = $fbIcons['msgyim'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgyim'] . '" border="0" alt="'.$userinfo->YIM.'" title="YIM: '.$userinfo->YIM.'" />' : ' YIM: '.$userinfo->YIM.'';
                                }
                                if ($userinfo->SKYPE != '') {
                                    $msg_skype = $fbIcons['msgskype'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msgskype'] . '" border="0" alt="'.$userinfo->SKYPE.'" title="SKYPE: '.$userinfo->SKYPE.'" />' : 'SKYPE: '.$userinfo->SKYPE.'';
                                }
                                if ($userinfo->GTALK != '') {
                                    $msg_gtalk = $fbIcons['msggtalk'] ? '<img src="'. JB_URLICONSPATH . '' . $fbIcons['msggtalk'] . '" border="0" alt="'.$userinfo->GTALK.'" title="GTALK: '.$userinfo->GTALK.'" />' : 'GTALK: '.$userinfo->GTALK.'';
                                }
                                if ($userinfo->websiteurl != '') {
                                    $msg_website = $fbIcons['msgwebsite'] ? '<a href="http://'.$userinfo->websiteurl.'" target="_blank"><img src="'. JB_URLICONSPATH . '' . $fbIcons['msgwebsite'] . '" border="0" alt="'.$userinfo->websitename.'" title="'.$userinfo->websitename.'" /></a>' : '<a href="http://'.$userinfo->websiteurl.'" target="_blank">'.$userinfo->websitename.'</a>';
                                }

                                // Finish: Additional Info //


                                //Show admins the IP address of the user:
                                if ($is_Moderator)
                                {
                                    $msg_ip = $fmessage->ip;
                                }

                                $fb_subject_txt = $fmessage->subject;

                                $table = array_flip(get_html_translation_table(HTML_ENTITIES));

                                $fb_subject_txt = strtr($fb_subject_txt, $table);
                                $fb_subject_txt = smile::fbHtmlSafe($fb_subject_txt);
                                $fb_subject_txt = stripslashes($fb_subject_txt);

                                $msg_subject = stripslashes($fb_subject_txt);
                                $msg_date = date(_DATETIME, $fmessage->time);
                                $fb_message_txt = stripslashes($fmessage->message);

                                $fb_message_txt = smile::smileReplace($fb_message_txt, 0, $fbConfig->disemoticons, $smileyList);
                                $fb_message_txt = nl2br($fb_message_txt);
                                //$fb_message_txt = str_replace("<P>&nbsp;</P><br />","",$fb_message_txt);
                                //$fb_message_txt = str_replace("</P><br />","</P>",$fb_message_txt);
                                //$fb_message_txt = str_replace("<P><br />","<P>",$fb_message_txt);

                                //filter bad words
                                if ($fbConfig->badwords && class_exists('Badword') && Badword::filter($fb_message_txt, $my)) {
                                	if (method_exists('Badword','flush')) {
                                		$fb_message_txt = Badword::flush($fb_message_txt, $my);
                                	} else {
                               			$fb_message_txt = _COM_A_BADWORDS_NOTICE;
                                	}
                                }

                                // Code tag: restore TABS as we had to 'hide' them from the rest of the logic
                                $fb_message_txt = str_replace("__FBTAB__", "&#009;", stripslashes($fb_message_txt));

                                // Joomla Mambot Support , Thanks hacksider
                                if ($fbConfig->jmambot)
                                {
                                    global $_MAMBOTS;
                                    $row = new t();
                                    $row->text = $fb_message_txt;
                                    $_MAMBOTS->loadBotGroup( 'content' );
                                    $params =& new mosParameters( '' );
                                    $results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, 0 ), true );
                                    $msg_text = $row->text;
                                }
                                else
                                {
                                	$msg_text = $fb_message_txt;
                                }
                                // Finish Joomla Mambot Support

                                if ($fbConfig->cb_profile)
                                {
                                    $database->setQuery("select fbsignature from #__comprofiler where user_id=$fmessage->userid");
                                    $signature = $database->loadResult();
                                }
                                else {
                                    $signature = $userinfo->signature;
                                }

                                if ($signature)
                                {
                                    $signature = stripslashes(smile::smileReplace($signature, 0, $fbConfig->disemoticons, $smileyList));
                                    $signature = nl2br($signature);
                                    //wordwrap:
                                    $signature = smile::htmlwrap($signature, $fbConfig->wrap);
                                    //restore the \n (were replaced with _CTRL_) occurences inside code tags, but only after we have striplslashes; otherwise they will be stripped again
                                    //$signature = str_replace("_CRLF_", "\\n", stripslashes($signature));
                                    $msg_signature = $signature;
                                }

                                if ((($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite == 1) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                                {
                                    //user is allowed to reply/quote
                                    $msg_reply = fb_link::GetTopicPostReplyLink('reply', $catid, $fmessage->id , $fbIcons['reply']?'<img src="' . JB_URLICONSPATH . $fbIcons['reply'] . '" alt="Reply" border="0" title="' . _VIEW_REPLY . '" />':_GEN_REPLY);
                                    $msg_quote = fb_link::GetTopicPostReplyLink('quote', $catid, $fmessage->id , $fbIcons['quote']?'<img src="' . JB_URLICONSPATH . $fbIcons['quote'] . '" alt="Quote" border="0" title="' . _VIEW_QUOTE . '" />':_GEN_QUOTE);
                                }
                                else
                                {
                                    //user is not allowed to write a post
                                    if ($topicLock == 1) {
                                        $msg_closed = _POST_LOCK_SET;
                                    }
                                    else {
                                        $msg_closed = _VIEW_DISABLED;
                                    }
                                }

                                $showedEdit = 0; //reset this value
                                //Offer an moderator the delete link
                                if ($is_Moderator)
                                {
                                    $msg_delete = fb_link::GetTopicPostLink('delete', $catid, $fmessage->id , $fbIcons['delete']?'<img src="' . JB_URLICONSPATH . $fbIcons['delete'] . '" alt="Delete" border="0" title="' . _VIEW_DELETE . '" />':_GEN_DELETE);
                                    $msg_merge = fb_link::GetTopicPostLink('merge', $catid, $fmessage->id , $fbIcons['merge']?'<img src="' . JB_URLICONSPATH . $fbIcons['merge'] . '" alt="Merge" border="0" title="' . _VIEW_MERGE . '" />':_GEN_MERGE);
                                    $msg_split = fb_link::GetTopicPostLink('split', $catid, $fmessage->id , $fbIcons['split']?'<img src="' . JB_URLICONSPATH . $fbIcons['split'] . '" alt="Split" border="0" title="' . _VIEW_SPLIT . '" />':_GEN_SPLIT);
                                }

                                if ($fbConfig->useredit && $my_id != "")
                                {
                                    //Now, if the viewer==author and the viewer is allowed to edit his/her own post then offer an 'edit' link
                                    $allowEdit = 0;
                                    if ($my_id == $fmessage->userid)
                                    {
                                        if(((int)$fbConfig->useredittime)==0)
                                        {
                                            $allowEdit = 1;
                                        }
                                        else
                                        {
                                            //Check whether edit is in time
                                            $modtime = $fmessage->modified_time;
                                            if(!$modtime)
                                            {
                                                $modtime = $fmessage->time;
                                            }
                                            if(($modtime + ((int)$fbConfig->useredittime)) >= FBTools::fbGetInternalTime())
                                            {
                                                $allowEdit = 1;
                                            }
                                        }
                                    }
                                    if($allowEdit)
                                    {
                                        $msg_edit = fb_link::GetTopicPostLink('edit', $catid, $fmessage->id , $fbIcons['edit']?'<img src="' . JB_URLICONSPATH . $fbIcons['edit'] . '" alt="Edit" border="0" title="' . _VIEW_EDIT . '" />':_GEN_EDIT);
                                        $showedEdit = 1;
                                    }
                                }

                                if ($is_Moderator && $showedEdit != 1)
                                {
                                    //Offer a moderator always the edit link except when it is already showing..
                                    $msg_edit = fb_link::GetTopicPostLink('edit', $catid, $fmessage->id , $fbIcons['edit']?'<img src="' . JB_URLICONSPATH . $fbIcons['edit'] . '" alt="Edit" border="0" title="' . _VIEW_EDIT . '" />':_GEN_EDIT);
                                }

                                if ($is_Moderator && $fmessage->parent == '0')
                                {
                                    // offer the moderator always the move link to relocate a topic to another forum
                                    // and the (un)sticky bit links
                                    // and the (un)lock links
                                    // but ONLY when it is a topic and not a reply
                                    $msg_move = fb_link::GetTopicPostLink('move', $catid, $fmessage->id , $fbIcons['move']?'<img src="' . JB_URLICONSPATH . $fbIcons['move'] . '" alt="Move" border="0" title="' . _VIEW_MOVE . '" />':_GEN_MOVE);

                                    if ($fmessage->ordering == 0)
                                    {
                                        $msg_sticky = fb_link::GetTopicPostLink('sticky', $catid, $fmessage->id , $fbIcons['sticky']?'<img src="' . JB_URLICONSPATH . $fbIcons['sticky'] . '" alt="Sticky" border="0" title="' . _VIEW_STICKY . '" />':_GEN_STICKY);
                                    }
                                    else
                                    {
                                        $msg_sticky = fb_link::GetTopicPostLink('unsticky', $catid, $fmessage->id , $fbIcons['unsticky']?'<img src="' . JB_URLICONSPATH . $fbIcons['unsticky'] . '" alt="Unsticky" border="0" title="' . _VIEW_UNSTICKY . '" />':_GEN_UNSTICKY);
                                    }

                                    if ($fmessage->locked == 0)
                                    {
                                        $msg_lock = fb_link::GetTopicPostLink('lock', $catid, $fmessage->id , $fbIcons['lock']?'<img src="' . JB_URLICONSPATH . $fbIcons['lock'] . '" alt="Lock" border="0" title="' . _VIEW_LOCK . '" />':_GEN_LOCK);
                                    }
                                    else
                                    {
                                        $msg_lock = fb_link::GetTopicPostLink('unlock', $catid, $fmessage->id , $fbIcons['unlock']?'<img src="' . JB_URLICONSPATH . $fbIcons['unlock'] . '" alt="Unlock" border="0" title="' . _VIEW_UNLOCK . '" />':_GEN_UNLOCK);
                                    }
                                }

                                //(JJ)
                                if (file_exists(JB_ABSTMPLTPATH . '/message.php')) {
                                    include (JB_ABSTMPLTPATH . '/message.php');
                                }
                                else {
                                    include (JB_ABSPATH . '/template/default/message.php');
                                }

                                unset(
                                $msg_id,
                                $msg_username,
                                $msg_avatar,
                                $msg_usertype,
                                $msg_userrank,
                                $msg_userrankimg,
                                $msg_posts,
                                $msg_move,
                                $msg_karma,
                                $msg_karmaplus,
                                $msg_karmaminus,
                                $msg_ip,
                                $msg_ip_link,
                                $msg_date,
                                $msg_subject,
                                $msg_text,
                                $msg_signature,
                                $msg_reply,
                                $msg_birthdate,
                                $msg_quote,
                                $msg_edit,
                                $msg_closed,
                                $msg_delete,
                                $msg_sticky,
                                $msg_lock,
                                $msg_aim,
                                $msg_icq,
                                $msg_msn,
                                $msg_yim,
                                $msg_skype,
                                $msg_gtalk,
                                $msg_website,
                                $msg_yahoo,
                                $msg_buddy,
                                $msg_profile,
                                $msg_online,
                                $msg_pms,
                                $msg_loc,
                                $msg_regdate,
                                $msg_prflink,
                                $msg_location,
                                $msg_gender,
                                $msg_personal,
                                $myGraph);
                                $useGraph = 0;
                            } // end for
                        }
                        ?>
                    </td>
                </tr>

                <?php
                if ($view != "flat")
                {
                ?>

                    <tr>
                        <td>
                            <?php
                            if (file_exists(JB_ABSTMPLTPATH . '/thread.php')) {
                                include (JB_ABSTMPLTPATH . '/thread.php');
                            }
                            else {
                                include (JB_ABSPATH . '/template/default/thread.php');
                            }
                            ?>
                        </td>
                    </tr>

                <?php
                }
                ?>
            </table>


            <!-- bottom nav -->
            <table border = "0" cellspacing = "0" class = "jr-topnav" cellpadding = "0" width="100%">
                <tr>
                    <td class = "jr-topnav-left">
                        <?php
                        //go to bottom
                        echo '<a name="forumbottom" /> ';
                        echo fb_link::GetSamePageAnkerLink('forumtop', $fbIcons['toparrow'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);

                        if (($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite)
                        {
                            //this user is allowed to post a new topic:
                            echo fb_link::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                        }

                        if ((($fbConfig->pubwrite == 0 && $my_id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                        {
                            //this user is allowed to reply to this topic:
                            echo fb_link::GetTopicPostReplyLink('reply', $catid, $thread, $fbIcons['topicreply'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['topicreply'] . '" alt="' . _GEN_POST_REPLY . '" title="' . _GEN_POST_REPLY . '" border="0" />' : _GEN_POST_REPLY);
                        }

                        if ($fb_cansubscribe == 1)
                        {
                            // this user is allowed to subscribe - check performed further up to eliminate duplicate checks
                            // for top and bottom navigation
                            echo fb_link::GetTopicPostLink('subscribe', $catid, $id, $fbIcons['subscribe'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['subscribe'] . '" alt="' . _VIEW_SUBSCRIBETXT . '" title="' . _VIEW_SUBSCRIBETXT . '" border="0" />' : _VIEW_SUBSCRIBETXT);
                        }

                        if ($my_id != 0 && $fbConfig->allowsubscriptions && $fb_cansubscribe == 0)
                        {
                            // this user is allowed to unsubscribe
                            echo fb_link::GetTopicPostLink('unsubscribe', $catid, $id, $fbIcons['unsubscribe'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['unsubscribe'] . '" alt="' . _VIEW_UNSUBSCRIBETXT . '" title="' . _VIEW_UNSUBSCRIBETXT . '" border="0" />' : _VIEW_UNSUBSCRIBETXT);
                        }

                        if ($fb_canfavorite == 1)
                        {
                            // this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
                            // for top and bottom navigation
                            echo fb_link::GetTopicPostLink('favorite', $catid, $id, $fbIcons['favorite'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['favorite'] . '" alt="' . _VIEW_FAVORITETXT . '" title="' . _VIEW_FAVORITETXT . '" border="0" />' : _VIEW_FAVORITETXT);
                        }

                        if ($my_id != 0 && $fbConfig->allowfavorites && $fb_canfavorite == 0)
                        {
                            // this user is allowed to unfavorite
                            echo fb_link::GetTopicPostLink('unfavorite', $catid, $id, $fbIcons['unfavorite'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['unfavorite'] . '" alt="' . _VIEW_UNFAVORITETXT . '" title="' . _VIEW_UNFAVORITETXT . '" border="0" />' : _VIEW_UNFAVORITETXT);
                        }
                        // FINISH: FAVORITES
                        ?>
                    </td>

                    <td class = "jr-topnav-right">
                        <?php
                        if ($total != 0)
                        {
                        ?>

                            <div class = "jr-pagenav">
                                <ul>
                                    <li class = "jr-pagenav-text"><?php echo _PAGE ?></li>

                                    <li class = "jr-pagenav-nb">

                                    <?php
                                    // TODO: fxstein - Need to perform SEO cleanup
                                    echo $pageNav->writePagesLinks("index.php?option=com_fireboard&amp;Itemid=$Itemid&amp;func=view&amp;id=$id&amp;catid=$catid");
                                    ?>

                                    </li>
                                </ul>
                            </div>

                        <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <!-- /bottom nav -->
            <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0"  class="fb_bottom_pathway">
                <tr>
                    <td>
                        <div id="fb_bottom_pathway">
                            <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL);?>">
    <?php echo $fbIcons['forumlist'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['forumlist'] . '" border="0" alt="' . _GEN_FORUMLIST . '" title="' . _GEN_FORUMLIST . '">' : _GEN_FORUMLIST; ?> </a>

                            <?php
                            if (file_exists($mosConfig_absolute_path . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png')) {
                                echo ' <img src="' . JB_JLIVEURL . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png" alt="" /> ';
                            }
                            else {
                                echo ' <img src="' . JB_JLIVEURL . '/images/M_images/arrow.png" alt="" /> ';
                            }
                            ?>

                            <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=listcat&amp;catid='.$objCatParentInfo->id);?>" rel="nofollow"> <?php echo $objCatParentInfo->name; ?> </a>

                            <?php
                            if (file_exists($mosConfig_absolute_path . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png')) {
                                echo ' <img src="' . JB_JLIVEURL . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png" alt="" /> ';
                            }
                            else {
                                echo ' <img src="' . JB_JLIVEURL . '/images/M_images/arrow.png" alt="" /> ';
                            }
                            ?>

                            <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid);?>" rel="nofollow"> <?php echo $objCatInfo->name; ?> </a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                    </td>
                </tr>
            </table>
            <!-- Begin: Forum Jump -->
            <div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
            <table class = "fb_blocktable" id="fb_bottomarea"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                <thead>
                    <tr>
                        <th  class = "th-right">
                            <?php
                            //(JJ) FINISH: CAT LIST BOTTOM
                            if ($fbConfig->enableforumjump)
                            require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                            ?>
                        </th>
                    </tr>
                </thead>
                <tbody><tr><td></td></tr></tbody>
            </table>
            </div>
</div>
</div>
</div>
</div>
                <!-- Finish: Forum Jump -->

<?php
    }
}
else {
    echo _FB_NO_ACCESS;
}

?>
