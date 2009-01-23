<?php
/**
* @version $Id: myprofile.php 1016 2008-08-16 15:09:55Z racoon $
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

if ($my->id != "" && $my->id != 0)
{


/* if ($my->id < 1)
{
   mosRedirect("index.php?option=com_fireboard" . FB_FB_ITEMID_SUFFIX, "Please login first");
}

*/

//we got a valid and logged on user so we can go on
if (file_exists(JB_ABSTMPLTPATH . '/fb_pathway.php'))
{
    require_once (JB_ABSTMPLTPATH . '/fb_pathway.php');
}
else
{
    require_once (JB_ABSPATH . '/template/default/fb_pathway.php');
}

if (!$fbConfig->cb_profile) //<-- IF CB profile active begin
{

	//Get joomla userinfo needed later on, this limits the amount of queries
    $juserinfo = new mosUser($database);
    $juserinfo->load($my->id);

    //Get userinfo needed later on, this limits the amount of queries
    $userinfo = new fbUserprofile($database);
    $userinfo->load($my->id);

    //use ClexusPM avatar if configured
    if ($fbConfig->avatar_src == "clexuspm")
    {
        $database->setQuery("SELECT picture FROM #__mypms_profiles WHERE userid='$my->id'");
        $avatar = $database->loadResult();
    }
    elseif ($fbConfig->avatar_src == "cb")
    {
        $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='$my->id'");
        $avatar = $database->loadResult();
    }
    else
    {
        $avatar = $fbavatar;
    }

    //user type determination
    $ugid = $userinfo->gid;
    $uIsMod = 0;
    $uIsAdm = 0;

    if ($ugid > 0)
    { //only get the groupname from the ACL if we're sure there is one
        $agrp = strtolower($acl->get_group_name($ugid, 'ARO'));
    }

    if ($ugid == 0)
    {
        $usr_usertype = _VIEW_VISITOR;
    }
    else
    {
        if (strtolower($agrp) == "administrator" || strtolower($agrp) == "superadministrator" || strtolower($agrp) == "super administrator")
        {
            $usr_usertype = _VIEW_ADMIN;
            $uIsAdm = 1;
        }
        elseif ($uIsMod)
        {
            $usr_usertype = _VIEW_MODERATOR;
        }
        else
        {
            $usr_usertype = _VIEW_USER;
        }
    }

    //done usertype determination, phew...

    //Get the max# of posts for any one user

    $database->setQuery("SELECT max(posts) from #__fb_users");
    $maxPosts = $database->loadResult();

    //# of post for this user and ranking

    $numPosts = (int)$userinfo->posts;
    $ordering = $userinfo->ordering;
	$hideEmail = $userinfo->hideEmail;
	$showOnline = $userinfo->showOnline;
} // <-- IF CB profile active finish
?>
<!-- B:My Profile -->

