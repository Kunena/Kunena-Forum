<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Smilies
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewSmiley $this */

$iconPath = json_encode(JUri::root(true) . '/');
$this->document->addScriptDeclaration("function update_smiley(newimage) {
	document.smiley_image.src = {$iconPath} + newimage;
}");

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="smilies" />
			<input type="hidden" name="task" value="save" />
			<?php if ( $this->state->get('item.id') ): ?><input type="hidden" name="smileyid" value="<?php echo $this->state->get('item.id') ?>" /><?php endif; ?>
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo !$this->state->get('item.id') ? JText::_('COM_KUNENA_EMOTICONS_NEW_SMILEY'): JText::_('COM_KUNENA_EMOTICONS_EDIT_SMILEY'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td width="20%">
							<?php echo JText::_('COM_KUNENA_EMOTICONS_CODE'); ?>
						</td>
						<td width="80%">
							<input class="span2" type="text" name="smiley_code" value="<?php echo isset($this->smiley_selected) ? $this->smiley_selected->code : '' ?>" />
							<img name="smiley_image" src="<?php echo isset($this->smiley_selected) ? $this->escape($this->ktemplate->getSmileyPath($this->smiley_selected->location, true)) : '' ?>" border="0" alt="" />
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('COM_KUNENA_EMOTICONS_URL'); ?>
						</td>
						<td>
							<?php echo $this->listsmileys; ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICONBAR'); ?>
						</td>
						<td>
							<input type="checkbox" name="smiley_emoticonbar" value="1"
								<?php echo $this->state->get('item.id') && $this->smiley_selected->emoticonbar == 1 ? 'checked="checked"' : ''; ?> />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
