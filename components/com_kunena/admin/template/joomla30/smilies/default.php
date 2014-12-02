<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Smilies
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewSmilies $this */

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

if (version_compare(JVERSION, '3.2', '>'))
{
	JHtml::_('behavior.tabstate');
}
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
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
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_EMOTICONS'); ?></a></li>
			<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD'); ?></a></li>
		</ul>
		<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
			<div class="tab-pane  active" id="tab1">

				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=smilies') ?>" method="post" id="adminForm" name="adminForm">
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>" />
					<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>" />
					<?php echo JHtml::_( 'form.token' ); ?>

					<div id="filter-bar" class="btn-toolbar">
						<div class="filter-search btn-group pull-left">
							<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
							<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_ATTACHMENTS_FIELD_INPUT_SEARCHFILE'); ?>" value="<?php echo $this->escape($this->state->get('list.search')); ?>" title="<?php echo JText::_('COM_KUNENA_SMILIES_FIELD_INPUT_SEARCHSMILIES'); ?>" />
						</div>
						<div class="btn-group pull-left">
							<button class="btn tip" type="submit" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i class="icon-search"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?></button>
							<button class="btn tip" type="button" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
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
							<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
							<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
								<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering);?>
							</select>
						</div>
					</div>

					<table class="table table-striped" id="smileyList">
						<thead>
							<tr>
								<th width="1%" class="center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
								<th width="5%" class="center"><?php echo JText::_('COM_KUNENA_EMOTICON'); ?></th>
								<th width="8%"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_EMOTICONS_CODE', 'code', $this->listDirection, $this->listOrdering ); ?></th>
								<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_EMOTICONS_URL', 'location', $this->listDirection, $this->listOrdering ); ?></th>
								<th width="1%" class="nowrap center hidden-phone">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->listDirection, $this->listOrdering); ?>
								</th>
							</tr>
							<tr>
								<td class="hidden-phone center">
								</td>
								<td class="hidden-phone center">
								</td>
								<td class="nowrap center">
									<label for="filter_code" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN') ?></label>
									<input class="input-block-level input-filter filter" type="text" name="filter_code" id="filter_code" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterCode; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
								</td>
								<td class="nowrap center">
									<label for="filter_location" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN') ?></label>
									<input class="input-block-level input-filter filter" type="text" name="filter_location" id="filter_location" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterLocation; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
								</td>
								<td class="hidden-phone center">
								</td>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
									<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
						<?php
						$i = 0;
						if($this->pagination->total > 0) :
						foreach ( $this->items as $id => $row ) :
						?>
							<tr>
								<td class="hidden-phone center">
									<input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]" value="<?php echo $this->escape($row->id); ?>" onclick="Joomla.isChecked(this.checked);" />
								</td>
								<td class="hidden-phone center">
									<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
										<img src="<?php echo $this->escape($this->ktemplate->getSmileyPath($row->location, true)); ?>" alt="<?php echo $this->escape($row->location); ?>" />
									</a>
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($row->code); ?>
								</td>
								<td>
									<?php echo $this->escape($row->location); ?>
								</td>
								<td>
									<?php echo $this->escape($row->id); ?>
								</td>
							</tr>
						<?php
						endforeach;
						else : ?>
							<tr>
								<td colspan="10">
									<div class="well center filter-state">
										<span><?php echo JText::_('COM_KUNENA_FILTERACTIVE'); ?>
										<?php /*<a href="#" onclick="document.getElements('.filter').set('value', '');this.form.submit();return false;"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></a> */?>
										<?php if($this->filterActive) : ?>
											<button class="btn" type="button"  onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
											<?php else : ?>
											<button class="btn btn-success" type="button"  onclick="Joomla.submitbutton('add');"><?php echo JText::_('COM_KUNENA_NEW_SMILIE'); ?></button>
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

			<div class="tab-pane" id="tab2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
					<input type="hidden" name="view" value="smilies" />
					<input type="hidden" name="task" value="smileyupload" />
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
</div>
