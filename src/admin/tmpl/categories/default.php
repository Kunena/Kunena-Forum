<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
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
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect')
    ->useScript('jquery');

$app             = Factory::getApplication();
$user            = $this->getCurrentUser();
$userId          = $user->get('id');
// $filteredItem    = $this->escape($this->state->get('item.id'));
$listOrder       = $this->escape($this->state->get('list.ordering'));
$listDirn        = $this->escape($this->state->get('list.direction'));
$saveOrder       = ($listOrder == 'ordering' && strtolower($listDirn) == 'asc');
$saveOrderingUrl = 'index.php?option=com_kunena&view=categories&task=categories.saveorderajax';

if ($saveOrder) {
    HTMLHelper::_('kunenaforum.sortablelist', 'categoryList', 'adminForm', $listDirn, $saveOrderingUrl, false, true);
}

// $this->document->addScriptDeclaration(
//     "Joomla.orderTable = function () {
//         var table = document.getElementById(\"sortTable\");
//         var direction = document.getElementById(\"directionTable\");
//         var order = table.options[table.selectedIndex].value;
//         if (order != '" . $listOrder . "') {
//             dirn = 'asc';
//         } else {
//             dirn = direction.options[direction.selectedIndex].value;
//         }
//         Joomla.tableOrdering(order, dirn, '');
//     }"
// );
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
                    <tbody <?php if ($saveOrder) :
                            ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true" <?php
                                                                                                                                                                    endif; ?>>
                        <?php
                        $img_no             = '<i class="icon-cancel"></i>';
                        $img_yes            = '<i class="icon-checkmark"></i>';
                        $i                  = 0;

                        foreach ($this->categories as $item) :
                            $orderkey = array_search($item->id, $this->ordering[$item->parentid]);
                            $canEdit = $this->me->isAdmin($item);
                            $canCheckin = $this->user->authorise('core.admin', 'com_checkIn') || $item->checked_out == $this->user->id || $item->checked_out == 0;
                            $canEditOwn = $canEdit;
                            $canChange  = $canEdit && $canCheckin;

                            // Get the parents of item for sorting
                            if ($item->level > 0) {
                                $parentsStr       = "";
                                $_currentParentId = $item->parentid;
                                $parentsStr       = " " . $_currentParentId;

                                for ($i2 = 0; $i2 < $item->level; $i2++) {
                                    foreach ($this->ordering as $k => $v) {
                                        $v = implode("-", $v);
                                        $v = "-" . $v . "-";

                                        if (strpos($v, "-" . $_currentParentId . "-") !== false) {
                                            $parentsStr       .= " " . $k;
                                            $_currentParentId = $k;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $parentsStr = "";
                            }
                        ?>
                            <tr sortable-group-id="<?php echo $item->parentid; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="order text-center">
                                    <?php if ($canChange) :
                                        $disableClassName = '';
                                        $disabledLabel = '';

                                        if (!$saveOrder) :
                                            $disabledLabel    = Text::_('JORDERINGDISABLED');
                                            $disableClassName = 'inactive tip-top';
                                        endif; ?>
                                        <span class="sortable-handler<?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                                            <span class="icon-ellipsis-v"></span>
                                        </span>
                                        <input type="text" style="display:none;" name="order[]" size="5" value="<?php echo $orderkey; ?>" />
                                    <?php else :
                                    ?>
                                        <span class="sortable-handler inactive">
                                            <i class="icon-menu"></i>
                                        </span>
                                    <?php endif; ?>
                                    </th>
                                <td class="center">
                                    <?php echo HTMLHelper::_('kunenagrid.published', $i, $item->published, 'category.');; ?>
                                </td>
                                <th class="has-context">
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
                                <td class="center d-none d-md-table-cell">
                                    <span><?php echo $item->accessname; ?></span>
                                    <small>
                                        <?php echo Text::sprintf('(Access: %s)', $this->escape($item->accesstype)); ?>
                                    </small>
                                </td>
                                <td class="center d-none d-md-table-cell">
                                    <a class="btn btn-micro <?php echo $item->locked ? 'active' : ''; ?>" href="javascript: void(0);" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo ($item->locked ? 'un' : '') . 'lock'; ?>')">
                                        <?php echo $item->locked == 1 ? $img_yes : $img_no; ?>
                                    </a>
                                </td>
                                <?php if ($item->isSection()) :
                                ?>
                                    <td class="center d-none d-md-table-cell" colspan="3">
                                        <?php echo Text::_('COM_KUNENA_SECTION'); ?>
                                    </td>
                                <?php else :
                                ?>
                                    <td class="center d-none d-md-table-cell">
                                        <a class="btn btn-micro <?php echo $item->review ? 'active' : ''; ?>" href="javascript: void(0);" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo ($item->review ? 'un' : '') . 'review'; ?>')">
                                            <?php echo $item->review == 1 ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
                                    <td class="center d-none d-md-table-cell">
                                        <a class="btn btn-micro <?php echo $item->allowPolls ? 'active' : ''; ?>" href="javascript: void(0);" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo ($item->allowPolls ? 'deny' : 'allow') . '_polls'; ?>')">
                                            <?php echo $item->allowPolls == 1 ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
                                    <td class="center d-none d-md-table-cell">
                                        <a class="btn btn-micro <?php echo $item->allowAnonymous ? 'active' : ''; ?>" href="javascript: void(0);" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo ($item->allowAnonymous ? 'deny' : 'allow') . '_anonymous'; ?>')">
                                            <?php echo $item->allowAnonymous == 1 ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
                                <?php endif; ?>

                                <td class="center d-none d-md-table-cell">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php
                            $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <template id="joomla-dialog-batch"><?php echo $this->loadTemplate('batch'); ?></template>

                <input type="hidden" name="task" value="" />
                <!-- <input type="hidden" name="catid" value="<?php //echo $filteredItem; 
                                                                ?>" /> -->
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>