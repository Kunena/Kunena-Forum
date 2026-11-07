/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {

	/* To hide or open spoiler on click */
	$(document).on('click', '.kspoiler-expand', function (e) {
		e.preventDefault()
		const show = $(this.children[0]).val() === Joomla.Text._('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND');
		$(this.children[0]).val(show ? Joomla.Text._('COM_KUNENA_LIB_BBCODE_SPOILER_HIDE') : Joomla.Text._('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND'));
		$(this).parent().children('.kspoiler-wrapper').toggle()
	});

	/* To allow to close or open the quick-reply modal box */
	$('.openmodal').click(function () {
		const boxToOpen = $(this).attr('href');
		$(boxToOpen).css('visibility', 'visible');
	});

	/* Button to show more info on profilebox */
	$(".heading").click(function () {
		if (!$(this).hasClass('heading-less')) {
			$(this).prev(".heading").show();
			$(this).hide();
			$(this).next(".content").slideToggle(500);
		}
		else {
			const content = $(this).next(".heading").show();
			$(this).hide();
			content.next(".content").slideToggle(500);
		}
	});

	$('[id^="login-link"]').click(function () {
		$(this).ready(function () {
			if ($('#userdropdown').is(":visible")) {
				$(this).addClass('kdelay');
			}
			else {
				$('#userdropdown').css('display', 'inline-block');
				$('#userdropdown').css('visibility', 'visible').delay(500).queue(function () {
					$(this).addClass('kdelay');
				});
			}
		});
	});

	$(document).click(function () {
		$('.kdelay').css('display', 'none').removeClass('kdelay');
	});

	$('#userdropdown').click(function (e) {
		e.stopPropagation();
	});

	/* On moderate page display subject or field to enter manually the topic ID */
	$('#kmod_topics').change(function () {
		const id_item_selected = $(this).val();

		if (id_item_selected === 0) {
			$('#kmod_subject').hide();
		}
		else {
			$('#kmod_subject').show();
		}

		if (id_item_selected == -1) {
			$('#kmod_targetid').show();
		}
		else {
			$('#kmod_targetid').hide();
		}
	});

	$('#kmod_categories').change(function () {
		$.getJSON(
			kunena_url_ajax, {catid: $(this).val()}
		).done(function (json) {
			// Keep the fixed options (do not move, new topic, enter an id): their values are 0 or below, topics are above.
			const fixed_items = $('#kmod_topics option').filter(function () {
				return parseInt($(this).val(), 10) <= 0;
			}).clone();

			$('#kmod_topics').empty();
			fixed_items.appendTo('#kmod_topics');

			// Another category is a move: a message which would not move goes into a new topic there.
			if ($('#kmod_topics').val() == -2) {
				$('#kmod_topics').val(0).trigger('change');
			}

			$.each(json, function (index, object) {
				$.each(object, function (key, element) {
					// The subject is text typed by a member: it is set as text, never written as html.
					$('#kmod_topics').append($('<option>').val(element['id']).text(element['subject']));
				});
			});
		})
			.fail(function () {
				//TODO: handle the error of ajax request
			});
	});
});