<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
    <tr>
        <td class = "fb_myprofile_left" valign = "top"  width="20%">
        <!-- B:My Profile Left -->
            <?php
            if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php'))
            {
                include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php');
            }
            else
            {
                include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_menu.php');
            }
            ?>

        <!-- F:My Profile Left -->
        </td>

        <td class = "fb_myprofile_mid" valign = "top" width="5">&nbsp;

        </td>

        <td class = "fb_myprofile_right" valign = "top">
            <!-- B:My Profile Right -->

            <?php
            switch ($do)
            {
                case "show":
                default:
                    // B: Summary

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_summary.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_summary.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_summary.php');
                    }

                    // F: Summary
                    break;

                case "showmsg":

                    // B: Show Posts

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_msg.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_msg.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_msg.php');
                    }

                    // F: Show Posts
                    break;

                case "showavatar":

                    // B: Settings
                    if (!$fbConfig->cb_profile)
                    {
                        if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_avatar.php'))
                        {
                            include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_avatar.php');
                        }
                        else
                        {
                            include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_avatar.php');
                        }
                    }

                    // F: Settings
                    break;

                case "updateavatar":
                    $rowItemid = mosGetParam($_REQUEST, 'Itemid');

                    $deleteAvatar = mosGetParam($_POST, 'deleteAvatar', 0);
                    $avatar = mosGetParam($_POST, 'avatar', '');

                    if ($deleteAvatar == 1)
                    {
                        $avatar = "";
                    }

                    $database->setQuery("UPDATE #__fb_users set   avatar='$avatar'  where userid=$my_id");

                    if (!$database->query())
                    {
                        echo _USER_PROFILE_NOT_A . " <strong><font color=\"red\">" . _USER_PROFILE_NOT_B . "</font></strong> " . _USER_PROFILE_NOT_C . ".<br /><br />";
                    }
                    else
                    {
                        echo _USER_PROFILE_UPDATED . "<br /><br />";
                    }

                    echo _USER_RETURN_A . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . "&amp;func=uploadavatar") . '">' . _USER_RETURN_B . "</a><br /><br />";
            ?>

                <script language = "javascript">
                    setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=uploadavatar');?>'", 3500);
                </script>

                <?php
                break;

                case "showset":
                    // B: Settings
                    if (!$fbConfig->cb_profile)
                    {
                        if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_set.php'))
                        {
                            include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_set.php');
                        }
                        else
                        {
                            include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_set.php');
                        }
                    }

                    // F: Settings
                    break;

                case "updateset":
                    $rowItemid = mosGetParam($_REQUEST, 'Itemid');

                    $newview = mosGetParam($_POST, 'newview', 'flat');
                    (int)$neworder = mosGetParam($_POST, 'neworder', 0);
					(int)$newhideEmail = mosGetParam($_POST, 'newhideEmail', 1);
					(int)$newshowOnline = mosGetParam($_POST, 'newshowOnline', 1);

                    $database->setQuery("UPDATE #__fb_users set  view='$newview', ordering='$neworder', hideEmail='$newhideEmail', showOnline='$newshowOnline'  where userid=$my_id");
                    setcookie("fboard_settings[current_view]", $newview);

                    if (!$database->query())
                    {
                        echo _USER_PROFILE_NOT_A . " <strong><font color=\"red\">" . _USER_PROFILE_NOT_B . "</font></strong> " . _USER_PROFILE_NOT_C . ".<br /><br />";
                    }
                    else
                    {
                        echo _USER_PROFILE_UPDATED . "<br /><br />";
                    }

                    echo _USER_RETURN_A . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . "&amp;func=myprofile&amp;do=showset") . '">' . _USER_RETURN_B . "</a><br /><br />";
                ?>

                <script language = "javascript">
                    setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=showset');?>'", 3500);
                </script>

                <?php
                break;

                case "profileinfo":
                    // B: Signature

                    $bd = @explode("-" , $userinfo->birthdate);

                    $ulists["year"] = $bd[0];
                    $ulists["month"] = $bd[1];
                    $ulists["day"] = $bd[2];

                    $genders[] = mosHTML::makeOption("", "");
                    $genders[] = mosHTML::makeOption("1", _FB_MYPROFILE_MALE);
                    $genders[] = mosHTML::makeOption("2", _FB_MYPROFILE_FEMALE);

                    $ulists["gender"] = mosHTML::selectList( $genders, 'gender', 'class="inputbox"', 'value', 'text', $userinfo->gender );


                    if (!$fbConfig->cb_profile)
                    {
                        include (JB_ABSSOURCESPATH . 'fb_bb.js.php');

                        if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_profile_info.php'))
                        {
                            include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_profile_info.php');
                        }
                        else
                        {
                            include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_profile_info.php');
                        }
                    }

                    // F: Signature
                    break;

                case "saveprofileinfo":

                    $user_id = intval( mosGetParam( $_POST, 'id', 0 ));

    // do some security checks
    if ($my->id == 0 || $user_id == 0 || $user_id != $my->id) {
        mosNotAuth();
        return;
    }

        // simple spoof check security
    josSpoofCheck();

        $rowu = new fbUserprofile( $database );
        $rowu->load( (int)$user_id );

                    $deleteSig = mosGetParam($_POST, 'deleteSig', 0);
                    $signature = mosGetParam($_POST, 'message', null, _MOS_ALLOWRAW);
                    $bday1 = mosGetParam($_POST, 'bday1', '0000');
                    $bday2 = mosGetParam($_POST, 'bday2', '00');
                    $bday3 = mosGetParam($_POST, 'bday3', '00');


                        if (!$rowu->bind( $_POST, 'moderator posts karma group_id uhits' )) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
                    $rowu->birthdate = $bday1."-".$bday2."-".$bday3;


                    $signature = trim($signature);
                    //parse the message for some preliminary bbcode and stripping of HTML
                    //$rowu->signature = smile::bbencode_first_pass($signature);
                    $rowu->signature = $signature;

                    if ($deleteSig == 1)
                    {
                        $rowu->signature = "";
                    }

                    //mosMakeHtmlSafe($rowu);

                        if (!$rowu->check()) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$rowu->store()) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }

                        echo _USER_RETURN_A . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . "&amp;func=myprofile&amp;do=showsig") . '">' . _USER_RETURN_B . "</a><br /><br />";
                ?>



                <script language = "javascript">
                    setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=profileinfo');?>'", 3500);
                </script>
