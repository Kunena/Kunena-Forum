<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Trash
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTrash $this */
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
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="type" value="<?php echo $this->escape ($this->state->get('layout')) ?>" />
						<input type="hidden" name="layout" value="<?php echo $this->escape ($this->state->get('layout')) ?>" />
						<input type="hidden" name="filter_order" value="<?php echo intval ( $this->listOrdering ) ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->listDirection) ?>" />
						<input type="hidden" name="view" value="trash" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
						<legend><?php echo JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_MESSAGES') ?> <span class="pull-right"><?php echo $this->view_options_list;?></span></legend>

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
								<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
								<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
							</div>
							<div class="btn-group pull-right hidden-phone">
								<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
								<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
									<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
									<?php echo JHtml::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->escape($this->listDirection));?>
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

						<table class="table table-striped">
							<thead>
								<tr>
									<th width="1%" class="nowrap">
										<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'title', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_MENU_TOPIC', 'topic', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_CATEGORY', 'category', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_IP', 'ip', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'author', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'time', $this->listDirection, $this->listOrdering); ?>
									</th>
									<th class="nowrap" width="1%">
										<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'id', $this->listDirection, $this->listOrdering); ?>
									</th>
								</tr>
								<tr>
									<td>
									</td>
									<td>
										<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td>
										<label for="filter_topic" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_topic" id="filter_topic" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $this->filterTopic; ?>" title="<?php echo 'Filter'; ?>" />
									</td>
									<td>
										<label for="filter_category" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_category" id="filter_category" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterCategory; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap">
										<label for="filter_ip" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_ip" id="filter_ip" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterIp; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
										<label for="filter_author" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_author" id="filter_author" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterAuthor; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
										<label for="filter_time" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
										<input class="input-block-level input-filter filter" type="text" name="filter_time" id="filter_time" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterDate; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									</td>
									<td class="nowrap center">
									</td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="9">
										<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<?php
								$k = 0;
								$i = 0;
								if($this->pagination->total > 0) :
								foreach ( $this->trash_items as $id => $row ) :
								?>
									<tr class="row<?php echo $k; ?>">
										<td align="center"><?php echo JHtml::_('grid.id', $i++, intval($row->id)) ?></td>
										<td>
											<?php echo $this->escape($row->subject); ?>
										</td>
										<td>
											<?php echo $this->escape($row->getTopic()->subject); ?>
										</td>
										<td>
											<?php echo $this->escape($row->getCategory()->name); ?>
										</td>
										<td>
											<?php echo $this->escape($row->ip); ?>
										</td>
										<td>
											<?php echo $this->escape($row->getAuthor()->getName()); ?>
										</td>
										<td>
											<?php echo strftime('%Y-%m-%d %H:%M:%S',$row->time); ?>
										</td>
										<td>
											<?php echo intval($row->id) ?>
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
											<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
												<?php /*<a href="#" onclick="document.getElements('.filter').set('value', '');this.form.submit();return false;"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></a> */?>
												<?php if($this->filterActive || $this->pagination->total > 0) : ?>
													<button class="btn" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
												<?php else : ?>
													<?php //Currently no default state, might change later. ?>
												<?php endif; ?>
											</span>
											</div>
										</td>
									</tr>
								<?php endif; ?>
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
