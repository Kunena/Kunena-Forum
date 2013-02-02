<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Ranks
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
$sortFields[] = JHtml::_('select.option', 'id', JText::_('ID'));
$sortFields[] = JHtml::_('select.option', 'title', JText::_('JGLOBAL_TITLE'));
$sortFields[] = JHtml::_('select.option', 'special', JText::_('Special'));
$sortFields[] = JHtml::_('select.option', 'min', JText::_('Min Post Count'));

$sortDirection = array();
$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

$filterTitle = $this->escape($this->state->get('filter.title'));
$filterSpecial = $this->escape($this->state->get('filter.special'));
$filterMinPostCount = $this->escape($this->state->get('filter.min'));
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
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS'); ?></a></li>
		<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="tab1">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=ranks') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrdering; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
				<?php echo JHtml::_( 'form.token' ); ?>

				<div id="filter-bar" class="btn-toolbar">
					<div class="btn-group pull-left">
						<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i> Filter</button>
						<button class="btn tip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> Clear</button>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<?php echo $this->pagination->getLimitBox (); ?>
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
				</div>

				<table class="table table-striped adminlist" id="rankList">
					<thead>
						<tr>
							<th width="1%" align="center">#</th>
							<th width="1%"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
							<th width="10%"><?php echo JText::_('COM_KUNENA_RANKSIMAGE'); ?></th>
							<th width="58%"><?php echo JHtml::_('grid.sort', 'Title', 'title', $listDirection, $listOrdering ); ?></th>
							<th width="10%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_RANKS_SPECIAL', 'special', $listDirection, $listOrdering ); ?></th>
							<th width="10%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_RANKSMIN', 'min', $listDirection, $listOrdering ); ?></th>
						</tr>
						<tr>
							<td class="hidden-phone">
							</td>
							<td class="hidden-phone">
							</td>
							<td class="hidden-phone">
							</td>
							<td class="nowrap">
								<label for="filter_title" class="element-invisible"><?php echo 'Search in';?></label>
								<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterTitle; ?>" title="<?php echo 'Filter'; ?>" />
							</td>
							<td class="nowrap center">
								<label for="filter_special" class="element-invisible"><?php echo JText::_('All');?></label>
								<select name="filter_special" id="filter_special" class="select-filter filter" onchange="Joomla.orderTable()">
									<option value=""><?php echo JText::_('All');?></option>
									<?php echo JHtml::_('select.options', $this->specialOptions(), 'value', 'text', $filterSpecial); ?>
								</select>
							</td>
							<td class="nowrap center">
								<label for="filter_min" class="element-invisible"><?php echo 'Search in';?></label>
								<input class="input-block-level input-filter filter" type="text" name="filter_min" id="filter_min" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterMinPostCount; ?>" title="<?php echo 'Filter'; ?>" />
							</td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="6">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php $i = 0; foreach ( $this->items as $id => $row ) : ?>
					<tr>
						<td>
							<?php echo ($id + $this->pagination->limitstart + 1); ?>
						</td>
						<td>
							<input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]" value="<?php echo $this->escape($row->rank_id); ?>" onclick="Joomla.isChecked(this.checked);" />
						</td>
						<td>
							<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
								<img src="<?php echo $this->escape($this->ktemplate->getRankPath($row->rank_image, true)) ?>" alt="<?php echo $this->escape($row->rank_image); ?>" />
							</a>
						</td>
						<td class="nowrap">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
								<?php echo $this->escape($row->rank_title); ?>
							</a>
						</td>
						<td class="nowrap center">
							<?php echo $row->rank_special == 1 ? JText::_('COM_KUNENA_YES') : JText::_('COM_KUNENA_NO'); ?>
						</td>
						<td class="nowrap center">
							<?php echo $this->escape($row->rank_min); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
			</form>
		</div>

		<div class="tab-pane" id="tab2">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
				<input type="hidden" name="view" value="ranks" />
				<input type="hidden" name="task" value="rankupload" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_( 'form.token' ); ?>

				<input type="file" id="file-upload" class="btn" name="Filedata" />
				<input type="submit" id="file-upload-submit" class="btn btn-primary" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			</form>
		</div>
	</div>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
