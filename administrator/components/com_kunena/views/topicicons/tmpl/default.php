<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage TopicIcons
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
	<div class="kadmin-functitle icon-topicicons"><?php echo JText::_('COM_KUNENA_A_TOPICICONS_MANAGER'); ?></div>
		<dl class="tabs" id="pane">
		<dt><?php echo JText::_('COM_KUNENA_A_TOPICICONS'); ?></dt>
		<dd>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="topicicons" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo intval ( $this->state->get('list.ordering') ) ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<?php echo $this->iconsetlist; ?>
			<table class="adminlist">
			<thead>
				<tr>
					<th width="5" align="center">#</th>
					<th width="5" align="left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->topicicons ); ?>);" /></th>
					<th width="20%" align="left"><?php echo JText::_('COM_KUNENA_TOPICICON_NAME'); ?></th>
					<th width="20%" align="left"><?php echo JText::_('COM_KUNENA_TOPICICON_ID'); ?></th>
					<th width="50%" align="left" ><?php echo JText::_('COM_KUNENA_TOPICICON_IMAGE'); ?></th>
					<th width="10%" align="center" ><?php echo JText::_('COM_KUNENA_TOPICICON_PUBLISHED'); ?></th>
					<th width="10%" align="center" class="nowrap" ><?php echo JText::_('COM_KUNENA_TOPICICON_ORDERING'); ?><?php echo JHTML::_('grid.order',$this->topicicons); ?></th>
					<th width="10%" align="center" class="nowrap" ><?php echo JText::_('COM_KUNENA_TOPICICON_DEFAULT'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
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
					$y = 1;
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
					<td><?php echo JHTML::_('grid.id',  $y, $y) ?></td>
					<td align="center">
					<a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $y;
						?>','edit')"><?php
						echo $this->escape($row->name);
						?></a>
					</td>
					<td align="center">
					<a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $y;
						?>','edit')"><?php
						echo intval($row->id);
						?></a>
					</td>
					<td align="center"><a href="#edit"
						onclick="return listItemTask('cb<?php
						echo $y;
						?>','edit')"><img
						src="<?php
						echo $this->escape(JURI::root() . $this->ktemplate->getTopicsIconPath($row->filename, 'default'))
						?>"
						alt="<?php
						echo $this->escape($row->title);
						?>" title="<?php
						echo $this->escape($row->title);
						?>" border="0" /></a></td>
					<td class="nowrap"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $y; ?>','<?php echo  ($row->published ? 'un':'').'publish'; ?>')">
						<?php echo ($row->published == 1 ? $img_yes : $img_no); ?>
						</a>
					</td>
					<td class="right nowrap">
						<span><?php echo $this->navigation->orderUpIcon   ( $i, true,     'orderup',   'Move Up',   $row->ordering ); ?></span>
						<span><?php echo $this->navigation->orderDownIcon ( $i, $n, true, 'orderdown', 'Move Down', $row->ordering ); ?></span>
						<input type="text" name="order[<?php echo intval($row->id) ?>]" size="5" value="<?php echo $this->escape ( $row->ordering ); ?>" class="text_area" style="text-align: center" />
					</td>
					<td class="nowrap"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $y; ?>','<?php echo  ($row->isdefault ? 'not':'').'bydefault'; ?>')">
						<?php echo ($row->isdefault == 1 ? $img_yes : $img_no); ?>
						</a>
					</td>
				</tr>
				<?php
					$i++;
					$y++;
					}
					?>
			</table>
		</form>
		</dd>
		<dt title="<?php echo JText::_('COM_KUNENA_A_TOPICICON_UPLOAD'); ?>"><?php echo JText::_('COM_KUNENA_A_TOPICICON_UPLOAD'); ?></dt>
		<dd>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="view" value="topicicons" />
		<input type="hidden" name="task" value="topiciconupload" />
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
</div>
<div class="kadmin-footer">
	<?php echo KunenaVersion::getLongVersionHTML (); ?>
</div>