<?php

                break;

                case "showsub":
                    $pageperlistlm = 15;

                    $limit = intval(trim(mosGetParam($_REQUEST, 'limit', $pageperlistlm)));
                    $limitstart = intval(trim(mosGetParam($_REQUEST, 'limitstart', 0)));

                    $query = "select thread from #__fb_subscriptions where userid=$my->id";
                    $database->setQuery($query);

                    $total = count($database->loadObjectList());
                    	check_dberror("Unable to load subscriptions for user.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all subscriptions for this user
                    $database->setQuery("select thread from #__fb_subscriptions where userid=$my->id ORDER BY thread DESC LIMIT $limitstart, $limit");
                    $subslist = $database->loadObjectList();
                    	check_dberror("Unable to load subscriptions.");
                    $csubslist = count($subslist);

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_subs.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_subs.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_subs.php');
                    }

                    break;

                case "showfav":
                    $pageperlistlm = 15;

                    $limit = intval(trim(mosGetParam($_REQUEST, 'limit', $pageperlistlm)));
                    $limitstart = intval(trim(mosGetParam($_REQUEST, 'limitstart', 0)));

                    $query = "select thread from #__fb_favorites where userid=$my->id";
                    $database->setQuery($query);

                    $total = count($database->loadObjectList());
                    	check_dberror("Unable to load favorites.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all favorites for this user
                    $database->setQuery("select thread from #__fb_favorites where userid=$my->id ORDER BY thread DESC LIMIT $limitstart, $limit");
                    $favslist = $database->loadObjectList();
                    	check_dberror("Unable to load favorites.");
                    $cfavslist = count($favslist);

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_fav.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_fav.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_fav.php');
                    }

                    break;

                case "showmod":
                    //get all forums for which this user is assigned as moderator, BUT only if the user isn't an admin
                    //since these are moderators for all forums (regardless if a forum is set to be moderated)
                    if (!$is_admin)
                    {
                        $database->setQuery("select #__fb_moderation.catid,#__fb_categories.name from #__fb_moderation left join #__fb_categories on #__fb_categories.id=#__fb_moderation.catid where #__fb_moderation.userid=$my->id");
                        $modslist = $database->loadObjectList();
                        	check_dberror("Unable to load moderators.");
                        $cmodslist = count($modslist);
                    }

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_mod.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_mod.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_mod.php');
                    }

                    break;

                case "unsubscribe":
                    $cid = mosGetParam($_REQUEST, "cid", array ());

                    @array_walk($cid, "intval");
                    $cids = @implode(',', $cid);

                    $database->setQuery("DELETE FROM #__fb_subscriptions WHERE  userid=$my->id  AND thread in ($cids) ");

                    if (!$database->query())
                    {
                        echo _USER_UNSUBSCRIBE_A . " <strong><font color=\"red\">" . _USER_UNSUBSCRIBE_B . "</font></strong> " . _USER_UNSUBSCRIBE_C . ".<br /><br />";
                    }
                    else
                    {
                        echo _USER_UNSUBSCRIBE_YES . ".<br /><br />";
                    }

                    if ($fbConfig->cb_profile)
                    {
                        echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler&amp;Itemid='" . FB_CB_ITEMID . "'&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
                ?>

                <script language = "javascript">
                    setTimeout("location='index.php?option=com_comprofiler<?php echo FB_CB_ITEMID_SUFFIX; ?>&tab=getForumTab'", 3500);
                </script>

            <?php
                    }
                    else
                    {
                        echo _USER_RETURN_A . " <a href=\"" . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub') . "\">" . _USER_RETURN_B . "</a><br /><br />";
            ?>

                    <script language = "javascript">
                        setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=showsub');?>'", 3500);
                    </script>

            <?php
                    }

                    break;


					/////
					case "unsubscribeitem":
                   $database->setQuery("DELETE from #__fb_subscriptions where userid=$my->id and thread=$thread");

						if (!$database->query()) {
							echo _USER_UNSUBSCRIBE_A . " <strong><font color=\"red\">" . _USER_UNSUBSCRIBE_B . "</font></strong> " . _USER_UNSUBSCRIBE_C . ".<br /><br />";
						}
						else {
							echo _USER_UNSUBSCRIBE_YES . ".<br /><br />";
						}

						if ($fbConfig->cb_profile) {
							echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler&amp;Itemid='".FB_CB_ITEMID."'&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
					?>

						<script language = "javascript">
							setTimeout("location='index.php?option=com_comprofiler<?php echo FB_CB_ITEMID_SUFFIX; ?>&tab=getForumTab'", 3500);
						</script>
                        <a href="javascript:history.go(-1)"><?php echo _BACK ;?></a>

				<?php
						}
						else {
							echo _USER_RETURN_A . " <a href=\"". sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show')."\">" . _USER_RETURN_B . "</a><br /><br />";
				?>

						<script language = "javascript">
						setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=show');?>'", 3500);
						</script>
                        <a href="javascript:history.go(-1)"><?php echo _BACK ;?></a>

            <?php
                    }

                    break;



					////

                case "unfavorite":
                    $cid = mosGetParam($_REQUEST, "cid", array ());

                    @array_walk($cid, "intval");
                    $cids = @implode(',', $cid);

                    $database->setQuery("DELETE from #__fb_favorites where userid=$my->id  AND thread in ($cids)");

                    if (!$database->query())
                    {
                        echo _USER_UNFAVORITE_A . " <strong><font color=\"red\">" . _USER_UNFAVORITE_B . "</font></strong> " . _USER_UNFAVORITE_C . ".<br /><br />";
                    }
                    else
                    {
                        echo _USER_UNFAVORITE_YES . ".<br /><br />";
                    }

                    if ($fbConfig->cb_profile)
                    {
                        echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler" . FB_CB_ITEMID_SUFFIX . "&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
            ?>

                <script language = "javascript">
                    setTimeout("location='index.php?option=com_comprofiler".FB_CB_ITEMID_SUFFIX."&tab=getForumTab'", 3500);
                </script>

            <?php
                    }
                    else
                    {
                        echo _USER_RETURN_A . " <a href=\"" . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav') . "\">" . _USER_RETURN_B . "</a><br /><br />";
            ?>

                    <script language = "javascript">
                        setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=showfav');?>'", 3500);
                    </script>

                        <?php // B: unfavoriteall
                    }

                    break;

					//

					 case "unfavoriteitem":

						$database->setQuery("DELETE from #__fb_favorites where userid=$my->id and thread=$thread");

						if (!$database->query()) {
							echo _USER_UNFAVORITE_A . " <strong><font color=\"red\">" . _USER_UNFAVORITE_B . "</font></strong> " . _USER_UNFAVORITE_C . ".<br /><br />";
						}
						else {
							echo _USER_UNFAVORITE_YES . ".<br /><br />";
						}

						if ($fbConfig->cb_profile) {
							echo _USER_RETURN_A . " <a href=\"index.php?option=com_comprofiler".FB_CB_ITEMID_SUFFIX."&amp;tab=getForumTab\">" . _USER_RETURN_B . "</a><br /><br />";
				?>

						<script language = "javascript">
							setTimeout("location='index.php?option=com_comprofiler".FB_CB_ITEMID_SUFFIX."&tab=getForumTab'", 3500);
						</script>
                        <a href="javascript:history.go(-1)"><?php echo _BACK ;?></a>

				<?php
						}
						else {
							echo _USER_RETURN_A . " <a href=\"index.php?option=com_fireboard&amp;Itemid=$Itemid&amp;func=myprofile&amp;do=show\">" . _USER_RETURN_B . "</a><br /><br />";
				?>
					<a href="javascript:history.go(-1)"><?php echo _BACK ;?></a>

						<script language = "javascript">
							   setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=show');?>'", 3500);
						</script>

                   <?php
						}

                    break;

					//

                case "userdetails":


					/* We don't need
					// security check to see if link exists in a menu
                    $link = 'index.php?option=com_user&amp;task=UserDetails';

                    $query = "SELECT id" . "\n FROM #__menu" . "\n WHERE link LIKE '%$link%'" . "\n AND published = 1";
                    $database->setQuery($query);
                    $exists = $database->loadResult();

                    if (!$exists)
                    {
                        mosNotAuth();
                        return;
                    }
					*/

                    require_once ($mainframe->getCfg("absolute_path") . '/administrator/components/com_users/users.class.php');

                    $row = new mosUser($database);
                    $row->load((int)$my->id);
                    $row->orig_password = $row->password;

                    $row->name = trim($row->name);
                    $row->email = trim($row->email);
                    $row->username = trim($row->username);

                    $file = $mainframe->getPath('com_xml', 'com_users');
                    $params = &new mosUserParameters($row->params, $file, 'component');

                    if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_userdetails_form.php'))
                    {
                        include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_userdetails_form.php');
                    }
                    else
                    {
                        include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_userdetails_form.php');
                    }

                    break;

                case "usersave":
                    $user_id = intval(mosGetParam($_POST, 'id', 0));

                    $uid = $my->id;

                    // do some security checks
                    if ($uid == 0 || $user_id == 0 || $user_id != $uid)
                    {
                        mosNotAuth();
                        return;
                    }

                    // simple spoof check security
                    josSpoofCheck();

                    $row = new mosUser($database);
                    $row->load((int)$user_id);

                    $orig_password = $row->password;
                    $orig_username = $row->username;

                    if (!$row->bind($_POST, 'gid usertype'))
                    {
                        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }

                    $row->name = trim($row->name);
                    $row->email = trim($row->email);
                    $row->username = trim($row->username);

                    mosMakeHtmlSafe ($row);

                    if (isset($_POST['password']) && $_POST['password'] != '')
                    {
                        if (isset($_POST['verifyPass']) && ($_POST['verifyPass'] == $_POST['password']))
                        {
                            $row->password = trim($row->password);
                            $password = md5($row->password);

                            $before1013 = false;
                            if (class_exists('joomlaVersion')) {
                            	$joomlaVersion = new joomlaVersion();
                            	$before1013 = $joomlaVersion->getShortVersion() < "1.0.13";
                            }
                            if ($before1013) {
                            	$row->password = md5($row->password);
                            }
                            else {
                            //Joomla 1.0.13+ compatibility
                                $salt = mosMakePassword(16);
                                $crypt = md5($row->password.$salt);
                                $row->password = $crypt.':'.$salt;
                            }
                        }
                        else
                        {
                            echo "<script> alert(\"" . addslashes(_PASS_MATCH) . "\"); window.history.go(-1); </script>\n";
                            exit();
                        }
                    }
                    else
                    {
                        // Restore 'original password'
                        $row->password = $orig_password;
                    }

                    if ($mosConfig_frontend_userparams == '1' || $mosConfig_frontend_userparams == 1 || $mosConfig_frontend_userparams == NULL)
                    {
                        // save params
                        $params = mosGetParam($_POST, 'params', '');

                        if (is_array($params))
                        {
                            $txt = array ();

                            foreach ($params as $k => $v)
                            {
                                $txt[] = "$k=$v";
                            }

                            $row->params = implode("\n", $txt);
                        }
                    }

                    if (!$row->check())
                    {
                        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }

                    if (!$row->store())
                    {
                        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                        exit();
                    }

                    // check if username has been changed
                    if ($orig_username != $row->username)
                    {
                        // change username value in session table
                        $query
                            = "UPDATE #__session"
                            . "\n SET username = " . $database->Quote($row->username) . "\n WHERE username = " . $database->Quote($orig_username) . "\n AND userid = " . (int)$my->id . "\n AND gid = " . (int)$my->gid . "\n AND guest = 0";
                        $database->setQuery($query);
                        $database->query();
                    }

                    mosRedirect('index.php?option=com_fireboard&amp;func=myprofile' . FB_FB_ITEMID_SUFFIX, _FB_USER_DETAILS_SAVE);
                    break;
            }
            ?>

        <!-- F:My Profile Right -->
        </td>
    </tr>
</table>
<!-- F:My Profile -->

<?php
}
else
{
 echo '<b>'. _COM_A_REGISTERED_ONLY.'</b><br />';
   echo _FORUM_UNAUTHORIZIED2 ;
}
?>
<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_bottomarea" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                {
                    require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                }
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

