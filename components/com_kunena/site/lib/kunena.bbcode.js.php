<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Lib
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

defined('_JEXEC') or die();

$kunena_config = KunenaFactory::getConfig();

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
	previewRequest = new Request.JSON({secure: false, url: <?php echo json_encode(KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=edit&format=raw', false)); ?>,
	onSuccess: function(response){
	var __message = document.id("kbbcode-preview");
	if (__message) {
	__message.set("html", response.preview);
	__message.fireEvent('updated');
	}
	}}).post({body: document.id("kbbcode-message").get("value")
	});
	}
	}

<?php
// Now we instanciate the class in an object and implement all the buttons and functions.
?>
	window.addEvent('domready', function() {

<?php if ($this->poll)
{ ?>

	kbbcode.addFunction('Poll', function() {
	kToggleOrSwap("kbbcode-poll-options");
	}, {'id': 'kbbcode-poll-button',
	'class': 'kbbcode-poll-button',
	<?php
	if (empty($this->category->allow_polls))
	{
		echo '\'style\':\'display: none;\',';
	} ?>
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_POLL', true); ?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL', true); ?>'});

<?php
}
?>

	kEditorInitialize();
	});
<?php
$script = ob_get_contents();
ob_end_clean();

JFactory::getDocument()->addScriptDeclaration("// <![CDATA[
{$script}
// ]]>");
