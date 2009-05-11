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
$kunena_my = &JFactory::getUser();

global $is_Moderator;

$kunena_acl = &JFactory::getACL();
//securing form elements
$catid = (int)$catid;
$id = (int)$id;

$smileyList = smile::getEmoticons(0);

//ob_start();
$showedEdit = 0;
require_once (KUNENA_PATH_LIB .DS. 'kunena.authentication.php');
require_once (KUNENA_PATH_LIB .DS. 'kunena.statsbar.php');

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

$topicLock = 0;

$kunena_db->setQuery("SELECT * FROM #__fb_messages AS a LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE a.id={$id} and a.hold=0");
unset($this_message);
$this_message = $kunena_db->loadObject();
check_dberror('Unable to load message.');

if ((in_array($catid, $allow_forum)) || (isset($this_message->catid) && in_array($this_message->catid, $allow_forum)))
{
    $view = $view == "" ? $settings[current_view] : $view;
    setcookie("fboard_settings[current_view]", $view, time() + 31536000, '/');

    $topicLock = $this_message->locked;

    if (count($this_message) < 1) {
        echo '<p align="center">' . _MODERATION_INVALID_ID . '</p>';
    }
    else
    {
        $thread = $this_message->parent == 0 ? $this_message->id : $this_message->thread;

        // Test if this is a valid SEO URL if not we should redirect using a 301 - permanent redirect
        if ($view == "flat" && ($thread != $this_message->id || $catid != $this_message->catid))
        {
        	// Invalid SEO URL detected!
        	// Create permanent re-direct and quit
        	// This query to calculate the page this reply is sitting on within this thread
        	$query = "SELECT count(*)
        				FROM #__fb_messages AS a
        				WHERE a.thread=$thread
        					AND a.id<=$this_message->id";
        	$kunena_db->setQuery($query);
        	$replyCount = $kunena_db->loadResult();
        		check_dberror('Unable to calculate replyCount.');

        	$replyPage = $replyCount > $fbConfig->messages_per_page ? ceil($replyCount / $fbConfig->messages_per_page) : 1;

        	header("HTTP/1.1 301 Moved Permanently");
        	header("Location: " . htmlspecialchars_decode(CKunenaLink::GetThreadPageURL($fbConfig, 'view', $this_message->catid, $thread, $replyPage, $fbConfig->messages_per_page, $this_message->id)));

        	$mainframe->close();
        }

        if ($kunena_my->id)
        {
            //mark this topic as read
            $kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid={$kunena_my->id}");
            $readTopics = $kunena_db->loadResult();

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

            $kunena_db->setQuery("UPDATE #__fb_sessions set readtopics='{$readTopics}' WHERE userid={$kunena_my->id}");
            $kunena_db->query();
        }

        //update the hits counter for this topic & exclude the owner
        if ($this_message->userid != $kunena_my->id) {
            $kunena_db->setQuery("UPDATE #__fb_messages SET hits=hits+1 WHERE id=$thread AND parent=0");
            $kunena_db->query();
        }
        // changed to 0 to fix the missing post when the thread splits over multiple pages
        $i = 0;

        $ordering = ($fbConfig->default_sort == 'desc' ? 'desc' : 'asc'); // Just to make sure only valid options make it

        // Get messages of current thread
        $kunena_db->setQuery("(SELECT * FROM #__fb_messages AS a "
           ."\n LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE a.id='$thread' AND a.hold=0 AND a.catid='$catid') UNION (SELECT * FROM #__fb_messages AS a "
           ."\n LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE a.thread='$thread' AND a.hold=0 AND a.catid='$catid') ORDER BY time $ordering");

        if ($view != "flat") $flat_messages[] = $this_message;

        foreach ($kunena_db->loadObjectList()as $message)
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

        if ($ordering=='desc')
        {
            $idmatch = $i - $idmatch;
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

            $limitstart = intval(JRequest::getVar('limitstart', $limitstart, $HASH = "REQUEST"));
            $total = count($flat_messages);

            if ($total > $limit)
            {
				jimport('joomla.html.pagination');

					$pageNav = new JPagination( $total, $limitstart, $limit );
                // slice out elements based on limits
                $flat_messages = array_slice($flat_messages, $pageNav->limitstart, $pageNav->limit);
            }
            else {
                $total = 0;
            }
        }

        //Get the category name for breadcrumb
        unset($objCatInfo, $objCatParentInfo);
        $kunena_db->setQuery("SELECT * from #__fb_categories where id='$catid'");
        $objCatInfo = $kunena_db->loadObject();
        //Get Parent's cat.name for breadcrumb
        $kunena_db->setQuery("SELECT name,id from #__fb_categories WHERE id='$objCatInfo->parent'");
        $objCatParentInfo = $kunena_db->loadObject();

        //Perform subscriptions check only once
        $fb_cansubscribe = 0;
        if ($fbConfig->allowsubscriptions && ("" != $kunena_my->id || 0 != $kunena_my->id))
        {
            $kunena_db->setQuery("SELECT thread from #__fb_subscriptions where userid=$kunena_my->id and thread='$thread'");
            $fb_subscribed = $kunena_db->loadResult();

            if ($fb_subscribed == "") {
                $fb_cansubscribe = 1;
            }
        }
        //Perform favorites check only once
        $fb_canfavorite = 0;
        if ($fbConfig->allowfavorites && ("" != $kunena_my->id || 0 != $kunena_my->id))
        {
            $kunena_db->setQuery("SELECT thread from #__fb_favorites where userid=$kunena_my->id and thread='$thread'");
            $fb_favorited = $kunena_db->loadResult();

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
            if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
                require_once (KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
            }
            else {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
            }
            ?>
        </div>
        <!-- top nav -->
        <?php if($objCatInfo->headerdesc) { ?>
        <div class="fb_forum-headerdesc"><?php
        			$headerdesc = stripslashes(smile::smileReplace($objCatInfo->headerdesc, 0, $fbConfig->disemoticons, $smileyList));
			        $headerdesc = nl2br($headerdesc);
			        //wordwrap:
			        $headerdesc = smile::htmlwrap($headerdesc, $fbConfig->wrap);
					echo $headerdesc;
        ?></div>
        <?php } ?>
        <table border = "0" cellspacing = "0" class = "jr-topnav" cellpadding = "0" width="100%">
            <tr>
                <td class = "jr-topnav-left">
                    <?php
                    //go to bottom
                    echo '<a name="forumtop" /> ';
                    echo CKunenaLink::GetSamePageAnkerLink('forumbottom', $fbIcons['bottomarrow'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);

                    if (($fbConfig->pubwrite == 0 && $kunena_my->id != 0) || $fbConfig->pubwrite)
                    {
                        //this user is allowed to post a new topic:
                        echo CKunenaLink::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                    }

                    if ((($fbConfig->pubwrite == 0 && $kunena_my->id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                    {
                        //this user is allowed to reply to this topic:
                        echo CKunenaLink::GetTopicPostReplyLink('reply', $catid, $thread, $fbIcons['topicreply'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['topicreply'] . '" alt="' . _GEN_POST_REPLY . '" title="' . _GEN_POST_REPLY . '" border="0" />' : _GEN_POST_REPLY);
                    }

                    if ($fb_cansubscribe == 1)
                    {
                        // this user is allowed to subscribe - check performed further up to eliminate duplicate checks
                        // for top and bottom navigation
                        echo CKunenaLink::GetTopicPostLink('subscribe', $catid, $id, $fbIcons['subscribe'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['subscribe'] . '" alt="' . _VIEW_SUBSCRIBETXT . '" title="' . _VIEW_SUBSCRIBETXT . '" border="0" />' : _VIEW_SUBSCRIBETXT);
                    }

                    if ($kunena_my->id != 0 && $fbConfig->allowsubscriptions && $fb_cansubscribe == 0)
                    {
                        // this user is allowed to unsubscribe
                        echo CKunenaLink::GetTopicPostLink('unsubscribe', $catid, $id, $fbIcons['unsubscribe'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unsubscribe'] . '" alt="' . _VIEW_UNSUBSCRIBETXT . '" title="' . _VIEW_UNSUBSCRIBETXT . '" border="0" />' : _VIEW_UNSUBSCRIBETXT);
                    }

                    if ($fb_canfavorite == 1)
                    {
                        // this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
                        // for top and bottom navigation
                        echo CKunenaLink::GetTopicPostLink('favorite', $catid, $id, $fbIcons['favorite'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['favorite'] . '" alt="' . _VIEW_FAVORITETXT . '" title="' . _VIEW_FAVORITETXT . '" border="0" />' : _VIEW_FAVORITETXT);
                    }

                    if ($kunena_my->id != 0 && $fbConfig->allowfavorites && $fb_canfavorite == 0)
                    {
                        // this user is allowed to unfavorite
                        echo CKunenaLink::GetTopicPostLink('unfavorite', $catid, $id, $fbIcons['unfavorite'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unfavorite'] . '" alt="' . _VIEW_UNFAVORITETXT . '" title="' . _VIEW_UNFAVORITETXT . '" border="0" />' : _VIEW_UNFAVORITETXT);
                    }
                    // FINISH: FAVORITES
                    ?>
                    <!-- Begin: Total Favorite -->
                    <?php
                    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_favorites where thread='$thread'");
                    $fb_totalfavorited = $kunena_db->loadResult();

                    if ($fb_totalfavorited) {
                    echo '<span class="fb_totalfavorite">';
                    echo _KUNENA_TOTALFAVORITE;
                    echo $fb_totalfavorited;
                    echo '</span>';
                    }
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
                                echo $pageNav->getPagesLinks( JRoute::_(KUNENA_LIVEURLREL."&amp;func=view&amp;id=$id&amp;catid=$catid"));
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
                                <span class = "fb_title fbl"><b><?php echo _KUNENA_TOPIC; ?></b> <?php echo $jr_topic_title; ?></span>
                            </div>
                            <!-- FORUM TOOLS -->

                            <?php

                            //(JJ) BEGIN: RECENT POSTS
                            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php')) {
                                include (KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php');
                            }
                            else {
                                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/forumtools/forumtools.php');
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
                        $kunena_db->setQuery("SELECT a.userid FROM #__fb_users AS a" . "\n LEFT JOIN #__fb_moderation AS b" . "\n ON b.userid=a.userid" . "\n WHERE b.catid='$catid'");
                        $catModerators = $kunena_db->loadResultArray();


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
                            $kunena_db->setQuery("select locked from #__fb_categories where id={$this_message->catid}");
                            $topicLock = $kunena_db->loadResult();
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

                                //meta description and keywords
								$metaKeys=(htmlspecialchars(stripslashes($fmessage->subject)). ', ' .htmlspecialchars(stripslashes($objCatParentInfo->name)) . ', ' . htmlspecialchars(stripslashes($fbConfig->board_title)) . ', ' . htmlspecialchars($mainframe->getCfg('sitename')));
								$metaDesc=(htmlspecialchars(stripslashes($fmessage->subject)) . ' - ' .htmlspecialchars(stripslashes($objCatParentInfo->name)) . ' - ' . htmlspecialchars(stripslashes($objCatInfo->name)) .' - ' . htmlspecialchars(stripslashes($fbConfig->board_title)));

								    $document =& JFactory::getDocument();
								    $cur = $document->get( 'description' );
								    $metaDesc = $cur .'. ' . $metaDesc;
								    $document =& JFactory::getDocument();
								    $document->setMetadata( 'keywords', $metaKeys );
								    $document->setDescription($metaDesc);

                                //filter out clear html
                                $fmessage->name = htmlspecialchars($fmessage->name);
                                $fmessage->email = htmlspecialchars($fmessage->email);
                                $fmessage->subject = htmlspecialchars($fmessage->subject);

                                //Get userinfo needed later on, this limits the amount of queries
                                unset($userinfo);
                                $kunena_db->setQuery("SELECT  a.*,b.name,b.username,b.gid FROM #__fb_users as a LEFT JOIN #__users as b on b.id=a.userid where a.userid='$fmessage->userid'");
                                $userinfo = $kunena_db->loadObject();
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
                                $msg_username = $fmessage->email != "" && $kunena_my->id > 0 && $fbConfig->showemail ? CKunenaLink::GetEmailLink($fmessage->email, $fb_username) : $fb_username;

                                if ($fbConfig->allowavatar)
                                {
                                    $Avatarname = $userinfo->username;

                                    if ($fbConfig->avatar_src == "jomsocial")
									{
										// Get CUser object
										$jsuser =& CFactory::getUser($fmessage->userid);
									    $msg_avatar = '<span class="fb_avatar"><img src="' . $jsuser->getThumbAvatar() . '" alt=" " /></span>';
									}
                                    else if ($fbConfig->avatar_src == "clexuspm") {
                                        $msg_avatar = '<span class="fb_avatar"><img src="' . MyPMSTools::getAvatarLinkWithID($fmessage->userid) . '" /></span>';
                                    }
                                    else if ($fbConfig->avatar_src == "cb")
                                    {
                                            $msg_avatar = '<span class="fb_avatar">' .$kunenaProfile->showAvatar($fmessage->userid) . '</span>';
                                    }
                                    else
                                    {
                                        $avatar = $userinfo->avatar;

                                        if ($avatar != '') {
                                            $msg_avatar = '<span class="fb_avatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" /></span>';
                                        }

                                        else {$msg_avatar = '<span class="fb_avatar"><img  border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" /></span>'; }
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
                                        $agrp = strtolower($kunena_acl->get_group_name($ugid, 'ARO'));
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
                                                $kunena_db->setQuery("SELECT * FROM #__fb_ranks WHERE rank_id = '$userinfo->rank'");
                                            } else {
                                                //post count rank
                                                $kunena_db->setQuery("SELECT * FROM #__fb_ranks WHERE ((rank_min <= $numPosts) AND (rank_special = 0))  ORDER BY rank_min DESC LIMIT 1");
                                            }
                                            $rank = $kunena_db->loadObject();
                                            $rText = $rank->rank_title;
                                            $rImg = KUNENA_URLRANKSPATH . $rank->rank_image;
                                        }

                                        if ($uIsMod and !$showSpRank)
                                        {
                                            $rText = _RANK_MODERATOR;
                                            $rImg = KUNENA_URLRANKSPATH . 'rankmod.gif';
                                        }

                                        if ($uIsAdm and !$showSpRank)
                                        {
                                            $rText = _RANK_ADMINISTRATOR;
                                            $rImg = KUNENA_URLRANKSPATH . 'rankadmin.gif';
                                        }

                                        if ($fbConfig->rankimages) {
                                            $msg_userrankimg = '<img src="' . $rImg . '" alt="" />';
                                        }

                                        $msg_userrank = $rText;





                                        $useGraph = 0; //initialization

                                        if (!$fbConfig->poststats)
                                        {
                                            $msg_posts = '<div class="viewcover">' .
                                              "<strong>" . _POSTS . " $numPosts" . "</strong>" .
                                              "</div>";

                                            $useGraph = 0;
                                        }
                                        else
                                        {
                                            $myGraph = new phpGraph;
                                            //$myGraph->SetGraphTitle(_POSTS);
                                            $myGraph->AddValue(_POSTS, $numPosts);
                                            $myGraph->SetRowSortMode(0);
                                            $myGraph->SetBarImg(KUNENA_URLGRAPHPATH . "col" . $fbConfig->statscolor . "m.png");
                                            $myGraph->SetBarImg2(KUNENA_URLEMOTIONSPATH . "graph.gif");
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

                                    if ($kunena_my->id != '0' && $kunena_my->id != $fmessage->userid)
                                    {
                                        $msg_karmaminus = CKunenaLink::GetKarmaLink('decrease', $catid, $fmessage->id, $fmessage->userid, '<img src="'.($fbIcons['karmaminus']?(KUNENA_URLICONSPATH . $fbIcons['karmaminus']):(KUNENA_URLEMOTIONSPATH . "karmaminus.gif")).'" alt="Karma-" border="0" title="' . _KARMA_SMITE . '" align="middle" />' );
                                        $msg_karmaplus  = CKunenaLink::GetKarmaLink('increase', $catid, $fmessage->id, $fmessage->userid, '<img src="'.($fbIcons['karmaplus']?(KUNENA_URLICONSPATH . $fbIcons['karmaplus']):(KUNENA_URLEMOTIONSPATH . "karmaplus.gif")).'" alt="Karma+" border="0" title="' . _KARMA_APPLAUD . '" align="middle" />' );
                                    }
                                }
                                /*let's see if we should use Missus integration */
                                if ($fbConfig->pm_component == "missus" && $fmessage->userid && $kunena_my->id)
                                {
                                    //we should offer the user a Missus link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms
                                    = "<a href=\"" . JRoute::_('index.php?option=com_missus&amp;func=newmsg&amp;user=' . $fmessage->userid . '&amp;subject=' . _GEN_FORUM . ': ' . urlencode(utf8_encode($fmessage->subject))) . "\"><img src='";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= KUNENA_URLICONSPATH  . $fbIcons['pms'];;
                                    }

                                    $msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }

                                /*let's see if we should use JIM integration */
                                if ($fbConfig->pm_component == "jim" && $fmessage->userid && $kunena_my->id)
                                {
                                    //we should offer the user a JIM link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_jim&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src='";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= KUNENA_URLICONSPATH  .  $fbIcons['pms'];;
                                    }

                                    $msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }
                                /*let's see if we should use uddeIM integration */
                                if ($fbConfig->pm_component == "uddeim" && $fmessage->userid && $kunena_my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_uddeim&amp;task=new&recip=' . $fmessage->userid) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }
                                /*let's see if we should use myPMS2 integration */
                                if ($fbConfig->pm_component == "pms" && $fmessage->userid && $kunena_my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->username;
                                    $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_pms&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                }

                                // online - ofline status
                                if ($fmessage->userid > 0)
                                {
                                    $sql = "SELECT count(userid) FROM #__session WHERE userid=" . $fmessage->userid;
                                    $kunena_db->setQuery($sql);
                                    $isonline = $kunena_db->loadResult();

                                    if ($isonline && $userinfo->showOnline ==1 ) {
                                        $msg_online .= $fbIcons['onlineicon'] ? '<img src="'
                                        . KUNENA_URLICONSPATH . $fbIcons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
                                    }
                                    else {
                                        $msg_online .= $fbIcons['offlineicon'] ? '<img src="'
                                        . KUNENA_URLICONSPATH . $fbIcons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
                                    }
                                }
                                /* PM integration */
                                if ($fbConfig->pm_component == "jomsocial" && $fmessage->userid && $kunena_my->id)
                                {
                                	$onclick = CMessaging::getPopup($fmessage->userid);
                                	$msg_pms = '<a href="javascript:void(0)" onclick="'. $onclick . "\">";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= "<img src=\"".KUNENA_URLICONSPATH.$fbIcons['pms']."\" alt=\""._VIEW_PMS."\" border=\"0\" title=\""._VIEW_PMS."\" />";
                                    }
                                    else
                                    {
                                    	$msg_pms .= _VIEW_PMS;
                                    }

                                    $msg_pms .= "</a>";
                                	//$msg_pms = '<a href="javascript:void(0)" onclick="'. $onclick .'">Send message</a>';
                                }
                                else if ($fbConfig->pm_component == "clexuspm" && $fmessage->userid && $kunena_my->id)
                                {
                                    //we should offer the user a PMS link
                                    //first get the username of the user to contact
                                    $PMSName = $userinfo->aid;
                                    $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;task=new&amp;to=' . $fmessage->userid . '&title=' . $fmessage->subject) . "\"><img src=\"";

                                    if ($fbIcons['pms']) {
                                        $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
                                    }
                                    else {
                                        $msg_pms .= KUNENA_JLIVEURL . "/components/com_mypms/images/icons/message_12px.gif";
                                    }

                                    $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
                                    //mypms pro profile link
                                    $msg_profile = "<a href=\"" . MyPMSTools::getProfileLink($fmessage->userid) . "\"><img src=\"";

                                    if ($fbIcons['userprofile']) {
                                        $msg_profile .= KUNENA_URLICONSPATH . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profile .= KUNENA_JLIVEURL . "/components/com_mypms/images/managecontact_icon.gif";
                                    }

                                    $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                    //mypms add buddy link
                                    $msg_buddy = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;user=' . $PMSName . '&amp;task=addbuddy') . "\"><img src=\"";

                                    if ($fbIcons['pms2buddy']) {
                                        $msg_buddy .= KUNENA_URLICONSPATH . $fbIcons['pms2buddy'];
                                    }
                                    else {
                                        $msg_buddy .= KUNENA_JLIVEURL . "/components/com_mypms/images/messages/addbuddy.gif";
                                    }

                                    $msg_buddy .= "\" alt=\"" . _VIEW_ADDBUDDY . "\" border=\"0\" title=\"" . _VIEW_ADDBUDDY . "\" /></a>";
                                    $kunena_db->setQuery("SELECT icq,ym,msn,aim,website,location FROM #__mypms_profiles WHERE user='" . $PMSName . "'");
                                    $profileitems = $kunena_db->loadObjectList();
                                    	check_dberror("Unable to load mypms profile.");

                                    foreach ($profileitems as $profileitems)
                                    {
                                        if ($profileitems->aim)
                                        $msg_aim = "<a href=\"aim:goim?screenname=" . str_replace(" ", "+", $profileitems->aim) . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "aim.png\" border=0 alt=\"\" /></a>";

                                        if ($profileitems->icq)
                                        $msg_icq = "<a href=\"http://www.icq.com/whitepages/wwp.php?uin=" . $profileitems->icq . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "icq.png\" border=0 alt=\"\" /></a>";

                                        if ($profileitems->msn)
                                        $msg_msn = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;task=showprofile&amp;user=' . $PMSName) . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "msn.png\" border=0 alt=\"\" /></a>";

                                        if ($profileitems->ym)
                                        $msg_yahoo = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $profileitems->ym . "&.src=pg\"><img src=\"http://opi.yahoo.com/online?u=" . $profileitems->ym . "&m=g&t=0\" border=0 alt=\"\" /></a>";

                                        if ($profileitems->location)
                                        $msg_loc = $profileitems->location;
                                    }

                                    unset ($profileitems);
                                }

                                //Check if the Integration settings are on, and set the variables accordingly.
                                if ($fbConfig->fb_profile == "cb")
                                {
                                    if ($fbConfig->fb_profile == 'cb' && $fmessage->userid > 0)
                                    {
                                        $msg_prflink = CKunenaCBProfile::getProfileURL($fmessage->userid);
                                        $msg_profile = "<a href=\"" . $msg_prflink . "\">                                              <img src=\"";

                                        if ($fbIcons['userprofile']) {
                                            $msg_profile .= KUNENA_URLICONSPATH . $fbIcons['userprofile'];
                                        }
                                        else {
                                            $msg_profile .= KUNENA_JLIVEURL . "/components/com_comprofiler/images/profiles.gif";
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
                                        $msg_profile .= KUNENA_URLICONSPATH . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profile .= KUNENA_JLIVEURL . "/components/com_mypms/images/managecontact_icon.gif";
                                    }

                                    $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
                                }
                                else if ($userinfo->gid > 0)
                                {
                                    //Kunena Profile link.
                                    $msg_prflink = JRoute::_(KUNENA_LIVEURLREL.'&amp;func=fbprofile&amp;task=showprf&amp;userid=' . $fmessage->userid);
                                    $msg_profileicon = "<img src=\"";

                                    if ($fbIcons['userprofile']) {
                                        $msg_profileicon .= KUNENA_URLICONSPATH . $fbIcons['userprofile'];
                                    }
                                    else {
                                        $msg_profileicon .= KUNENA_URLICONSPATH . "profile.gif";
                                    }

                                    $msg_profileicon .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" />";
                                    $msg_profile = CKunenaLink::GetProfileLink($fbConfig, $fmessage->userid, $msg_profileicon);
                                }

                                // Begin: Additional Info //
                                if ($userinfo->gender != '') {
                                    $gender = _KUNENA_NOGENDER;
                                    if ($userinfo->gender ==1)  {
                                        $gender = ''._KUNENA_MYPROFILE_MALE;
                                        $msg_gender = $fbIcons['msgmale'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgmale'] . '" border="0" alt="'._KUNENA_MYPROFILE_GENDER.': '.$gender.'" title="'._KUNENA_MYPROFILE_GENDER.': '.$gender.'" />' : ''._KUNENA_MYPROFILE_GENDER.': '.$gender.'';
                                    }

                                    if ($userinfo->gender ==2)  {
                                        $gender = ''._KUNENA_MYPROFILE_FEMALE;
                                        $msg_gender = $fbIcons['msgfemale'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgfemale'] . '" border="0" alt="'._KUNENA_MYPROFILE_GENDER.': '.$gender.'" title="'._KUNENA_MYPROFILE_GENDER.': '.$gender.'" />' : ''._KUNENA_MYPROFILE_GENDER.': '.$gender.'';
                                    }

                                }

                                if ($userinfo->personalText != '') {
                                    $msg_personal = html_entity_decode_utf8(stripslashes($userinfo->personalText));
                                }

                                if ($userinfo->ICQ != '') {
                                    $msg_icq = '<a href="http://www.icq.com/people/cmd.php?uin='.$userinfo->ICQ.'&action=message"><img src="http://status.icq.com/online.gif?icq='.$userinfo->ICQ.'&img=5" title="ICQ#: '.$userinfo->ICQ.'" alt="ICQ#: '.$userinfo->ICQ.'" /></a>';
                                }
                                if ($userinfo->location != '') {
                                    $msg_location = $fbIcons['msglocation'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msglocation'] . '" border="0" alt="'._KUNENA_MYPROFILE_LOCATION.': '.$userinfo->location.'" title="'._KUNENA_MYPROFILE_LOCATION.': '.html_entity_decode_utf8(stripslashes($userinfo->location)).'" />' : ' '._KUNENA_MYPROFILE_LOCATION.': '.$userinfo->location.'';
                                }
                                if ($userinfo->birthdate !='0001-01-01' AND $userinfo->birthdate !='0000-00-00' and $userinfo->birthdate !='') {
                                    $msg_birthdate = $fbIcons['msgbirthdate'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgbirthdate'] . '" border="0" alt="'._KUNENA_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'" title="'._KUNENA_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'" />' : ' '._KUNENA_MYPROFILE_BIRTHDATE.': '.$userinfo->birthdate.'';
                                }

                                if ($userinfo->AIM != '') {
                                    $msg_aim = $fbIcons['msgaim'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgaim'] . '" border="0" alt="'.$userinfo->AIM.'" title="AIM: '.$userinfo->AIM.'" />' : 'AIM: '.$userinfo->AIM.'';
                                }
                                if ($userinfo->MSN != '') {
                                    $msg_msn = $fbIcons['msgmsn'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgmsn'] . '" border="0" alt="'.$userinfo->MSN.'" title="MSN: '.$userinfo->MSN.'" />' : 'MSN: '.$userinfo->MSN.'';
                                }
                                if ($userinfo->YIM != '') {
                                    $msg_yim = $fbIcons['msgyim'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgyim'] . '" border="0" alt="'.$userinfo->YIM.'" title="YIM: '.$userinfo->YIM.'" />' : ' YIM: '.$userinfo->YIM.'';
                                }
                                if ($userinfo->SKYPE != '') {
                                    $msg_skype = $fbIcons['msgskype'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msgskype'] . '" border="0" alt="'.$userinfo->SKYPE.'" title="SKYPE: '.$userinfo->SKYPE.'" />' : 'SKYPE: '.$userinfo->SKYPE.'';
                                }
                                if ($userinfo->GTALK != '') {
                                    $msg_gtalk = $fbIcons['msggtalk'] ? '<img src="'. KUNENA_URLICONSPATH . $fbIcons['msggtalk'] . '" border="0" alt="'.$userinfo->GTALK.'" title="GTALK: '.$userinfo->GTALK.'" />' : 'GTALK: '.$userinfo->GTALK.'';
                                }
                                if ($userinfo->websiteurl != '') {
                                    $msg_website = $fbIcons['msgwebsite'] ? '<a href="http://'.$userinfo->websiteurl.'" target="_blank"><img src="'. KUNENA_URLICONSPATH . $fbIcons['msgwebsite'] . '" border="0" alt="'.$userinfo->websitename.'" title="'.$userinfo->websitename.'" /></a>' : '<a href="http://'.$userinfo->websiteurl.'" target="_blank">'.$userinfo->websitename.'</a>';
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
                                $fb_subject_txt = stripslashes($fb_subject_txt);
                                $msg_subject = smile::fbHtmlSafe($fb_subject_txt);

                                $msg_date = date(_DATETIME, $fmessage->time);
                                $fb_message_txt = stripslashes($fmessage->message);

                                $fb_message_txt = smile::smileReplace($fb_message_txt, 0, $fbConfig->disemoticons, $smileyList);
                                $fb_message_txt = nl2br($fb_message_txt);
                                //$fb_message_txt = str_replace("<P>&nbsp;</P><br />","",$fb_message_txt);
                                //$fb_message_txt = str_replace("</P><br />","</P>",$fb_message_txt);
                                //$fb_message_txt = str_replace("<P><br />","<P>",$fb_message_txt);

                                //filter bad words
                                if ($fbConfig->badwords && class_exists('Badword') && Badword::filter($fb_message_txt, $kunena_my)) {
                                	if (method_exists('Badword','flush')) {
                                		$fb_message_txt = Badword::flush($fb_message_txt, $kunena_my);
                                	} else {
                               			$fb_message_txt = _COM_A_BADWORDS_NOTICE;
                                	}
                                }

                                // Code tag: restore TABS as we had to 'hide' them from the rest of the logic
                                $fb_message_txt = str_replace("__FBTAB__", "&#009;", $fb_message_txt);

                                $msg_text = CKunenaTools::prepareContent($fb_message_txt);

                                $signature = $userinfo->signature;
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

                                if ((($fbConfig->pubwrite == 0 && $kunena_my->id != 0) || $fbConfig->pubwrite == 1) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                                {
                                    //user is allowed to reply/quote
                                    $msg_reply = CKunenaLink::GetTopicPostReplyLink('reply', $catid, $fmessage->id , $fbIcons['reply']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['reply'] . '" alt="Reply" border="0" title="' . _VIEW_REPLY . '" />':_GEN_REPLY);
                                    $msg_quote = CKunenaLink::GetTopicPostReplyLink('quote', $catid, $fmessage->id , $fbIcons['quote']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['quote'] . '" alt="Quote" border="0" title="' . _VIEW_QUOTE . '" />':_GEN_QUOTE);
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
                                    $msg_delete = CKunenaLink::GetTopicPostLink('delete', $catid, $fmessage->id , $fbIcons['delete']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['delete'] . '" alt="Delete" border="0" title="' . _VIEW_DELETE . '" />':_GEN_DELETE);
                                    $msg_merge = CKunenaLink::GetTopicPostLink('merge', $catid, $fmessage->id , $fbIcons['merge']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['merge'] . '" alt="' . _GEN_MERGE . '" border="0" title="' . _GEN_MERGE . '" />':_GEN_MERGE);
                                    $msg_split = CKunenaLink::GetTopicPostLink('split', $catid, $fmessage->id , $fbIcons['split']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['split'] . '" alt="' . _GEN_SPLIT . '" border="0" title="' . _GEN_SPLIT . '" />':_GEN_SPLIT);
                                }

                                if ($fbConfig->useredit && $kunena_my->id != "")
                                {
                                    //Now, if the viewer==author and the viewer is allowed to edit his/her own post then offer an 'edit' link
                                    $allowEdit = 0;
                                    if ($kunena_my->id == $fmessage->userid)
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
                                            if(($modtime + ((int)$fbConfig->useredittime)) >= CKunenaTools::fbGetInternalTime())
                                            {
                                                $allowEdit = 1;
                                            }
                                        }
                                    }
                                    if($allowEdit)
                                    {
                                        $msg_edit = CKunenaLink::GetTopicPostLink('edit', $catid, $fmessage->id , $fbIcons['edit']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['edit'] . '" alt="Edit" border="0" title="' . _VIEW_EDIT . '" />':_GEN_EDIT);
                                        $showedEdit = 1;
                                    }
                                }

                                if ($is_Moderator && $showedEdit != 1)
                                {
                                    //Offer a moderator always the edit link except when it is already showing..
                                    $msg_edit = CKunenaLink::GetTopicPostLink('edit', $catid, $fmessage->id , $fbIcons['edit']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['edit'] . '" alt="Edit" border="0" title="' . _VIEW_EDIT . '" />':_GEN_EDIT);
                                }

                                if ($is_Moderator && $fmessage->parent == '0')
                                {
                                    // offer the moderator always the move link to relocate a topic to another forum
                                    // and the (un)sticky bit links
                                    // and the (un)lock links
                                    // but ONLY when it is a topic and not a reply
                                    $msg_move = CKunenaLink::GetTopicPostLink('move', $catid, $fmessage->id , $fbIcons['move']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['move'] . '" alt="Move" border="0" title="' . _VIEW_MOVE . '" />':_GEN_MOVE);

                                    if ($fmessage->ordering == 0)
                                    {
                                        $msg_sticky = CKunenaLink::GetTopicPostLink('sticky', $catid, $fmessage->id , $fbIcons['sticky']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['sticky'] . '" alt="Sticky" border="0" title="' . _VIEW_STICKY . '" />':_GEN_STICKY);
                                    }
                                    else
                                    {
                                        $msg_sticky = CKunenaLink::GetTopicPostLink('unsticky', $catid, $fmessage->id , $fbIcons['unsticky']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['unsticky'] . '" alt="Unsticky" border="0" title="' . _VIEW_UNSTICKY . '" />':_GEN_UNSTICKY);
                                    }

                                    if ($fmessage->locked == 0)
                                    {
                                        $msg_lock = CKunenaLink::GetTopicPostLink('lock', $catid, $fmessage->id , $fbIcons['lock']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['lock'] . '" alt="Lock" border="0" title="' . _VIEW_LOCK . '" />':_GEN_LOCK);
                                    }
                                    else
                                    {
                                        $msg_lock = CKunenaLink::GetTopicPostLink('unlock', $catid, $fmessage->id , $fbIcons['unlock']?'<img src="' . KUNENA_URLICONSPATH . $fbIcons['unlock'] . '" alt="Unlock" border="0" title="' . _VIEW_UNLOCK . '" />':_GEN_UNLOCK);
                                    }
                                }

                                //(JJ)
                                if (file_exists(KUNENA_ABSTMPLTPATH . '/message.php')) {
                                    include (KUNENA_ABSTMPLTPATH . '/message.php');
                                }
                                else {
                                    include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'message.php');
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
                            if (file_exists(KUNENA_ABSTMPLTPATH . '/thread.php')) {
                                include (KUNENA_ABSTMPLTPATH . '/thread.php');
                            }
                            else {
                                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'thread.php');
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
                        echo CKunenaLink::GetSamePageAnkerLink('forumtop', $fbIcons['toparrow'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);

                        if (($fbConfig->pubwrite == 0 && $kunena_my->id != 0) || $fbConfig->pubwrite)
                        {
                            //this user is allowed to post a new topic:
                            echo CKunenaLink::GetPostNewTopicLink($catid, $fbIcons['new_topic'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                        }

                        if ((($fbConfig->pubwrite == 0 && $kunena_my->id != 0) || $fbConfig->pubwrite) && ($topicLock == 0 || ($topicLock == 1 && $is_Moderator)))
                        {
                            //this user is allowed to reply to this topic:
                            echo CKunenaLink::GetTopicPostReplyLink('reply', $catid, $thread, $fbIcons['topicreply'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['topicreply'] . '" alt="' . _GEN_POST_REPLY . '" title="' . _GEN_POST_REPLY . '" border="0" />' : _GEN_POST_REPLY);
                        }

                        if ($fb_cansubscribe == 1)
                        {
                            // this user is allowed to subscribe - check performed further up to eliminate duplicate checks
                            // for top and bottom navigation
                            echo CKunenaLink::GetTopicPostLink('subscribe', $catid, $id, $fbIcons['subscribe'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['subscribe'] . '" alt="' . _VIEW_SUBSCRIBETXT . '" title="' . _VIEW_SUBSCRIBETXT . '" border="0" />' : _VIEW_SUBSCRIBETXT);
                        }

                        if ($kunena_my->id != 0 && $fbConfig->allowsubscriptions && $fb_cansubscribe == 0)
                        {
                            // this user is allowed to unsubscribe
                            echo CKunenaLink::GetTopicPostLink('unsubscribe', $catid, $id, $fbIcons['unsubscribe'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unsubscribe'] . '" alt="' . _VIEW_UNSUBSCRIBETXT . '" title="' . _VIEW_UNSUBSCRIBETXT . '" border="0" />' : _VIEW_UNSUBSCRIBETXT);
                        }

                        if ($fb_canfavorite == 1)
                        {
                            // this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
                            // for top and bottom navigation
                            echo CKunenaLink::GetTopicPostLink('favorite', $catid, $id, $fbIcons['favorite'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['favorite'] . '" alt="' . _VIEW_FAVORITETXT . '" title="' . _VIEW_FAVORITETXT . '" border="0" />' : _VIEW_FAVORITETXT);
                        }

                        if ($kunena_my->id != 0 && $fbConfig->allowfavorites && $fb_canfavorite == 0)
                        {
                            // this user is allowed to unfavorite
                            echo CKunenaLink::GetTopicPostLink('unfavorite', $catid, $id, $fbIcons['unfavorite'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unfavorite'] . '" alt="' . _VIEW_UNFAVORITETXT . '" title="' . _VIEW_UNFAVORITETXT . '" border="0" />' : _VIEW_UNFAVORITETXT);
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
                                    echo $pageNav->getPagesLinks("index.php?option=com_kunena&amp;Itemid=$Itemid&amp;func=view&amp;id=$id&amp;catid=$catid");
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
                            <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL);?>">
    <?php echo $fbIcons['forumlist'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlist'] . '" border="0" alt="' . _GEN_FORUMLIST . '" title="' . _GEN_FORUMLIST . '">' : _GEN_FORUMLIST; ?> </a>

                            <?php
                            if (file_exists(KUNENA_ROOT_PATH .DS. 'templates/' . $mainframe->getTemplate() . '/images/arrow.png')) {
                                echo ' <img src="' . KUNENA_JLIVEURL . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png" alt="" /> ';
                            }
                            else {
                                echo ' <img src="' . KUNENA_JLIVEURL . '/images/M_images/arrow.png" alt="" /> ';
                            }
                            ?>

                            <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=listcat&amp;catid='.$objCatParentInfo->id);?>" rel="nofollow"> <?php echo $objCatParentInfo->name; ?> </a>

                            <?php
                            if (file_exists(KUNENA_ROOT_PATH .DS. 'templates/' . $this->getTemplate() . '/images/arrow.png')) {
                                echo ' <img src="' . KUNENA_JLIVEURL . '/templates/' . $this->getTemplate() . '/images/arrow.png" alt="" /> ';
                            }
                            else {
                                echo ' <img src="' . KUNENA_JLIVEURL . '/images/M_images/arrow.png" alt="" /> ';
                            }
                            ?>

                            <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid);?>" rel="nofollow"> <?php echo $objCatInfo->name; ?> </a>
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
                            require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
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
    echo _KUNENA_NO_ACCESS;
}

if ($fbConfig->highlightcode)
{
	echo '
	<script type="text/javascript" src="'.KUNENA_DIRECTURL . '/template/default/plugin/chili/jquery.chili-2.2.js"></script>
	<script id="setup" type="text/javascript">
	ChiliBook.recipeFolder     = "'.KUNENA_DIRECTURL . '/template/default/plugin/chili/";
	ChiliBook.stylesheetFolder     = "'.KUNENA_DIRECTURL . '/template/default/plugin/chili/";
	</script>
	';
}

?>
