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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

$sortFields = array();
if ($this->state->get( 'list.view_selected') == 'topics') {
	$sortFields[] = JHtml::_('select.option', 'tt.subject', JText::_('COM_KUNENA_TRASH_TITLE'));
	$sortFields[] = JHtml::_('select.option', 'm.ip', JText::_('COM_KUNENA_TRASH_IP'));
	$sortFields[] = JHtml::_('select.option', 'tt.first_post_userid', JText::_('COM_KUNENA_TRASH_AUTHOR_USERID'));
	$sortFields[] = JHtml::_('select.option', 'tt.first_post_guest_name', JText::_('COM_KUNENA_TRASH_AUTHOR'));
	$sortFields[] = JHtml::_('select.option', 'tt.first_post_time', JText::_('COM_KUNENA_TRASH_DATE'));
} else {
	$sortFields[] = JHtml::_('select.option', 'm.subject', JText::_('COM_KUNENA_TRASH_TITLE'));
	$sortFields[] = JHtml::_('select.option', 'm.ip', JText::_('COM_KUNENA_TRASH_IP'));
	$sortFields[] = JHtml::_('select.option', 'm.userid', JText::_('COM_KUNENA_TRASH_AUTHOR_USERID'));
	$sortFields[] = JHtml::_('select.option', 'm.name', JText::_('COM_KUNENA_TRASH_AUTHOR'));
	$sortFields[] = JHtml::_('select.option', 'm.time', JText::_('COM_KUNENA_TRASH_DATE'));
}
$sortFields[] = JHtml::_('select.option', 'a.id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$filterSearch = $this->escape($this->state->get('list.search'));
$filterTitle = $this->escape($this->state->get('list.filter_title'));
$filterCategory	= $this->escape($this->state->get('list.filter_category'));
$filterIp = $this->escape($this->state->get('list.filter_ip'));
$filterAuthor = $this->escape($this->state->get('list.filter_author'));
$filterDate	= $this->escape($this->state->get('list.filter_date'));
$listOrdering = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

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

<div id="j-sidebar-container" class="span2">
	<div id="sidebar">
		<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
	</div>
</div>
<div id="j-main-container" class="span10">
	<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="type" value="<?php echo $this->escape ($this->state->get('list.view_selected')) ?>" />
		<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
		<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<fieldset>
			<legend><?php echo $this->state->get( 'list.view_selected') == 'topics' ? JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_TOPICS' ) : JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_MESSAGES') ?> <span class="pull-right"><?php echo $this->view_options_list;?></span></legend>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search'; ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->navigation->getLimitBox(); ?>
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

			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<input type="checkbox"  name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
						</th>
						<th>
							<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'tt.subject', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'm.subject', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th>
							<?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_CATEGORY', 'm.category', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="15%" class="nowrap center">
							<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JText::_('COM_KUNENA_TRASH_IP') : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_IP', 'm.ip', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<!--<th width="5%" class="nowrap center">
							<?php //echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'tt.first_post_userid', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'm.userid', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>-->
						<th width="10%" class="nowrap center">
							<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'tt.first_post_guest_name', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'm.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'tt.first_post_time', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'm.time', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'tt.id', $this->state->get('list.direction'), $this->state->get('list.ordering')) :  JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'm.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
						</th>
					</tr>
					<tr>
						<td class="hidden-phone">
						</td>
						<td class="hidden-phone">
							<label for="filter_title" class="element-invisible"><?php echo 'Search in';?></label>
							<input class="input-block-level input-filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterTitle; ?>" title="<?php echo 'Filter'; ?>" />
						</td>
						<td class="hidden-phone">
							<label for="filter_category" class="element-invisible"><?php echo 'Search in';?></label>
							<input class="input-block-level input-filter" type="text" name="filter_category" id="filter_category" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterCategory; ?>" title="<?php echo 'Filter'; ?>" />
						</td>
						<td class="nowrap">
							<label for="filter_ip" class="element-invisible"><?php echo 'Search in';?></label>
							<input class="input-block-level input-filter" type="text" name="filter_ip" id="filter_ip" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterIp; ?>" title="<?php echo 'Filter'; ?>" />
						</td>
						<td class="nowrap center">
							<label for="filter_author" class="element-invisible"><?php echo 'Search in';?></label>
							<input class="input-block-level input-filter" type="text" name="filter_author" id="filter_author" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterAuthor; ?>" title="<?php echo 'Filter'; ?>" />
						</td>
						<td class="nowrap center">
							<label for="filter_date" class="element-invisible"><?php echo 'Search in';?></label>
							<input class="input-block-level input-filter" type="text" name="filter_date" id="filter_date" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterDate; ?>" title="<?php echo 'Filter'; ?>" />
						</td>
						<td class="nowrap center">
						</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->navigation->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php $i=0; foreach ( $this->trash_items as $id => $row ) : ?>
				<tr>
					<td><?php echo JHtml::_('grid.id', $i++, intval($row->id)) ?></td>
					<td><?php echo isset($row->subject) ? $this->escape($row->subject) : $this->escape($row->title); ?></td>
					<td>
						<?php
						if ($this->state->get('list.view_selected') == 'topics') {
							$cat = KunenaForumCategoryHelper::get($row->category_id);
							echo $this->escape($cat->name);
						} else {
							$cat = KunenaForumCategoryHelper::get($row->catid);
							echo $this->escape($cat->name);
						}
						?>
					</td>
					<td>
					<?php
						if ($this->state->get('list.view_selected') == 'topics') {
							$message = KunenaForumMessageHelper::get($row->id);
							echo $this->escape($message->ip);
						} else {
							echo $this->escape($row->ip);
						}
					?>
					</td>
					<!--<td><?php
						/*if ($this->state->get('list.view_selected') == 'topics') {
							echo intval($row->first_post_userid);
						} else {
							echo intval($row->userid);
						}*/
						?>
					</td>-->
					<td><?php echo $this->escape($row->getAuthor()->getName()); ?>
					</td>
					<td><?php
						if ($this->state->get('list.view_selected') == 'topics') {
							echo strftime('%Y-%m-%d %H:%M:%S',$row->last_post_time);
						} else {
							echo strftime('%Y-%m-%d %H:%M:%S',$row->time);
						}
						?>
					</td>
					<td><?php echo intval($row->id); ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</fieldset>
	</form>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
