<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Attachments
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

$sortFields = array();
$sortFields[] = JHtml::_('select.option', 'a.filename', JText::_('COM_KUNENA_FILENAME'));
$sortFields[] = JHtml::_('select.option', 'a.filetype', JText::_('COM_KUNENA_ATTACHMENTS_FILETYPE'));
$sortFields[] = JHtml::_('select.option', 'a.size', JText::_('COM_KUNENA_FILESIZE'));
$sortFields[] = JHtml::_('select.option', 'a.id', JText::_('JGRID_HEADING_ID'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$filterSearch	= $this->escape($this->state->get('list.search'));
$filterName	= $this->escape($this->state->get('list.filter_name'));
$filterType	= $this->escape($this->state->get('list.filter_type'));
$filterSize	= $this->escape($this->state->get('list.filter_size'));
$filterDimensions	= $this->escape($this->state->get('list.filter_dimensions'));
$filterUsername = $this->escape($this->state->get('list.filter_username'));
$filterPost	= $this->escape($this->state->get('list.filter_post'));
$listOrdering	= $this->escape($this->state->get('list.ordering'));
$listDirection	= $this->escape($this->state->get('list.direction'));

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
	<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="attachments" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrdering; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
		<input type="hidden" name="limitstart" value="<?php echo intval($this->navigation->limitstart); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search File'; ?>" value="<?php echo $filterSearch; ?>" title="<?php echo 'Search File'; ?>" />
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

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
				<th><?php echo JHtml::_('grid.sort', 'Name', 'a.filename', $listDirection, $listOrdering ); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'Type', 'a.filetype', $listDirection, $listOrdering ); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'Size', 'a.size', $listDirection, $listOrdering ); ?>
				<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_A_IMGB_DIMS', 'a.img_dims', $listDirection, $listOrdering ); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_USERNAME', 'a.username', $listDirection, $listOrdering ); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'Post', 'a.post', $listDirection, $listOrdering ); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirection, $listOrdering ); ?></th>
			</tr>
			<tr>
				<td class="hidden-phone">
				</td>
				<td class="nowrap">
					<label for="filter_name" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_name" id="filter_name" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterName; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap">
					<label for="filter_type" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_type" id="filter_type" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterType; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap">
					<label for="filter_size" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_size" id="filter_size" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterSize; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap">
					<label for="filter_dims" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_dims" id="filter_dims" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterDimensions; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap">
					<label for="filter_username" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_username" id="filter_username" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterUsername; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap">
					<label for="filter_post" class="element-invisible"><?php echo 'Search in';?></label>
					<input class="input-block-level input-filter" type="text" name="filter_post" id="filter_post" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterPost; ?>" title="<?php echo 'Filter'; ?>" />
				</td>
				<td class="nowrap center hidden-phone">
				</td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<div class="pagination">
						<?php echo $this->navigation->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
				$i = 0;
				foreach($this->items as $id=>$attachment) :
					$instance = KunenaForumMessageAttachmentHelper::get($attachment->id);
					$message = $instance->getMessage();
					$path = JPATH_ROOT.'/'.$attachment->folder.'/'.$attachment->filename;
					if ( $instance->isImage($attachment->filetype) && is_file($path)) list($width, $height) = getimagesize( $path );
			?>
			<tr>
				<td><?php echo JHtml::_('grid.id', $i, intval($attachment->id)) ?></td>
				<td><?php echo $instance->getThumbnailLink() . ' ' . KunenaForumMessageAttachmentHelper::shortenFileName($attachment->filename, 10, 15) ?></td>
				<td><?php echo $this->escape($attachment->filetype); ?></td>
				<td><?php echo number_format ( intval ( $attachment->size ) / 1024, 0, '', ',' ) . ' KB'; ?></td>
				<td><?php echo isset($width) && isset($height) ? $width . ' x ' . $height  : '' ?></td>
				<td><?php echo $this->escape($message->name); ?></td>
				<td><?php echo $this->escape($message->subject); ?></td>
				<td><?php echo intval($attachment->id); ?></td>
			</tr>
			<?php $i++; endforeach; ?>
		</tbody>
	</table>

	</form>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
