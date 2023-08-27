<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.order' && strtolower($listDirn) == 'asc');

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('jquery');

Factory::getDocument()->addScript(Uri::root() . 'media/kunena/core/js/multiselect-uncompressed.js');

Factory::getDocument()->addScriptDeclaration(
    "jQuery(document).ready(function() {
        Joomla.JMultiSelect('adminForm');
    });"
);

if ($this->saveOrder)
{
    HTMLHelper::_('kunenaforum.sortablelist', 'categoryList', 'adminForm', $this->listDirection, $this->saveOrderingUrl, false, true);
}

$filterItem = $this->escape($this->state->get('item.id'));

Factory::getDocument()->addScriptDeclaration(
    "Joomla.orderTable = function () {
        var table = document.getElementById(\"sortTable\");
        var direction = document.getElementById(\"directionTable\");
        var order = table.options[table.selectedIndex].value;
        if (order != '" . $listOrder . "') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }"
);
?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>"
                      method="post" name="adminForm"
                      id="adminForm">
                    <input type="hidden" name="task" value=""/>
                    <input type="hidden" name="catid" value="<?php echo $this->filter->Item; ?>"/>
                    <input type="hidden" name="boxchecked" value="0"/>
                    <input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>"/>
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>"/>
                    <?php echo HTMLHelper::_('form.token'); ?>

                    <div id="filter-bar" class="btn-toolbar">
                        <div class="filter-search btn-group pull-left">
                            <label for="filter_search"
                                   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
                            <input type="text" name="filter_search" id="filter_search" class="filter form-control"
                                   placeholder="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"
                                   value="<?php echo $this->filter->Search; ?>"
                                   title="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"/>
                        </div>
                        <div class="btn-group pull-left">
                            <button class="btn btn-outline-primary tip" type="submit"
                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i
                                        class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
                            </button>
                            <button class="btn btn-outline-primary tip" type="button"
                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
                                    onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i
                                        class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
                            </button>
                        </div>
                        <div class="btn-group pull-right d-none d-md-block">
                            <label for="limit"
                                   class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                            <?php echo $this->pagination->getLimitBox(); ?>
                        </div>
                        <div class="btn-group pull-right d-none d-md-block">
                            <label for="directionTable"
                                   class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
                            <select name="directionTable" id="directionTable" class="form-select input-medium"
                                    onchange="Joomla.orderTable()">
                                <option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
                                <?php echo HTMLHelper::_('select.options', $this->getSortDirectionFields(), 'value', 'text', $this->list->Direction); ?>
                            </select>
                        </div>
                        <div class="btn-group pull-right">
                            <label for="sortTable"
                                   class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
                            <select name="sortTable" id="sortTable" class="form-select input-medium" onchange="Joomla.orderTable()">
                                <option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
                                <?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->list->Ordering); ?>
                            </select>
                        </div>
                        <!-- TODO: not implemented
                            <div class="btn-group pull-right">
                                <label for="sortTable" class="element-invisible"><?php // Echo Text::_('JGLOBAL_SORT_BY');?></label>
                                <select name="levellimit" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                                    <option value=""><?php // Echo Text::_('JOPTION_SELECT_MAX_LEVELS');?></option>
                                    <?php // echo HTMLHelper::_('select.options', $this->levelFields, 'value', 'text', $this->filterLevels);?>
                                </select>
                            </div>-->
                        <div class="clearfix"></div>
                    </div>

                    <table class="table table-striped" id="categoryList">
                        <thead>
                        <tr>
                            <th width="1%" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th width="1%" class="nowrap center d-none d-md-block">
                                
                            </th>
                            <th width="5%" class="nowrap center">
                                <?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', 'asc', '', null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                            </th>
                            <th width="1%" class="nowrap">
                                <?php echo Text::_('COM_KUNENA_GO'); ?>
                            </th>
                            <th width="51%" class="nowrap">
                                <?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="20%" class="nowrap center d-none d-md-block">
                                <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
                                <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOCKED', 'p.locked', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
                                <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_REVIEW', 'p.review', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="5%" class="center">
                                <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_POLL', 'p.allowPolls', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
                                <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $this->listDirection, $this->listOrdering); ?>
                            </th>
                            <th width="1%" class="nowrap center d-none d-md-block">
                                <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->listDirection, $this->listOrdering); ?>
                            </th>
                        </tr>
                        <tr>
                            <td class="d-none d-md-table-cell">
                            </td>
                            <td class="d-none d-md-table-cell">
                            </td>
                            <td class="nowrap center">
                                <label for="filter_published"
                                       class="element-invisible"><?php echo Text::_('All'); ?></label>
                                <select name="filter_published" id="filter_published"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filter->Published, true); ?>
                                </select>
                            </td>
                            <td>
                            </td>
                            <td class="nowrap">
                                <label for="filterTitle"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
                                <input class="input-block-level input-filter filter form-control" type="text"
                                       name="filterTitle"
                                       id="filterTitle"
                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                       value="<?php echo $this->filter->Title; ?>"
                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                            </td>
                            <td class="nowrap center d-none d-md-table-cell">
                                <label for="filterAccess"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filterAccess" id="filterAccess"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->filter->Access); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filterLocked"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filterLocked" id="filterLocked"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', $this->lockOptions(), 'value', 'text', $this->filter->Locked); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filterReview"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filterReview" id="filterReview"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', $this->reviewOptions(), 'value', 'text', $this->filter->Review); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filterAllowPolls"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filterAllowPolls" id="filterAllowPolls"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', $this->allowPollsOptions(), 'value', 'text', $this->filter->Allow_polls); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filterAnonymous"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filterAnonymous" id="filterAnonymous"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', $this->anonymousOptions(), 'value', 'text', $this->filter->Anonymous); ?>
                                </select>
                            </td>
                            <td class="nowrap center d-none d-md-table-cell">
                            </td>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <?php echo $this->pagination->getListFooter(); ?>
                                <?php // Load the batch processing form. ?>
                                <?php echo $this->loadTemplate('batch'); ?>
                                <?php // Load the modal to confirm delete. ?>
                                <?php echo $this->loadTemplate('confirmdelete'); ?>
                            </td>
                        </tr>
                        </tfoot>
                        <tbody <?php if ($saveOrder) :
                            ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php
                               endif; ?>>
                        <?php
                        $img_no             = '<i class="icon-cancel"></i>';
                        $img_yes            = '<i class="icon-checkmark"></i>';
                        $i                  = 0;

                        if ($this->pagination->total >= 0) :
                            foreach ($this->categories as $item) :
                                $orderkey = array_search($item->id, $this->ordering[$item->parentid]);
                                $canEdit = $this->me->isAdmin($item);
                                $canCheckin = $this->user->authorise('core.admin', 'com_checkIn') || $item->checked_out == $this->user->id || $item->checked_out == 0;
                                $canEditOwn = $canEdit;
                                $canChange  = $canEdit && $canCheckin;

                                // Get the parents of item for sorting
                                if ($item->level > 0)
                                {
                                    $parentsStr       = "";
                                    $_currentParentId = $item->parentid;
                                    $parentsStr       = " " . $_currentParentId;

                                    for ($i2 = 0; $i2 < $item->level; $i2++)
                                    {
                                        foreach ($this->ordering as $k => $v)
                                        {
                                            $v = implode("-", $v);
                                            $v = "-" . $v . "-";

                                            if (strpos($v, "-" . $_currentParentId . "-") !== false)
                                            {
                                                $parentsStr       .= " " . $k;
                                                $_currentParentId = $k;
                                                break;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $parentsStr = "";
                                }
                                ?>
                                <tr sortable-group-id="<?php echo $item->parentid; ?>" item-id="<?php echo $item->id ?>"
                                        parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                    </td>
                                    <td class="order nowrap center hidden-phone">
                                    <?php if ($canChange)
                                        :
                                        $disableClassName = '';
                                        $disabledLabel = '';

                                        if (!$this->saveOrder)
                                        :
                                            $disabledLabel    = Text::_('JORDERINGDISABLED');
                                            $disableClassName = 'inactive tip-top';
                                        endif; ?>
                                        <span class="sortable-handler<?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                                            <span class="icon-ellipsis-v"></span>
                                        </span>
                                        <input type="text" style="display:none;" name="order[]" size="5"
                                              value="<?php echo $orderkey; ?>"/>
                                    <?php else:
                                    ?>
                                        <span class="sortable-handler inactive">
                                        <i class="icon-menu"></i>
                                    </span>
                                    <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <?php echo HTMLHelper::_('kunenagrid.published', $i, $item->published, 'category.');; ?>
                                    </td>
                                    <td class="center">
                                        <?php if (!$this->filter->Item || ($this->filter->Item != $item->id && $item->parentid)) :
                                            ?>
                                            <button class="btn btn-micro"
                                                    title="Display only this item and its children"
                                                    onclick="jQuery('input[name=catid]').val(<?php echo $item->id ?>);this.form.submit()">
                                                <i class="icon-location"></i>
                                            </button>
                                        <?php else :
                                            ?>
                                            <button class="btn btn-micro"
                                                    title="Display only this item and its children"
                                                    onclick="jQuery('input[name=catid]').val(<?php echo $item->parentid ?>);this.form.submit()">
                                                <i class="icon-arrow-up"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="has-context">
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
                                        <small>
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        </small>
                                    </td>
                                    <td class="center d-none d-md-table-cell">
                                        <span><?php echo $item->accessname; ?></span>
                                        <small>
                                            <?php echo Text::sprintf('(Access: %s)', $this->escape($item->accesstype)); ?>
                                        </small>
                                    </td>
                                    <td class="center d-none d-md-table-cell">
                                        <a class="btn btn-micro <?php echo $item->locked ? 'active' : ''; ?>"
                                           href="javascript: void(0);"
                                           onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->locked ? 'un' : '') . 'lock'; ?>')">
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
                                            <a class="btn btn-micro <?php echo $item->review ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->review ? 'un' : '') . 'review'; ?>')">
                                                <?php echo $item->review == 1 ? $img_yes : $img_no; ?>
                                            </a>
                                        </td>
                                        <td class="center d-none d-md-table-cell">
                                            <a class="btn btn-micro <?php echo $item->allowPolls ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowPolls ? 'deny' : 'allow') . '_polls'; ?>')">
                                                <?php echo $item->allowPolls == 1 ? $img_yes : $img_no; ?>
                                            </a>
                                        </td>
                                        <td class="center d-none d-md-table-cell">
                                            <a class="btn btn-micro <?php echo $item->allowAnonymous ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowAnonymous ? 'deny' : 'allow') . '_anonymous'; ?>')">
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
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td colspan="10">
                                    <div class="card card-block bg-faded p-2 center filter-state">
                                            <span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
                                                <?php
                                                if ($this->filter->Active) :
                                                    ?>
                                                    <button class="btn btn-outline-primary" type="button"
                                                            onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
                                                <?php else :
                                                    ?>
                                                    <button class="btn btn-outline-success" type="button"
                                                            onclick="Joomla.submitbutton('add');"><?php echo Text::_('COM_KUNENA_NEW_CATEGORY'); ?></button>
                                                <?php endif; ?>
                                            </span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="pull-right small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
