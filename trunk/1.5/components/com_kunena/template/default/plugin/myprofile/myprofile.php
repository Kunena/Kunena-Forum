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

require_once (KUNENA_PATH_LIB .DS. "kunena.user.class.php");

$app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$fbConfig =& CKunenaConfig::getInstance();

$document->setTitle(_GEN_MYPROFILE . ' - ' . stripslashes($fbConfig->board_title));

if ($kunena_my->id != "" && $kunena_my->id != 0)
{
	//Get joomla userinfo needed later on, this limits the amount of queries
    $juserinfo = new JUser($kunena_my->id);

    //Get userinfo needed later on, this limits the amount of queries
    $userinfo = new CKunenaUserprofile();

    //use ClexusPM avatar if configured
    if ($fbConfig->avatar_src == "clexuspm")
    {
        $kunena_db->setQuery("SELECT picture FROM #__mypms_profiles WHERE userid='$kunena_my->id'");
        $avatar = $kunena_db->loadResult();
    }
    elseif ($fbConfig->avatar_src == "cb")
    {
        $kunena_db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='$kunena_my->id'");
        $avatar = $kunena_db->loadResult();
    }
    else
    {
        $avatar = $fbavatar;
    }

    //user type determination
    $ugid = $juserinfo->gid;
    $uIsMod = 0;
    $uIsAdm = 0;

    if ($ugid > 0)
    { //only get the groupname from the ACL if we're sure there is one
        $agrp = strtolower($kunena_acl->get_group_name($ugid, 'ARO'));
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

    $kunena_db->setQuery("SELECT max(posts) from #__fb_users");
    $maxPosts = $kunena_db->loadResult();

    //# of post for this user and ranking

    $numPosts = (int)$userinfo->posts;
    $ordering = $userinfo->ordering;
	$hideEmail = $userinfo->hideEmail;
	$showOnline = $userinfo->showOnline;
?>
<!-- B:My Profile -->

<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
    <tr>
        <td class = "fb_myprofile_left" valign = "top"  width="20%">
        <!-- B:My Profile Left -->
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php'))
            {
                include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php');
            }
            else
            {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_menu.php');
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

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_summary.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_summary.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_summary.php');
                    }

                    // F: Summary
                    break;

                case "showmsg":

                    // B: Show Posts

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_msg.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_msg.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_msg.php');
                    }

                    // F: Show Posts
                    break;

                case "avatar":
                    // B: Avatar
                    if ($fbConfig->fb_profile != 'cb' && $fbConfig->fb_profile != 'jomsocial')
                    {
                        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_avatar_upload.php'))
                        {
                            include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_avatar_upload.php');
                        }
                        else
                        {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_avatar_upload.php');
                        }
                    }

                    // F: Avatar
                	break;
                    
                case "showset":
                    // B: Settings
                	if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_set.php'))
                	{
                		include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_set.php');
                    }
                    else
                    {
                    	include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_set.php');
                    }

                    // F: Settings
                    break;

                case "updateset":
                    $rowItemid = JRequest::getInt('Itemid');

//                    $newview = JRequest::getVar('newview', 'flat');
                    $newview = 'flat';
                    (int)$neworder = JRequest::getInt('neworder', 0);
					(int)$newhideEmail = JRequest::getInt('newhideEmail', 1);
					(int)$newshowOnline = JRequest::getInt('newshowOnline', 1);

                    $kunena_db->setQuery("UPDATE #__fb_users set  view='$newview', ordering='$neworder', hideEmail='$newhideEmail', showOnline='$newshowOnline'  where userid=$kunena_my->id");
                    setcookie("fboard_settings[current_view]", $newview);

                    if (!$kunena_db->query())
                    {
						$app->enqueueMessage(_USER_PROFILE_NOT_A._USER_PROFILE_NOT_B._USER_PROFILE_NOT_C, 'notice');
                    }
                    else
                    {
						$app->enqueueMessage(_USER_PROFILE_UPDATED);
                    }

                    $app->redirect(JRoute::_(KUNENA_LIVEURLREL . "&amp;func=myprofile"));
                break;

                case "profileinfo":
                    // B: Signature

                    $bd = @explode("-" , $userinfo->birthdate);

                    $ulists["year"] = $bd[0];
                    $ulists["month"] = $bd[1];
                    $ulists["day"] = $bd[2];

                    $genders[] = JHTML::_('select.option', "", "");
                    $genders[] = JHTML::_('select.option', "1", _KUNENA_MYPROFILE_MALE);
                    $genders[] = JHTML::_('select.option', "2", _KUNENA_MYPROFILE_FEMALE);

                    $ulists["gender"] = JHTML::_('select.genericlist',  $genders, 'gender', 'class="inputbox"', 'value', 'text', $userinfo->gender );


                    if ($fbConfig->fb_profile != 'cb' && $fbConfig->fb_profile != 'jomSocial')
                    {
                        include (KUNENA_PATH_LIB .DS. 'kunena.bbcode.js.php');

                        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_profile_info.php'))
                        {
                            include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_profile_info.php');
                        }
                        else
                        {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_profile_info.php');
                        }
                    }

                    // F: Signature
                    break;

                case "saveprofileinfo":

                    $user_id = intval( JRequest::getVar('id', 0 ));

    // do some security checks
    if ($kunena_my->id == 0 || $user_id == 0 || $user_id != $kunena_my->id) {
        JError::raiseError( 403, JText::_("ALERTNOTAUTH") );;
        return;
    }

        $rowu = new CKunenaUserprofile();

                    $deleteSig = JRequest::getVar('deleteSig', 0);
                    $signature = JRequest::getVar('message', null, 'REQUEST', 'string', JREQUEST_ALLOWRAW);
                    $bday1 = JRequest::getVar('bday1', '0000');
                    $bday2 = JRequest::getVar('bday2', '00');
                    $bday3 = JRequest::getVar('bday3', '00');


                        if (!$rowu->bind( $_POST, 'moderator posts karma group_id uhits' )) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        $app->close();
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

                    //JFilterOutput::objectHTMLSafe($rowu);

                        if (!$rowu->check()) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        $app->close();
    }

    if (!$rowu->store()) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        $app->close();
    }

						$app->enqueueMessage(_USER_PROFILE_UPDATED);
						$app->redirect(JRoute::_(KUNENA_LIVEURLREL . "&amp;func=myprofile"));
                break;

                case "showsub":
                    $pageperlistlm = 15;

                    $limit = JRequest::getInt('limit', $pageperlistlm);
                    $limitstart = JRequest::getInt('limitstart', 0);

                    $query = "select thread from #__fb_subscriptions where userid=$kunena_my->id";
                    $kunena_db->setQuery($query);

                    $total = count($kunena_db->loadObjectList());
                    	check_dberror("Unable to load subscriptions for user.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all subscriptions for this user
                    $kunena_db->setQuery("select thread from #__fb_subscriptions where userid=$kunena_my->id ORDER BY thread DESC LIMIT $limitstart, $limit");
                    $subslist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load subscriptions.");
                    $csubslist = count($subslist);

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_subs.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_subs.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_subs.php');
                    }

                    break;

                case "showfav":
                    $pageperlistlm = 15;

                    $limit = JRequest::getInt('limit', $pageperlistlm);
                    $limitstart = JRequest::getInt('limitstart', 0);

                    $query = "select thread from #__fb_favorites where userid=$kunena_my->id";
                    $kunena_db->setQuery($query);

                    $total = count($kunena_db->loadObjectList());
                    	check_dberror("Unable to load favorites.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all favorites for this user
                    $kunena_db->setQuery("select thread from #__fb_favorites where userid=$kunena_my->id ORDER BY thread DESC LIMIT $limitstart, $limit");
                    $favslist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load favorites.");
                    $cfavslist = count($favslist);

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_fav.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_fav.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_fav.php');
                    }

                    break;

                case "showmod":
                    //get all forums for which this user is assigned as moderator, BUT only if the user isn't an admin
                    //since these are moderators for all forums (regardless if a forum is set to be moderated)
                    if (!$is_admin)
                    {
                        $kunena_db->setQuery("select #__fb_moderation.catid,#__fb_categories.name from #__fb_moderation left join #__fb_categories on #__fb_categories.id=#__fb_moderation.catid where #__fb_moderation.userid=$kunena_my->id");
                        $modslist = $kunena_db->loadObjectList();
                        	check_dberror("Unable to load moderators.");
                        $cmodslist = count($modslist);
                    }

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_mod.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_mod.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_mod.php');
                    }

                    break;

                case "unsubscribe":
                    $cid = JRequest::getVar("cid", array ());

                    @array_walk($cid, "intval");
                    $cids = @implode(',', $cid);

                    $kunena_db->setQuery("DELETE FROM #__fb_subscriptions WHERE  userid=$kunena_my->id  AND thread in ($cids) ");

                    if (!$kunena_db->query())
                    {
						$app->enqueueMessage(_USER_UNSUBSCRIBE_A._USER_UNSUBSCRIBE_B._USER_UNSUBSCRIBE_C, 'notice');
                    }
                    else
                    {
						$app->enqueueMessage(_USER_UNSUBSCRIBE_YES);
                    }
                    
                    if ($fbConfig->fb_profile == 'cb')
                    {
						$forumtab_url = CKunenaCBProfile::getForumTabURL();
						$app->redirect(JRoute::_($forumtab_url));
                    }
                    else
                    {
                    	$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'));
                    }

                    break;


					/////
					case "unsubscribeitem":
                   $kunena_db->setQuery("DELETE from #__fb_subscriptions where userid=$kunena_my->id and thread=$thread");

						if (!$kunena_db->query()) {
							$app->enqueueMessage(_USER_UNSUBSCRIBE_A._USER_UNSUBSCRIBE_B._USER_UNSUBSCRIBE_C, 'notice');
						}
						else {
							$app->enqueueMessage(_USER_UNSUBSCRIBE_YES);
						}

						if ($fbConfig->fb_profile == 'cb') {
							$forumtab_url = CKunenaCBProfile::getForumTabURL();
							$app->redirect(JRoute::_($forumtab_url));

						}
						else {
							$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'));
                    }

                    break;



					////

                case "unfavorite":
                    $cid = JRequest::getVar("cid", array ());

                    @array_walk($cid, "intval");
                    $cids = @implode(',', $cid);

                    $kunena_db->setQuery("DELETE from #__fb_favorites where userid=$kunena_my->id  AND thread in ($cids)");

                    if (!$kunena_db->query())
                    {
                    	$app->enqueueMessage(_USER_UNFAVORITE_A._USER_UNFAVORITE_B._USER_UNFAVORITE_C, 'notice');
                    }
                    else
                    {
                    	$app->enqueueMessage(_USER_UNFAVORITE_YES);
                    }

                    if ($fbConfig->fb_profile == 'cb')
                    {
						$forumtab_url = CKunenaCBProfile::getForumTabURL();
						$app->redirect(JRoute::_($forumtab_url));
                    }
                    else
                    {
                    	$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'));
                    }

                    break;

					 case "unfavoriteitem":

						$kunena_db->setQuery("DELETE from #__fb_favorites where userid=$kunena_my->id and thread=$thread");

						if (!$kunena_db->query()) {
                    		$app->enqueueMessage(_USER_UNFAVORITE_A._USER_UNFAVORITE_B._USER_UNFAVORITE_C, 'notice');
						}
						else {
							$app->enqueueMessage(_USER_UNFAVORITE_YES);
						}

						if ($fbConfig->fb_profile == 'cb') {
							$forumtab_url = CKunenaCBProfile::getForumTabURL();
							$app->redirect(JRoute::_($forumtab_url));
						}
						else {
							$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'));
						}

                    break;

					//

                case "userdetails":


					/* We don't need
					// security check to see if link exists in a menu
                    $link = 'index.php?option=com_user&amp;task=UserDetails';

                    $query = "SELECT id" . "\n FROM #__menu" . "\n WHERE link LIKE '%$link%'" . "\n AND published = 1";
                    $kunena_db->setQuery($query);
                    $exists = $kunena_db->loadResult();

                    if (!$exists)
                    {
                        JError::raiseError( 403, JText::_("ALERTNOTAUTH") );;
                        return;
                    }
					*/

                    require_once (KUNENA_ROOT_PATH_ADMIN .DS. 'components/com_users/users.class.php');

                    $row = new JUser($kunena_my->id);
                    $row->orig_password = $row->password;

                    $row->name = trim($row->name);
                    $row->email = trim($row->email);
                    $row->username = trim($row->username);

                    $file = $app->getPath('com_xml', 'com_users');
                    $params = &new JParameter($row->params, $file, 'component');

                    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_userdetails_form.php'))
                    {
                        include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile_userdetails_form.php');
                    }
                    else
                    {
                        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile_userdetails_form.php');
                    }

                    break;

                case "usersave":
                    $user_id = JRequest::getInt('id', 0);
                    $uid = $kunena_my->id;

                    // do some security checks
                    if ($uid == 0 || $user_id == 0 || $user_id != $uid)
                    {
                        JError::raiseError( 403, JText::_("ALERTNOTAUTH") );;
                        return;
                    }

                    $row = new JUser($user_id);

                    if (!$row->bind($_POST))
                    {
                        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                        $app->close();
                    }
                    if (in_array($app->getCfg( "frontend_userparams" ), array( '1', null)))
                    {
                        // save params
                        $params = JRequest::getVar('params', '');

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

                    if (!$row->save(TRUE))
                    {
                        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
                        $app->close();
                    }

                    // check if username has been changed
                    if ($orig_username != $row->username)
                    {
                        // change username value in session table
                        $query
                            = "UPDATE #__session"
                            . "\n SET username = " . $kunena_db->Quote($row->username) . "\n WHERE username = " . $kunena_db->Quote($orig_username) . "\n AND userid = " . (int)$kunena_my->id . "\n AND gid = " . (int)$kunena_my->gid . "\n AND guest = 0";
                        $kunena_db->setQuery($query);
                        $kunena_db->query();
                    }

                    $app->redirect(JRoute::_('index.php?option=com_kunena&amp;func=myprofile'), _KUNENA_USER_DETAILS_SAVE);
                    break;
            }
            ?>

        <!-- F:My Profile Right -->
        
<!-- Begin: Forum Jump -->
<table class = "fb_blocktable" id = "fb_bottomarea" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                {
                    require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                }
                ?>
            </th>
        </tr>
    </thead>
	<tbody><tr><td></td></tr></tbody>
</table>
<!-- Finish: Forum Jump -->

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