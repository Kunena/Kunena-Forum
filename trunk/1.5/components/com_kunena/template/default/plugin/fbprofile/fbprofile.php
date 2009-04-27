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
**/

defined( '_JEXEC' ) or die('Restricted access');

$acl = &JFactory::getACL();


$fbConfig =& CKunenaConfig::getInstance();

if ($fbConfig->fb_profile == 'cb') {
        $userid = JRequest::getVar('userid', null);
	$url = CKunenaCBProfile::getProfileURL($userid);
	header("HTTP/1.1 307 Temporary Redirect");
	header("Location: " . htmlspecialchars_decode($url));
	$mainframe->close();
}

$mainframe->setPageTitle(_KUNENA_USERPROFILE_PROFILE . ' - ' . stripslashes($fbConfig->board_title));

if ($my->id) //registered only
{
    require_once(KUNENA_PATH_LIB .DS. 'kunena.authentication.php');
    require_once(KUNENA_PATH_LIB .DS. 'kunena.statsbar.php');

    $task = JRequest::getVar('task', "");

    switch ($task)
    {
        case "showprf":
            $userid = JRequest::getVar('userid', null);

            $page = 0;
            showprf((int)$userid, $page);
            break;
    }
}
else {
    echo '<h3>' . _COM_A_REGISTERED_ONLY . '</h3>';
}

