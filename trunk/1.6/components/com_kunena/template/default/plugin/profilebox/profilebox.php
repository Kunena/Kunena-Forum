<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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
defined( '_JEXEC' ) or die();


$kunena_config =& CKunenaConfig::getInstance();
$kunena_my = &JFactory::getUser();
$kunena_db = &JFactory::getDBO();
//first we gather some information about this person
$kunena_db->setQuery("SELECT su.view, u.name, u.username, su.avatar FROM #__fb_users AS su"
                    . " LEFT JOIN #__users AS u on u.id=su.userid WHERE su.userid={$kunena_my->id}", 0, 1);

$_user = $kunena_db->loadObject();
$Itemid = JRequest::getInt('Itemid');

$this->kunena_avatar = NULL;
if ($_user != NULL)
{
	$prefview = $_user->view;
	if ($kunena_config->username) $this->kunena_username = $_user->username; // externally used  by fb_pathway, myprofile_menu
	else $this->kunena_username = $_user->name;
	$this->kunena_avatar = $_user->avatar;
}

$jr_avatar = '';
if ($kunena_config->avatar_src == "jomsocial")
{
	// Get CUser object
	$jsuser =& CFactory::getUser($kunena_my->id);
    $jr_avatar = '<img src="' . $jsuser->getThumbAvatar() . '" alt=" " />';
}
else if ($kunena_config->avatar_src == "cb")
{
	$kunenaProfile =& CkunenaCBProfile::getInstance();
	$jr_avatar = $kunenaProfile->showAvatar($kunena_my->id);
}
else if ($kunena_config->avatar_src == "aup") // integration AlphaUserPoints
{
	$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
	if ( file_exists($api_AUP)) {
		( $kunena_config->fb_profile=='aup' ) ? $showlink=1 : $showlink=0;
		$jr_avatar = AlphaUserPointsHelper::getAupAvatar( $kunena_my->id, $showlink, $kunena_config->avatarsmallwidth, $kunena_config->avatarsmallheight );
	} // end integration AlphaUserPointselse
}
else
{
    if ($this->kunena_avatar != "") {
		if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/s_' . $this->kunena_avatar)) {
            $jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/' . $this->kunena_avatar . '" alt=" " style="max-width: '.$kunena_config->avatarsmallwidth.'px; max-height: '.$kunena_config->avatarsmallheight.'px;" />';
		} else {
		  $jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/s_' . $this->kunena_avatar . '" alt=" " />';
		}
    }
    else {
 		$jr_avatar = '<img src="'.KUNENA_LIVEUPLOADEDPATH.'/avatars/s_nophoto.jpg" alt=" " />';
        $jr_profilelink = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile' , _PROFILEBOX_MYPROFILE , _PROFILEBOX_MYPROFILE , 'nofollow');
    }

}

if ($kunena_config->fb_profile == "cb" || $kunena_config->fb_profile == "jomsocial")
{
    $jr_profilelink = CKunenaLink::GetProfileLink($kunena_config, $kunena_my->id, _PROFILEBOX_MYPROFILE);
}
else
{
    $jr_profilelink = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile' , _PROFILEBOX_MYPROFILE, _PROFILEBOX_MYPROFILE, 'follow');
}

// AFTER LOGIN AREA
if ($kunena_config->fb_profile == 'cb')
{
	$loginlink = CKunenaLink::GetCBLoginLink(_PROFILEBOX_LOGIN);
	$logoutlink = CKunenaLink::GetCBLogoutLink(_PROFILEBOX_LOGOUT);
	$registerlink = CKunenaLink::GetCBRegisterLink(_PROFILEBOX_REGISTER);
	$lostpasslink = CKunenaLink::GetCBLostPWLink(_PROFILEBOX_LOST_PASSWORD);
}
else if ($kunena_config->fb_profile == 'jomsocial')
{
	$loginlink = CKunenaLink::GetJomsocialLoginLink(_PROFILEBOX_LOGIN);
	$logoutlink = CKunenaLink::GetJomsocialLoginLink(_PROFILEBOX_LOGOUT);
	$registerlink = CKunenaLink::GetJomsocialRegisterLink(_PROFILEBOX_REGISTER);
	$lostpasslink = CKunenaLink::GetJomsocialLoginLink(_PROFILEBOX_LOST_PASSWORD);
}
else
{
	$loginlink = CKunenaLink::GetLoginLink(_PROFILEBOX_LOGIN);
	$logoutlink = CKunenaLink::GetLoginLink(_PROFILEBOX_LOGOUT);
	$registerlink = CKunenaLink::GetRegisterLink(_PROFILEBOX_REGISTER);
	$lostpasslink = CKunenaLink::GetLostpassLink(_PROFILEBOX_LOST_PASSWORD);
}

if ($kunena_my->id)
{
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kprofilebox" id="kprofilebox">
	<tbody id="topprofilebox_tbody">
		<tr class="ksectiontableentry1">
			<td class="td-1  km" align="left" width="5%">
<?php echo CKunenaLink::GetProfileLink($kunena_config, $kunena_my->id, $jr_avatar);?>
                </td>

			<td valign="top" class="td-2  km kprofileboxcnt" align="left">
<?php echo _PROFILEBOX_WELCOME; ?>, <b><?php echo $this->kunena_username; ?></b>

			<br />

			<?php echo CKunenaLink::GetShowLatestLink(_PROFILEBOX_SHOW_LATEST_POSTS);?> | <?php echo $jr_profilelink; ?> |  <?php echo $logoutlink.'&nbsp;';?>
<?php
$user_fields = @explode(',', $kunena_config->annmodid);

if (in_array($kunena_my->id, $user_fields) || $kunena_my->usertype == 'Administrator' || $kunena_my->usertype == 'Super Administrator') {
    $is_editor = true;
}
else {
    $is_editor = false;
}

if ($is_editor) {
?>
| <a href="<?php echo CKunenaLink::GetAnnouncementURL($kunena_config, 'show');?>"><?php echo _ANN_ANNOUNCEMENTS; ?> </a>
<?php } ?>
| <?php echo CKunenaLink::GetSearchLink($kunena_config, 'search', '', 0, 0, _KUNENA_SEARCH_ADVSEARCH);?>

</td>
	<?php
	if (JDocumentHTML::countModules ( 'kunena_profilebox' )) :
	?>
			<td>
		<?php
		CKunenaTools::showModulePosition ( 'kunena_profilebox' );
		?>
			</td>
	<?php
	endif;
	?>

            </tr>
	</tbody>
</table>

<?php
} else {
    // LOGOUT AREA
    ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kprofilebox" id="kprofilebox">
	<tbody id="topprofilebox_tbody">
		<tr class="ksectiontableentry1">
			<td valign="top" class="td-1  km kprofileboxcnt" align="left">
				<?php echo _PROFILEBOX_WELCOME; ?>, <b><?php echo _PROFILEBOX_GUEST; ?></b>
				<br /> <?php echo _PROFILEBOX_PLEASE; ?>
                <?php echo $loginlink;?> <?php echo _PROFILEBOX_OR; ?>
                <?php echo $registerlink;?>.
				&nbsp;&nbsp;<?php echo $lostpasslink;?>
			</td>
			<?php
			if (JDocumentHTML::countModules ( 'kunena_profilebox' )) :
			?>
				<td>
					<?php
					CKunenaTools::showModulePosition ( 'kunena_profilebox' );
					?>
				</td>
			<?php
			endif;
			?>
            </tr>
	</tbody>
</table>

<?php
}
?>
