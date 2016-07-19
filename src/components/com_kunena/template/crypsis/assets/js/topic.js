/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {

	/* To hide or open spoiler on click */
	$('.kspoiler').each(function( index ) {
		$( this ).click(function() {
			if ( !$(this).find('.kspoiler-content').is(':visible') ) {
				$(this).find('.kspoiler-content').show();
				$(this).find('.kspoiler-expand').hide();
				$(this).find('.kspoiler-hide').show();
			} else {
				$(this).find('.kspoiler-content').hide();
				$(this).find('.kspoiler-expand').show();
				$(this).find('.kspoiler-hide').hide();
			}
		});
	});

	/* To allow to close or open the quick-reply modal box */
	$('.openmodal').click(function () {
		var boxToOpen = $(this).attr('href');
		$(boxToOpen).css('visibility', 'visible');
	});

	$('[id^="login-link"]').click(function() {
		$(this).ready(function () {
			if ($('#userdropdown').is(":visible")) {
				$('#userdropdown').css('visibility', 'hidden');
				$('#userdropdown').css('display', 'none');
			}
			else {
				$('#userdropdown').css('display', 'inline-block');
				$('#userdropdown').css('visibility', 'visible');
			}
		});
	});

	$(document).ready(function() {
		if ($('#userdropdown').is(":visible")) {
				$(document).click(function() {
					$("#userdropdown").hide('slow');
			});
		}
	});

	$('#userdropdown').click(function(e){
		e.stopPropagation();
	});

	/* Button to show more info on profilebox */
	$(".heading").click(function () {
		if (!$(this).hasClass('heading-less')) {
			$(this).prev(".heading").show();
			$(this).hide();
			$(this).next(".content").slideToggle(500);
		} else {
			var content = $(this).next(".heading").show();
			$(this).hide();
			content.next(".content").slideToggle(500);
		}
	});

	/* On moderate page display subject or field to enter manually the topic ID */
	$('#kmod_topics').change(function () {
		var id_item_selected = $(this).val();
		if (id_item_selected != 0) {
			$('#kmod_subject').hide();
		} else {
			$('#kmod_subject').show();
		}

		if (id_item_selected == -1) {
			$('#kmod_targetid').show();
		} else {
			$('#kmod_targetid').hide();
		}
	});

	if ($.fn.jsSocials != undefined) {
		$("#share").jsSocials({
			showCount: true,
			showLabel: true,
			shares: ["email", "twitter", "facebook", "googleplus", "linkedin", "pinterest", "stumbleupon", "whatsapp"]
		});
		$('.jssocials-share-whatsapp').addClass('visible-phone');
	}

	$('#kmod_categories').change(function () {
		$.getJSON(
			kunena_url_ajax, {catid: $(this).val()}
		).done(function (json) {
			var first_item = $('#kmod_topics option:nth(0)').clone();
			var second_item = $('#kmod_topics option:nth(1)').clone();

			$('#kmod_topics').empty();
			first_item.appendTo('#kmod_topics');
			second_item.appendTo('#kmod_topics');

			$.each(json, function (index, object) {
				$.each(object, function (key, element) {
					$('#kmod_topics').append('<option value="' + element['id'] + '">' + element['subject'] + '</option>');
				});
			});
		});
	});
});
