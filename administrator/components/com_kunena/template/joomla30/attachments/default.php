<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Attachments
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
?>
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
 		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="attachments" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>
<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search File'; ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo 'Search File'; ?>" />
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
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo  $this->navigation->getLimitBox (); ?>
				</select>
			</div>
		<div class="clr">&nbsp;</div>
			<table class="kadmin-sort">
			<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $this->escape ($this->state->get('list.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th align="center" width="5">#</th>
						<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
						<th class="title"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_FILENAME', 'a.filename', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FILETYPE', 'a.filetype', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_FILESIZE', 'a.size', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
						<th class="center"><?php echo JText::_('COM_KUNENA_A_IMGB_DIMS'); ?>	</th>
						<th class="center"><?php echo JText::_('COM_KUNENA_ATTACHMENTS_USERNAME'); ?></th>
						<th class="center"><?php echo JText::_('COM_KUNENA_MESSAGE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="14">
							<div class="pagination">

								<?php echo $this->navigation->getPagesLinks (); ?>
							</div>
						</td>
					</tr>
				</tfoot>
		<?php
			$k = 0;
			$i = 0;
			$n = count($this->items);
			foreach($this->items as $id=>$attachment) {
				$instance = KunenaForumMessageAttachmentHelper::get($attachment->id);
				$message = $instance->getMessage();
				$path = JPATH_ROOT.'/'.$attachment->folder.'/'.$attachment->filename;
				if ( $instance->isImage($attachment->filetype) && is_file($path)) list($width, $height) =	getimagesize( $path );
		?>
			<tr <?php echo 'class = "row' . $k . '"';?>>
				<td class="right"><?php echo $i + $this->navigation->limitstart + 1; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, intval($attachment->id)) ?></td>
				<td class="left" width="70%"><?php echo $instance->getThumbnailLink() . ' ' . KunenaForumMessageAttachmentHelper::shortenFileName($attachment->filename, 10, 15) ?></td>
				<td class="center"><?php echo intval($attachment->id); ?></td>
				<td class="center"><?php echo $attachment->filetype; ?></td>
				<td class="center"><?php echo number_format ( intval ( $attachment->size ) / 1024, 0, '', ',' ) . ' KB'; ?></td>
				<td class="center"><?php echo isset($width) && isset($height) ? $width . ' x ' . $height  : '' ?></td>
				<td class="center"><?php echo $message->name; ?></td>
				<td class="center"><?php echo $message->subject; ?></td>
			</tr>
				<?php
				$i++;
				$k = 1 - $k;
				}
				?>
		</table>

		</form>
	</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
