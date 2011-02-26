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
$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
$document->addScriptDeclaration('function update_smiley(newimage)
			{
				document.smiley_image.src = "'.
				$this->escape(KURL_SITE . $this->smileypath).'" + newimage;
			}');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-ranks"><?php if ( !$this->state->get('item.id') ): ?><?php echo JText::_('COM_KUNENA_EMOTICONS_NEW_SMILEY'); ?><?php else: ?><?php echo JText::_('COM_KUNENA_EMOTICONS_EDIT_SMILEY'); ?><?php endif; ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_CODE');
					?></td>
					<td width="200"><input class="post" type="text" name="smiley_code"
						value="<?php echo isset($this->smiley_selected) ? $this->smiley_selected->code : '' ?>" /></td>
					<td rowspan="3" width="50"><img name="smiley_image"
						src="<?php echo isset($this->smiley_selected) ? $this->escape(KURL_SITE.$this->template->getSmileyPath($this->smiley_selected->location)) : '' ?>" border="0" alt="" /> &nbsp;</td>
					<td rowspan="3">&nbsp;</td>
				</tr>
				<tr align="center">
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_URL');
					?></td>
					<td><?php echo $this->listsmileys?> &nbsp;
					</td>
				</tr>
				<tr>
					<td width="100"><?php
					echo JText::_('COM_KUNENA_EMOTICONS_EMOTICONBAR');
					?></td>
					<td><input type="checkbox" name="smiley_emoticonbar" value="1"
						<?php
					if ($this->state->get('item.id') && $this->smiley_selected->emoticonbar == 1) {
						echo 'checked="checked"';
					}
					?> /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"> </td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="smilies" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="smileyid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>