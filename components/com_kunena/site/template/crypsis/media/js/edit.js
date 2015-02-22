/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/**
 *  Helper function for to perform JSON request for preview
 */
var previewActive=false;

function kPreviewHelper(previewActive) {
	if (previewActive == true){
		if ( jQuery('#kbbcode-message').val() != null ) {
			jQuery.ajax({
				type: 'POST',
				url: jQuery('#kpreview_url').val(),
				async: false,
				dataType: 'json',
				data: {body : jQuery('#kbbcode-message').val() },
				success: function(data){
					jQuery('#kbbcode-preview').html(data.preview);
				}
			});
		}
	}
}

jQuery(document).ready(function() {

	/* To enabled emojis in kunena textera feature like on github */
	if ( jQuery('#kemojis_allowed').val() ) {
		var item = '';
		if ( jQuery('#kbbcode-message').length > 0 ) {
			item = '#kbbcode-message';
		} else if ( jQuery('.qreply').length > 0 ) 	{
			item = '.qreply';
		}

		if ( item!=undefined ) {
			jQuery(item).atwho({
				at: ":",
				tpl:"<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
				callbacks: {
					remote_filter: function(query, callback) {
						if(query.length > 0) {
							jQuery.ajax({
								url: jQuery( "#kurl_emojis" ).val(),
								data: {
									search : query
								},
								success: function(data) {
									callback(data.emojis);
								}
							});
						}
					}
				}
			});
		}
	}

	/* To display preview area when clicking on preview button */
	jQuery("#kbutton-preview").click(function() {
		var preview = jQuery("#kbbcode-preview");
		var message = jQuery("#kbbcode-message");

		if ( preview.length > 0 ) {
			if ( !preview.is(":visible") ) {
				preview.css('display', 'block');

				message.css('width', '95%');

				previewActive = true;
				kPreviewHelper(previewActive);
			} else {
				previewActive = false;
				preview.css('display', 'none');
				message.css('width', '95%');
			}
			preview.attr('class', 'kbbcode-preview-bottom controls');
			var height = message.css('height');
			preview.css('height', message.css('height'));
		}
	});

	jQuery('#kbbcode-message').bind('input propertychange', function() {
		kPreviewHelper(previewActive);
	});
});

