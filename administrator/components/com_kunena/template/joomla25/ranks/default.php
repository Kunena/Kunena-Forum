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

//JHtml::addIncludePath(KPATH_ADMIN.'/libraries/html/html');
//JHtml::_('kunenatabs.start');

/*$paneOptions = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
}',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);*/
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering;; ?>') {
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
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS'); ?></a></li>
						<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></a></li>
					</ul>
					<div class="tab-content">
						<div id="tab1" class="tab-pane active">
							<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
								<input type="hidden" name="view" value="ranks" />
								<input type="hidden" name="task" value="" />
								<input type="hidden" name="boxchecked" value="0" />
								<input type="hidden" name="limitstart" value="<?php echo intval ( $this->pagination->limitstart ) ?>" />
								<?php echo JHtml::_( 'form.token' ); ?>

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
											<th width="5%" class="nowrap">
												<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" />
											</th>
											<th width="10%" class="nowrap">
												<?php echo JText::_('COM_KUNENA_RANKSIMAGE'); ?>
											</th>
											<th width="58%" class="nowrap">
												<?php echo JHtml::_('grid.sort', 'COM_KUNENA_RANKS_LABEL_TITLE', 'title', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
											</th>
											<th  width="10%" class="center nowrap">
												<?php echo JHtml::_('grid.sort', 'COM_KUNENA_RANKS_SPECIAL', 'special', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
											</th>
											<th width="10%" class="center nowrap">
												<?php echo JHtml::_('grid.sort', 'COM_KUNENA_RANKSMIN', 'min', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
											</th>
											<th width="1%" class="center nowrap">
												<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
											</th>
										</tr>
										<tr>
											<td class="center">
											</td>
											<td class="center">
											</td>
											<td class="nowrap center">
												<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN');?>:</label>
												<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
											</td>
											<td class="nowrap center">
												<select name="filter_special" id="filter_special" class="select-filter filter">
													<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
													<?php echo JHtml::_('select.options', $this->specialOptions(), 'value', 'text', $this->filterSpecial); ?>
												</select>
											</td>
											<td class="nowrap center">
												<label for="filter_min" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCH_IN') ?>:</label>
												<input class="input-block-level input-filter filter" type="text" name="filter_min" id="filter_min" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterMinPostCount; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
											</td>
											<td>
											</td>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<td colspan="6">
												<div class="pagination">
													<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY')?><?php echo $this->pagination->getLimitBox (); ?></div>
													<?php echo $this->pagination->getPagesLinks (); ?>
													<div class="limit"><?php echo $this->pagination->getResultsCounter (); ?></div>
												</div>
											</td>
										</tr>
									</tfoot>
									<tbody>
										<?php
											$k = 1;
											$i = 0;
											foreach ( $this->items as $id => $row ) {
												$k = 1 - $k;
												?>
										<tr class="row<?php echo $k; ?>">
											<td align="center">
												<input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]" value="<?php echo $this->escape($row->rank_id); ?>" onclick="isChecked(this.checked);" />
											</td>
											<td>
												<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
													<img src="<?php echo $this->escape($this->ktemplate->getRankPath($row->rank_image, true)) ?>" alt="<?php echo $this->escape($row->rank_image); ?>" border="0" />
												</a>
											</td>
											<td class="nowrap">
												<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')"><?php echo $this->escape($row->rank_title); ?></a>
											</td>
											<td class="nowrap center"><?php
												if ($row->rank_special == 1) {
													echo JText::_('COM_KUNENA_YES');
												} else {
													echo JText::_('COM_KUNENA_NO');
												} ?>
											</td>
											<td class="nowrap center">
												<?php echo $this->escape($row->rank_min); ?>
											</td>
											<td class="nowrap center">
												<?php echo $this->escape($row->rank_id); ?>
											</td>
										</tr>
										<?php
											}
											?>
									</tbody>
								</table>
							</form>
						</div>
						<div id="tab2" class="tab-pane">
							<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
								<input type="hidden" name="view" value="ranks" />
								<input type="hidden" name="task" value="rankupload" />
								<input type="hidden" name="boxchecked" value="0" />
								<?php echo JHtml::_( 'form.token' ); ?>

								<div style="padding:10px;">
									<input type="file" id="file-upload" name="Filedata" />
									<button type="submit" id="file-upload-submit" class="btn"><?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?></button>
									<span id="upload-clear"></span>
								</div>
								<ul class="upload-queue" id="upload-queue">
									<li style="display: none" />
								</ul>
							</form>
						</div>
					</div>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
