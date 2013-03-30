<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Trash
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
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
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="type" value="<?php echo $this->escape ($this->state->get('layout')) ?>" />
						<input type="hidden" name="layout" value="<?php echo $this->escape ($this->state->get('layout')) ?>" />
						<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
						<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
						<input type="hidden" name="view" value="trash" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
						<legend><?php echo JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_('COM_KUNENA_TRASH_TOPICS') ?> <span class="pull-right"><?php echo $this->view_options_list;?></span></legend>

						<div id="filter-bar" class="btn-toolbar">
							<div class="filter-search btn-group pull-left">
								<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
								<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_ATTACHMENTS_FIELD_INPUT_SEARCHFILE'); ?>" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
							</div>
							<div class="btn-group pull-left">
								<button class="btn tip" type="submit" ><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?></button>
								<button class="btn tip" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
							</div>
							<div class="btn-group pull-right hidden-phone">
								<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
								<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
									<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
									<?php echo JHtml::_('select.options', $this->sortDirectionOrdering, 'value', 'text', $this->escape($this->state->get('list.direction')));?>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>

						<table class="table table-striped">
							<thead>
								<tr>
									<th width="1%" class="nowrap">
										<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->trash_items ); ?>);" />
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_CATEGORY', 'category', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'author', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'time', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
									</th>
									<th class="nowrap" width="1%">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
									</th>
								</tr>
								<tr>
									<td>
									</td>
									<td>
										<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td>
										<label for="filter_category" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter" type="text" name="filter_category" id="filter_category" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterCategory; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
										<label for="filter_author" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter" type="text" name="filter_author" id="filter_author" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterAuthor; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
										<label for="filter_time" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter" type="text" name="filter_time" id="filter_time" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterDate; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
									</td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="9">
										<div class="pagination">
											<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'). $this->navigation->getLimitBox (); ?></div>
											<?php echo $this->navigation->getPagesLinks (); ?>
											<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
										</div>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
									$k = 0;
									$i = 0;
									foreach ( $this->trash_items as $id => $row ) :
										$k = 1 - $k;
										?>
								<tr class="row<?php echo $k; ?>">
									<td align="center"><?php echo JHtml::_('grid.id', $i++, intval($row->id)) ?></td>
									<td>
										<?php echo $this->escape($row->subject); ?>
									</td>
									<td>
										<?php echo $this->escape($row->getCategory()->name); ?>
									</td>
									<td>
										<?php echo $this->escape($row->getAuthor()->getName()); ?>
									</td>
									<td>
										<?php echo strftime('%Y-%m-%d %H:%M:%S',$row->last_post_time); ?>
									</td>
									<td>
										<?php echo intval($row->id) ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						</fieldset>
					</form>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
