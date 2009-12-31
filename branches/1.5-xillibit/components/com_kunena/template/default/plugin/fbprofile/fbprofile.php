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

$kunena_app =& JFactory::getApplication();
$kunena_acl = &JFactory::getACL();
$kunena_config =& CKunenaConfig::getInstance();

if ($kunena_config->fb_profile == 'cb') {
        $userid = JRequest::getInt('userid', 0);
	$url = CKunenaCBProfile::getProfileURL($userid);
	header("HTTP/1.1 307 Temporary Redirect");
	header("Location: " . htmlspecialchars_decode($url));
	$kunena_app->close();
}

$document=& JFactory::getDocument();
$document->setTitle(_KUNENA_USERPROFILE_PROFILE . ' - ' . stripslashes($kunena_config->board_title));

if ($kunena_my->id) //registered only
{
    require_once(KUNENA_PATH_LIB .DS. 'kunena.authentication.php');
    require_once(KUNENA_PATH_LIB .DS. 'kunena.statsbar.php');

    $task = JRequest::getCmd('task', 'showprf');

    switch ($task)
    {
        case "showprf":
            $userid = JRequest::getInt('userid', 0);

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
    $kunena_config =& CKunenaConfig::getInstance();
    $kunena_acl = &JFactory::getACL();
    $kunena_my = &JFactory::getUser();
    $kunena_db = &JFactory::getDBO();
    global $kunena_emoticons;

    //Get userinfo needed later on, this limits the amount of queries
    $kunena_db->setQuery("SELECT a.*, b.* FROM #__fb_users AS a INNER JOIN #__users AS b ON b.id=a.userid WHERE a.userid='{$userid}'");

    $userinfo = $kunena_db->loadObject();
    check_dberror('Unable to get user profile info.');

    if (!$userinfo) {
	$kunena_db->setQuery("SELECT * FROM #__users WHERE id='{$userid}'");
	$userinfo = $kunena_db->loadObject();
	check_dberror('Unable to get user profile info.');

	if (!$userinfo) {
		echo '<h3>' . _KUNENA_PROFILE_NO_USER . '</h3>';
		return;
	} else {
		// Check moderator status (admin is moderator)
		$aro_group = $kunena_acl->getAroGroup($userid);
		$kunena_is_admin = (strtolower($aro_group->name) == 'super administrator' || strtolower($aro_group->name) == 'administrator');

		// there's no profile; set userid and moderator status.
		$kunena_db->setQuery("INSERT INTO #__fb_users (userid,moderator) VALUES ('$userid','$kunena_is_admin')");
		$kunena_db->query();
		check_dberror('Unable to create user profile.');

		$kunena_db->setQuery("SELECT a.*, b.* FROM #__fb_users AS a LEFT JOIN #__users AS b ON b.id=a.userid WHERE a.userid='{$userid}'");

		$userinfo = $kunena_db->loadObject();
		check_dberror('Unable to get user profile info.');

		// TODO: For future use
		// echo '<h3>' . _KUNENA_PROFILE_NOT_FOUND . '</h3>';
		// return;
	}

    }

	// User Hits
	$kunena_db->setQuery('UPDATE #__fb_users SET uhits=uhits+1 WHERE userid='.$userid);
	$kunena_db->query() or trigger_dberror("Unable to update user hits.");

	// get userprofile hits
	$msg_html->userhits = $userinfo->uhits;

    //get the username:
    $fb_username = "";

    if ($kunena_config->username) {
        $fb_queryName = "username";
    }
    else {
        $fb_queryName = "name";
    }

    $fb_username = $userinfo->{$fb_queryName};

    $lists["userid"] = $userid;

	$msg_html->username = $fb_username;
    // $msg_html->username = ($fmessage->email != "" && $kunena_my->id > 0 && $kunena_config->showemail == '1') ? "<a href=\"mailto:" . $fmessage->email . "\">" . $fb_username . "</a>" : $fb_username;

    if ($kunena_config->allowavatar)
    {
        $Avatarname = $userinfo->username;

        if ($kunena_config->avatar_src == "jomsocial")
		{
			// Get CUser object
			$user =& CFactory::getUser($userid);
		    $msg_html->avatar = '<span class="fb_avatar"><img src="' . $user->getAvatar() . '" alt="" /></span>';
		}
        else if ($kunena_config->avatar_src == "clexuspm") {
            $msg_html->avatar = '<span class="fb_avatar"><img src="' . MyPMSTools::getAvatarLinkWithID($userid, "b") . '" alt="" /></span>';
        }
        else if ($kunena_config->avatar_src == "cb")
        {
            $kunenaProfile = CKunenaCBProfile::getInstance();
			$msg_html->avatar = '<span class="fb_avatar">' . $kunenaProfile->showAvatar($userid, '', 0) . '</span>';
        }
		else if ($kunena_config->avatar_src == "aup")
		{
			$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
			if ( file_exists($api_AUP)) {
				( $kunena_config->fb_profile=='aup' ) ? $showlink=1 : $showlink=0;
				$msg_html->avatar = '<span class="fb_avatar">'.AlphaUserPointsHelper::getAupAvatar( $userinfo->userid, $showlink ).'</span>';
			}
		}
        else
        {
        	$avatar = $userinfo->avatar;

        	if ($avatar != '')
        	{
        		if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/l_' . $avatar))
        		{
        			$msg_html->avatar = '<span class="fb_avatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '"  alt="" style="max-width: '.$kunena_config->avatarwidth.'px; max-height: '.$kunena_config->avatarheight.'px;" /></span>';
				}
				else
				{
					$msg_html->avatar = '<span class="fb_avatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '"  alt="" /></span>';
				}
        	}

        	else {$msg_html->avatar = '<span class="fb_avatar"><img  border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/nophoto.jpg"  alt="" /></span>'; }
        }
    }

    if ($kunena_config->showuserstats)
    {
        //user type determination
        $ugid = $userinfo->gid;
        $uIsMod = 0;
        $uIsAdm = 0;

        if ($ugid > 0) { //only get the groupname from the ACL if we're sure there is one
            $agrp = strtolower($kunena_acl->get_group_name($ugid, 'ARO'));
        }

        if ($ugid == 0) {
            $msg_html->usertype = _VIEW_VISITOR;
        }
        else
        {
            if (strtolower($agrp) == "administrator" || strtolower($agrp) == "superadministrator" || strtolower($agrp) == "super administrator")
            {
                $msg_html->usertype = _VIEW_ADMIN;
                $uIsAdm = 1;
            }
            elseif ($uIsMod) {
                $msg_html->usertype = _VIEW_MODERATOR;
            }
            else {
                $msg_html->usertype = _VIEW_USER;
            }
        }

        //done usertype determination, phew...

        //Get the max# of posts for any one user
        $kunena_db->setQuery("SELECT MAX(posts) FROM #__fb_users");
        $maxPosts = $kunena_db->loadResult();

        //# of post for this user and ranking

        $numPosts = (int)$userinfo->posts;

							//ranking
							if ($kunena_config->showranking)
							{

								if ($userinfo->rank != '0')
								{
												//special rank
												$kunena_db->setQuery("SELECT * FROM #__fb_ranks WHERE rank_id='{$userinfo->rank}'");
												$getRank = $kunena_db->loadObjectList();
													check_dberror("Unable to load ranks.");
												$rank=$getRank[0];
												$rText = $rank->rank_title;
												$rImg = KUNENA_URLRANKSPATH . $rank->rank_image;
									}
									if ($userinfo->rank == '0')
									{
											//post count rank
												$kunena_db->setQuery("SELECT * FROM #__fb_ranks WHERE ((rank_min <= '{$numPosts}') AND (rank_special = '0')) ORDER BY rank_min DESC", 0, 1);
												$getRank = $kunena_db->loadObjectList();
													check_dberror("Unable to load ranks.");
												$rank=$getRank[0];
												$rText = $rank->rank_title;
												$rImg = KUNENA_URLRANKSPATH . $rank->rank_image;
									}

									if ($userinfo->rank == '0' && $uIsMod)
									{
													$rText = _RANK_MODERATOR;
													$rImg = KUNENA_URLRANKSPATH . 'rankmod.gif';
									}

									if ($userinfo->rank == '0' && $uIsAdm)
									{
													$rText = _RANK_ADMINISTRATOR;
													$rImg = KUNENA_URLRANKSPATH . 'rankadmin.gif';
									}

									if ($kunena_config->rankimages) {
													$msg_html->userrankimg = '<img src="' . $rImg . '" alt="" />';
									}

								$msg_html->userrank = $rText;

            $useGraph = 0; //initialization

            if (!$kunena_config->poststats)
            {
                $msg_html->posts = '<div class="viewcover">' .
                             "<strong>" . _POSTS . " $numPosts" . "</strong>" .
                             "</div>";
                $useGraph = 0;
            }
            else
            {
                $msg_html->myGraph = new phpGraph;
                //$msg_html->myGraph->SetGraphTitle(_POSTS);
                $msg_html->myGraph->AddValue(_POSTS, $numPosts);
                $msg_html->myGraph->SetRowSortMode(0);
                $msg_html->myGraph->SetBarImg(KUNENA_URLGRAPHPATH . "col" . $kunena_config->statscolor . "m.png");
                $msg_html->myGraph->SetBarImg2(KUNENA_URLEMOTIONSPATH . "graph.gif");
                $msg_html->myGraph->SetMaxVal($maxPosts);
                $msg_html->myGraph->SetShowCountsMode(2);
                $msg_html->myGraph->SetBarWidth(4); //height of the bar
                $msg_html->myGraph->SetBorderColor("#333333");
                $msg_html->myGraph->SetBarBorderWidth(0);
                $msg_html->myGraph->SetGraphWidth(120); //should match column width in the <TD> above -5 pixels
                //$msg_html->myGraph->BarGraphHoriz();
                $useGraph = 1;
            }
        }
    }

	// Start Integration AlphaUserPoints
	// *********************************
	$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
	if ($kunena_config->alphauserpoints && file_exists($api_AUP)) {
		//Get the max# of points for any one user
		$database  =& JFactory::getDBO();
		$database->setQuery("SELECT max(points) from #__alpha_userpoints");
		$maxPoints = $database->loadResult();

		$database->setQuery("SELECT points from #__alpha_userpoints WHERE `userid`='".$userid."'");
		$numPoints = $database->loadResult();

		$msg_html->myGraphAUP = new phpGraph;
		$msg_html->myGraphAUP->AddValue(_KUNENA_AUP_POINTS, $numPoints);
		$msg_html->myGraphAUP->SetRowSortMode(0);
		$msg_html->myGraphAUP->SetBarImg(KUNENA_URLGRAPHPATH . "col" . $kunena_config->statscolor . "m.png");
		$msg_html->myGraphAUP->SetBarImg2(KUNENA_URLEMOTIONSPATH . "graph.gif");
		$msg_html->myGraphAUP->SetMaxVal($maxPoints);
		$msg_html->myGraphAUP->SetShowCountsMode(2);
		$msg_html->myGraphAUP->SetBarWidth(4); //height of the bar
		$msg_html->myGraphAUP->SetBorderColor("#333333");
		$msg_html->myGraphAUP->SetBarBorderWidth(0);
		$msg_html->myGraphAUP->SetGraphWidth(120); //should match column width in the <TD> above -5 pixels
		$useGraph = 1;
	}
	// End Integration AlphaUserPoints
	// *******************************

    //karma points and buttons
    if ($kunena_config->showkarma && $userid != '0')
    {
        $karmaPoints = $userinfo->karma;
        $karmaPoints = (int)$karmaPoints;
        $msg_html->karma = "<strong>" . _KARMA . ":</strong> $karmaPoints";

		$msg_html->karmaminus = '';
		$msg_html->karmaplus = '';
        if ($kunena_my->id != '0' && $kunena_my->id != $userid)
        {
            $msg_html->karmaminus .= "<a href=\"" . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=karma&amp;do=decrease&amp;userid=' . $userid) . "\"><img src=\"";

            if (isset($kunena_emoticons['karmaminus'])) {
                $msg_html->karmaminus .= KUNENA_URLICONSPATH . $kunena_emoticons['karmaminus'];
            }
            else {
                $msg_html->karmaminus .= KUNENA_URLEMOTIONSPATH . "karmaminus.gif";
            }

            $msg_html->karmaminus .= "\" alt=\"Karma-\" border=\"0\" title=\"" . _KARMA_SMITE . "\" align=\"middle\" /></a>";
            $msg_html->karmaplus .= "<a href=\"" . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=karma&amp;do=increase&amp;userid=' . $userid) . "\"><img src=\"";

            if (isset($kunena_emoticons['karmaplus'])) {
                $msg_html->karmaplus .= KUNENA_URLICONSPATH . $kunena_emoticons['karmaplus'];
            }
            else {
                $msg_html->karmaplus .= KUNENA_URLEMOTIONSPATH . "karmaplus.gif";
            }

            $msg_html->karmaplus .= "\" alt=\"Karma+\" border=\"0\" title=\"" . _KARMA_APPLAUD . "\" align=\"middle\" /></a>";
        }
    }

    /*let's see if we should use uddeIM integration */

    if ($kunena_config->pm_component == "uddeim" && $userid && $kunena_my->id)
    {

        //we should offer the user a PMS link
        //first get the username of the user to contact
        $PMSName = $userinfo->username;
        $msg_html->pms = "<a href=\"" . JRoute::_('index.php?option=com_uddeim&amp;task=new&recip=' . $userid) . "\"><img src=\"";

        if ($kunena_emoticons['pms']) {
            $msg_html->pms .= KUNENA_URLICONSPATH . $kunena_emoticons['pms'];
        }
        else {
            $msg_html->pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
        }

        $msg_html->pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
    }

    /*let's see if we should use myPMS2 integration */
    if ($kunena_config->pm_component == "pms" && $userid && $kunena_my->id)
    {
        //we should offer the user a PMS link
        //first get the username of the user to contact
        $PMSName = $userinfo->username;
        $msg_html->pms = "<a href=\"" . JRoute::_('index.php?option=com_pms&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src=\"";

        if ($kunena_emoticons['pms']) {
            $msg_html->pms .= KUNENA_URLICONSPATH . $kunena_emoticons['pms'];
        }
        else {
            $msg_html->pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
        }

        $msg_html->pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
    }

    // online - ofline status

    if ($userid > 0)
    {
        $sql = "SELECT COUNT(userid) FROM #__session WHERE userid='{$userid}'";

        $kunena_db->setQuery($sql);

        $isonline = $kunena_db->loadResult();

        if ($isonline && $userinfo->showOnline ==1 ) {
            $msg_html->online = isset($kunena_emoticons['onlineicon'])
                ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
        }
        else {
            $msg_html->online = isset($kunena_emoticons['offlineicon'])
                ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
        }
    }

    /* ClexusPM integration */

    if ($kunena_config->pm_component == "clexuspm")
    {

        //we should offer the user a PMS link

        //first get the username of the user to contact

        $PMSName = $userinfo->aid;
        $msg_html->pms = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;task=new&amp;to=' . $userid . '' . $fmessage->subject) . "\"><img src=\"";

        if ($kunena_emoticons['pms']) {
            $msg_html->pms .= KUNENA_URLICONSPATH . $kunena_emoticons['pms'];
        }
        else {
            $msg_html->pms .= KUNENA_JLIVEURL . "/components/com_mypms/images/icons/message_12px.gif";
        }

        $msg_html->pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
        //mypms pro profile link
        $msg_html->profile = "<a href=\"" . MyPMSTools::getProfileLink($userid) . "\"><img src=\"";

        if ($kunena_emoticons['userprofile']) {
            $msg_html->profile .= KUNENA_URLICONSPATH . $kunena_emoticons['userprofile'];
        }
        else {
            $msg_html->profile .= KUNENA_JLIVEURL . "/components/com_mypms/images/managecontact_icon.gif";
        }

        $msg_html->profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
        //mypms add buddy link
        $msg_html->buddy = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;user=' . $PMSName . '&amp;task=addbuddy') . "\"><img src=\"";

        if ($kunena_emoticons['pms2buddy']) {
            $msg_html->buddy .= KUNENA_URLICONSPATH . $kunena_emoticons['pms2buddy'];
        }
        else {
            $msg_html->buddy .= KUNENA_JLIVEURL . "/components/com_mypms/images/messages/addbuddy.gif";
        }

        $msg_html->buddy .= "\" alt=\"" . _VIEW_ADDBUDDY . "\" border=\"0\" title=\"" . _VIEW_ADDBUDDY . "\" /></a>";

        $kunena_db->setQuery("SELECT icq, ym, msn, aim, website, location FROM #__mypms_profiles WHERE user='{$PMSName}'");
        $profileitems = $kunena_db->loadObjectList();
        	check_dberror("Unable to load mypms_profiles.");

        foreach ($profileitems as $profileitems)
        {
            if ($profileitems->aim)
                $msg_html->aim = "<a href=\"aim:goim?screenname=" . str_replace(" ", "+", $profileitems->aim) . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "aim.png\" border=0 alt=\"\" /></a>";

            if ($profileitems->icq)
                $msg_html->icq = "<a href=\"http://www.icq.com/whitepages/wwp.php?uin=" . $profileitems->icq . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "icq.png\" border=0 alt=\"\" /></a>";

            if ($profileitems->msn)
                $msg_html->msn = "<a href=\"" . JRoute::_('index.php?option=com_mypms&amp;task=showprofile&amp;user=' . $PMSName) . "\"><img src=\"" . KUNENA_URLEMOTIONSPATH . "msn.png\" border=0 alt=\"\" /></a>";

            if ($profileitems->ym)
                $msg_html->yahoo = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $profileitems->ym . "&.src=pg\"><img src=\"http://opi.yahoo.com/online?u=" . $profileitems->ym . "&m=g&t=0\" border=0 alt=\"\" /></a>";

            if ($profileitems->location)
                $msg_html->loc = $profileitems->location;
        }

        unset($profileitems);
    }

    $jr_username = $userinfo->name;
?>

    <table class="fb_profile_cover" width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
        <tr>
            <td class = "<?php echo KUNENA_BOARD_CLASS; ?>profile-left" align="center" valign="top" width="25%">
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

            <td class = "<?php echo KUNENA_BOARD_CLASS; ?>profile-right" valign="top" width="74%">
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
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<table class = "fb_blocktable" id="fb_bottomarea"   border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($kunena_config->enableforumjump)
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
