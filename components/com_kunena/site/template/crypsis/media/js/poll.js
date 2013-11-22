/**
* Kunena Component
* @package Kunena.Template.Crypsis
*
* @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

jQuery(document).ready(function() {
	/**
	 * This function will insert directly in DOM the new field for poll with attibutes
	 */
	function create_new_field_now(optionid, options) {
		var polldiv = jQuery('<div></div>').attr('class','polloption').appendTo('#kbbcode-poll-options');
			
		var label = jQuery('<label>').text(Joomla.JText._('COM_KUNENA_POLL_OPTION_NAME')+' '+optionid+' ');
		polldiv.append(label);
			
		newInput = document.createElement('input');
		newInput.setAttribute('name', 'polloptionsID[new'+optionid+']');
		newInput.setAttribute('id', 'field_option'+optionid);
		newInput.setAttribute('class', 'inputbox');
		newInput.setAttribute('maxlength', '25');
		newInput.setAttribute('type', 'text');
		newInput.setAttribute('onmouseover', 'document.id("helpbox").set("value", "'+Joomla.JText._('COM_KUNENA_EDITOR_HELPLINE_OPTION')+'")');
		polldiv.append(newInput);
	}
	
	if( jQuery('#kbutton-poll-add') != undefined ) {
		jQuery('#kbutton-poll-add').click(function() {
			var nboptionsmax = jQuery('#nb_options_allowed').val();
			var koptions = jQuery('#kbbcode-poll-options').children('div.polloption');
				
			if(!nboptionsmax || (koptions.length < nboptionsmax && koptions.length > 1 ) ){
				create_new_field_now(koptions.length+1,nboptionsmax);
			} else if ( !nboptionsmax || koptions.length < 1 ) {
				create_new_field_now(koptions.length+1,nboptionsmax);
				create_new_field_now(koptions.length+2,nboptionsmax);
			} else {
				// Set error message with alert bootstrap way
				jQuery('#kpoll-alert-error').removeAttr('style');
			}
		});
	}
	if( jQuery('#kbutton-poll-rem') != undefined ) {
		jQuery('#kbutton-poll-rem').click(function() {
			var koption = jQuery ('div.polloption:last');
			if(koption) {
				var isvisible = jQuery('#kpoll-alert-error').attr('style');
				if( isvisible == undefined ){
					jQuery('#kpoll-alert-error').attr('style','display:none;');
				}
				koption.remove();
			}
		});
	}
	
	if( jQuery('#postcatid') != undefined ) {
		jQuery('#postcatid').change(function() {
			var catid = jQuery('select#postcatid option').filter(':selected').val();
			if ( pollcategoriesid[catid] !== undefined ) {
				jQuery('#kbbcode-poll-button').removeAttr('style');
			} else {
				jQuery('#kbbcode-poll-button').attr('style','display:none;');
			}
		});
	}
});