<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

Text::script('COM_KUNENA_GEN_REMOVE_AVATAR');
Text::script('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_AVATAR');

// Add assets
$this->wa->registerAndUseStyle('fileupload', 'media/kunena/core/css/fileupload.css')
    ->registerAndUseScript('jquery.ui.widget', 'media/kunena/core/js/jquery.ui.widget.js')
    ->registerAndUseScript('load-image', 'media/kunena/core/js/load-image.all.min.js')
    ->registerAndUseScript('canvas-to-blob', 'media/kunena/core/js/canvas-to-blob.min.js')
    ->registerAndUseScript('jquery.fileupload', 'media/kunena/core/js/jquery.fileupload.js')
    ->registerAndUseScript('jquery.fileupload-process', 'media/kunena/core/js/jquery.fileupload-process.js')
    ->registerAndUseScript('jquery.iframe-transport', 'media/kunena/core/js/jquery.iframe-transport.js')
    ->registerAndUseScript('jquery.fileupload-image', 'media/kunena/core/js/jquery.fileupload-image.js')
    ->registerAndUseScript('upload.main', 'media/kunena/core/js/upload.avatar.js');

$this->doc->addScriptOptions('com_kunena.avatar_remove_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=removeavatar&format=json&' . Session::getFormToken() . '=1', false));
$this->doc->addScriptOptions('com_kunena.avatar_preload_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=loadavatar&format=json&' . Session::getFormToken() . '=1', false));
$this->doc->addScriptOptions('com_kunena.avatar_upload_url', KunenaRoute::_('index.php?option=com_kunena&view=user&task=upload&format=json'));
$this->doc->addScriptOptions('com_kunena.avatar_delete', KunenaIcons::delete());
?>
<h3>
    <?php echo $this->headerText; ?>
</h3>

<!-- Placeholder to display errors messages or exception for attachments  -->
<div id="kavatars-message-container" aria-live="polite"></div>
<!-- End of placeholder -->

<table class="table table-bordered table-striped">

    <?php if ($this->config->allowAvatarUpload) :
        ?>
        <tr>
            <td>
                <label for="kavatar-upload"><?php echo Text::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD'); ?></label>
            </td>
            <td>
                    <span class="btn btn-outline-primary fileinput-button">
                        <?php echo KunenaIcons::plus(); ?>
                        <span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_ADD_AVATAR_BUTTON') ?></span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileupload" type="file" name="file" multiple>
                        </span>

                <div id="files" class="files"></div>
                <div id="dropzone">
                    <div class="dropzone">
                        <div class="default message">
                            <span id="klabel_info_drop_browse">
                                <?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div id="kattach-list"></div>
                <input id="kunena_userid" type="hidden" value="<?php echo $this->user->id; ?>"/>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ($this->config->allowAvatarGallery && ($this->galleryOptions || $this->galleryImages)) :
        ?>
        <tr>
            <td class="col-md-3">
                <label><?php echo Text::_('COM_KUNENA_PROFILE_AVATAR_GALLERY'); ?></label>
                <input id="kunena_url_avatargallery" type="hidden"
                       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=galleryimages&format=raw') ?>"/>
            </td>
            <td>

                <?php if ($this->galleryOptions) :
                    ?>
                    <div>
                        <?php echo HTMLHelper::_(
                            'select.genericlist',
                            $this->galleryOptions,
                            'gallery',
                            '',
                            'value',
                            'text',
                            $this->gallery,
                            'avatar_gallery_select'
                        ); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->galleryImages) :
                    ?>
                    <ul id="gallery_list" class="thumbnails">

                        <?php foreach ($this->galleryImages as $image) :
                            ?>
                            <li class="col-md-2">
                                <input type="radio" name="avatar_gallery" id="radio<?php echo $image ?>"
                                       value="<?php echo "gallery/{$image}"; ?>" <?php echo !empty($image->checked) ? ' checked="checked" ' : '' ?> />
                                <label class=" radio thumbnail" for="radio<?php echo $image ?>">
                                    <img loading=lazy src="<?php echo "{$this->galleryUri}/{$image}"; ?>" alt="avatar"/>
                                </label>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                <?php endif; ?>

            </td>
        </tr>
    <?php endif; ?>

</table>
