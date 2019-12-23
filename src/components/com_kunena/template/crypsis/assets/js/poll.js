/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	/**
	 * Get the number of field options inserted in form
	 */
	function getOptionsNumber() {
		var myoptions = $('#kbbcode-poll-options').children('div.polloption');

		return myoptions.length;
	}

	/**
	 * This function will insert directly in DOM the new field for poll with attibutes
	 */
	function createNewOptionField() {
		var options = getOptionsNumber();
		options++;

		var polldiv = $('<div></div>').attr('class', 'polloption').appendTo('#kbbcode-poll-options');

		var label = $('<label>').text(Joomla.JText._('COM_KUNENA_POLL_OPTION_NAME') + ' ' + options + ' ');
		polldiv.append(label);

		newInput = document.createElement('input');
		newInput.setAttribute('name', 'polloptionsID[new' + options + ']');
		newInput.setAttribute('id', 'field_option' + options);
		newInput.setAttribute('class', 'inputbox inputpollclear');
		newInput.setAttribute('maxlength', '100');
		newInput.setAttribute('type', 'text');
		polldiv.append(newInput);
	}

	var kpollalert = $('#kpoll-alert-error');
	var polladd = $('#kbutton-poll-add');
	if (polladd !== undefined) {
		polladd.click(function () {
			var nboptionsmax = $('#nb_options_allowed').val();
			var koptions = getOptionsNumber();

			if (!nboptionsmax || (koptions < nboptionsmax && koptions >= 2)) {
				createNewOptionField();
			}
			else if (!nboptionsmax || koptions < 2) {
				createNewOptionField();
				createNewOptionField();
			}
			else {
				// Set error message with alert bootstrap way
				kpollalert.show();
				polladd.hide();
			}
		});
	}

	var pollremove = $('#kbutton-poll-rem');
	if (pollremove !== undefined) {
		pollremove.click(function () {
			var koption = $('div.polloption:last');
			if (koption) {
				var isvisible = kpollalert.is(":visible");

				if (isvisible) {
					kpollalert.hide();
					$('#kbutton-poll-add').show();
				}

				koption.remove();
			}
		});
	}

	var postcatid = $('#postcatid');
	if (postcatid !== undefined) {
		postcatid.change(function () {
			var catid = $('select#postcatid option').filter(':selected').val();
			var pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));

			if (pollcategoriesid[catid] !== undefined) {
				$('.pollbutton').show();
			}
			else {
				$('.pollbutton').hide();
			}
		});
	}

	var results = $('#poll-results');
	var hide = $('#kpoll_hide_results');

	$('#kpoll_go_results').click(function () {
		if (results.is(':visible') === true) {
			results.hide();
			hide.hide();
		}
		else {
			results.show();
			hide.show();
			$('#kpoll_go_results').hide();
		}
	});

	hide.click(function () {
		if (results.is(':visible') === true) {
			results.hide();
			hide.show();
			$('#kpoll_hide_results').hide();
		}
		else {
			results.show();
			hide.show();
			$('#kpoll_go_results').hide();
		}
	});

	$('#kpoll-moreusers').click(function () {
		$('#kpoll-moreusers-div').show();
	});
});
