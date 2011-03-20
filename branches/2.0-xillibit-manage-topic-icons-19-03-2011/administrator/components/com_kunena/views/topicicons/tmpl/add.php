<?php
/**
 * @version $Id: default.php 4416 2011-02-16 08:43:29Z mahagr $
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
$document->addScriptDeclaration('function update_topicicon(newimage)
			{
				document.topicicon_image.src = "'.
				$this->escape(KURL_SITE . $this->template->getPath()).'/images/icons/" + newimage;
			}');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-topicicons"><?php if ( !$this->state->get('item.id') ): ?><?php echo JText::_('COM_KUNENA_A_NEW_TOPICICON'); ?><?php else: ?><?php echo JText::_('COM_KUNENA_A_EDIT_TOPICICON'); ?><?php endif; ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_CODE');
					?></td>
					<td width="200"><input class="post" type="text" name="topiciconname"
						value="<?php echo isset($this->topicicon->name) ? $this->topicicon->name : '' ?>" /></td>
					<td rowspan="3" width="50"><img name="topicicon_image"
						src="<?php echo isset($this->topicicon) ? $this->escape(KURL_SITE.$this->template->getTopicsIconPath($this->topicicon->filename)) : '' ?>" border="0" alt="" /> &nbsp;</td>
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
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="topicicons" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="topiciconid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>