function showprf($userid, $page)
{
    $fbConfig =& CKunenaConfig::getInstance();

    $acl = &JFactory::getACL();
    $my = &JFactory::getUser();
    $database = &JFactory::getDBO();
    // ERROR: mixed global $fbIcons
    global $fbIcons;

    //Get userinfo needed later on, this limits the amount of queries
    unset($userinfo);
    $database->setQuery("SELECT a.*, b.* FROM #__fb_users as a"
                        . "\n LEFT JOIN #__users as b on b.id=a.userid"
                        . "\n where a.userid=$userid");

    $userinfo = $database->loadObject();
    check_dberror('Unable to get user profile info.');

    if (!$userinfo) {
	$database->setQuery("SELECT * FROM #__users WHERE id=$userid");
	$userinfo = $database->loadObject();
	check_dberror('Unable to get user profile info.');

	if (!$userinfo) {
		echo '<h3>' . _KUNENA_PROFILE_NO_USER . '</h3>';
		return;
	} else {
		// Check moderator status (admin is moderator)
		$aro_group = $acl->getAroGroup($userid);
		if ($aro_group)
		$aro_group->group_id = $aro_group->id;  // changed fieldname in Joomla 1.5: "group_id" -> "id"
		$is_admin = (strtolower($aro_group->name) == 'super administrator' || strtolower($aro_group->name) == 'administrator');

		// there's no profile; set userid and moderator status.
		$database->setQuery("INSERT INTO #__fb_users (userid,moderator) VALUES ('$userid','$is_admin')");
		$database->query();
		check_dberror('Unable to create user profile.');

		$database->setQuery("SELECT a.*, b.* FROM #__fb_users as a"
			. "\n LEFT JOIN #__users as b on b.id=a.userid"
			. "\n where a.userid=$userid");

		$userinfo = $database->loadObject();
		check_dberror('Unable to get user profile info.');

		// TODO: For future use
		// echo '<h3>' . _KUNENA_PROFILE_NOT_FOUND . '</h3>';
		// return;
	}

    }

	// User Hits
	$database->setQuery('UPDATE #__fb_users SET uhits=uhits+1 WHERE userid='.$userid);
	$database->query() or trigger_dberror("Unable to update user hits.");

	// get userprofile hits
	$msg_userhits = $userinfo->uhits;

    //get the username:
    $fb_username = "";

    if ($fbConfig->username) {
        $fb_queryName = "username";
    }
    else {
        $fb_queryName = "name";
    }

    $fb_username = $userinfo->{$fb_queryName};

    if ($fb_username == "" || $fbConfig->changename) {
        $fb_username = html_entity_decode_utf8(stripslashes($fmessage->name));
    }

    $msg_id = $fmessage->id;
    $lists["userid"] = $userid;
    $msg_username = ($fmessage->email != "" && $my->id > 0 && $fbConfig->showemail == '1') ? "<a href=\"mailto:" . $fmessage->email . "\">" . $fb_username . "</a>" : $fb_username;

    if ($fbConfig->allowavatar)
    {
        $Avatarname = $userinfo->username;

        if ($fbConfig->avatar_src == "jomsocial")
		{
			// Get CUser object
			$user =& CFactory::getUser($userid);
		    $msg_avatar = '<span class="fb_avatar"><img src="' . $user->getAvatar() . '" alt="" /></span>';
		}
        else if ($fbConfig->avatar_src == "clexuspm") {
            $msg_avatar = '<span class="fb_avatar"><img src="' . MyPMSTools::getAvatarLinkWithID($userid, "b") . '" alt="" /></span>';
        }
        else if ($fbConfig->avatar_src == "cb")
        {
            $kunenaProfile = CKunenaCBProfile::getInstance();
			$msg_avatar = '<span class="fb_avatar">' . $kunenaProfile->showAvatar($userid, '', 0) . '</span>';
        }
        else
        {
        	$avatar = $userinfo->avatar;

        	if ($avatar != '')
        	{
        		if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/l_' . $avatar))
        		{
        			$msg_avatar = '<span class="fb_avatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '"  alt="" /></span>';
				}
				else
				{
					$msg_avatar = '<span class="fb_avatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/l_' . $avatar . '"  alt="" /></span>';
				}
        	}

        	else {$msg_avatar = '<span class="fb_avatar"><img  border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/nophoto.jpg"  alt="" /></span>'; }
        }
    }

    if ($fbConfig->showuserstats)
    {
        //user type determination
        $ugid = $userinfo->gid;
        $uIsMod = 0;
        $uIsAdm = 0;

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

        //Get the max# of posts for any one user
        $database->setQuery("SELECT max(posts) from #__fb_users");
        $maxPosts = $database->loadResult();

        //# of post for this user and ranking

        $numPosts = (int)$userinfo->posts;

							//ranking
							if ($fbConfig->showranking)
							{

								if ($userinfo->rank != '0')
								{
												//special rank
												$database->setQuery("SELECT * FROM #__fb_ranks WHERE rank_id = '$userinfo->rank'");
												$getRank = $database->loadObjectList();
													check_dberror("Unable to load ranks.");
												$rank=$getRank[0];
												$rText = $rank->rank_title;
												$rImg = KUNENA_URLRANKSPATH . $rank->rank_image;
									}
									if ($userinfo->rank == '0')
									{
											//post count rank
												$database->setQuery("SELECT * FROM #__fb_ranks WHERE ((rank_min <= $numPosts) AND (rank_special = 0))  ORDER BY rank_min DESC LIMIT 1");
												$getRank = $database->loadObjectList();
													check_dberror("Unable to load ranks.");
												$rank=$getRank[0];
												$rText = $rank->rank_title;
												$rImg = KUNENA_URLRANKSPATH . $rank->rank_image;
									}

									if ($uIsMod)
									{
													$rText = _RANK_MODERATOR;
													$rImg = KUNENA_URLRANKSPATH . 'rankmod.gif';
									}

									if ($uIsAdm)
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
                $myGraph->SetGraphWidth(120); //should match column width in the <TD> above -5 pixels
                //$myGraph->BarGraphHoriz();
                $useGraph = 1;
            }
        }
    }

    //karma points and buttons
    if ($fbConfig->showkarma && $userid != '0')
    {
        $karmaPoints = $userinfo->karma;
        $karmaPoints = (int)$karmaPoints;
        $msg_karma = "<strong>" . _KARMA . ":</strong> $karmaPoints";

        if ($my->id != '0' && $my->id != $userid)
        {
            $msg_karmaminus = "<a href=\"" . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=karma&amp;do=decrease&amp;userid=' . $userid . '&amp;pid=' . $fmessage->id . '&amp;catid=' . $catid . '') . "\"><img src=\"";

            if ($fbIcons['karmaminus']) {
                $msg_karmaminus .= KUNENA_URLICONSPATH . $fbIcons['karmaminus'];
            }
            else {
                $msg_karmaminus .= KUNENA_URLEMOTIONSPATH . "karmaminus.gif";
            }

            $msg_karmaminus .= "\" alt=\"Karma-\" border=\"0\" title=\"" . _KARMA_SMITE . "\" align=\"middle\" /></a>";
            $msg_karmaplus = "<a href=\"" . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=karma&amp;do=increase&amp;userid=' . $userid . '&amp;pid=' . $fmessage->id . '&amp;catid=' . $catid . '') . "\"><img src=\"";

            if ($fbIcons['karmaplus']) {
                $msg_karmaplus .= KUNENA_URLICONSPATH . $fbIcons['karmaplus'];
            }
            else {
                $msg_karmaplus .= KUNENA_URLEMOTIONSPATH . "karmaplus.gif";
            }

            $msg_karmaplus .= "\" alt=\"Karma+\" border=\"0\" title=\"" . _KARMA_APPLAUD . "\" align=\"middle\" /></a>";
        }
    }

    /*let's see if we should use uddeIM integration */

    if ($fbConfig->pm_component == "uddeim" && $userid && $my->id)
    {

        //we should offer the user a PMS link
        //first get the username of the user to contact
        $PMSName = $userinfo->username;
        $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_uddeim&amp;task=new&recip=' . $userid) . "\"><img src=\"";

        if ($fbIcons['pms']) {
            $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
        }
        else {
            $msg_pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
        }

        $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
    }

    /*let's see if we should use myPMS2 integration */
    if ($fbConfig->pm_component == "pms" && $userid && $my->id)
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

    if ($userid > 0)
    {
        $sql = "SELECT count(userid) FROM #__session WHERE userid=" . $userid;

        $database->setQuery($sql);

        $isonline = $database->loadResult();

        if ($isonline && $userinfo->showOnline ==1 ) {
            $msg_online .= $fbIcons['onlineicon']
                ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
        }
        else {
            $msg_online .= $fbIcons['offlineicon']
                ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
        }
    }

    /* ClexusPM integration */

    if ($fbConfig->pm_component == "clexuspm")
    {

        //we should offer the user a PMS link

        //first get the username of the user to contact

        $PMSName = $userinfo->aid;
        $msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;task=new&amp;to=' . $userid . '' . $fmessage->subject) . "\"><img src=\"";

        if ($fbIcons['pms']) {
            $msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
        }
        else {
            $msg_pms .= KUNENA_JLIVEURL . "/components/com_mypms/images/icons/message_12px.gif";
        }

        $msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
        //mypms pro profile link
        $msg_profile = "<a href=\"" . MyPMSTools::getProfileLink($userid) . "\"><img src=\"";

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

        $database->setQuery("SELECT icq,ym,msn,aim,website,location FROM #__mypms_profiles WHERE user='" . $PMSName . "'");
        $profileitems = $database->loadObjectList();
        	check_dberror("Unable to load mypms_profiles.");

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

        unset($profileitems);
    }

    //Check if the Community Builder settings are on, and set the variables accordingly.
    if ($fbConfig->fb_profile == 'cb' && $userid > 0)
    {
        $msg_profile = "<a href=\"" . JRoute::_('index.php?option=com_comprofiler&amp;task=userProfile&amp;user=' . $userid . '&amp;Itemid=1') . "\"><img src=\"";

        if ($fbIcons['userprofile']) {
            $msg_profile .= KUNENA_URLICONSPATH . $fbIcons['userprofile'];
        }
        else {
            $msg_profile .= KUNENA_JLIVEURL . "/components/com_comprofiler/images/profiles.gif";
        }

        $msg_profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
    }

    /* */

    $jr_username = $user->name;

    // (JJ) JOOMLA STYLE CHECK
    if ($fbConfig->joomlastyle < 1) {
        $boardclass = "fb_";
    }
?>

    <table class="fb_profile_cover" width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
        <tr>
            <td class = "<?php echo $boardclass; ?>profile-left" align="center" valign="top" width="25%">
            <!-- Kunena Profile -->
                <?php
                if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/userinfos.php')) {
                    include(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/userinfos.php');
                }
                else {
                    include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/fbprofile/userinfos.php');
                }
                ?>

            <!-- /Kunena Profile -->
            </td>

            <td class = "<?php echo $boardclass; ?>profile-right" valign="top" width="74%">
            <!-- User Messages -->



            <?php

                if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/summary.php')) {
                    include(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/summary.php');
                }
                else {
                    include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/fbprofile/summary.php');
                }
                ?>

                <?php
                if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/forummsg.php')) {
                    include(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/forummsg.php');
                }
                else {
                    include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/fbprofile/forummsg.php');
                }
                ?>
            </td>
        </tr>
    </table>

    <?php
/*    end of function        */
}
?>
<!-- -->

<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id="fb_bottomarea"   border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once(KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
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
