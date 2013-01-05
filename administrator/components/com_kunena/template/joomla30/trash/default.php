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
JHtml::_('formbehavior.chosen', 'select');

$changeOrder 	= ($this->state->get('list.ordering') == 'ordering' && $this->state->get('list.direction') == 'asc');

?>
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">

             <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php echo $this->state->get( 'list.view_selected') ? JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_TOPICS' ) : JText::_('COM_KUNENA_TRASH_VIEW').' '.JText::_( 'COM_KUNENA_TRASH_MESSAGES') ?></div>
                         <div class="row-striped">
                         <br />
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" method="post" id="adminForm" name="adminForm">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search message'; ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo 'Search message'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn" rel="tooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo  $this->navigation->getLimitBox (); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo  $this->navigation->getLimitBox (); ?>
				</select>
			</div>
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->trash_items ); ?>);" /></th>
					<th width="5" align="left"><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'tt.id', $this->state->get('list.direction'), $this->state->get('list.ordering')) :  JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_ID', 'm.id', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'tt.subject', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_TITLE', 'm.subject', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo JText::_('COM_KUNENA_TRASH_CATEGORY');
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JText::_('COM_KUNENA_TRASH_IP') : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_IP', 'm.ip', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'tt.first_post_userid', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR_USERID', 'm.userid', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'tt.first_post_guest_name', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_AUTHOR', 'm.name', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
					<th align="left" ><?php
					echo $this->state->get( 'list.view_selected') == 'topics' ? JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'tt.first_post_time', $this->state->get('list.direction'), $this->state->get('list.ordering')) : JHtml::_( 'grid.sort', 'COM_KUNENA_TRASH_DATE', 'm.time', $this->state->get('list.direction'), $this->state->get('list.ordering'));
					?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<div class="pagination">
							<?php echo $this->navigation->getPagesLinks (); ?>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 0;
					$i = 0;
					foreach ( $this->trash_items as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php echo JHtml::_('grid.id', $i++, intval($row->id)) ?></td>
					<td >
						<?php
						echo intval($row->id);
						?>
						</td>
					<td ><?php
						echo isset($row->subject) ? $this->escape($row->subject) : $this->escape($row->title);
						?></td>
					<td ><?php if ($this->state->get( 'list.view_selected')) {
						$cat = KunenaForumCategoryHelper::get($row->category_id);
						echo $this->escape($cat->name);
					} else {
						 $cat = KunenaForumCategoryHelper::get($row->catid);
						echo $this->escape($cat->name);
					}
						?></td>
					<td ><?php
						if ($this->state->get( 'list.view_selected')) {
							$message = KunenaForumMessageHelper::get($row->id);
							echo $message->ip;
						} else {
							echo $this->escape($row->ip);
						}
						?></td>
					<td ><?php
					if ($this->state->get( 'list.view_selected')) {
						echo intval($row->first_post_userid);
					} else {
						echo intval($row->userid);
					}

						?></td>
					<td ><?php
						echo $this->escape($row->getAuthor()->getName());
						?></td>
					<td ><?php
					if ($this->state->get( 'list.view_selected') ) {
						echo strftime('%Y-%m-%d %H:%M:%S',$row->last_post_time);
					} else {
						echo strftime('%Y-%m-%d %H:%M:%S',$row->time);
					}
						?></td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="type" value="<?php echo $this->escape ($this->state->get('list.view_selected')) ?>" />
			<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</form>
	</div>
    </div>

</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
