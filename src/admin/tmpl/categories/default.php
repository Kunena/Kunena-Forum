<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https: //www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https: //www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$app    = Factory::getApplication();
$user   = $this->getCurrentUser();
$userId = $user->get('id');
$listOrder       = $this->escape($this->state->get('list.ordering'));
$listDirn        = $this->escape($this->state->get('list.direction'));
$saveOrder       = ($listOrder == 'ordering' && strtolower($listDirn) == 'asc');

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_kunena&view=categories&task=categories.saveorderajax';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table itemList" id="categoryList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <td scope="col" class="w-1 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder); ?>
                            </td>
                            <th scope="col" class="w-1">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_CATEGORIES_LABEL_ENABLED', 'p.published', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-50">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'p.title', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-20">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOCKED', 'p.locked', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_REVIEW', 'p.review', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_CATEGORIES_LABEL_POLL', 'p.allowPolls', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-1">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false" <?php endif; ?>>
                        <?php
                        $img_no  = '<span class="icon-unpublish" aria-hidden="true"></span>';
                        $img_yes = '<span class="icon-publish" aria-hidden="true"></span>';

                        foreach ($this->categories as $i => $item) :
                            $orderkey               = array_search($item->id, $this->ordering[$item->parentid]);
                            $canEdit                = $this->me->isAdmin($item);
                            $canCheckin             = $this->user->authorise('core.admin', 'com_checkIn') || $item->checked_out == $this->user->id || $item->checked_out == 0;
                            $canEditOwn             = $canEdit;
                            $canChange              = $canEdit && $canCheckin;
                            $categoryPublishTask    = $item->published ? 'categories.unpublish' : 'categories.publish';
                            $categoryLockedTask     = $item->locked ? 'categories.unlock' : 'categories.lock';
                            $categoryReviewTask     = $item->review ? 'categories.unreview' : 'categories.review';
                            $categoryAllowpollsTask = $item->allowPolls ? 'categories.denypolls' : 'categories.allowpolls';
                            $categoryAnonymousTask  = $item->allowAnonymous ? 'categories.denyanonymous' : 'categories.allowanonymous';

                            // Get the parents of item for sorting
                            if ($item->level > 0) {
                                $parentsStr = '';
                                $_currentParentId = $item->parentid;
                                $parentsStr = ' ' . $_currentParentId;
                                for ($i2 = 0; $i2 < $item->level; $i2++) {
                                    foreach ($this->ordering as $k => $v) {
                                        $v = implode('-', $v);
                                        $v = '-' . $v . '-';
                                        if (strpos($v, '-' . $_currentParentId . '-') !== false) {
                                            $parentsStr .= ' ' . $k;
                                            $_currentParentId = $k;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $parentsStr = '';
                            }
                        ?>
                            <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->parentid; ?>" data-item-id="<?php echo $item->id ?>" data-parents="<?php echo $parentsStr ?>" data-level="<?php echo $item->level ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <?php
                                    $iconClass = '';
                                    if (!$canChange) {
                                        $iconClass = ' inactive';
                                    } elseif (!$saveOrder) {
                                        $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                    }
                                    ?>
                                    <span class="sortable-handler<?php echo $iconClass ?>">
                                        <span class="icon-ellipsis-v"></span>
                                    </span>
                                    <?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" class="hidden" name="order[]" size="5" value="<?php echo $orderkey; ?>">
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="js-grid-item-action tbody-icon <?php echo $item->published ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $categoryPublishTask ?>" data-item-form-id="" aria-labelledby="cbpublish<?php echo $i; ?>-desc"><?php echo $item->published ? $img_yes : $img_no; ?></a>
                                    <div role="tooltip" id="cbpublish<?php echo $i; ?>-desc"><?php echo $item->published ? Text::_('COM_KUNENA_TASK_UNPUBLISH_CATEGORY') : Text::_('COM_KUNENA_TASK_PUBLISH_CATEGORY'); ?></div>
                                </td>
                                <th class="">
                                    <div class="break-word">
                                        <?php
                                        echo str_repeat('<span class="gi">&mdash;</span>', $item->level);

                                        if ($item->checked_out) {
                                            $canCheckin = $item->checked_out == 0 || $item->checked_out == $this->user->id || $this->user->authorise('core.admin', 'com_checkIn');
                                            $editor     = KunenaFactory::getUser($item->editor)->getName();
                                            echo HTMLHelper::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'category.', $canCheckin);
                                        }
                                        ?>
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $item->id); ?>">
                                            <?php echo $this->escape($item->name); ?>
                                        </a>
                                        <div class="small break-word">
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        </div>
                                    </div>
                                </th>
                                <td class="">
                                    <span><?php echo $item->accessname; ?></span>
                                    <small>
                                        <?php echo Text::sprintf('(Access: %s)', $this->escape($item->accesstype)); ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="js-grid-item-action tbody-icon <?php echo $item->locked ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $categoryLockedTask ?>" data-item-form-id="" aria-labelledby="cblocked<?php echo $i; ?>-desc"><?php echo $item->locked ? $img_yes : $img_no; ?></a>
                                    <div role="tooltip" id="cblocked<?php echo $i; ?>-desc"><?php echo $item->locked ? Text::_('COM_KUNENA_TASK_UNLOCK_CATEGORY') : Text::_('COM_KUNENA_TASK_LOCK_CATEGORY'); ?></div>
                                </td>
                                <?php if ($item->isSection()) :
                                ?>
                                    <td class="text-center " colspan="3">
                                        <?php echo Text::_('COM_KUNENA_SECTION'); ?>
                                    </td>
                                <?php else :
                                ?>
                                    <td class="text-center">
                                        <a href="#" class="js-grid-item-action tbody-icon <?php echo $item->review ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $categoryReviewTask ?>" data-item-form-id="" aria-labelledby="cbreview<?php echo $i; ?>-desc"><?php echo $item->review ? $img_yes : $img_no; ?></a>
                                        <div role="tooltip" id="cbreview<?php echo $i; ?>-desc"><?php echo $item->review ? Text::_('COM_KUNENA_TASK_UNREVIEW_CATEGORY') : Text::_('COM_KUNENA_TASK_REVIEW_CATEGORY'); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="js-grid-item-action tbody-icon <?php echo $item->allowPolls ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $categoryAllowpollsTask ?>" data-item-form-id="" aria-labelledby="cballowpolls<?php echo $i; ?>-desc"><?php echo $item->allowPolls ? $img_yes : $img_no; ?></a>
                                        <div role="tooltip" id="cballowpolls<?php echo $i; ?>-desc"><?php echo $item->allowPolls ? Text::_('COM_KUNENA_TASK_DENYPOLLS_CATEGORY') : Text::_('COM_KUNENA_TASK_ALLOWPOLLS_CATEGORY'); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="js-grid-item-action tbody-icon <?php echo $item->allowAnonymous ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $categoryAnonymousTask ?>" data-item-form-id="" aria-labelledby="cbanonymous<?php echo $i; ?>-desc"><?php echo $item->allowAnonymous ? $img_yes : $img_no; ?></a>
                                        <div role="tooltip" id="cbreview<?php echo $i; ?>-desc"><?php echo $item->allowAnonymous ? Text::_('COM_KUNENA_TASK_DENYANONYMOUS_CATEGORY') : Text::_('COM_KUNENA_TASK_ALLOWANONYMOUS_CATEGORY'); ?></div>
                                    </td>
                                <?php endif; ?>
                                <td class="d-none d-md-table-cell">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php  // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <?php echo HTMLHelper::_(
                    'bootstrap.renderModal',
                    'joomla-dialog-batch',
                    [
                        'title'  => Text::_('COM_KUNENA_BATCH_OPTIONS'),
                    ],
                    $this->loadTemplate('batch')
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