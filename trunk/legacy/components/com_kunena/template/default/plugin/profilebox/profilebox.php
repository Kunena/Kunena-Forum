<?php
/**
* @version $Id: profilebox.php 901 2008-08-03 21:39:37Z fxstein $
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
//first we gather some information about this person
$database->setQuery("SELECT su.view, u.name, su.moderator,su.avatar FROM #__fb_users as su"
                    . "\nLEFT JOIN #__users as u on u.id=su.userid WHERE su.userid={$my->id}");

$database->loadObject($_user);

$prefview = $_user->view;
$username = $_user->name; // externally used  by fb_pathway, myprofile_menu
$moderator = $_user->moderator;
$fbavatar = $_user->avatar;
$jr_username = $_user->name;

$jr_avatar = '';
if ($fbConfig->avatar_src == "clexuspm")
{
    $jr_avatar = '<img src="' . MyPMSTools::getAvatarLinkWithID($my->id) . '" alt=" " />';
}
else if ($fbConfig->avatar_src == "cb")
{
    $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id=".$my->id);
    $avatar = $database->loadResult();
    if ($avatar != "") {
        $imgpath = JB_JLIVEURL . '/images/comprofiler/';

        if (eregi("gallery/", $avatar) == false)
            $imgpath .= "tn" . $avatar;
        else
            $imgpath .= $avatar;
    } else {
        $imgpath = JB_JLIVEURL."/components/com_comprofiler/plugin/language/default_language/images/tnnophoto.jpg";
    }
    $jr_avatar = '<img src="' . $imgpath . '" alt=" " />';
}
else
{
    if ($fbavatar != "") {
		if(!file_exists(FB_ABSUPLOADEDPATH . '/avatars/s_' . $fbavatar)) {
            $jr_avatar = '<img src="'.FB_LIVEUPLOADEDPATH.'/avatars/' . $fbavatar . '" alt=" " />';
		} else {
		  $jr_avatar = '<img src="'.FB_LIVEUPLOADEDPATH.'/avatars/s_' . $fbavatar . '" alt=" " />';
		}
    }
    else {
 		$jr_avatar = '<img src="'.FB_LIVEUPLOADEDPATH.'/avatars/s_nophoto.jpg" alt=" " />';
        $jr_profilelink = '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
    }

}

if ($fbConfig->fb_profile == "clexuspm") {
    $jr_profilelink = '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
}
else if ($fbConfig->fb_profile == "cb")
{
    $jr_profilelink = '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
    if($fbConfig->cb_profile) {
        $jr_profilelink = '<a href="' . sefRelToAbs('index.php?option=com_comprofiler&amp;task=userDetails'.FB_CB_ITEMID_SUFFIX) . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
    }
}
else
{
    $jr_profilelink = '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '" >' . _PROFILEBOX_MYPROFILE . '</a>';
}

$jr_myposts = '<a href="' . sefRelToAbs(JB_LIVEURLREL .  '&amp;func=showauthor&amp;task=showmsg&amp;auth=' . $my->id . '') . '" >' . _PROFILEBOX_SHOW_MYPOSTS . '</a>';
$jr_latestpost = sefRelToAbs(JB_LIVEURLREL . '&amp;func=latest');
?>

<?php // AFTER LOGIN AREA
$j15 = FBTools::isJoomla15();
if ($fbConfig->cb_profile)
{
    $loginlink = sefRelToAbs('index.php');
    $logoutlink = sefRelToAbs('index.php?option=logout');
    $registerlink = sefRelToAbs('index.php?option=com_comprofiler&amp;task=registers');//.FB_CB_ITEMID_SUFFIX);
    $lostpasslink = sefRelToAbs('index.php?option=com_comprofiler&amp;task=lostPassword');//.FB_CB_ITEMID_SUFFIX);
    if($j15) {
      $loginlink = sefRelToAbs('index.php?option=com_user&amp;view=login');
      $logoutlink = sefRelToAbs('index.php?option=com_user&amp;view=login');
    }
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
<?php echo $jr_avatar; ?>
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
$annlink = 'index.php?option=com_fireboard&amp;func=announcement&amp;do=show'.FB_FB_ITEMID_SUFFIX;

?>
| <a href = "<?php echo $annlink;?>"><?php echo _ANN_ANNOUNCEMENTS; ?> </a>
<?php } ?>

</td>
                <?php
                if (mosCountModules('fb_1'))
                {
                ?>

          <td>
                            <div class = "fb_profilebox_modul">
                                <?php
                                mosLoadModules('fb_1', -2);
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
                if (mosCountModules('fb_1'))
                {
                ?>

                        <td>
                            <div class = "fb_profilebox_modul">
                                <?php
                                mosLoadModules('fb_1', -2);
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
