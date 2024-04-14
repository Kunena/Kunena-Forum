<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Attachments
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$app       = Factory::getApplication();
$user      = $this->getCurrentUser();
$userId    = $user->id;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=attachments') ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table attachmentsList" id="attachmentsList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TITLE', 'filename', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TYPE', 'filetype', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_SIZE', 'size', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_IMAGEDIMENSIONS'); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_ATTACHMENTS_USERNAME', 'username', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_MESSAGE', 'post', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-1 text-center d-none d-lg-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;

                        foreach ($this->items as $id => $attachment) :
                            $message = $attachment->getMessage();
                        ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, intval($attachment->id)) ?>
                                </td>
                                <th>
                                    <div class="d-flex">
                                        <div class="image me-2 align-self-center">
                                            <?php echo $attachment->getLayout()->render('thumbnail'); ?>
                                        </div>
                                        <div class="filename">
                                            <small><?php echo $attachment->getFilename(); ?></small>
                                        </div>
                                    </div>
                                </th>
                                <td><?php echo $this->escape($attachment->filetype); ?></td>
                                <td><?php echo number_format(intval($attachment->size) / 1024, 0, '', ',') . ' ' . Text::_('COM_KUNENA_A_FILESIZE_KB'); ?></td>
                                <td><?php echo $attachment->width > 0 ? $attachment->width . ' x ' . $attachment->height : '' ?></td>
                                <td><?php echo $this->escape($message->getAuthor()->getName()); ?></td>
                                <td><?php echo $this->escape($message->subject); ?></td>
                                <td><?php echo intval($attachment->id); ?></td>
                            </tr>
                            <?php $i++;; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>