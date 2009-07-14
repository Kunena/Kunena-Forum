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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

$fbConfig =& CKunenaConfig::getInstance();
//first we gather some information about this person
$database->setQuery("SELECT su.view, u.name, su.moderator,su.avatar FROM #__fb_users as su"
                    . "\nLEFT JOIN #__users as u on u.id=su.userid WHERE su.userid={$my->id}");

$database->loadObject($_user);

$fbavatar = NULL;
if ($_user != NULL)
{
	$prefview = $_user->view;
	$username = $_user->name; // externally used  by fb_pathway, myprofile_menu
	$moderator = $_user->moderator;
	$fbavatar = $_user->avatar;
	$jr_username = $_user->name;
}

$jr_avatar = '';
if ($fbConfig->avatar_src == "jomsocial")
{
	// Get CUser object
	$jsuser =& CFactory::getUser($my->id);
    $jr_avatar = '<img src="' . $jsuser->getThumbAvatar() . '" alt=" " />';
}
else if ($fbConfig->avatar_src == "clexuspm")
{
    $jr_avatar = '<img src="' . MyPMSTools::getAvatarLinkWithID($my->id) . '" alt=" " />';
}
else if ($fbConfig->avatar_src == "cb")
{
	$jr_avatar = $kunenaProfile->showAvatar($my->id);
}
else
{
    if ($fbavatar != "") {
		if(!file_exists(KUNENA_ABSUPLOADEDPATH . '/avatars/s_' . $fbavatar)) {
            $jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/' . $fbavatar . '" alt=" " />';
		} else {
		  $jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/s_' . $fbavatar . '" alt=" " />';
		}
    }
    else {
 		$jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/s_nophoto.jpg" alt=" " />';
        $jr_profilelink = '<a href="' . sefRelToAbs(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
    }

}

if ($fbConfig->fb_profile == "cb" || $fbConfig->fb_profile == "jomsocial")
{
    $jr_profilelink = CKunenaLink::GetProfileLink($fbConfig, $my->id, _PROFILEBOX_MYPROFILE);
}
else if ($fbConfig->fb_profile == "clexuspm") {
    $jr_profilelink = '<a href="' . sefRelToAbs(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
}
else
{
    $jr_profilelink = '<a href="' . sefRelToAbs(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
}

$jr_myposts = '<a href="' . sefRelToAbs(KUNENA_LIVEURLREL .  '&amp;func=showauthor&amp;task=showmsg&amp;auth=' . $my->id . '') . '" >' . _PROFILEBOX_SHOW_MYPOSTS . '</a>';
$jr_latestpost = sefRelToAbs(KUNENA_LIVEURLREL . '&amp;func=latest');
?>

<?php // AFTER LOGIN AREA
$j15 = CKunenaTools::isJoomla15();
if ($fbConfig->fb_profile == 'cb')
{
	$loginlink = CKunenaCBProfile::getLoginURL();
	$logoutlink = CKunenaCBProfile::getLogoutURL();
	$registerlink = CKunenaCBProfile::getRegisterURL();
	$lostpasslink = CKunenaCBProfile::getLostPasswordURL();
}
else
{
    $loginlink = sefRelToAbs('index.php?option=com_login&amp;Itemid=' . $Itemid);
    $logoutlink = sefRelToAbs('index.php?option=logout');
    $registerlink = sefRelToAbs('index.php?option=com_registration&amp;task=register&amp;Itemid=' . $Itemid);
    $lostpasslink = sefRelToAbs('index.php?option=com_registration&amp;task=lostPassword&amp;Itemid=' . $Itemid);
    if($j15) {
      $loginlink = sefRelToAbs('index.php?option=com_user&amp;view=login');
      $logoutlink = sefRelToAbs('index.php?option=com_user&amp;view=login');
      $registerlink = sefRelToAbs('index.php?option=com_user&amp;task=register&amp;Itemid=' . $Itemid);
      $lostpasslink = sefRelToAbs('index.php?option=com_user&amp;view=reset&amp;Itemid=' . $Itemid);
    }
}

if ($my->id)
{
?>

    <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0" class = "fb_profilebox" >
        <tbody id = "topprofilebox_tbody">
            <tr class = "<?php echo $boardclass ;?>sectiontableentry1">
                <td  class = "td-1  fbm" align="left" width="5%">
<?php echo CKunenaLink::GetProfileLink($fbConfig, $my->id, $jr_avatar);?>
                </td>

                <td valign = "top" class = "td-2  fbm fb_profileboxcnt" align="left">
<?php echo _PROFILEBOX_WELCOME; ?>, <b><?php echo $jr_username; ?></b>

                <br />

                <a href = "<?php echo $jr_latestpost ; ?>"><?php
    echo _PROFILEBOX_SHOW_LATEST_POSTS; ?> </a> | <?php echo $jr_profilelink; ?> |  <a href = "<?php echo $logoutlink;?>"><?php echo _PROFILEBOX_LOGOUT; ?></a>
<?php
$user_fields = @explode(',', $fbConfig->annmodid);

if (in_array($my->id, $user_fields) || $my->usertype == 'Administrator' || $my->usertype == 'Super Administrator') {
    $is_editor = true;
}
else {
    $is_editor = false;
}

if ($is_editor) {
?>
| <a href = "<?php echo CKunenaLink::GetAnnouncementURL($fbConfig, 'show');?>"><?php echo _ANN_ANNOUNCEMENTS; ?> </a>
<?php } ?>
| <?php echo CKunenaLink::GetSearchLink($fbConfig, 'search', '', 0, 0, _KUNENA_SEARCH_ADVSEARCH);?>

</td>
                <?php
                if (mosCountModules('kunena_profilebox') || mosCountModules('kna_pbox'))
                {
                ?>

          <td>
                            <div class = "fb_profilebox_modul">
                                <?php
                                if (CKunenaTools::isJoomla15())
                                {
                                	$document	= &JFactory::getDocument();
                                	$renderer	= $document->loadRenderer('modules');
                                	$options	= array('style' => 'xhtml');
                                	$position	= 'kunena_profilebox';
                                	echo $renderer->render($position, $options, null);
                                }
                                else
                                {
                                	mosLoadModules('kna_pbox', -2);
                                }
                                ?>
                            </div>

</td>
                <?php
                }
                ?>

            </tr>
        </tbody>
    </table>

    <?php
}
else
{
    // LOGOUT AREA
    ?>

    <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0"  class = "fb_profilebox">
        <tbody id = "topprofilebox_tbody">
            <tr class = "<?php echo $boardclass ;?>sectiontableentry1">
                <td valign = "top" class = "td-1  fbm fb_profileboxcnt" align="left">
<?php echo _PROFILEBOX_WELCOME; ?>, <b><?php echo _PROFILEBOX_GUEST; ?></b>

                <br/> <?php echo _PROFILEBOX_PLEASE; ?>

                <a href = "<?php echo $loginlink;?>"><?php echo _PROFILEBOX_LOGIN; ?></a> <?php echo _PROFILEBOX_OR; ?> <a href = "<?php echo $registerlink;?>"><?php echo _PROFILEBOX_REGISTER; ?></a>.

                &nbsp;&nbsp;

                <a href = "<?php echo $lostpasslink;?>"><?php echo _PROFILEBOX_LOST_PASSWORD; ?></a>

</td>
                <?php
                if (mosCountModules('kunena_profilebox') || mosCountModules('kna_pbox'))
                {
                ?>

                        <td>
                            <div class = "fb_profilebox_modul">
                                <?php
                                if (CKunenaTools::isJoomla15())
                                {
                                	$document	= &JFactory::getDocument();
                                	$renderer	= $document->loadRenderer('modules');
                                	$options	= array('style' => 'xhtml');
                                	$position	= 'kunena_profilebox';
                                	echo $renderer->render($position, $options, null);
                                }
                                else
                                {
                                	mosLoadModules('kna_pbox', -2);
                                }
                                ?>
                            </div>
                       </td>

                <?php
                }
                ?>

            </tr>
        </tbody>
    </table>

<?php
}
?>
