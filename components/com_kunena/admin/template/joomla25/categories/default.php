<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewCategories $this */

$user = JFactory::getUser();
$me = KunenaUserHelper::getMyself();
$userId	= $user->get('id');

$changeOrder = ($this->listOrdering == 'ordering' && $this->listDirection == 'asc');
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		var dirn = '';
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
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>" />
						<input type="hidden" name="boxchecked" value="0" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<div id="filter-bar" class="btn-toolbar">
							<div class="filter-search btn-group pull-left">
								<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
								<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>" />
							</div>
							<div class="btn-group pull-left">
								<button class="btn tip" type="submit" ><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?></button>
								<button class="btn tip" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
							</div>
							<div class="btn-group pull-right hidden-phone">
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
								<label for="sortTable" class="element-invisible"><?php echo JText::_('COM_KUNENA_SORT_TABLE_BY');?></label>
								<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
									<option value=""><?php echo JText::_('COM_KUNENA_SORT_TABLE_BY');?></option>
									<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering);?>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>

						<table class="table table-striped ">
							<thead>
								<tr>
									<th width="5%" class="nowrap">
										<small>
											<?php  if ($changeOrder) echo JHtml::_('grid.order',  $this->categories, 'filesave.png', 'saveorder' ); ?>
											<?php echo JHtml::_('grid.sort', 'COM_KUNENA_REORDER', 'ordering', $this->listDirection, $this->listOrdering ); ?>
										</small>
									</th>
									<th width="1%" class="nowrap">
										<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->categories ); ?>);" />
									</th>
									<th width="5%" class="nowrap">
										<?php echo JHtml::_('grid.sort', 'JSTATUS', 'p.published', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th width="24%" class="nowrap center">
										<?php echo JHTML::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->listDirection, $this->listOrdering); ?>
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
									<th width="1%" class="nowrap center">
										<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->listDirection, $this->listOrdering); ?>
									</th>
								</tr>
								<tr>
									<td>
									</td>
									<td>
									</td>
									<td class="nowrap center">
										<label for="filter_published" class="element-invisible"><?php echo JText::_('All');?></label>
										<select name="filter_published" id="filter_published" class="select-filter filter" onchange="Joomla.orderTable()">
											<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
											<?php echo JHtml::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filterPublished, true); ?>
										</select>
									</td>
									<td class="nowrap">
										<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
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
									<td class="nowrap center">
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
							$k = 0;
							$i = 0;
							$n = count($this->categories);
							$img_yes = '<span class="state publish"><span class="text">Enabled</span></span>';
							$img_no = '<span class="state unpublish"><span class="text">Disabled</span></span>';
							if($this->pagination->total > 0) :
							foreach($this->categories as $category) {
							?>
								<tr <?php echo 'class = "row' . $k . '"';?>>
									<td class="center nowrap">
										<div class="input-prepend input-append order">
											<?php if ($changeOrder) : ?>
												<?php if ($category->down) : ?>
													<a class="jgrid btn" type="button" onclick="return listItemTask('cb<?php echo $i ?>','orderdown')" href="javascript:void(0)">
														<span class="state downarrow">
															<span class="text">Move Down</span>
														</span>
													</a>
												<?php else : ?>
													<span class="btn">
														<span class="state downarrow-disabled">
															<!--<span class="text">Move Down Disabled</span>-->
														</span>
													</span>
												<?php endif ?>
												<input class="input-micro center" type="text" name="order[<?php echo intval($category->id) ?>]" value="<?php echo intval($category->ordering); ?>" />
												<?php if ($category->up) : ?>
													<a class="jgrid btn" type="button" onclick="return listItemTask('cb<?php echo $i ?>','orderup')" href="javascript:void(0)">
														<span class="state uparrow">
															<span class="text">Move Down</span>
														</span>
													</a>
												<?php else : ?>
													<span class="btn">
														<span class="state uparrow-disabled">
															<!--<span class="text">Move Up Disabled</span>-->
														</span>
													</span>
												<?php endif ?>
											<?php else : ?>
											<input class="input-micro  center" disabled="disabled" type="text" name="order[<?php echo intval($category->id) ?>]" value="<?php echo intval($category->ordering); ?>" />
											<?php endif ?>
										</div>
									</td>
									<td><?php echo JHtml::_('grid.id', $i, intval($category->id)) ?></td>
									<td class="center"><?php echo JHtml::_('grid.published', $category, $i) ?></td>
									<td class="left">
										<?php
										echo str_repeat('<span class="gi">&mdash;</span>', $category->level);
										if ($category->checked_out) {
											$canCheckin = $category->checked_out == 0 || $category->checked_out == $user->id || $user->authorise('core.admin', 'com_checkin');
											$editor = KunenaFactory::getUser($category->editor)->getName();
											echo JHtml::_('jgrid.checkedout', $i, $editor, $category->checked_out_time, 'categories.', $canCheckin);
										}
										?>
										<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=categories&layout=edit&catid='.(int) $category->id);?>">
											<?php echo $this->escape($category->name); ?>
										</a>
										<small>
											<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($category->alias));?>
										</small>
									</td>
									<td class="center">
										<span><?php echo $this->escape ( $category->accessname ); ?></span>
										<small>
											<?php echo JText::sprintf('(Access: %s)', $this->escape( $category->accesstype ));?>
										</small>
									</td>
									<td class="center hidden-phone">
										<a class="jgrid"  href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->locked ? 'un':'').'lock'; ?>')">
											<?php echo ($category->locked == 1 ? $img_yes : $img_no); ?>
										</a>
									</td>
									<?php if ($category->isSection()) : ?>
										<td class="center hidden-phone" colspan="3">
											<?php echo JText::_('COM_KUNENA_SECTION'); ?>
										</td>
									<?php else : ?>
										<td class="center hidden-phone">
											<a class="jgrid" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->review ? 'un':'').'review'; ?>')">
												<?php echo ($category->review == 1 ? $img_yes : $img_no); ?>
											</a>
										</td>
										<td class="center">
											<a class="jgrid" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_polls ? 'deny':'allow').'_polls'; ?>')">
												<?php echo ($category->allow_polls == 1 ? $img_yes : $img_no); ?>
											</a>
										</td>
										<td class="center hidden-phone">
											<a class="jgrid" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo ($category->allow_anonymous ? 'deny':'allow').'_anonymous'; ?>')">
												<?php echo ($category->allow_anonymous == 1 ? $img_yes : $img_no); ?>
											</a>
										</td>
									<?php endif; ?>
									<td class="center hidden-phone">
										<?php echo (int) $category->id; ?>
									</td>
								</tr>
							<?php
							$i++;
							$k = 1 - $k;
							}
							else : ?>
								<tr>
									<td colspan="10">
										<div class="well center filter-state">
											<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
											<?php /*<a href="#" onclick="document.getElements('.filter').set('value', '');this.form.submit();return false;"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></a> */?>
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
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
