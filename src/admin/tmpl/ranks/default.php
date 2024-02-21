<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Ranks
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$app       = Factory::getApplication();
$user      = $this->getCurrentUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_kunena&view=ranks'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table itemList" id="rankList">
                        <thead>
                            <tr>
                                <td scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-5">
                                    <?php echo Text::_('COM_KUNENA_RANKSIMAGE'); ?>
                                </th>
                                <th scope="col" class="w-10">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKS_LABEL_TITLE', 'rankTitle', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKS_SPECIAL', 'rankSpecial', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKSMIN', 'rankMin', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-1">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'rankId', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <?php
                                $canEdit = $user->authorise('core.edit', 'com_kunena');
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->rankId, false, 'cid', 'cb', $item->rankTitle); ?>
                                    </td>
                                    <td>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=rank&layout=edit&id=' . $item->rankId); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->rankTitle); ?>">
                                                <img loading=lazy src="<?php echo $this->escape($this->ktemplate->getRankPath($item->rankImage, true)) ?>" alt="<?php echo $this->escape($item->rankImage); ?>" /></a>
                                        <?php else : ?>
                                            <img loading=lazy src="<?php echo $this->escape($this->ktemplate->getRankPath($item->rankImage, true)) ?>" alt="<?php echo $this->escape($item->rankImage); ?>" />
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=rank&layout=edit&id=' . $item->rankId); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->rankTitle); ?>">
                                                <?php echo Text::_($item->rankTitle); ?></a>
                                        <?php else : ?>
                                            <?php echo Text::_($item->rankTitle); ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        <?php echo $item->rankSpecial == 1 ? Text::_('COM_KUNENA_YES') : Text::_('COM_KUNENA_NO'); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($item->rankMin); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($item->rankId); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

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