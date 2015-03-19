<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Lib
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

defined('_JEXEC') or die();

ob_start();
if (!empty($this->poll)) :
	$this->addScript('js/poll.js');
	?>
	var KUNENA_POLL_CATS_NOT_ALLOWED = "<?php echo JText::_('COM_KUNENA_POLL_CATS_NOT_ALLOWED', true) ?>";
	var KUNENA_EDITOR_HELPLINE_OPTION = "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_OPTION', true) ?>";
	var KUNENA_POLL_OPTION_NAME = "<?php echo JText::_('COM_KUNENA_POLL_OPTION_NAME', true) ?>";
	var KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW = "<?php echo JText::_('COM_KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW', true) ?>";
	var KUNENA_ICON_ERROR = <?php echo json_encode(KunenaFactory::getTemplate()->getImagePath('publish_x.png')) ?>;
<?php endif ?>
<?php if ($this->me->userid) : ?>
	var kunena_anonymous_name = "<?php echo JText::_('COM_KUNENA_USERNAME_ANONYMOUS', true) ?>";
<?php endif ?>

	window.addEvent('domready', function(){

	function kunenaSelectUsername(obj, kuser) {
	if (obj.get('checked')) {
	document.id('kauthorname').set('value',kunena_anonymous_name).removeProperty('disabled');
	document.id('kanynomous-check-name').setStyle('display');
	} else {
	document.id('kanynomous-check-name').setStyle('display','none');
	document.id('kauthorname').set('value',kuser).set('disabled', 'disabled');
	}
	}

	function kunenaCheckPollallowed(catid) {
	if ( pollcategoriesid[catid] !== undefined ) {
	document.id('kbbcode-poll-button').setStyle('display');
	} else {
	document.id('kbbcode-poll-button').setStyle('display','none');
	}
	}

	function kunenaCheckAnonymousAllowed(catid) {
	if(document.id('kanynomous-check') !== null && document.id('kanonymous') !== null) {
	if ( arrayanynomousbox[catid] !== undefined ) {
	document.id('kanynomous-check').setStyle('display');
	document.id('kanonymous').set('checked','checked');
	} else {
	document.id('kanynomous-check').setStyle('display','none');
	kbutton.removeProperty('checked');
	}
	}
<?php if ($this->me->userid != 0) : ?>
	kunenaSelectUsername(kbutton,kuser);
<?php endif ?>
	}
	//    for hide or show polls if category is allowed
	if(document.id('postcatid') !== null) {
	document.id('postcatid').addEvent('change', function(e) {
	kunenaCheckPollallowed(this.value);
	});
	}

	if(document.id('kauthorname') !== undefined) {
	var kuser = document.id('kauthorname').get('value');
	var kbutton = document.id('kanonymous');
<?php if ($this->me->userid != 0) : ?>
	kunenaSelectUsername(kbutton, kuser);
	kbutton.addEvent('click', function(e) {
	kunenaSelectUsername(this, kuser);
	});
<?php endif ?>
	}
	//    to select if anynomous option is allowed on new topic tab
	if(document.id('postcatid') !== null) {
	document.id('postcatid').addEvent('change', function(e) {
	var postcatid = document.id('postcatid').value;
	kunenaCheckAnonymousAllowed(postcatid);
	});
	}

	if(document.id('postcatid') !== null) {
	kunenaCheckPollallowed(document.id('postcatid').getSelected().get("value"));
	kunenaCheckAnonymousAllowed(document.id('postcatid').getSelected().get("value"));
	}
	});

<?php
$script = ob_get_contents();
ob_end_clean();

$document = JFactory::getDocument();
$document->addScriptDeclaration("// <![CDATA[
{$script}
// ]]>");
