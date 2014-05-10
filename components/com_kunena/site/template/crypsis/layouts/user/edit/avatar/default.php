<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped">

	<?php if ($this->profile->avatar) : ?>
	<tr>
		<td class="span3">
			<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP'); ?></label>
		</td>
		<td>
			<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE'); ?></label>
		</td>
		<td>
			<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($this->config->allowavatarupload) : ?>
	<tr>
		<td>
			<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD'); ?></label>
		</td>
		<td>
			<div>
				<input id="kavatar-upload" type="file" class="button" name="avatarfile" />
			</div>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($this->config->allowavatargallery && ($this->galleryOptions || $this->galleryImages)) : ?>
	<tr>
		<td class="span3">
			<label><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY'); ?></label>
			<input id="kunena_url_avatargallery" type="hidden"
			       value="<?php echo $this->profile->getUrl(true, 'edit'); ?>" />
		</td>
		<td>

			<?php if ($this->galleryOptions) : ?>
			<div>
				<?php echo JHtml::_(
					'select.genericlist', $this->galleryOptions, 'gallery', '', 'value', 'text',
					$this->gallery, 'avatar_category_select'
				); ?>
			</div>
			<?php endif; ?>

			<?php if ($this->galleryImages) : ?>
			<ul class="thumbnails">

				<?php foreach ($this->galleryImages as $image) : ?>
				<li class="span2">
					<label class="thumbnail">
						<img src="<?php echo "{$this->galleryUri}/{$image}"; ?>" alt="" />
						<input type="radio" name="avatar" value="<?php echo "gallery/{$image}"; ?>" />
					</label>
				</li>
				<?php endforeach; ?>

			</ul>
			<?php endif; ?>

		</td>
	</tr>
	<?php endif; ?>

</table>
