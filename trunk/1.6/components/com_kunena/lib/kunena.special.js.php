<?php
/**
 * @version $Id: kunena.bbcode.js.php 3975 2010-12-12 15:20:17Z xillibit $
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
			$('kanynomous-check-name').removeProperty('style');
		} else {
			$('kanynomous-check-name').setStyle('display','none');
			$('kauthorname').set('value',kuser).set('disabled', 'disabled');
		}
	}

	function kunenaCheckPollallowed(catid) {
		if ( pollcategoriesid[catid] != undefined ) {
			$('kpoll-hide-not-allowed').removeProperty('style');
			$('kbbcode-separator5').removeProperty('style');
			$('kbbcode-poll-button').removeProperty('style');
			$('kpoll-not-allowed').set('text', ' ');
		} else {
			$('kbbcode-separator5').setStyle('display','none');
			$('kbbcode-poll-button').setStyle('display','none');
			$('kpoll-hide-not-allowed').setStyle('display','none');
		}
	}

	function kunenaCheckAnonymousAllowed(catid) {
		if ( arrayanynomousbox[catid] != undefined ) {
			$('kanynomous-check').removeProperty('style');
		} else {
			$('kanynomous-check').setStyle('display','none');
			kbutton.removeProperty('checked');
		}

		if ( arrayanynomousbox[catid] ) {
			$('kanonymous').set('checked','checked');
		}
		kunenaSelectUsername(kbutton,kuser);
	}
	//	for hide or show polls if category is allowed
	if($('postcatid') != undefined) {
		$('postcatid').getElements('option').each( function( catid ) {
			catid.addEvent('click', function(e) {
				kunenaCheckPollallowed(catid.value);
			})
		});
	}

	if($('kauthorname') != undefined) {
		var kuser = $('kauthorname').get('value');
		var kbutton = $('kanonymous');
		kunenaSelectUsername(kbutton, kuser);
		kbutton.addEvent('click', function(e) {
			kunenaSelectUsername(this, kuser);
		});
	}
	//	to select if anynomous option is allowed on new topic tab
	if($('postcatid') != undefined) {
		$('postcatid').getElements('option').each( function( catid ) {
			catid.addEvent('click', function(e) {
				kunenaCheckAnonymousAllowed(catid.value);
			})
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
