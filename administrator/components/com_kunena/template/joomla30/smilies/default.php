<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Smilies
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

$filterCode	= $this->escape($this->state->get('list.filter_code'));
$filterUrl = $this->escape($this->state->get('list.filter_url'));

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
		<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_EMOTICONS'); ?></a></li>
		<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD'); ?></a></li>
	</ul>

	<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
		<div class="tab-pane  active" id="tab1">

			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=smilies') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="limitstart" value="<?php echo intval($this->navigation->limitstart); ?>" />
				<?php echo JHtml::_( 'form.token' ); ?>

				<div id="filter-bar" class="btn-toolbar">
					<div class="btn-group pull-right hidden-phone">
						<?php echo $this->navigation->getLimitBox (); ?>
					</div>
				</div>

				<table class="table table-striped">
					<thead>
						<tr>
							<th width="1%" class="center">#</th>
							<th width="1%" class="center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
							<th width="5%" class="center"><?php echo JText::_('COM_KUNENA_EMOTICON'); ?></th>
							<th width="8%" class="center"><?php echo JHtml::_('grid.sort', 'COM_KUNENA_EMOTICONS_CODE', 'emoticon_code', $listDirection, $listOrdering ); ?></th>
							<th><?php echo JHtml::_('grid.sort', 'COM_KUNENA_EMOTICONS_URL', 'emoticon_url', $listDirection, $listOrdering ); ?></th>
						</tr>
						<tr>
							<td class="hidden-phone center">
							</td>
							<td class="hidden-phone center">
							</td>
							<td class="hidden-phone center">
							</td>
							<td class="nowrap center">
								<label for="filter_code" class="element-invisible"><?php echo 'Search in';?></label>
								<input class="input-block-level input-filter" type="text" name="filter_code" id="filter_code" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterCode; ?>" title="<?php echo 'Filter'; ?>" />
							</td>
							<td class="nowrap center">
								<label for="filter_url" class="element-invisible"><?php echo 'Search in';?></label>
								<input class="input-block-level input-filter" type="text" name="filter_url" id="filter_url" placeholder="<?php echo 'Filter'; ?>" value="<?php echo $filterUrl; ?>" title="<?php echo 'Filter'; ?>" />
							</td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="5">
								<?php echo $this->navigation->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<?php $i = 0; foreach ( $this->smileys as $id => $row ) : ?>
					<tr>
						<td class="hidden-phone center">
							<?php echo ($id + $this->navigation->limitstart + 1); ?>
						</td>
						<td class="hidden-phone center">
							<input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]" value="<?php echo $this->escape($row->id); ?>" onclick="Joomla.isChecked(this.checked);" />
						</td>
						<td class="hidden-phone center">
							<a href="#edit" onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
								<img src="<?php echo $this->escape($this->ktemplate->getSmileyPath($row->location, true)); ?>" alt="<?php echo $this->escape($row->location); ?>" />
							</a>
						</td>
						<td class="hidden-phone center">
							<?php echo $this->escape($row->code); ?>
						</td>
						<td>
							<?php echo $this->escape($row->location); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
		</form>
	</div>

	<div class="tab-pane" id="tab2">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
			<input type="hidden" name="view" value="smilies" />
			<input type="hidden" name="task" value="smileyupload" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
		</form>
	</div>
</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
