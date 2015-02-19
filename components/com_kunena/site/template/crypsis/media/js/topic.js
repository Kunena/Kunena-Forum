/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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

});

