/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

function showMessage() {
	div = document.getElementById('tow', 'test1', 'k', 'row');
	div.style.display = "block";
}

function hideMessage() {
	div = document.getElementById('tow','test2', 'k', 'row');
	div.style.display = "none";
}

jQuery(document).ready(function() {
	/* To hide or open spoiler on click */
	jQuery('.kspoiler').each(function( ) {	
		jQuery('.kspoiler-header').click(function() {
			if ( jQuery('.kspoiler-content').attr('style')=='display:none' ) {
				jQuery('.kspoiler-content').removeAttr('style');
				jQuery('.kspoiler-expand').attr('style','display:none;');
				jQuery('.kspoiler-hide').removeAttr('style');
			} else {
				jQuery('.kspoiler-content').attr('style','display:none;');
				jQuery('.kspoiler-expand').removeAttr('style');
				jQuery('.kspoiler-hide').attr('style','display:none;');
			}
		});
	});
	
	/* To check or uncheck boxes to select items */
	jQuery('input.kcheckall').click(function() {
		jQuery( '.kcheck' ).each(function( ) {			
			jQuery(this).prop('checked',!jQuery(this).prop('checked'));
		});
	});
		
	/* To close quick-reply form on hit on cancel button */
	jQuery('.kreply-cancel').click(function() {
		jQuery('.kreply-form').attr('style','display:none;');
	});
});
