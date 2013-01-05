<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$sortFields = array();
$sortFields[] = JHtml::_('select.option', 'p.published', JText::_('JSTATUS'));
$sortFields[] = JHtml::_('select.option', 'p.title', JText::_('JGLOBAL_TITLE'));
$sortFields[] = JHtml::_('select.option', 'p.type', JText::_('Type'));
$sortFields[] = JHtml::_('select.option', 'p.access', JText::_('Access'));
$sortFields[] = JHtml::_('select.option', 'p.id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$user			= JFactory::getUser();
$filterSearch	= $this->escape($this->state->get('list.search'));
$listOrdering	= $this->escape($this->state->get('list.ordering'));
$listDirection	= $this->escape($this->state->get('list.direction'));

$javascript = <<<END
Joomla.orderTable = function() {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '{$listOrdering}') {
		dirn = 'asc';
	} else {
		dirn = direction.options[direction.selectedIndex].value;
	}
	Joomla.tableOrdering(order, dirn, '');
}
END;
$this->document->addScriptDeclaration($javascript);
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrdering; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
			<div class="clearfix"></div>
			<div><?php echo $this->sidebar; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search categories'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search categories'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->navigation->getLimitBox (); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<?php echo JHtml::_('select.options', $sortDirection, 'value', 'text', $listDirection);?>
					</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrdering);?>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>

		<table class="table table-striped adminlist" id="categoryList">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(<?php echo count($this->categories); ?>);" />
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHTML::_('grid.sort', JText::_('JSTATUS'), 'p.published', $listDirection, $listOrdering); ?>
					</th>
					<th class="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_TITLE'), 'p.title', $listDirection, $listOrdering); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Type', 'p.type', $listDirection, $listOrdering); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Access', 'p.access', $listDirection, $listOrdering); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHTML::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'p.id', $listDirection, $listOrdering); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<div class="pagination"><?php echo $this->navigation->getPagesLinks(); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php $i = 0; foreach($this->categories as $item) : ?>
				<tr>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, (int) $item->id); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', (bool) $item->published, $i, '','cb'); ?>
					</td>
					<td class="has-context">
						<?php
							echo str_repeat('<span class="gi">&mdash;</span>', $item->level);
							if ($item->checked_out) {
								$canCheckin = $item->checked_out == 0 || $item->checked_out == $user->id || $user->authorise('core.admin', 'com_checkin');
								$editor = KunenaFactory::getUser($item->editor)->getName();
								echo JHtml::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'categories.', $canCheckin);
							}
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=categories&layout=edit&catid='.(int) $item->id);?>">
							<?php echo $this->escape($item->name); ?>
						</a>
						<small>
							<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
						</small>
					</td>
					<td class="hidden-phone">
						<?php echo $item->isSection() ? JText::_('COM_KUNENA_SECTION') : JText::_('COM_KUNENA_CATEGORY'); ?>
					</td>
					<td class="hidden-phone">
						<?php echo $this->escape($item->accessname); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php $i++; endforeach; ?>
			</tbody>
		</table>
	</div>
</form>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
