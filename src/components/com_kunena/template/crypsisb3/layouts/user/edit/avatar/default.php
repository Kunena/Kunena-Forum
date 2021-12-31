<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

Text::script('COM_KUNENA_GEN_REMOVE_AVATAR');
Text::script('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_AVATAR');

HTMLHelper::_('jquery.ui');
$this->addScript('load-image.all.min.js');
$this->addScript('canvas-to-blob.min.js');
$this->addScript('jquery.iframe-transport.js');
$this->addScript('jquery.fileupload.js');
$this->addScript('jquery.fileupload-process.js');
$this->addScript('jquery.fileupload-image.js');
$this->addScript('jquery.fileupload-validate.js');
$this->addScript('assets/js/upload.avatar.js');
$this->addStyleSheet('fileupload.css');

$this->addScriptOptions('com_kunena.avatar_remove_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=removeavatar&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.avatar_preload_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=loadavatar&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.avatar_delete', KunenaIcons::delete());
$this->addScriptOptions('com_kunena.avatar_upload_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=upload&format=json'));
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped">

	<?php if ($this->config->allowavatarupload)
		:
		?>
		<tr>
			<td>
				<label for="kavatar-upload"><?php echo Text::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD'); ?></label>
			</td>
			<td>
					<span class="btn btn-primary fileinput-button">
						<?php echo KunenaIcons::plus(); ?>
						<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_ADD_AVATAR_BUTTON') ?></span>
						<!-- The file input field used as target for the file upload widget -->
						<input id="fileupload" type="file" name="file" multiple>
						</span>

				<div id="files" class="files"></div>
				<div id="dropzone">
					<div class="dropzone">
						<div class="default message">
							<span
									id="klabel_info_drop_browse"><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?></span>
						</div>
					</div>
				</div>
				<div id="kattach-list"></div>
				<input id="kunena_userid" type="hidden" value="<?php echo $this->user->id; ?>"/>
			</td>
		</tr>
	<?php endif; ?>

	<?php if ($this->config->allowavatargallery && ($this->galleryOptions || $this->galleryImages))
		:
		?>
		<tr>
			<td class="col-md-3">
				<label><?php echo Text::_('COM_KUNENA_PROFILE_AVATAR_GALLERY'); ?></label>
				<input id="kunena_url_avatargallery" type="hidden"
				       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=galleryimages&format=raw') ?>"/>
			</td>
			<td>

				<?php if ($this->galleryOptions)
					:
					?>
					<div>
						<?php echo HTMLHelper::_(
							'select.genericlist', $this->galleryOptions, 'gallery', '', 'value', 'text',
							$this->gallery, 'avatar_gallery_select'
						); ?>
					</div>
				<?php endif; ?>

				<?php if ($this->galleryImages)
					:
					?>
					<ul id="gallery_list" class="thumbnails">

						<?php foreach ($this->galleryImages as $image)
							:
							?>
							<li class="col-md-2">
								<input type="radio" name="avatar_gallery" id="radio<?php echo $image ?>"
								       value="<?php echo "gallery/{$image}"; ?>" <?php echo !empty($image->checked) ? ' checked="checked" ' : '' ?> />
								<label class=" radio thumbnail" for="radio<?php echo $image ?>">
									<img src="<?php echo "{$this->galleryUri}/{$image}"; ?>" alt="avatar"/>
								</label>
							</li>
						<?php endforeach; ?>

					</ul>
				<?php endif; ?>

			</td>
		</tr>
	<?php endif; ?>

</table>
