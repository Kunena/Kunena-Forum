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
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
// FIXME : Deprecated under Joomla! 1.6
jimport('joomla.html.pane');
$myTabs = JPane::getInstance('tabs', array('startOffset'=>0));
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-ranks"><?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?></div>
		<dl class="tabs" id="pane">
		<dt><?php echo JText::_('COM_KUNENA_A_RANKS'); ?></dt>
		<dd>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="ranks" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<table class="adminlist">
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
							<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY')?><?php echo $this->navigation->getLimitBox (); ?></div>
							<?php echo $this->navigation->getPagesLinks (); ?>
							<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
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
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?>"><?php echo JText::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></dt>
		<dd>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="view" value="ranks" />
		<input type="hidden" name="task" value="rankupload" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>

		<div style="padding:10px;">
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			<span id="upload-clear"></span>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
		</form>
		</dd>
		</dl>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
