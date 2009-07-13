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
$kunenaProfile =& CKunenaProfile::getInstance();

$kunena_my = &JFactory::getUser();
$kunena_db = &JFactory::getDBO();
//first we gather some information about this person
$kunena_db->setQuery("SELECT su.view, u.name, su.moderator FROM #__fb_users AS su"
                    . " LEFT JOIN #__users AS u on u.id=su.userid WHERE su.userid={$kunena_my->id}", 0, 1);

$_user = $kunena_db->loadObject();

if ($_user != NULL)
{
	$prefview = $_user->view;
	$username = $_user->name; // externally used  by fb_pathway, myprofile_menu
	$moderator = $_user->moderator;
	$jr_username = $_user->name;
}

$jr_avatar = $kunenaProfile->showAvatar($kunena_my->id);
$jr_profilelink = CKunenaLink::GetProfileLink($fbConfig, $kunena_my->id, _PROFILEBOX_MYPROFILE);
$jr_latestpost = CKunenaLink::GetShowLatestLink(_PROFILEBOX_SHOW_LATEST_POSTS, 'nofollow')
?>

<?php // AFTER LOGIN AREA
$loginlink = $kunenaProfile->getLoginURL();
$logoutlink = $kunenaProfile->getLogoutURL();
$registerlink = $kunenaProfile->getRegisterURL();
$lostpasslink = $kunenaProfile->getLostPasswordURL();

if ($kunena_my->id)
{
?>

    <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0" class = "fb_profilebox" >
        <tbody id = "topprofilebox_tbody">
            <tr class = "<?php echo $boardclass ;?>sectiontableentry1">
                <td  class = "td-1  fbm" align="left" width="5%">
<?php echo CKunenaLink::GetProfileLink($fbConfig, $kunena_my->id, $jr_avatar);?>
                </td>

                <td valign = "top" class = "td-2  fbm fb_profileboxcnt" align="left">
<?php echo _PROFILEBOX_WELCOME; ?>, <b><?php echo $jr_username; ?></b>

                <br />

                <?php echo $jr_latestpost ; ?> | <?php echo $jr_profilelink; ?> |  <a href = "<?php echo $logoutlink;?>"><?php echo _PROFILEBOX_LOGOUT; ?></a>
<?php
$user_fields = @explode(',', $fbConfig->annmodid);

if (in_array($kunena_my->id, $user_fields) || $kunena_my->usertype == 'Administrator' || $kunena_my->usertype == 'Super Administrator') {
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
			if (JDocumentHTML::countModules('kunena_profilebox'))
			{
			?>

			<td>
				<div class = "fb_profilebox_modul">
				<?php
					$document	= &JFactory::getDocument();
					$renderer	= $document->loadRenderer('modules');
					$options	= array('style' => 'xhtml');
					$position	= 'kunena_profilebox';
					echo $renderer->render($position, $options, null);
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
			if (JDocumentHTML::countModules('kunena_profilebox'))
			{
			?>

			<td>
				<div class = "fb_profilebox_modul">
				<?php
					$document	= &JFactory::getDocument();
					$renderer	= $document->loadRenderer('modules');
					$options	= array('style' => 'xhtml');
					$position	= 'kunena_profilebox';
					echo $renderer->render($position, $options, null);
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
