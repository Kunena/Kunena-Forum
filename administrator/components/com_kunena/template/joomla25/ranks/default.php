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

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );

$paneOptions = array(
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
);
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?></div>
		<?php
			echo JHtml::_('tabs.start', 'pane', $paneOptions);
			echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_A_RANKS'), 'panel_ranks');
		?>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="ranks" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->pagination->limitstart ) ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
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
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY')?><?php echo $this->pagination->getLimitBox (); ?></div>
							<?php echo $this->pagination->getPagesLinks (); ?>
							<div class="limit"><?php echo $this->pagination->getResultsCounter (); ?></div>
						</div>
					</td>
				</tr>
			</tfoot>
				<?php
					$k = 1;
					$i = 0;
					foreach ( $this->items as $id => $row ) {
						$k = 1 - $k;
						?>
				<tr class="row<?php
						echo $k;
						?>">
					<td align="center"><?php
						echo ($id + $this->pagination->limitstart + 1);
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

		<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_A_RANKS_UPLOAD'), 'panel_upload'); ?>

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

		<?php echo JHtml::_('tabs.end'); ?>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
