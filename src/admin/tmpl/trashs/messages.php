<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Trash
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
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

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trashs') ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table itemList" id="messagesList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <td scope="col" class="w-1">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_ID', 'id', $listDirn, $listOrder); ?>
                            </td>
                            <td scope="col" class="w-20">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_TITLE', 'title', $listDirn, $listOrder); ?>
                            </td>
                            <th scope="col" class="w-20">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_MENU_TOPIC', 'topic', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-20">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_CATEGORY', 'category', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_AUTHOR', 'author', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_IP', 'ip', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_TRASH_DATE', 'time', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i      = 0;
                        $itemid = KunenaRoute::fixMissingItemID();

                        foreach ($this->items as $id => $row) :
                        ?>
                            <tr>
                                <td><?php echo HTMLHelper::_('grid.id', $i++, intval($row->id)) ?></td>
                                <th><?php echo intval($row->id); ?></th>
                                <th>
                                    <a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&catid=' . $row->category_id . '&id=' . $row->topic_id . '&mesid=' . $row->id . '&Itemid=' . $itemid . '#' . $row->id); ?>" target="_blank"><?php echo $this->escape($row->title); ?></a>
                                </th>
                                <td>
                                    <a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&catid=' . $row->category_id . '&id=' . $row->topic_id . '&Itemid=' . $itemid); ?>" target="_blank"><?php echo $this->escape($row->topic); ?></a>
                                </td>
                                <td><?php echo $this->escape($row->category); ?></td>
                                <td><?php echo $this->escape($row->author); ?></td>
                                <td><?php echo $this->escape($row->ip); ?></td>
                                <td><?php echo Factory::getDate($row->time)->format('Y-m-d h:m:s', $row->time); ?></td>
                            </tr>
                        <?php
                        endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="type" value="messages" />
                <input type="hidden" name="layout" value="messages" />
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