<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<h3>
	<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?>
</h3>

<table class="table table-bordered table-striped">
	<?php if ($this->profile->avatar) : ?>
	<tr>
		<td class="span3">
			<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP');?></label>
		</td>
		<td>
			<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE');?></label>
		</td>
		<td>
			<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
		</td>
	</tr>
	<?php endif; ?>
	<?php if ($this->config->allowavatarupload) : ?>
	<tr>
		<td>
			<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD');?></label>
		</td>
		<td>
			<div>
				<input id="kavatar-upload" type="file" class="button" name="avatarfile" />
			</div>
		</td>
	</tr>
	<?php endif; ?>
	<?php if ($this->config->allowavatargallery) : ?>
	<tr>
		<td class="span3">
			<label><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY');?></label>
			<input id="kunena_url_avatargallery" type="hidden" value="<?php echo $this->me->getUrl(true, 'edit') ?>" />
		</td>
		<td>
			<div>
				<?php echo $this->galleries; ?>
			</div>
			<ul class="thumbnails">
				<?php foreach ($this->galleryimg as $avatarimg) : ?>
				<li class="span2">
					<label class="thumbnail">
						<img src="<?php echo $this->galleryurl .'/'. ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>" alt="" />
						<input type="radio" name="avatar" value="<?php echo 'gallery/' . ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>"/>
					</label>
				</li>
				<?php endforeach; ?>
			</ul>
		</td>
	</tr>
<?php /* TODO: ????
	<tr>
		<td>
			<?php foreach($this->galleryImagesList as $name=>$gallery): ?>
			<input type="hidden" id="Kunena_<?php echo $name ?>" name="<?php echo $name ?>" value='<?php echo $gallery ?>' />
			<?php endforeach; ?>
			<input type="hidden" id="Kunena_Image_Gallery_URL" name="Kunena_Image_Gallery_URL" value="<?php echo JURI::root().'media/kunena/avatars/gallery' ?>" />
		</td>
	</tr>
*/ ?>
	<?php endif; ?>
</table>
