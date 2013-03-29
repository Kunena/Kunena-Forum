<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$script = "function RestoreConfig() { document.getElementById('adminFormModal').submit(); }";
 // Add to document head
JFactory::getDocument()->addScriptDeclaration($script);
?>
<fieldset>
	<legend> <?php echo JText::_('COM_KUNENA_CONFIG_MODAL_TITLE') ?> </legend>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post" name="adminFormModal" id="adminFormModal">
			<a onclick="javascript:RestoreConfig();" href="#">
				<img src="<?php echo Juri::root().'administrator/components/com_kunena/images/button_validate.png' ?>" title="<?php echo JText::_('COM_KUNENA_CONFIG_MODAL_VALIDATE') ?>" alt="<?php echo JText::_('COM_KUNENA_CONFIG_MODAL_VALIDATE') ?>" />
			</a>
			<a onclick="parent.SqueezeBox.close();" href="#">
				<img src="<?php echo Juri::root().'administrator/components/com_kunena/images/button_cancel.png' ?>" title="<?php echo JText::_('COM_KUNENA_CONFIG_MODAL_CANCEL') ?>" alt="<?php echo JText::_('COM_KUNENA_CONFIG_MODAL_CANCEL') ?>" />
			</a>
			<input type="hidden" name="task" value="setdefault" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ) ?>
		</form>
</fieldset>
<div class="kadmin-footer">
	<?php echo KunenaVersion::getLongVersionHTML () ?>
</div>