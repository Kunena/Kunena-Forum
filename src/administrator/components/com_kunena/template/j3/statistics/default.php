<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator.Template
 * @subpackage    Logs
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var KunenaAdminViewLogs $this */

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');
HTMLHelper::_('bootstrap.popover');

$filterItem = $this->escape($this->state->get('item.id'));
?>

<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering; ?>') {
			dirn = 'asc';
		} else {
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
				<i class="icon-chart"></i>
				<?php echo Text::_('COM_KUNENA_MENU_STATISTICS') ?>
			</div>
			<hr class="hr-condensed">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=statistics'); ?>"
			      method="post" name="adminForm"
			      id="adminForm">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<div id="filter-bar" class="btn-toolbar">
					<div class="btn-group pull-left">
						<?php echo HTMLHelper::calendar($this->filterTimeStart, 'filter_time_start', 'filter_time_start', '%Y-%m-%d', array('class' => 'filter btn-wrapper', 'placeholder' => Text::_('COM_KUNENA_STATISTICS_START_DATE'))); ?>
						<?php echo HTMLHelper::calendar($this->filterTimeStop, 'filter_time_stop', 'filter_time_stop', '%Y-%m-%d', array('class' => 'filter btn-wrapper', 'placeholder' => Text::_('COM_KUNENA_STATISTICS_END_DATE'))); ?>
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

				<table class="table table-striped" id="logList">
					<thead>
					<tr>
						<th class="">
							<?php echo Text::_('COM_KUNENA_STATISTICS_NAME') ?>
						</th>
						<th class="">
							<?php echo Text::_('COM_KUNENA_STATISTICS_USERNAME') ?>
							<small>(id)</small>
						</th>
						<th class="center">
							<?php echo Text::_('COM_KUNENA_STATISTICS_POSTS') ?>
						</th>
						<th class="center">
							<?php echo Text::_('COM_KUNENA_STATISTICS_MOVES') ?>
						</th>
						<th class="center">
							<?php echo Text::_('COM_KUNENA_STATISTICS_EDITS') ?>
						</th>
						<th class="center">
							<?php echo Text::_('COM_KUNENA_STATISTICS_DELETES') ?>
						</th>
						<th class="center">
							<?php echo Text::_('COM_KUNENA_STATISTICS_THANK_YOU') ?>
						</th>
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
					$i = 0;

					if ($this->pagination->total > 0)
						:
						foreach ($this->items as $item)
							:
							$user = KunenaUserHelper::get($item->user_id);
							?>
							<tr>
								<td>
									<?php echo $this->escape($user->name); ?>
								</td>
								<td>
									<?php echo $this->escape($user->username) . ' <small>(' . $this->escape($item->user_id) . ')</small>'; ?>
								</td>
								<td class="center">
									<?php echo (int) $item->posts; ?>
								</td>
								<td class="center">
									<?php echo (int) $item->moves; ?>
								</td>
								<td class="center">
									<?php echo (int) $item->edits; ?>
								</td>
								<td class="center">
									<?php echo (int) $item->deletes; ?>
								</td>
								<td class="center">
									<?php echo (int) $item->thanks; ?>
								</td>
							</tr>
							<?php
							$i++;
						endforeach;
					else

						:
						?>
						<tr>
							<td colspan="10">
								<div class="well center filter-state">
							<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
								<?php
								if ($this->filterActive)
									:
									?>
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
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
