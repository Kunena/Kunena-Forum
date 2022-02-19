<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');

if (!empty($this->list->Ordering))
{
	$wa->addInlineScript(
		'Joomla.orderTable = function () {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		if (order != ' . $this->list->Ordering . ') {
        dirn = "asc";
        } else {
        dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, "");
        }'
	);
}

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

if ($this->list->saveOrder && !empty($this->items))
{
	$saveOrderingUrl = $this->list->saveOrderingUrl;
	HTMLHelper::_('draggablelist.draggable');
}

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
                    <input type="hidden" name="filter_order" value="<?php echo $this->list->Ordering; ?>"/>
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->list->Direction; ?>"/>
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
                        <div class="btn-group pull-right hidden-phone">
                            <label for="limit"
                                   class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
							<?php echo $this->pagination->getLimitBox(); ?>
                        </div>
                        <div class="btn-group pull-right hidden-phone">
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
                            <th width="1%" class="nowrap center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', 'asc', '', null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                            </th>
                            <th width="1%" class="hidden-phone">
                                <input type="checkbox" name="checkall-toggle" value=""
                                       title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
                                       onclick="Joomla.checkAll(this)"/>
                            </th>
                            <th width="5%" class="nowrap center">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'p.published', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="1%" class="nowrap">
								<?php echo Text::_('COM_KUNENA_GO'); ?>
                            </th>
                            <th width="51%" class="nowrap">
								<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="20%" class="nowrap center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOCKED', 'p.locked', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_REVIEW', 'p.review', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="5%" class="center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_POLL', 'p.allowPolls', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="5%" class="nowrap center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                            <th width="1%" class="nowrap center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->list->Direction, $this->list->Ordering); ?>
                            </th>
                        </tr>
                        <tr>
                            <td class="hidden-phone">
                            </td>
                            <td class="hidden-phone">
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
                            <td class="nowrap center hidden-phone">
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
                            <td class="nowrap center hidden-phone">
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
                        <tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
						<?php
						$img_no             = '<i class="icon-cancel"></i>';
						$img_yes            = '<i class="icon-checkmark"></i>';
						$i                  = 0;

						if ($this->pagination->total >= 0)
							:
							foreach ($this->categories as $item) :
								$canEdit = $this->me->isAdmin($item);
								$canCheckin = $this->user->authorise('core.admin', 'com_checkIn') || $item->checked_out == $this->user->id || $item->checked_out == 0;
								$canEditOwn = $canEdit;
								$canChange  = $canEdit && $canCheckin;
								?>
                                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->id; ?>">
                                    <td class="text-center">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                    </td>
									<?php
									$iconClass = '';

									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
                                    elseif (!$saveOrder)
									{
										$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
									}
									?>
                                    <span class="sortable-handler <?php echo $iconClass ?>">
											<span class="icon-ellipsis-v" aria-hidden="true"></span>
										</span>
									<?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" name="order[]" size="5"
                                               value="<?php echo $item->ordering; ?>"
                                               class="width-20 text-area-order hidden">
									<?php endif; ?>
                                    <td class="center hidden-phone">
										<?php echo HTMLHelper::_('grid.id', $i, (int) $item->id); ?>
                                    </td>
                                    <td class="center">
										<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, '', 'cb'); ?>
                                    </td>
                                    <td class="center">
										<?php if (!$this->filter->Item || ($this->filter->Item != $item->id && $item->parentid))
											:
											?>
                                            <button class="btn btn-micro"
                                                    title="Display only this item and its children"
                                                    onclick="jQuery('input[name=catid]').val(<?php echo $item->id ?>);this.form.submit()">
                                                <i class="icon-location"></i>
                                            </button>
										<?php else:
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

										if ($item->checked_out)
										{
											$canCheckin = $item->checked_out == 0 || $item->checked_out == $this->user->id || $this->user->authorise('core.admin', 'com_checkIn');
											$editor     = KunenaFactory::getUser($item->editor)->getName();
											echo HTMLHelper::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'categories.', $canCheckin);
										}
										?>
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $item->id); ?>">
											<?php echo $this->escape($item->name); ?>
                                        </a>
                                        <small>
											<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        </small>
                                    </td>
                                    <td class="center hidden-phone">
                                        <span><?php echo $item->accessname; ?></span>
                                        <small>
											<?php echo Text::sprintf('(Access: %s)', $this->escape($item->accesstype)); ?>
                                        </small>
                                    </td>
                                    <td class="center hidden-phone">
                                        <a class="btn btn-micro <?php echo $item->locked ? 'active' : ''; ?>"
                                           href="javascript: void(0);"
                                           onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->locked ? 'un' : '') . 'lock'; ?>')">
											<?php echo $item->locked == 1 ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
									<?php if ($item->isSection())
										:
										?>
                                        <td class="center hidden-phone" colspan="3">
											<?php echo Text::_('COM_KUNENA_SECTION'); ?>
                                        </td>
									<?php else

										:
										?>
                                        <td class="center hidden-phone">
                                            <a class="btn btn-micro <?php echo $item->review ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->review ? 'un' : '') . 'review'; ?>')">
												<?php echo $item->review == 1 ? $img_yes : $img_no; ?>
                                            </a>
                                        </td>
                                        <td class="center hidden-phone">
                                            <a class="btn btn-micro <?php echo $item->allowPolls ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowPolls ? 'deny' : 'allow') . '_polls'; ?>')">
												<?php echo $item->allowPolls == 1 ? $img_yes : $img_no; ?>
                                            </a>
                                        </td>
                                        <td class="center hidden-phone">
                                            <a class="btn btn-micro <?php echo $item->allowAnonymous ? 'active' : ''; ?>"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowAnonymous ? 'deny' : 'allow') . '_anonymous'; ?>')">
												<?php echo $item->allowAnonymous == 1 ? $img_yes : $img_no; ?>
                                            </a>
                                        </td>
									<?php endif; ?>

                                    <td class="center hidden-phone">
										<?php echo (int) $item->id; ?>
                                    </td>
                                </tr>
								<?php
								$i++;
							endforeach;
						else                    :
							?>
                            <tr>
                                <td colspan="10">
                                    <div class="card card-block bg-faded p-2 center filter-state">
											<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
												<?php
												if ($this->filter->Active)
													:
													?>
                                                    <button class="btn btn-outline-primary" type="button"
                                                            onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
												<?php else
													:
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
