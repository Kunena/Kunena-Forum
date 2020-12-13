<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');

$user = Factory::getUser();

?>

<script type="text/javascript">
	Joomla.orderTable = function () {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering; ?>') {
			dirn = 'asc';
		}
		else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header">
				<i class="icon-puzzle"></i>
				<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?>
			</div>
			<hr class="hr-condensed">
			<form action="index.php?option=com_kunena&view=plugins" method="post" name="adminForm" id="adminForm">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<label for="filter_search"
						       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
						<input type="text" name="filter_search" id="filter_search" class="filter"
						       placeholder="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"
						       value="<?php echo $this->filterSearch; ?>"
						       title="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"/>
					</div>
					<div class="btn-group pull-left">
						<button class="btn tip" type="submit"
						        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i
									class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
						</button>
						<button class="btn tip" type="button"
						        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
						        onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i
									class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
						</button>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit"
						       class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
						<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="directionTable"
						       class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
						<select name="directionTable" id="directionTable" class="input-medium"
						        onchange="Joomla.orderTable()">
							<option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
							<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection); ?>
						</select>
					</div>
					<div class="btn-group pull-right">
						<label for="sortTable"
						       class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
						<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
							<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering); ?>
						</select>
					</div>
					<div class="clearfix"></div>
				</div>
				<table class="table table-striped" id="articleList">
					<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value=""
							       title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
							       onclick="Joomla.checkAll(this)"/>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'enabled', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th class="title">
							<?php echo HTMLHelper::_('grid.sort', 'COM_PLUGINS_NAME_HEADING', 'name', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo HTMLHelper::_('grid.sort', 'COM_PLUGINS_ELEMENT_HEADING', 'element', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="10%" class="hidden-phone center">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'extension_id', $this->listDirection, $this->listOrdering); ?>
						</th>
					</tr>
					<tr>
						<td class="hidden-phone">
						</td>
						<td class="nowrap center">
							<label for="filter_enabled" class="element-invisible"><?php echo Text::_('All'); ?></label>
							<select name="filter_enabled" id="filter_enabled" class="select-filter filter"
							        onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filterEnabled, true); ?>
							</select>
						</td>
						<td class="nowrap">
							<label for="filter_name"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_name"
							       id="filter_name"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterName; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td class="nowrap center">
							<label for="filter_element"
							       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_element"
							       id="filter_element"
							       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
							       value="<?php echo $this->filterElement; ?>"
							       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
						</td>
						<td class="nowrap center">
							<label for="filter_access" class="element-invisible"><?php echo Text::_('All'); ?></label>
							<select name="filter_access" id="filter_access" class="select-filter filter"
							        onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
								<?php echo HTMLHelper::_('select.options', HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->filterAccess, true); ?>
							</select>
						</td>
						<td class="nowrap center hidden-phone">
						</td>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="10">
							<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					$i                  = 0;
					$k                  = 0;
					if ($this->pagination->total > 0) :
						foreach ($this->items as $i => $item) :
							$canEdit = $user->authorise('core.edit', 'com_plugins');
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
							$canChange  = $user->authorise('core.edit.state', 'com_plugins') && $canCheckin;
							?>
							<tr>
								<td class="center hidden-phone">
									<?php echo HTMLHelper::_('grid.id', $i, $item->extension_id); ?>
								</td>
								<td class="center">
									<?php echo HTMLHelper::_('jgrid.published', $item->enabled, $i, '', $canChange); ?>
								</td>
								<td>
									<?php if ($item->checked_out) : ?>
										<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, '', $canCheckin); ?>
									<?php endif; ?>
									<?php if ($canEdit) : ?>
										<a href="#plugin<?php echo $item->extension_id; ?>Modal" data-toggle="modal"
										   id="title-><?php echo $item->extension_id; ?>">
											<?php echo $item->name; ?>
										</a>
										<?php echo \Joomla\CMS\HTML\HTMLHelper::_(
											'bootstrap.renderModal',
											'plugin' . $item->extension_id . 'Modal',
											array(
												'url'         => \Joomla\CMS\Router\Route::_('index.php?option=com_plugins&client_id=0&task=plugin.edit&extension_id=' . $item->extension_id . '&tmpl=component&layout=modal'),
												'title'       => $item->name,
												'height'      => '400',
												'width'       => '800px',
												'bodyHeight'  => '70',
												'modalWidth'  => '80',
												'closeButton' => false,
												'backdrop'    => 'static',
												'keyboard'    => false,
												'footer'      => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true"'
													. ' onclick="jQuery(\'#plugin' . $item->extension_id . 'Modal iframe\').contents().find(\'#closeBtn\').click();">'
													. \Joomla\CMS\Language\Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
													. '<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"'
													. ' onclick="jQuery(\'#plugin' . $item->extension_id . 'Modal iframe\').contents().find(\'#saveBtn\').click();">'
													. \Joomla\CMS\Language\Text::_("JSAVE") . '</button>'
													. '<button type="button" class="btn btn-success" aria-hidden="true" onclick="jQuery(\'#plugin' . $item->extension_id
													. 'Modal iframe\').contents().find(\'#applyBtn\').click(); return false;">'
													. \Joomla\CMS\Language\Text::_("JAPPLY") . '</button>',
											)
										); ?>
									<?php else : ?>
										<?php echo $item->name; ?>
									<?php endif; ?>
								</td>
								<td class="nowrap small hidden-phone">
									<?php echo $this->escape($item->element); ?>
								</td>
								<td class="small hidden-phone center">
									<?php echo $this->escape($item->access_level); ?>
								</td>
								<td class="center hidden-phone">
									<?php echo (int) $item->extension_id; ?>
								</td>
							</tr>
							<?php
							$i++;
							$k = 1 - $k;
						endforeach;
					else : ?>
						<tr>
							<td colspan="10">
								<div class="well center filter-state">
									<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
										<?php if ($this->filterActive || $this->pagination->total > 0) : ?>
											<button class="btn" type="button"
											        onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
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
		<div class="pull-right small">
			<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
		</div>
	</div>
