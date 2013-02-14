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
//JHtml::_('formbehavior.chosen', '');

$sortFields = array();
$sortFields[] = JHtml::_('select.option', 'p.published', JText::_('JSTATUS'));
$sortFields[] = JHtml::_('select.option', 'p.title', JText::_('JGLOBAL_TITLE'));
$sortFields[] = JHtml::_('select.option', 'p.type', JText::_('Type'));
$sortFields[] = JHtml::_('select.option', 'p.access', JText::_('Access'));
$sortFields[] = JHtml::_('select.option', 'p.id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$user = JFactory::getUser();
$filterSearch = $this->escape($this->state->get('filter.search'));
$filterPublished = $this->escape($this->state->get('filter.published'));
$filterTitle = $this->escape($this->state->get('filter.title'));
$filterType	= $this->escape($this->state->get('filter.type'));
$filterAccess = $this->escape($this->state->get('filter.access'));
$filterLocked = $this->escape($this->state->get('filter.locked'));
$filterReview = $this->escape($this->state->get('filter.review'));
$filterAnonymous = $this->escape($this->state->get('filter.anonymous'));
$listOrdering = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$canChange = $saveOrder = false;

$this->document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/layout.css' );

?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrdering; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrdering; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search categories'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search categories'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i> Filter</button>
				<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> Clear</button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->navigation->getListFooter(); ?>
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
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirection, $listOrdering, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', JText::_('JSTATUS'), 'p.published', $listDirection, $listOrdering); ?>
					</th>
					<th class="nowrap">
						<?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_TITLE'), 'p.title', $listDirection, $listOrdering); ?>
					</th>
					<th width="7%" class="nowrap center hidden-phone">
						<?php /*TODO: STRING Text */ echo JHTML::_('grid.sort', 'Access', 'p.access', $listDirection, $listOrdering); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_LOCKED'), 'p.locked', $listDirection, $listOrdering); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_REVIEW'), 'p.review', $listDirection, $listOrdering); ?>
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'), 'p.anonymous', $listDirection, $listOrdering); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'p.id', $listDirection, $listOrdering); ?>
					</th>
				</tr>
				<tr>
					<td class="hidden-phone">
					</td>
					<td class="hidden-phone">
					</td>
					<td class="nowrap center">
						<label for="filter_published" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_published" id="filter_published" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->publishedOptions(), 'value', 'text', $filterPublished, true); ?>
						</select>
					</td>
					<td class="nowrap">
						<label for="filter_title" class="element-invisible"><?php echo 'Search in';?></label>
						<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterTitle; ?>" title="<?php echo 'Filter'; ?>" />
					</td>
					<td class="nowrap center hidden-phone">
						<label for="filter_access" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_access" id="filter_access" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $filterAccess); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_locked" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_locked" id="filter_locked" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->lockOptions(), 'value', 'text', $filterLocked); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_review" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_review" id="filter_review" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->reviewOptions(), 'value', 'text', $filterReview); ?>
						</select>
					</td>
					<td class="nowrap center">
						<label for="filter_anonymous" class="element-invisible"><?php echo JText::_('All');?></label>
						<select name="filter_anonymous" id="filter_anonymous" class="select-filter filter" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('All');?></option>
							<?php echo JHtml::_('select.options', $this->anonymousOptions(), 'value', 'text', $filterAnonymous); ?>
						</select>
					</td>
					<td class="nowrap center hidden-phone">
					</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->navigation->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$img_no = '<i class="icon-cancel"></i>';
				$img_yes = '<i class="icon-checkmark"></i>';
				$i = 0;
				foreach($this->categories as $item) : ?>
				<tr>
					<td class="order nowrap center hidden-phone">
						<?php if ($canChange) :
							$disableClassName = '';
							$disabledLabel	  = '';

							if (!$saveOrder) :
								$disabledLabel    = JText::_('JORDERINGDISABLED');
								$disableClassName = 'inactive tip-top';
							endif; ?>
							<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
							<i class="icon-menu"></i>
						</span>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
						<?php endif; ?>
					</td>
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
					<td class="center hidden-phone">
						<?php echo $this->escape($item->accessname); ?>
					</td>
					<td class="center hidden-phone">
						<a class ="btn btn-micro <?php echo ($item->locked ? 'active':''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->locked ? 'un':'').'lock'; ?>')">
							<?php echo ($item->locked == 1 ? $img_yes : $img_no); ?>
						</a>
					</td>
					<?php if ($item->isSection()) : ?>
					<td class="center hidden-phone" colspan="2">
						<?php echo JText::_('COM_KUNENA_SECTION'); ?>
					</td>
					<?php else : ?>
					<td class="center hidden-phone">
						<a class ="btn btn-micro <?php echo ($item->review ? 'active':''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->review ? 'un':'').'review'; ?>')">
							<?php echo ($item->review == 1 ? $img_yes : $img_no); ?>
						</a>
					</td>
					<td class="center hidden-phone">
						<a class ="btn btn-micro <?php echo ($item->allow_anonymous ? 'active':''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->allow_anonymous ? 'deny':'allow').'_anonymous'; ?>')">
							<?php echo ($item->allow_anonymous == 1 ? $img_yes : $img_no); ?>
						</a>
					</td>
					<?php endif; ?>
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
