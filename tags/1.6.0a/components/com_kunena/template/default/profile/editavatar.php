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
$i=0;
?>

<h2><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?></h2>
<table
	class="<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>" id="kflattable">
<?php if ($this->profile->avatar): ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP');?></label>
			</td><td>
				<input id="kavatar_keep" type="radio" name="avatar" value="keep" checked="checked" />
			</td>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE');?></label>
			</td><td>
				<input id="kavatar_delete" type="radio" name="avatar" value="delete"/>
			</td>
		</tr>
<?php endif; ?>
<?php if ($this->_config->allowavatarupload):?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD');?></label>
			</td><td>
				<div><input id="kavatar_upload" type="file" class="button" name="avatarfile" /></div>
			</td>
		</tr>
<?php endif; ?>
<?php if ($this->_config->allowavatargallery):?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="td-0 km center">
				<label for="kavatar_gallery"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY');?></label>
			</td><td>
				<table class="kblocktable" id ="kforumua_gal" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td class="kuadesc">
						<?php echo $this->galleries; ?>
						</td>
					</tr>
					<tr>
						<td class="kuadesc">

						<script type="text/javascript">
							<!--
							function switch_avatar_category(gallery)
							{
							if (gallery == "")
								return;

							location.href="<?php echo CKunenaLink::GetMyProfileURL ( $this->_config, $this->user->id, 'edit', true )?>"+'&gallery='+gallery;
							}
							// -->
						</script>

<?php
$kid = 0;
foreach ($this->galleryimg as $avatarimg)
{
	echo '<span>';
	echo '<label for="kavatar'.$kid.'"><img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/gallery/' . $this->gallery . $avatarimg . '" alt="" /></label>';
	echo '<input id="kavatar'.$kid.'" type="radio" name="avatar" value="gallery/' . $this->gallery . $avatarimg . '"/>';
	echo "</span>";
	$kid++;
}
?>
						</td>
					</tr>
				</table>

			</td>
		</tr>
<?php endif; ?>
</table>
