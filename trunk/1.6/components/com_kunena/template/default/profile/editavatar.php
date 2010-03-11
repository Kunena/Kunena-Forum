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
 **/
defined( '_JEXEC' ) or die();
require_once ( KUNENA_PATH_FUNCS .DS. 'profile.php');
$userprofile = new CKunenaProfile($this->user->id);
if (empty($userprofile->profile->avatar)) {
	$userprofile->profile->avatar = KUNENA_LIVEUPLOADEDPATH . '/avatars/nophoto.jpg';
} else {
	$userprofile->profile->avatar = KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $userprofile->profile->avatar;
}
?>

<h1><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?></h1>
<form action="<?php echo CKunenaLink::GetProfileSettingsURL($this->config, $this->user->id, '', $rel='nofollow', $redirect=false,'saveavatar'); ?>" method="post" enctype="multipart/form-data" name="kprofileEditing">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
	<tbody>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_MYPROFILE_MYAVATAR'); ?></td><td><div class="avatar-lg"><img src="<?php echo $userprofile->profile->avatar; ?>" alt=""/></div></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_SET_NEW_AVATAR'); ?></td><td><input name="kavatar" type="file"></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center"><?php echo JText::_('COM_KUNENA_CHOOSE_AVATAR'); ?></td><td></td>
		</tr>
		<tr class="ksectiontableentry2">
			<td class="td-0 km center" colspan="2"><input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_GEN_SUBMIT'); ?>" name="Submit" /></td>
		</tr>
	</tbody>
</table>
</form>
