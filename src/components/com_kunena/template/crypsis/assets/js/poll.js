/**
* Kunena Component
* @package Kunena.Template.Crypsis
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

jQuery(document).ready(function() {
	/**
	 * Get the number of field options inserted in form
	 */
	function getOptionsNumber()
	{
		var myoptions = jQuery('#kbbcode-poll-options').children('div.polloption');

		return myoptions.length;
	}

	/**
	 * This function will insert directly in DOM the new field for poll with attibutes
	 */
	function createNewOptionField() {
		var	options = getOptionsNumber();
		options++;

		var polldiv = jQuery('<div></div>').attr('class','polloption').appendTo('#kbbcode-poll-options');

		var label = jQuery('<label>').text(Joomla.JText._('COM_KUNENA_POLL_OPTION_NAME')+' '+options+' ');
		polldiv.append(label);

		newInput = document.createElement('input');
		newInput.setAttribute('name', 'polloptionsID[new'+options+']');
		newInput.setAttribute('id', 'field_option'+options);
		newInput.setAttribute('class', 'inputbox');
		newInput.setAttribute('maxlength', '100');
		newInput.setAttribute('type', 'text');
		polldiv.append(newInput);
	}

	if( jQuery('#kbutton-poll-add') != undefined ) {
		jQuery('#kbutton-poll-add').click(function() {
			var nboptionsmax = jQuery('#nb_options_allowed').val();
			var koptions = getOptionsNumber();

			if(!nboptionsmax || (koptions < nboptionsmax && koptions >= 2) ){
				createNewOptionField();
			} else if (!nboptionsmax || koptions < 2) {
				createNewOptionField();
				createNewOptionField();
			} else {
				// Set error message with alert bootstrap way
				jQuery('#kpoll-alert-error').show();

				jQuery('#kbutton-poll-add').hide();
			}
		});
	}
	if( jQuery('#kbutton-poll-rem') != undefined ) {
		jQuery('#kbutton-poll-rem').click(function() {
			var koption = jQuery ('div.polloption:last');
			if(koption) {
				var isvisible = jQuery('#kpoll-alert-error').is(":visible");

				if (isvisible){
					jQuery('#kpoll-alert-error').hide();

					jQuery('#kbutton-poll-add').show();
				}
				koption.remove();
			}
		});
	}

	if( jQuery('#postcatid') != undefined ) {
		jQuery('#postcatid').change(function() {
			var catid = jQuery('select#postcatid option').filter(':selected').val();
			if ( pollcategoriesid[catid] !== undefined ) {
				jQuery('.pollbutton').show();
			} else {
				jQuery('.pollbutton').hide();
			}
		});
	}

	if ( typeof pollcategoriesid != 'undefined' && jQuery('#poll_exist_edit').length == 0 ) {
		var catid = jQuery('#kcategory_poll').val();

		if ( pollcategoriesid[catid] !== undefined ) {
			jQuery('.pollbutton').show();
		} else {
			jQuery('.pollbutton').hide();
		}
	} else if ( jQuery('#poll_exist_edit').length > 0 ) {
		jQuery('.pollbutton').show();
	} else {
		jQuery('.pollbutton').hide();
	}

	jQuery('#kpoll_go_results').click(function() {
		if(jQuery('#poll-results').is(':visible')==true)
		{
			jQuery('#poll-results').hide();
			jQuery('#kpoll_hide_results').hide();
		}
		else
		{
			jQuery('#poll-results').show();
			jQuery('#kpoll_hide_results').show();
			jQuery('#kpoll_go_results').hide();
		}
	});

	jQuery('#kpoll_hide_results').click(function() {
		if(jQuery('#poll-results').is(':visible')==true)
		{
			jQuery('#poll-results').hide();
      jQuery('#kpoll_go_results').show();
			jQuery('#kpoll_hide_results').hide();
		}
		else
		{
			jQuery('#poll-results').show();
			jQuery('#kpoll_hide_results').show();
			jQuery('#kpoll_go_results').hide();
		}
	});

	jQuery('#kpoll-moreusers').click(function() {
		jQuery('#kpoll-moreusers-div').show();
	});
});
