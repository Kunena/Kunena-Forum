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

$iconPath = json_encode(JUri::root().'media/kunena/topic_icons/');
$document->addScriptDeclaration("function update_topicicon(newimage) {
	document.topicicon_image.src = {$iconPath} + newimage;
}");
?>
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">

	<div class="kadmin-functitle icon-topicicons"><?php if ( !$this->state->get('item.id') ): ?><?php echo JText::_('COM_KUNENA_A_NEW_TOPICICON'); ?><?php else: ?><?php echo JText::_('COM_KUNENA_A_EDIT_TOPICICON'); ?><?php endif; ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="topicicons" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="topiciconid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHtml::_( 'form.token' ); ?>

			<table class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_CODE');
					?></td>
					<td width="200"><input class="post" type="text" name="topiciconname"
						value="<?php echo isset($this->topicicon->name) ? $this->topicicon->name : '' ?>" /></td>
					<td rowspan="3" width="50"><img name="topicicon_image"
						src="<?php echo isset($this->topicicon) ? $this->escape(JUri::root(true).'/'.$this->ktemplate->getTopicsIconPath($this->topicicon->filename)) : '' ?>" border="0" alt="" /> &nbsp;</td>
					<td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_TOPICICONS_LIST');
					?></td>
					<td><?php echo $this->listtopicicons?> &nbsp;
					</td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_TOPICICON_PUBLISHED');
					?></td>
					<td><input type="checkbox" name="published" value="1"
						<?php
					if ($this->state->get('item.id') && $this->topicicon->published == 1) {
						echo 'checked="checked"';
					}
					?> /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"> </td>
				</tr>
			</table>
		</form>
	</div>

</div>

<div class="pull-right small">
	<?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
