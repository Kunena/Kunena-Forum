/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function() {

	/* To hide or open spoiler on click */
	jQuery('.kspoiler').click(function() {
		if ( !jQuery('.kspoiler-content').is(':visible') ) {
			jQuery(this).find('.kspoiler-content').show();
			jQuery(this).find('.kspoiler-expand').hide();
			jQuery(this).find('.kspoiler-hide').show();
		} else {
			jQuery(this).find('.kspoiler-content').hide();
			jQuery(this).find('.kspoiler-expand').show();
			jQuery(this).find('.kspoiler-hide').hide();
		}
	});

	/* To allow to close or open the quick-reply modal box */
	jQuery('.openmodal').click(function() {
		var boxToOpen = jQuery(this).attr('href');
		jQuery(boxToOpen ).css('visibility', 'visible');
	});

	/* Button to show more info on profilebox */
	jQuery(".heading").click(function() {
		if ( !jQuery(this).hasClass('heading-less') ) {
			jQuery(this).prev(".heading").show();
			jQuery(this).hide();
			jQuery(this).next(".content").slideToggle(500);
		} else {
			var content = jQuery(this).next(".heading").show();
			jQuery(this).hide();
			content.next(".content").slideToggle(500);
		}
	});

	/* On moderate page display subject or field to enter manually the topic ID */
	jQuery('#kmod_topics').change(function() {
		var id_item_selected = jQuery(this).val();

		if (id_item_selected != 0) {
			jQuery('#kmod_subject').hide();
		} else {
			jQuery('#kmod_subject').show();
		}

		if (id_item_selected == -1) {
			jQuery('#kmod_targetid').show();
		} else {
			jQuery('#kmod_targetid').hide();
		}
	});

	jQuery('#kmod_categories').change(function() {
		jQuery.getJSON(
			kunena_url_ajax, { catid: jQuery(this).val() }
		).done(function( json ) {
			var first_item = jQuery('#kmod_topics option:nth(0)').clone();
			var second_item = jQuery('#kmod_topics option:nth(1)').clone();

			jQuery('#kmod_topics').empty();
			first_item.appendTo('#kmod_topics');
			second_item.appendTo('#kmod_topics');

			jQuery.each(json,function(index, object) {
				jQuery.each(object, function(key, element) {
					jQuery('#kmod_topics').append('<option value="'+element['id']+'">'+element['subject']+'</option>');
				});
			});
		});
	});
});

