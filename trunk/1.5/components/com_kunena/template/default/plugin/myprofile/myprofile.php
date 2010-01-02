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

global $kunena_is_admin;

require_once (KUNENA_PATH_LIB .DS. "kunena.user.class.php");

$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$kunena_config =& CKunenaConfig::getInstance();

$document->setTitle(_GEN_MYPROFILE . ' - ' . stripslashes($kunena_config->board_title));

if ($kunena_my->id != "" && $kunena_my->id != 0)
{
	$do = JRequest::getCmd('do', 'show');

	//Get joomla userinfo needed later on, this limits the amount of queries
    $juserinfo = new JUser($kunena_my->id);

    //Get userinfo needed later on, this limits the amount of queries
    $userinfo = new CKunenaUserprofile();

    if ($kunena_config->avatar_src == "cb")
    {
        $kunena_db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='{$kunena_my->id}'");
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

    if ($ugid == 0)
    {
        $usr_usertype = _VIEW_VISITOR;
    }
    else
    {
        if (CKunenaTools::isAdmin())
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

    $kunena_db->setQuery("SELECT MAX(posts) FROM #__fb_users");
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
                    if ($kunena_config->fb_profile != 'cb' && $kunena_config->fb_profile != 'jomsocial')
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

                    $kunena_db->setQuery("UPDATE #__fb_users SET view='$newview', ordering='$neworder', hideEmail='$newhideEmail', showOnline='$newshowOnline' WHERE userid='$kunena_my->id'");
                    setcookie("fboard_settings[current_view]", $newview);

                    if (!$kunena_db->query())
                    {
						$kunena_app->enqueueMessage(_USER_PROFILE_NOT_A._USER_PROFILE_NOT_B._USER_PROFILE_NOT_C, 'notice');
                    }
                    else
                    {
						$kunena_app->enqueueMessage(_USER_PROFILE_UPDATED);
                    }

                    $kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . "&amp;func=myprofile"));
                break;

                case "profileinfo":
                    // B: Signature

                    $bd = @explode("-" , $userinfo->birthdate);

                    $this->ulists["year"] = $bd[0];
                    $this->ulists["month"] = $bd[1];
                    $this->ulists["day"] = $bd[2];

                    $genders[] = JHTML::_('select.option', "", "");
                    $genders[] = JHTML::_('select.option', "1", _KUNENA_MYPROFILE_MALE);
                    $genders[] = JHTML::_('select.option', "2", _KUNENA_MYPROFILE_FEMALE);

                    $this->ulists["gender"] = JHTML::_('select.genericlist',  $genders, 'gender', 'class="inputbox"', 'value', 'text', $userinfo->gender );


                    if ($kunena_config->fb_profile != 'cb' && $kunena_config->fb_profile != 'jomSocial')
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

                    $user_id = JRequest::getInt('id', 0 );

    // do some security checks
    if ($kunena_my->id == 0 || $user_id == 0 || $user_id != $kunena_my->id) {
        JError::raiseError( 403, JText::_("ALERTNOTAUTH") );;
        return;
    }

        $rowu = new CKunenaUserprofile();

                    $deleteSig = JRequest::getInt('deleteSig', 0);
                    $signature = JRequest::getVar('message', null, 'REQUEST', 'string', JREQUEST_ALLOWRAW);
                    $bday1 = JRequest::getVar('bday1', '0000');
                    $bday2 = JRequest::getVar('bday2', '00');
                    $bday3 = JRequest::getVar('bday3', '00');


                        if (!$rowu->bind( $_POST, 'moderator posts karma group_id uhits' )) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        $kunena_app->close();
    }
                    $rowu->birthdate = $bday1."-".$bday2."-".$bday3;


                    $signature = JString::trim($signature);
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
        $kunena_app->close();
    }

    if (!$rowu->store()) {
        echo "<script> alert('".$rowu->getError()."'); window.history.go(-1); </script>\n";
        $kunena_app->close();
    }

						$kunena_app->enqueueMessage(_USER_PROFILE_UPDATED);
						$kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . "&amp;func=myprofile"));
                break;

                case "showsub":
                    $pageperlistlm = 15;

                    $limit = JRequest::getInt('limit', $pageperlistlm);
                    $limitstart = JRequest::getInt('limitstart', 0);

                    $query = "SELECT COUNT(*) FROM #__fb_subscriptions WHERE userid='{$kunena_my->id}'";
                    $kunena_db->setQuery($query);

                    $total = $kunena_db->loadResult();
                    	check_dberror("Unable to load subscriptions for user.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all subscriptions for this user
                    $kunena_db->setQuery("SELECT thread FROM #__fb_subscriptions WHERE userid='{$kunena_my->id}' ORDER BY thread DESC", $limitstart, $limit);
                    $this->kunena_subslist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load subscriptions.");
                    $this->kunena_csubslist = count($this->kunena_subslist);

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

                    $query = "SELECT COUNT(*) FROM #__fb_favorites WHERE userid='{$kunena_my->id}'";
                    $kunena_db->setQuery($query);

                    $total = $kunena_db->loadResult();
                    	check_dberror("Unable to load favorites.");

                    if ($total <= $limit)
                    {
                        $limitstart = 0;
                    }

                    //get all favorites for this user
                    $kunena_db->setQuery("SELECT thread FROM #__fb_favorites WHERE userid='{$kunena_my->id}' ORDER BY thread DESC", $limitstart, $limit);
                    $this->kunena_favslist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load favorites.");
                    $this->kunena_cfavslist = count($this->kunena_favslist);

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
                    if (!CKunenaTools::isAdmin())
                    {
                        $kunena_db->setQuery("SELECT m.catid, c.id, c.name FROM #__fb_moderation AS m LEFT JOIN #__fb_categories AS c ON c.id=m.catid WHERE m.userid='{$kunena_my->id}'");
                        $this->kunena_modslist = $kunena_db->loadObjectList();
                        	check_dberror("Unable to load moderators.");
                        $this->kunena_cmodslist = count($this->kunena_modslist);
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

                    $kunena_db->setQuery("DELETE FROM #__fb_subscriptions WHERE userid=$kunena_my->id  AND thread in ($cids) ");

                    if (!$kunena_db->query())
                    {
						$kunena_app->enqueueMessage(_USER_UNSUBSCRIBE_A._USER_UNSUBSCRIBE_B._USER_UNSUBSCRIBE_C, 'notice');
                    }
                    else
                    {
						$kunena_app->enqueueMessage(_USER_UNSUBSCRIBE_YES);
                    }

                    if ($kunena_config->fb_profile == 'cb')
                    {
						$forumtab_url = CKunenaCBProfile::getForumTabURL();
						$kunena_app->redirect(JRoute::_($forumtab_url));
                    }
                    else
                    {
                    	$kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'));
                    }

                    break;


					/////
					case "unsubscribeitem":
                   $kunena_db->setQuery("DELETE from #__fb_subscriptions where userid=$kunena_my->id and thread=$thread");

						if (!$kunena_db->query()) {
							$kunena_app->enqueueMessage(_USER_UNSUBSCRIBE_A._USER_UNSUBSCRIBE_B._USER_UNSUBSCRIBE_C, 'notice');
						}
						else {
							$kunena_app->enqueueMessage(_USER_UNSUBSCRIBE_YES);
						}

						if ($kunena_config->fb_profile == 'cb') {
							$forumtab_url = CKunenaCBProfile::getForumTabURL();
							$kunena_app->redirect(JRoute::_($forumtab_url));

						}
						else {
							$kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'));
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
                    	$kunena_app->enqueueMessage(_USER_UNFAVORITE_A._USER_UNFAVORITE_B._USER_UNFAVORITE_C, 'notice');
                    }
                    else
                    {
                    	$kunena_app->enqueueMessage(_USER_UNFAVORITE_YES);
                    }

                    if ($kunena_config->fb_profile == 'cb')
                    {
						$forumtab_url = CKunenaCBProfile::getForumTabURL();
						$kunena_app->redirect(JRoute::_($forumtab_url));
                    }
                    else
                    {
                    	$kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'));
                    }

                    break;

					 case "unfavoriteitem":

						$kunena_db->setQuery("DELETE from #__fb_favorites where userid=$kunena_my->id and thread=$thread");

						if (!$kunena_db->query()) {
                    		$kunena_app->enqueueMessage(_USER_UNFAVORITE_A._USER_UNFAVORITE_B._USER_UNFAVORITE_C, 'notice');
						}
						else {
							$kunena_app->enqueueMessage(_USER_UNFAVORITE_YES);
						}

						if ($kunena_config->fb_profile == 'cb') {
							$forumtab_url = CKunenaCBProfile::getForumTabURL();
							$kunena_app->redirect(JRoute::_($forumtab_url));
						}
						else {
							$kunena_app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'));
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

                    $row->name = JString::trim($row->name);
                    $row->email = JString::trim($row->email);
                    $row->username = JString::trim($row->username);

                    $file = $kunena_app->getPath('com_xml', 'com_users');
                    $params = new JParameter($row->params, $file, 'component');

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
                        $kunena_app->close();
                    }
                    if (in_array($kunena_app->getCfg( "frontend_userparams" ), array( '1', null)))
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
                        $kunena_app->close();
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

                    $kunena_app->redirect(JRoute::_('index.php?option=com_kunena&amp;func=myprofile'), _KUNENA_USER_DETAILS_SAVE);
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
                if ($kunena_config->enableforumjump)
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