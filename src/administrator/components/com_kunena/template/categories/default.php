<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator.Template
 * @subpackage  Categories
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAdminViewCategories $this

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

if ($this->saveOrder)
{
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', $this->listDirection, $this->saveOrderingUrl, false, true);
}

$filterItem = $this->escape($this->state->get('item.id'));
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
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
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="catid" value="<?php echo $filterItem; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>" />
			<?php echo JHtml::_('form.token'); ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
					<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>" value="<?php echo $this->filterSearch; ?>" title="<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn tip" type="submit" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i class="icon-search"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?></button>
					<button class="btn tip" type="button" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<?php echo JHtml::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection);?>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering);?>
					</select>
				</div>
				<!-- TODO: not implemented
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php //echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="levellimit" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php //echo JText::_('JOPTION_SELECT_MAX_LEVELS');?></option>
						<?php //echo JHtml::_('select.options', $this->levelFields, 'value', 'text', $this->filterLevels);?>
					</select>
				</div>-->
				<div class="clearfix"></div>
			</div>

			<table class="table table-striped" id="categoryList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', 'asc', '', null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'p.published', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JText::_('COM_KUNENA_GO'); ?>
						</th>
						<th width="51%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="20%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_LOCKED', 'p.locked', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_REVIEW', 'p.review', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="5%" class="center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_POLL', 'p.allow_polls', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->listDirection, $this->listOrdering); ?>
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
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filterPublished, true); ?>
							</select>
						</td>
						<td>
						</td>
						<td class="nowrap">
							<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap center hidden-phone">
							<label for="filter_access" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_access" id="filter_access" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->filterAccess); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_locked" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_locked" id="filter_locked" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->lockOptions(), 'value', 'text', $this->filterLocked); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_review" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_review" id="filter_review" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->reviewOptions(), 'value', 'text', $this->filterReview); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_allow_polls" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_allow_polls" id="filter_allow_polls" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->allowpollsOptions(), 'value', 'text', $this->filterAllow_polls); ?>
							</select>
						</td>
						<td class="nowrap center">
							<label for="filter_anonymous" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></label>
							<select name="filter_anonymous" id="filter_anonymous" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->anonymousOptions(), 'value', 'text', $this->filterAnonymous); ?>
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
				$img_no = '<i class="icon-cancel"></i>';
				$img_yes = '<i class="icon-checkmark"></i>';
				$i = 0;

				if ($this->pagination->total > 0) :
				foreach($this->categories as $item) :
				$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
				$canEdit    = $this->me->isAdmin($item);
				$canCheckin = $this->user->authorise('core.admin', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
				$canEditOwn = $canEdit;
				$canChange  = $canEdit && $canCheckin;

					// Get the parents of item for sorting
					if ($item->level > 0)
					{
						$parentsStr = "";
						$_currentParentId = $item->parent_id;
						$parentsStr = " " . $_currentParentId;
						for ($i2 = 0; $i2 < $item->level; $i2++)
						{
							foreach ($this->ordering as $k => $v)
							{
								$v = implode("-", $v);
								$v = "-" . $v . "-";

								if (strpos($v, "-" . $_currentParentId . "-") !== false)
								{
									$parentsStr .= " " . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					} else {
						$parentsStr = "";
					}
				?>
					<tr sortable-group-id="<?php echo $item->parent_id;?>" item-id="<?php echo $item->id?>" parents="<?php echo $parentsStr?>" level="<?php echo $item->level?>">
						<td class="order nowrap center hidden-phone">
							<?php if ($canChange) :
								$disableClassName = '';
								$disabledLabel	  = '';

								if (!$this->saveOrder) :
									$disabledLabel    = JText::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
								<i class="icon-menu"></i>
							</span>
								<input type="text" style="display:none;" name="order[]" size="5" value="<?php echo $orderkey; ?>" />
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
							<?php echo JHtml::_('jgrid.published', $item->published, $i, '', 'cb'); ?>
						</td>
						<td class="center">
							<?php if (!$filterItem || ($filterItem != $item->id && $item->parent_id)) : ?>
								<button class="btn btn-micro" title="Display only this item and its children" onclick="jQuery('input[name=catid]').val(<?php echo $item->id ?>);this.form.submit()">
									<i class="icon-location"></i>
								</button>
							<?php else : ?>
								<button class="btn btn-micro" title="Display only this item and its children" onclick="jQuery('input[name=catid]').val(<?php echo $item->parent_id ?>);this.form.submit()">
									<i class="icon-arrow-up"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="has-context">
							<?php
								echo str_repeat('<span class="gi">&mdash;</span>', $item->level);
								if ($item->checked_out) {
									$canCheckin = $item->checked_out == 0 || $item->checked_out == $this->user->id || $this->user->authorise('core.admin', 'com_checkin');
									$editor = KunenaFactory::getUser($item->editor)->getName();
									echo JHtml::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'categories.', $canCheckin);
								}
							?>
							<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=categories&layout=edit&catid=' . (int) $item->id);?>">
								<?php echo $this->escape($item->name); ?>
							</a>
							<small>
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</small>
						</td>
						<td class="center hidden-phone">
							<span><?php echo $item->accessname; ?></span>
							<small>
								<?php echo JText::sprintf('(Access: %s)', $this->escape($item->accesstype));?>
							</small>
						</td>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo ($item->locked ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->locked ? 'un' : '') . 'lock'; ?>')">
								<?php echo ($item->locked == 1 ? $img_yes : $img_no); ?>
							</a>
						</td>
						<?php if ($item->isSection()) : ?>
						<td class="center hidden-phone" colspan="3">
							<?php echo JText::_('COM_KUNENA_SECTION'); ?>
						</td>
						<?php else : ?>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo ($item->review ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->review ? 'un' : '') . 'review'; ?>')">
								<?php echo ($item->review == 1 ? $img_yes : $img_no); ?>
							</a>
						</td>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo ($item->allow_polls ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->allow_polls ? 'deny' : 'allow') . '_polls'; ?>')">
								<?php echo ($item->allow_polls == 1 ? $img_yes : $img_no); ?>
							</a>
						</td>
						<td class="center hidden-phone">
							<a class ="btn btn-micro <?php echo ($item->allow_anonymous ? 'active' : ''); ?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($item->allow_anonymous ? 'deny' : 'allow') . '_anonymous'; ?>')">
								<?php echo ($item->allow_anonymous == 1 ? $img_yes : $img_no); ?>
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
				else : ?>
					<tr>
						<td colspan="10">
							<div class="well center filter-state">
								<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
									<?php if($this->filterActive) : ?>
										<button class="btn" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
									<?php else : ?>
										<button class="btn btn-success" type="button"  onclick="Joomla.submitbutton('add');"><?php echo JText::_('COM_KUNENA_NEW_CATEGORY'); ?></button>
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
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
