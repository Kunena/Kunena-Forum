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
$i=0;
?>

<h2><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?></h2>
<form action="<?php echo CKunenaLink::GetProfileSettingsURL($this->config, $this->user->id, '', $rel='nofollow', $redirect=false,'saveavatar'); ?>" method="post" enctype="multipart/form-data" name="kprofileEditing">
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
<?php if ($userprofile->profile->avatar): ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE');?></label>
			</td><td>
				<input id="kavatar_delete" type="radio" name="action" value="delete"/>
			</td>
		</tr>
<?php endif; ?>
<?php if ($this->_config->allowavatarupload):?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD');?></label>
			</td><td>
				<input id="kavatar_upload" type="radio" name="action" value="upload" /> <input type="file" class="button" name="avatar" />
			</td>
		</tr>
<?php endif; ?>
<?php if ($this->_config->allowavatargallery):?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_gallery"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY');?></label>
			</td><td>
				<input id="kavatar_gallery" type="radio" name="action" value="gallery" />
			</td>
		</tr>
<?php endif; ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center" colspan="2"><input class="kbutton kbutton ks" type="submit" value="<?php echo JText::_('COM_KUNENA_GEN_SUBMIT'); ?>" name="Submit" /></td>
		</tr>
</table>
</form>
