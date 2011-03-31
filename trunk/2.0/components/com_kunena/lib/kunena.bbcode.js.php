<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();

$kunena_config = KunenaFactory::getConfig ();

ob_start();

//
// function kPreviewHelper (elementId)
//
// Helper function for to perform JSON request for preview
//
?>
function kPreviewHelper()
{
	if (_previewActive == true){
		previewRequest = new Request.JSON({url: "<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=edit&format=raw');?>",
				  							onSuccess: function(response){
			var __message = document.id("kbbcode-preview");
			if (__message) {
				__message.set("html", response.preview);
			}
			}}).post({body: document.id("kbbcode-message").get("value")
		});
	}
}

<?php
// Now we instanciate the class in an object and implement all the buttons and functions.
?>
window.addEvent('domready', function() {

kbbcode.addFunction('#', function() {
}, {'class': 'kbbcode-separator'}
);

<?php if( $this->poll ){ ?>

kbbcode.addFunction('Poll', function() {
	kToggleOrSwap("kbbcode-poll-options");
}, {'id': 'kbbcode-poll-button',
	'class': 'kbbcode-poll-button',
<?php
if (empty($this->category->allow_polls)) {
	echo '\'style\':\'display: none;\',';
} ?>
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_POLL');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL');?>'});

<?php
}
?>

<?php if ($this->my->id != 0) { ?>
kbbcode.addFunction('PreviewBottom', function() {
	kToggleOrSwapPreview("kbbcode-preview-bottom");
}, {'id': 'kbbcode-previewbottom-button',
	'class': 'kbbcode-previewbottom-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWBOTTOM');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWBOTTOM');?>'});

kbbcode.addFunction('PreviewRight', function() {
	kToggleOrSwapPreview("kbbcode-preview-right");
}, {'id': 'kbbcode-previewright-button',
	'class': 'kbbcode-previewright-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWRIGHT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWRIGHT');?>'});

<?php } ?>
kEditorInitialize();
});
<?php
$script = ob_get_contents();
ob_end_clean();

JFactory::getDocument()->addScriptDeclaration( "// <![CDATA[
{$script}
// ]]>");
