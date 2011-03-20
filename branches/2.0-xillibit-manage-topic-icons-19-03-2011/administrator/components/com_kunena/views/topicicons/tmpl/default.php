<?php
/**
 * @version $Id: default.php 4416 2011-02-16 08:43:29Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
// FIXME : Deprecated under Joomla! 1.6
jimport('joomla.html.pane');
$myTabs = JPane::getInstance('tabs', array('startOffset'=>0));
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-topicicons"><?php echo JText::_('COM_KUNENA_A_TOPICICONS_MANAGER'); ?></div>
	   <dl class="tabs" id="pane">
		<dt><?php echo JText::_('COM_KUNENA_A_TOPICICONS'); ?></dt>
		<dd>
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->ranks ); ?>);" /></th>
					<th width="20%" align="left"><?php echo JText::_('COM_KUNENA_TOPICICON_NAME'); ?></th>
					<th width="50%" align="left" ><?php echo JText::_('COM_KUNENA_TOPICICON_IMAGE'); ?></th>
					<th width="10%" align="center" ><?php echo JText::_('COM_KUNENA_TOPICICON_PUBLISHED'); ?></th>
					<th width="10%" align="center" class="nowrap" ><?php echo JText::_('COM_KUNENA_TOPICICON_ORDERING'); ?></th>
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
					$n = count($this->topicicons);
					$img_yes = '<img src="images/tick.png" alt="'.JText::_('COM_KUNENA_A_YES').'" />';
					$img_no = '<img src="images/publish_x.png" alt="'.JText::_('COM_KUNENA_A_NO').'" />';
					foreach ( $this->topicicons as $id => $row ) {
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
						echo $row->id;
						?>" name="cid[]"
						value="<?php
						echo $this->escape($row->id);
						?>"
						onclick="isChecked(this.checked);" /></td>
					<td align="center">
					<a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $row->id;
						?>','edit')"><?php
						echo $this->escape($row->name);
						?></a>
					</td>
					<td><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $row->id;
						?>','edit')"><img
						src="<?php
						echo $this->escape(KURL_SITE . $this->template->getTopicsIconPath($row->filename))
						?>"
						alt="<?php
						echo $this->escape($row->name);
						?>" border="0" /></a></td>
					<td class="nowrap"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo  ($row->published ? 'un':'').'publish'; ?>')">
						<?php echo ($row->published == 1 ? $img_yes : $img_no); ?></td>
						</a>
					</td>
					<td align="center">
						<span><?php echo $this->navigation->orderUpIcon ( $i, $row->up, 'orderup', 'Move Up', 1 ); ?></span>
						<span><?php echo $this->navigation->orderDownIcon ( $i, $n, $row->down, 'orderdown', 'Move Down', 1 ); ?></span>
						<input type="text" name="order[<?php echo intval($row->id) ?>]" size="5" value="<?php echo $this->escape ( $row->ordering ); ?>" class="text_area" style="text-align: center" />
					</td>
				</tr>
				<?php
					}
					?>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="topicicons" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_A_TOPICICON_UPLOAD'); ?>"><?php echo JText::_('COM_KUNENA_A_TOPICICON_UPLOAD'); ?></dt>
		<dd>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
		<div style="padding:10px;">
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
			<span id="upload-clear"></span>
		</div>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
		<input type="hidden" name="option" value="com_kunena" />
		<input type="hidden" name="view" value="topicicons" />
		<input type="hidden" name="task" value="topiciconupload" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		</dd>
		</dl>
	</div>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>