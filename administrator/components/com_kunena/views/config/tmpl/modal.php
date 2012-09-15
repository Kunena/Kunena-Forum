<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$script = array();
$script[] = '    function RestoreConfig() {';
$script[] = '        document.getElementById(\'adminFormModal\').submit();';
$script[] = '    }';
 // Add to document head
JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
?>
<fieldset>
	<legend> Confirmation de la restauration par défaut de vos paramétres de configuration </legend>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post" name="adminFormModal" id="adminFormModal">
			<a onclick="javascript:RestoreConfig();" href="#">
				<img src="<?php echo Juri::root().'administrator/components/com_kunena/images/button_validate.png' ?>" title="valider" />
			</a>
			<a onclick="parent.SqueezeBox.close();" href="#">
				<img src="<?php echo Juri::root().'administrator/components/com_kunena/images/button_cancel.png' ?>" title="annuler" />
			</a>
			<input type="hidden" name="task" value="setdefault" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ) ?>
		</form>
</fieldset>
<div class="kadmin-footer">
	<?php echo KunenaVersion::getLongVersionHTML () ?>
</div>