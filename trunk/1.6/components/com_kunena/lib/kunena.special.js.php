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

ob_start();
?>

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
			document.id('kpoll-hide-not-allowed').setStyle('display');
			document.id('kbbcode-separator5').setStyle('display');
			document.id('kbbcode-poll-button').setStyle('display');
			document.id('kpoll-not-allowed').set('text', ' ');
		} else {
			document.id('kbbcode-separator5').setStyle('display','none');
			document.id('kbbcode-poll-button').setStyle('display','none');
			document.id('kpoll-hide-not-allowed').setStyle('display','none');
		}
	}

	function kunenaCheckAnonymousAllowed(catid) {
		if ( arrayanynomousbox[catid] !== undefined ) {
			document.id('kanynomous-check').setStyle('display');
		} else {
			document.id('kanynomous-check').setStyle('display','none');
			kbutton.removeProperty('checked');
		}

		if ( arrayanynomousbox[catid] ) {
			document.id('kanonymous').set('checked','checked');
		}
		<?php if ($this->my->id != 0) { ?>
		kunenaSelectUsername(kbutton,kuser);
		<?php } ?>
	}
	//	for hide or show polls if category is allowed
	if(document.id('postcatid') !== null) {
		document.id('postcatid').addEvent('change', function(e) {
			kunenaCheckPollallowed(this.value);
		});
	}

	if(document.id('kauthorname') !== undefined) {
		var kuser = document.id('kauthorname').get('value');
		var kbutton = document.id('kanonymous');
		<?php if ($this->my->id != 0) { ?>
		kunenaSelectUsername(kbutton, kuser);
		kbutton.addEvent('click', function(e) {
			kunenaSelectUsername(this, kuser);
		});
		<?php } ?>
	}
	//	to select if anynomous option is allowed on new topic tab
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
$document->addScriptDeclaration( "// <![CDATA[
{$script}
// ]]>");
