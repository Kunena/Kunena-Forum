<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();

ob_start();
?>

window.addEvent('domready', function(){

	function kunenaSelectUsername(obj, kuser) {
		if (obj.get('checked')) {
			$('kauthorname').set('value',kunena_anonymous_name).removeProperty('disabled');
			$('kanynomous-check-name').setStyle('display');
		} else {
			$('kanynomous-check-name').setStyle('display','none');
			$('kauthorname').set('value',kuser).set('disabled', 'disabled');
		}
	}

	function kunenaCheckPollallowed(catid) {
		if ( pollcategoriesid[catid] != undefined ) {
			$('kpoll-hide-not-allowed').setStyle('display');
			$('kbbcode-separator5').setStyle('display');
			$('kbbcode-poll-button').setStyle('display');
			$('kpoll-not-allowed').set('text', ' ');
		} else {
			$('kbbcode-separator5').setStyle('display','none');
			$('kbbcode-poll-button').setStyle('display','none');
			$('kpoll-hide-not-allowed').setStyle('display','none');
		}
	}

	function kunenaCheckAnonymousAllowed(catid) {
		if ( arrayanynomousbox[catid] != undefined ) {
			$('kanynomous-check').setStyle('display');
		} else {
			$('kanynomous-check').setStyle('display','none');
			kbutton.removeProperty('checked');
		}

		if ( arrayanynomousbox[catid] ) {
			$('kanonymous').set('checked','checked');
		}
		<?php if ($this->my->id != 0) { ?>
		kunenaSelectUsername(kbutton,kuser);
		<?php } ?>
	}
	//	for hide or show polls if category is allowed
	if($('postcatid') != undefined) {
		$('postcatid').addEvent('change', function(e) {
			kunenaCheckPollallowed(this.value);
		});
	}

	if($('kauthorname') != undefined) {
		var kuser = $('kauthorname').get('value');
		var kbutton = $('kanonymous');
		<?php if ($this->my->id != 0) { ?>
		kunenaSelectUsername(kbutton, kuser);
		kbutton.addEvent('click', function(e) {
			kunenaSelectUsername(this, kuser);
		});
		<?php } ?>
	}
	//	to select if anynomous option is allowed on new topic tab
	if($('postcatid') != undefined) {
		$('postcatid').addEvent('change', function(e) {
			kunenaCheckAnonymousAllowed(catid.value);
		});
	}

	if($('postcatid') != undefined) {
		window.onload=kunenaCheckPollallowed($('postcatid').getSelected().get("value"));
		window.onload=kunenaCheckAnonymousAllowed($('postcatid').getSelected().get("value"));
	}
});

<?php
$script = ob_get_contents();
ob_end_clean();

$document = JFactory::getDocument();
$document->addScriptDeclaration( "// <![CDATA[
{$script}
// ]]>");
