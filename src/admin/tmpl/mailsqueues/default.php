<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Mailsqueues
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=mailsqueues') ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table itemList" id="mailsqueueList">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-5 d-none d-lg-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-20">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_A_MAILSQUEUE_SUBJECT', 'subject', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_A_MAILSQUEUE_MESSAGEID', 'messageId', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-20 d-none d-md-table-cell">
                                    <?php echo Text::_('COM_KUNENA_A_MAILSQUEUE_URL'); ?>
                                </th>
                                <th scope="col" class="w-15 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_A_MAILSQUEUE_CATEGORYNAME', 'categoryName', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo Text::_('COM_KUNENA_A_MAILSQUEUE_ONCE'); ?>
                                </th>
                                <th scope="col" class="w-5 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_A_MAILSQUEUE_SEND', 'send', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; foreach ($this->items as $row) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, intval($row->id)); ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php echo $this->escape($row->id); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($row->subject); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo (int) $row->messageId; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php
                                        $rawUrl     = $row->url;
                                        $displayUrl = mb_strlen($rawUrl) > 50 ? mb_substr($rawUrl, 0, 50) . '…' : $rawUrl;
                                        echo '<span title="' . $this->escape($rawUrl) . '">' . $this->escape($displayUrl) . '</span>';
                                        ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($row->categoryName); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($row->once); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $row->send ? '<span class="badge bg-success">' . Text::_('JYES') . '</span>' : '<span class="badge bg-secondary">' . Text::_('JNO') . '</span>'; ?>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo $this->pagination->getListFooter(); ?>

                <?php echo HTMLHelper::_(
                    'bootstrap.renderModal',
                    'joomla-dialog-purge',
                    [
                        'title' => Text::_('COM_KUNENA_A_MAILSQUEUE_PURGE'),
                    ],
                    $this->loadTemplate('purge')
                ); ?>

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
