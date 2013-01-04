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
JHtml::_('formbehavior.chosen', 'select');
$paneOptions = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("tab-pane active").removeClass("tab-pane");
}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("tab-pane").removeClass("tab-pane active");
}',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
	</div>
		<!-- Right side -->
			<div class="span10">
            <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header"><?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?></div>
                         <div class="row-striped">
                         <br />	
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="ranks" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>
            <div class="btn-group pull-right hidden-phone">
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
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS'); ?></a></li>
                <li><a href="#tab2" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></a></li>
              </ul>
              <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
              <div class="tab-pane  active" id="tab1">
              <fieldset>
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->ranks ); ?>);" /></th>
					<th width="20%" align="left"><?php echo JText::_('COM_KUNENA_RANKSIMAGE'); ?></th>
					<th width="50%" align="left" ><?php echo JText::_('COM_KUNENA_RANKS'); ?></th>
					<th width="10%" align="center" ><?php echo JText::_('COM_KUNENA_RANKS_SPECIAL'); ?></th>
					<th width="10%" align="center" class="nowrap" ><?php echo JText::_('COM_KUNENA_RANKSMIN'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<div class="pagination">
								<div class="center"><?php echo $this->navigation->getPagesLinks (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 1;
					$i = 0;
					foreach ( $this->ranks as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php
						echo ($id + $this->navigation->limitstart + 1);
						?></td>
					<td align="center"><input type="checkbox"
						id="cb<?php
						echo $id;
						?>" name="cid[]"
						value="<?php
						echo $this->escape($row->rank_id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','edit')"><img
						src="<?php
						echo $this->escape($this->ktemplate->getRankPath($row->rank_image, true))
						?>"
						alt="<?php
						echo $this->escape($row->rank_image);
						?>" border="0" /></a></td>
					<td class="nowrap"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $id;
						?>','edit')"><?php
						echo $this->escape($row->rank_title);
						?></a></td>
					<td align="center"><?php
						if ($row->rank_special == 1) {
							echo JText::_('COM_KUNENA_YES');
						} else {
							echo JText::_('COM_KUNENA_NO');
						}
						?></td>
					<td align="center"><?php
						echo $this->escape($row->rank_min);
						?></td>
				</tr>
				<?php
					}
					?>
			</table>
		</form>
        </fieldset>

		</div>
<div class="tab-pane" id="tab2">
		<fieldset>

		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="view" value="ranks" />
		<input type="hidden" name="task" value="rankupload" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<div style="padding:10px;">
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			<span id="upload-clear"></span>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
		</form>

</fieldset>
	</div>
    	</div>
        </div>
        </div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
