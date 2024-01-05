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

HTMLHelper::_('behavior.multiselect');

$app       = Factory::getApplication();
$user      = $app->getIdentity();
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
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                 </th>
                                <th width="10%">
                                    <?php echo Text::_('COM_KUNENA_RANKSIMAGE'); ?>
                                </th>
                                <th width="58%">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKS_LABEL_TITLE', 'a.`rankTitle`', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%" class="nowrap center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKS_SPECIAL', 'a.`rankSpecial`', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%" class="nowrap center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_RANKSMIN', 'a.`rankMin`', $listDirn, $listOrder); ?>
                                </th>
                                <th width="1%" class="nowrap center hidden-phone">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.`rankId`', $listDirn, $listOrder); ?>
                                </th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <?php
                                // $item->max_ordering = 0;
                                $ordering   = ($listOrder == 'a.`ordering`');
                                $canCreate  = $user->authorise('core.create', 'com_kunena');
                                $canEdit    = $user->authorise('core.edit', 'com_kunena');
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || ($item->checked_out == $userId) || ($item->checked_out == 0);
                                $canChange  = $user->authorise('core.edit.state', 'com_kunena') && $canCheckin;
                                $link       = Route::_('index.php?option=com_kunena&task=file.edit&id=' . (int) $item->rankId);

                                ?>
                                <tr data-draggable-group="0" item-id="<?php echo $item->rankId; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->rankId, false, 'cid', 'cb', $item->rankTitle); ?>
                                    </td>
                                    <td>
                                        <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'ranks.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=rank&layout=edit&id=' . $item->rankId); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->rankTitle); ?>">
                                            <img loading=lazy src="<?php echo $this->escape($this->ktemplate->getRankPath($item->rankImage, true)) ?>"
                                                alt="<?php echo $this->escape($item->rankImage); ?>"/></a>
                                        <?php else : ?>
                                            <img loading=lazy src="<?php echo $this->escape($this->ktemplate->getRankPath($item->rankImage, true)) ?>"
                                                alt="<?php echo $this->escape($item->rankImage); ?>"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'ranks.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=rank&layout=edit&id=' . $item->rankId); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->rankTitle); ?>">
                                            <?php echo Text::_($item->rankTitle); ?></a>
                                        <?php else : ?>
                                            <?php echo Text::_($item->rankTitle); ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="nowrap center">
                                        <?php echo $item->rankSpecial == 1 ? Text::_('COM_KUNENA_YES') : Text::_('COM_KUNENA_NO'); ?>
                                    </td>
                                    <td class="nowrap center">
                                        <?php echo $this->escape($item->rankMin); ?>
                                    </td>
                                    <td class="nowrap center">
                                        <?php echo $this->escape($item->rankId); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="pull-right small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
