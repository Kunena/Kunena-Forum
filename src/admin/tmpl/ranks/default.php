<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Ranks
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

HTMLHelper::_('bootstrap.framework');

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<script type="text/javascript">
    Joomla.orderTable = function () {
        const table = document.getElementById("sortTable");
        const direction = document.getElementById("directionTable");
        const order = table.options[table.selectedIndex].value;
        if (order !== '<?php echo $this->list->Ordering; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <div class="tabbable-panel">
                    <div class="tabbable-line">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab1" type="button" role="tab"
                                        aria-controls="tab1"
                                        aria-selected="true"><?php echo Text::_('COM_KUNENA_A_RANKS'); ?></button>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab1-tab" data-bs-toggle="tab"
                                        data-bs-target="#tab2" type="button" role="tab"
                                        aria-controls="tab2"
                                        aria-selected="true"><?php echo Text::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel"
                                 aria-labelledby="tab1-tab">
                                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=ranks') ?>"
                                      method="post"
                                      id="adminForm" name="adminForm">
                                    <input type="hidden" name="task" value=""/>
                                    <input type="hidden" name="boxchecked" value="0"/>
                                    <input type="hidden" name="filter_order"
                                           value="<?php echo $this->list->Ordering; ?>"/>
                                    <input type="hidden" name="filter_order_Dir"
                                           value="<?php echo $this->list->Direction; ?>"/>
									<?php echo HTMLHelper::_('form.token'); ?>

                                    <div id="filter-bar" class="btn-toolbar">
                                        <div class="filter-search btn-group pull-left">
                                            <label for="filter_search"
                                                   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
                                            <input type="text" name="filter_search" id="filter_search"
                                                   class="filter form-control"
                                                   placeholder="<?php echo Text::_('COM_KUNENA_ATTACHMENTS_FIELD_INPUT_SEARCHFILE'); ?>"
                                                   value="<?php echo $this->escape($this->state->get('list.search')); ?>"
                                                   title="<?php echo Text::_('COM_KUNENA_RANKS_FIELD_INPUT_SEARCHRANKS'); ?>"/>
                                        </div>
                                        <div class="btn-group pull-left">
                                            <button class="btn btn-outline-primary tip" type="submit"
                                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>">
                                                <i class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
                                            </button>
                                            <button class="btn btn-outline-primary tip" type="button"
                                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
                                                    onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();">
                                                <i class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
                                            </button>
                                        </div>
                                        <div class="btn-group pull-right hidden-phone">
											<?php echo $this->pagination->getLimitBox(); ?>
                                        </div>
                                        <div class="btn-group pull-right hidden-phone">
                                            <label for="directionTable"
                                                   class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
                                            <select name="directionTable" id="directionTable" class="input-medium"
                                                    onchange="Joomla.orderTable()">
                                                <option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
												<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->list->Direction); ?>
                                            </select>
                                        </div>
                                        <div class="btn-group pull-right">
                                            <label for="sortTable"
                                                   class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
                                            <select name="sortTable" id="sortTable" class="input-medium"
                                                    onchange="Joomla.orderTable()">
                                                <option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
												<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->list->Ordering); ?>
                                            </select>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <table class="table table-striped" id="rankList">
                                        <thead>
                                        <tr>
                                            <th width="1%">
                                                <input type="checkbox" name="toggle" value=""
                                                       onclick="Joomla.checkAll(this)"/>
                                            </th>
                                            <th width="10%">
												<?php echo Text::_('COM_KUNENA_RANKSIMAGE'); ?>
                                            </th>
                                            <th width="58%">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKS_LABEL_TITLE', 'title', $this->list->Direction, $this->list->Ordering); ?>
                                            </th>
                                            <th width="10%" class="nowrap center">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKS_SPECIAL', 'special', $this->list->Direction, $this->list->Ordering); ?>
                                            </th>
                                            <th width="10%" class="nowrap center">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKSMIN', 'min', $this->list->Direction, $this->list->Ordering); ?>
                                            </th>
                                            <th width="1%" class="nowrap center hidden-phone">
												<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->list->Direction, $this->list->Ordering); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="hidden-phone">
                                            </td>
                                            <td class="hidden-phone">
                                            </td>
                                            <td class="nowrap">
                                                <label for="filterTitle"
                                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
                                                <input class="input-block-level input-filter filter form-control"
                                                       type="text"
                                                       name="filterTitle" id="filterTitle"
                                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                                       value="<?php echo $this->filter->Title; ?>"
                                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                                            </td>
                                            <td class="nowrap center">
                                                <label for="filter_special"
                                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                                <select name="filter_special" id="filter_special"
                                                        class="select-filter filter form-control"
                                                        onchange="Joomla.orderTable()">
                                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
													<?php echo HTMLHelper::_('select.options', $this->specialOptions(), 'value', 'text', $this->filter->Special); ?>
                                                </select>
                                            </td>
                                            <td class="nowrap center">
                                                <label for="filter_min"
                                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN') ?></label>
                                                <input class="input-block-level input-filter filter form-control"
                                                       type="text"
                                                       name="filter_min" id="filter_min"
                                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                                       value="<?php echo $this->filter->MinPostCount; ?>"
                                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                                            </td>
                                            <td class="hidden-phone">
                                            </td>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <td colspan="6">
												<?php echo $this->pagination->getListFooter(); ?>
                                            </td>
                                        </tr>
                                        </tfoot>
                                        <tbody>
										<?php
										$i = 0;

										if ($this->pagination->total > 0)
											:
											foreach ($this->items as $id => $row)
												:
												?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]"
                                                               value="<?php echo $this->escape($row->rankId); ?>"
                                                               onclick="Joomla.isChecked(this.checked);"/>
                                                    </td>
                                                    <td>
                                                        <a href="#edit"
                                                           onclick="return Joomla.listItemTask('cb<?php echo $id; ?>','rank.edit')">
                                                            <img loading=lazy
                                                                 src="<?php echo $this->escape($this->ktemplate->getRankPath($row->rankImage, true)) ?>"
                                                                 alt="<?php echo $this->escape($row->rankImage); ?>"/>
                                                        </a>
                                                    </td>
                                                    <td class="nowrap">
                                                        <a href="#edit"
                                                           onclick="return Joomla.listItemTask('cb<?php echo $id; ?>','rank.edit')">
															<?php echo Text::_($row->rankTitle); ?>
                                                        </a>
                                                    </td>
                                                    <td class="nowrap center">
														<?php echo $row->rankSpecial == 1 ? Text::_('COM_KUNENA_YES') : Text::_('COM_KUNENA_NO'); ?>
                                                    </td>
                                                    <td class="nowrap center">
														<?php echo $this->escape($row->rankMin); ?>
                                                    </td>
                                                    <td class="nowrap center">
														<?php echo $this->escape($row->rankId); ?>
                                                    </td>
                                                </tr>
											<?php
											endforeach;
										else:
											?>
                                            <tr>
                                                <td colspan="10">
                                                    <div class="card card-block bg-faded p-2 center filter-state">
															<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
																<?php
																if ($this->filter->Active)
																	:
																	?>
                                                                    <button class="btn btn-outline-primary"
                                                                            type="button"
                                                                            onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
																<?php else

																	:
																	?>
                                                                    <button class="btn btn-outline-success"
                                                                            type="button"
                                                                            onclick="Joomla.submitbutton('add');"><?php echo Text::_('COM_KUNENA_NEW_RANK'); ?></button>
																<?php endif; ?>
															</span>
                                                    </div>
                                                </td>
                                            </tr>
										<?php endif; ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                            <div class="tab-pane fade show" id="tab2" role="tabpanel"
                                 aria-labelledby="tab2-tab">
                                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>"
                                      id="uploadForm" method="post"
                                      enctype="multipart/form-data">
                                    <input type="hidden" name="task" value="ranks.rankUpload"/>
                                    <input type="hidden" name="boxchecked" value="0"/>
									<?php echo HTMLHelper::_('form.token'); ?>

                                    <input type="file" id="file-upload" class="btn btn-outline-primary"
                                           name="Filedata"/>
                                    <input type="submit" id="file-upload-submit" class="btn btn-outline-primary"
                                           value="<?php echo Text::_('COM_KUNENA_A_START_UPLOAD'); ?>"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
